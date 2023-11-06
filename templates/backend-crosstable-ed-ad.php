<?php
ob_start();
ob_implicit_flush();
if(isset($_POST["Export"])  && $_GET['page'] == "manage-connection-academic-epic"){
	header_remove(); 
	header('Content-Type: text/csv');  
	header('Content-Disposition: attachment; filename="academicxepicdata.csv"'); 
	global $wpdb;

	$header_row = array(
		'ED_Code',
		'AD_Code'
	);
	$data_rows = array();

	$results = $wpdb->get_results("SELECT * FROM wuos_referenceacademicxepic");
	foreach ( $results as $item ) 
	{
		$adCode = $wpdb->get_results("SELECT AD_Code FROM wuos_academicdepartment WHERE AD_ID = '".$item->AD_ID."'"); 
		$edCode = $wpdb->get_results("SELECT ED_Code FROM wuos_epicdepartment WHERE ED_ID = '".$item->ED_ID."'"); 
		$row = array(
			$edCode[0]->ED_Code,
			$adCode[0]->AD_Code
		);
		$data_rows[] = $row;
	}
	

	$fh = fopen( 'php://output', 'w' );
	fputcsv( $fh, $header_row );
	foreach ( $data_rows as $data_row ) 
	{
		fputcsv( $fh, $data_row );
	}

	
	die();
	ob_end_flush();
	fclose($fh);
}
    /** 
     * A page for managing Academic Department data
     **/
	function findEDID($code)
	{
		global $wpdb;
		$findEpic = $wpdb->get_results("SELECT ED_ID FROM wuos_epicdepartment WHERE ED_Code = '".$code."'"); 

		$returnID = 0;
		if(@count($findEpic) > 0)
		{
			$returnID = $findEpic[0]->ED_ID;
		}

		return $returnID;
	}

	function findDuplicateADxED($adID, $edID)
	{
		global $wpdb;
		$findDuplicate = $wpdb->get_results("SELECT COUNT(*) AS FindDuplicate FROM wuos_referenceacademicxepic rxe INNER JOIN wuos_epicdepartment ed on ed.ED_ID = rxe.ED_ID INNER JOIN wuos_academicdepartment ad on ad.AD_ID = rxe.AD_ID WHERE ed.ED_ID = ".$edID." AND ad.AD_ID = ".$adID);

		$returnCount = $findDuplicate[0]->FindDuplicate;

		return $returnCount;
	}

    function page_manage_epic_department_to_academic()
	{
	    ?>
		
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="<?php echo plugins_url('sortable-0.8.0/css/sortable-theme-dark.css', dirname(__FILE__)); ?>" />
		<script src="<?php echo plugins_url('sortable-0.8.0/js/sortable.min.js', dirname(__FILE__)); ?>"></script>

		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<h1>Manage Epic Department to Academic Departments</h1>
				</div>
			</div>

		<?php
		/** 
		 * Adds CSV data to Academic Department table on import
		 **/
			global $wpdb;
			if(isset($_POST['upload']))
			{
				$file = $_FILES['chooseFile']['name'];
				$file_data = $_FILES['chooseFile']['tmp_name'];
				$handle = fopen($file_data, "r");
				$c = 0;
				if($file)
				{
					while(($filesop = fgetcsv($handle, 1000, ",")) !== false)
					{
						if($c > 0)
						{
							$edCode = $filesop[0];
							$adCode = $filesop[1];

							if($edCode != "" && $adCode != "")
							{
								$edID = findEDID($edCode);
								$adID = findADID($adCode);

								if($edID > 0 && $adID > 0)
								{
									if(findDuplicateADxED($adID, $edID) < 1)
									{
										$data = array(
											'ED_ID' => $edID,
											'AD_ID' => $adID,
										);

										$wpdb->insert('wuos_referenceacademicxepic', $data);
									}
								}
							}
						}
						$c++;
					}

					if($c > 0)
					{
						echo "<h2>File has been imported</h2>";
					}
				}
			}

			if($_GET['update_list'] != "true"){ 
                $epicOptions = $wpdb->get_results( "SELECT DISTINCT ED_Name, ED_ID FROM wuos_epicdepartment GROUP BY(ED_ID)"); 
                $academicOptions = $wpdb->get_results( "SELECT DISTINCT AD_Name, AD_ID FROM wuos_academicdepartment GROUP BY(AD_ID)"); 
                $epicOptionsHTML = ""; 
                $academicOptionsHTML = ""; 
                foreach($epicOptions as $row){
                    $epicOptionsHTML .= "<option value='". $row->ED_ID . "'>". $row->ED_Name . "</option>";
                }
                foreach($academicOptions as $row){
                    $academicOptionsHTML .= "<option value='". $row->AD_ID . "'>".  $row->AD_Name . "</option>";
                }
			?>
             <div class="row">
				<div class="col-sm-12">
					<hr class="blackLine" />
				</div>
				<div class="col-sm-6">

					<div class="row">
                        <form action="" class="crosstable-ad-ed-form" id="crosstable-ad-ed-form" method="post"  >
							<label for="ad-id" className="col-sm-4 col-form-label">Academic Department:</label>
							<div class="col-sm-8 smallMarginBottom">
								<select class="form-select" id="ad-id" name="ad-id">
									<?php echo $academicOptionsHTML ?>
								</select>
							</div>
							<label for="ed-id" className="col-sm-4 col-form-label">Epic Department:</label>
							<div class="col-sm-8 smallMarginBottom">
								<select class="form-select" id="ed-id" name="ed-id" multiple>
									<?php echo $epicOptionsHTML ?>
								</select>
							</div>
                            <input type="hidden" name="action" value="send_form" style="display: none; visibility: hidden; opacity: 0;">
                            <input type="submit" value="Add Epic Department Connection" class="btn btn-primary">
                        </form>
                    </div>

                </div>

                <div class="col-sm-6">
					<form class="crosstable-ad-ed-upload-form backend-upload-form" method="post" enctype="multipart/form-data">
                    <div class="file-upload">
                        <div class="file-select">
							<div class="file-select-button" id="fileName">Import Connection List</div>
							<div class="file-select-name" id="noFile">No file chosen...</div> 
							<input type="file" name="chooseFile" id="chooseFile" required>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <input type="submit" name="upload" class="btn btn-primary">
                    </div>
					<div style="margin-top: 50px;">
						<strong>Download Sample Import CSV file</strong><br />
						<a href="/wp-content/plugins/washu-open_scheduling/SampleFiles/Sample_Academic_To_Epic_Connection.csv">Download</a>
					</div>
                    </form> 
                </div>

				<div class="col-sm-12">
					<hr class="blackLine" />
				</div>

            </div>
		
        <?php
		}
        global $wpdb;
        // Query to fetch academic department data from database table and storing in $results
        $results = $wpdb->get_results( "SELECT * FROM wuos_referenceacademicxepic"); 

        // Checks if academic department data exists in database and displays data in table if it exists
        if(!empty($results) && $_GET['update_list'] != "true")                        
        {
			$academicOptions = $wpdb->get_results( "SELECT DISTINCT AD_Name, AD_ID FROM wuos_academicdepartment GROUP BY(AD_ID)"); 
			$academicOptionsHTML = ""; 
			foreach($academicOptions as $row){
				$academicOptionsHTML .= "<option>". $row->AD_Name . "</option>";
			}
        ?>  

			<div class="row">
				<div class="col-sm">
					<input type="text" id="searchTable" onkeyup="searchTable()" placeholder="Search by Epic Dept" title="Search by Epic Dept" class="form-control">
				</div>
				<div class="col-sm">
					<select id="departmentDropdown" oninput="filterTable()" class="form-select" style="margin-top:30px;">
						<option>Filter by Academic Dept</option>
						<?php echo $academicOptionsHTML ?>
					</select>
				</div>
				<div class="col-sm">
					<form class="form-horizontal" action="" method="post" name="upload_excel"   
						enctype="multipart/form-data">
						<div class="form-group">
							<div class="col-md-4 col-md-offset-4">
								<input type="submit" name="Export" class="btn btn-success" style="margin-top:30px;" value="Export data as CSV"/>
							</div>
						</div>                    
					</form> 
				</div>
				<div class="col-sm">
					<button class="btn btn-danger delete-all-btn" style="margin-top:30px;" >Delete Selected</button>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">

					<table class="crosstable-ad-ed-info-table backend-info-table sortable-theme-light" id="crosstable-ad-ed-info-table" width='100%' data-sortable>
						<thead>
							<tr><th class="hidden-id">Row ID</th><th  data-sortable-type="alpha">Department ID</th><th>Academic Dept</th><th>Epic Dept ID</th><th>Epic Dept</th><th>Delete</th><th>Select</th></tr>
						</thead>
						<tbody>							
							<?php      
							foreach($results as $row)
							{
								$epicID = $row->ED_ID;
								$academicID = $row->AD_ID;
								$academicDepartment = $wpdb->get_results("SELECT * FROM wuos_academicdepartment WHERE AD_ID = '$academicID'"); 
								$epic = $wpdb->get_results("SELECT * FROM wuos_epicdepartment WHERE ED_ID = '$epicID'"); 
								?>
							<tr class="row-<?php echo $row->RAE_ID ?>">
									<td class="hidden-id"><?php echo $row->RAE_ID ?></td> 
									<td><?php echo $academicDepartment[0]->AD_Code ?></td> 
									<td><?php echo $academicDepartment[0]->AD_Name ?></td>
									<td ><?php echo $epic[0]->ED_Code ?></td> 
									<td><?php echo $epic[0]->ED_Name ?></td>                    
									<td><button class="btn btn-danger delete-btn" id="rae-id-<?php echo $row->RAE_ID ?>">D</button></td>
									<td><input type="checkbox" class="select-item" id="select-<?php echo $row->RAE_ID ?>" name="select-item"></td>
									<span class="delete-notification-overlay" id="delete-<?php echo $row->RAE_ID ?>">
									<span class="delete-notification">
										<h2>You are deleting the record for <?php echo $academicDepartment[0]->AD_Name . ' -> ' . $epic[0]->ED_Name ?></h2>
										<h2>Are you sure you want to delete?</h2>
										<span>
											<button class="btn btn-primary record-delete-btn" id="rae-id-<?php echo $row->RAE_ID ?>">Yes</button>
											<button class="btn btn-danger cancel-btn">Cancel</button>
										</span> 
									</span>
									</span>
									<span class="delete-all-overlay delete-notification-overlay">
									<span class="delete-notification">
									<h2>You are deleting all selected records.</h2>
										<h2>Are you sure you want to delete?</h2>
										<span>
											<button class="btn btn-primary record-delete-all-btn" >Yes</button>
											<button class="btn btn-danger cancel-btn">Cancel</button>
										</span> 
									</span>
									</span>    
							</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
        <?php
        }
		?>
		</div>
        <script>

		//Filters table by department name
		function filterTable() {
			// Variables
			let dropdown, table, rows, cells, department, filter, academic;
			dropdown = document.getElementById("departmentDropdown");
			table = document.getElementById("crosstable-ad-ed-info-table");
			rows = table.getElementsByTagName("tr");
			filter = dropdown.value;

			// Loops through rows and hides those with departments that don't match the filter
			for (let row of rows) { // `for...of` loops through the NodeList
				row.classList.remove("cat-filter-active");
				cells = row.getElementsByTagName("td");
				department = cells[2] || null; // gets the 2nd `td` or nothing
				academic = cells[4] || null;
				var searchInput = document.getElementById("searchTable");
				var searchFilter = searchInput.value.toUpperCase();
				var academicText = "";
				
				if(academic){
					academicText = academic.textContent;
				}

				// if the filter is set to 'All', or this is the header row, or 2nd `td` text matches filter
				if ((filter === "All" || !department || (filter === department.textContent))) {
					if(academicText.toUpperCase().indexOf(searchFilter) > -1){
						row.style.display = ""; // shows this row
					}
				}
				else {
				row.classList.add("cat-filter-active");
				}
			}
        }

		//Filter values within HTML table based on record ID
		function searchTable() {
			var input, filter, table, tr, td, i, txtValue;
			input = document.getElementById("searchTable");
			filter = input.value.toUpperCase();
			table = document.getElementById("crosstable-ad-ed-info-table");
			tr = table.getElementsByTagName("tr");
			for (i = 0; i < tr.length; i++) 
			{
				td = tr[i].getElementsByTagName("td")[4];
				if (td) 
				{
					txtValue = td.textContent || td.innerText;
					if (txtValue.toUpperCase().indexOf(filter) > -1) 
					{
						if(!tr[i].classList.contains('cat-filter-active')){
							tr[i].style.display = "";
						}
					} 
					else 
					{
						tr[i].style.display = "none";
					}
				}       
			}
		}
        //Ajax function that takes the academic department form data and utilizes it within PHP function
		jQuery( 'form#crosstable-ad-ed-form' ).on( 'submit', function(event) {
			event.preventDefault();
			event.stopPropagation();

			var adID = jQuery('#ad-id').val();
			var edID = jQuery('#ed-id').val();
			
			if(edID.length != 0){

				if(edID.length > 1){
					var edArray = edID;
				}
				else{
					var singleED = edID[0];
				}

				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'add_new_connection_ad_ed',
						'ad-id': adID,
						'ed-id': singleED,
						'ed-id-array': edArray	
					},
					success: function (data) {
						location.reload();
						return false;
					},
					error: function (error) {
						alert('something happened and the action did not complete');
					}
				});
			}
			else{
				alert('You must select at least one epic department.');
			}

		});

		jQuery( document ).ready(function() {

			//Shows modal to confirm deletion of academic department record
			jQuery( '.delete-btn' ).click(function(event) {
				var raeID = event.target.id.replace('rae-id-', '');
				var deleteNotification = document.querySelector("#delete-" + raeID)
				deleteNotification.style.display = "block";
				var pluginBody = document.querySelector("body");
				pluginBody.style.overflow = "hidden";
				
			});

			//Cancel delete academic department record
			jQuery( '.cancel-btn' ).click(function(event) {
				var deleteNotification = event.target.parentNode.parentNode.parentNode;
				deleteNotification.style.display = "none";
				var pluginBody = document.querySelector("body");
				pluginBody.style.overflow = "unset";
				
			});

			//Deletes academic department record from table and database after confirmation 
			jQuery( '.record-delete-btn' ).click(function(event) {
				var deleteNotification = event.target.parentNode.parentNode.parentNode;
				deleteNotification.style.display = "none";
				var pluginBody = document.querySelector("body");
				pluginBody.style.overflow = "unset";
				var raeID = event.target.id.replace('rae-id-', '');
				var removeRow = document.querySelector(".row-" + raeID);
				removeRow.remove();
				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'delete_connection_ad_ed',
						'rae-id': raeID
					},
					success: function (data) {
						location.reload();
						return false;
					},
					error: function (error) {
						alert('something happened and the action did not complete');
					}
				});	

			});	
		});

		//Shows modal to confirm deletion of selected records
		jQuery('.delete-all-btn').click(function(event) {
			var deleteNotification = document.querySelector(".delete-all-overlay");
			deleteNotification.style.display = "block";
			var pluginBody = document.querySelector("body");
			pluginBody.style.overflow = "hidden";
			
		});

		//Deletes selected connections records from table and database after confirmation 
		jQuery('.record-delete-all-btn').click(function(event) {
			var deleteNotification = event.target.parentNode.parentNode.parentNode;
			deleteNotification.style.display = "none";
			var pluginBody = document.querySelector("body");
			pluginBody.style.overflow = "unset";
			var allID = document.querySelectorAll(".select-item");
			var idString = "";
			allID.forEach((element) => {
				if(element.checked == true){
					idString = idString + element.id.replace('select-', '') + ",";
					var removeRow = document.querySelector(".row-" + element.id.replace('select-', ''));
					removeRow.remove();
				}
			});
			
			idString = idString.slice(0, -1);

			jQuery.ajax({
				type: 'POST',
				dataType: 'json',
				url: "<?php echo admin_url('admin-ajax.php'); ?>", 
				data: { 
					'action' : 'delete_selected_axe_connections',
					'selectedConnections': idString
				},
				success: function (data) {
                        location.reload();
                        return false;
                 },
                error: function (error) {
                    alert('something happened and the action did not complete');
                 }
			});
			
		});
		</script>
	<?php
	}

/**
 * This function takes the academic department form data after the Ajax call and stores it within the WordPress database
 */

add_action( 'wp_ajax_nopriv_add_new_connection_ad_ed', 'add_new_connection_ad_ed' );
add_action( 'wp_ajax_add_new_connection_ad_ed', 'add_new_connection_ad_ed' );
function add_new_connection_ad_ed(){
	global $wpdb;
    $adID= sanitize_text_field($_POST["ad-id"]);
    $edID= sanitize_text_field($_POST["ed-id"]);
    $edIDArray = $_POST["ed-id-array"];

	if($edID != "")
	{
		if(findDuplicateADxED($adID, $edID) < 1)
		{
			/// Query to fetch academic department data from database table and storing in $results
			$sqlInsert = "INSERT INTO wuos_referenceacademicxepic (AD_ID, ED_ID) 
			VALUES ($adID, $edID);";
			$wpdb->query($sqlInsert);
			
		}
	}
	else{
		foreach($edIDArray as $id){
			if(findDuplicateADxED($adID, $id) < 1)
			{
				/// Query to fetch academic department data from database table and storing in $results
				$sqlInsert = "INSERT INTO wuos_referenceacademicxepic (AD_ID, ED_ID) 
				VALUES ($adID, $id);";
				$wpdb->query($sqlInsert);
				
			}
		}
	}

	return;
	wp_die();
}


/**
 * This function deletes existing academic department data
 */

add_action( 'wp_ajax_nopriv_delete_connection_ad_ed', 'delete_connection_ad_ed' );
add_action( 'wp_ajax_delete_connection_ad_ed', 'delete_connection_ad_ed' );
function delete_connection_ad_ed(){
	global $wpdb;
	$raeID = sanitize_text_field($_POST["rae-id"]);

    /// Query to fetch academic department data from database table and storing in $results
    $sqlDelete = "DELETE from wuos_referenceacademicxepic 
    WHERE RAE_ID = '$raeID'";
    $wpdb->query($sqlDelete);
	return;
    wp_die();

}

/**
 * This function deletes selected connection data
 */

add_action( 'wp_ajax_nopriv_delete_selected_axe_connections', 'delete_selected_axe_connections' );
add_action( 'wp_ajax_delete_selected_axe_connections', 'delete_selected_axe_connections' );
function delete_selected_axe_connections(){
	global $wpdb;
	$selectedConnections = sanitize_text_field($_POST["selectedConnections"]);

	$selectedArray = explode(",",$selectedConnections);

	foreach($selectedArray as $id){
		 /// Query to fetch academic department data from database table and storing in $results
		 $sqlDelete = "DELETE from wuos_referenceacademicxepic 
		 WHERE RAE_ID = '$id'";
		 $wpdb->query($sqlDelete);
	}
	return;
	wp_die();

}