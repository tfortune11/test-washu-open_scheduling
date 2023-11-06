<?php
ob_start();
ob_implicit_flush();
if(isset($_POST["Export"]) && $_GET['page'] == "manage-connection-academic-visit"){
	header_remove(); 

	header('Content-Type: text/csv');  
	header('Content-Disposition: attachment; filename="visitxacademicdata.csv"'); 
	global $wpdb;

	$header_row = array(
		'VT_Code',
		'AD_Code'
	);
	$data_rows = array();

	$results = $wpdb->get_results("SELECT * FROM wuos_referenceacademicxvisit");
	foreach ( $results as $item ) 
	{
		$adCode = $wpdb->get_results("SELECT AD_Code FROM wuos_academicdepartment WHERE AD_ID = '".$item->AD_ID."'"); 
		$vtCode = $wpdb->get_results("SELECT VT_VisitTypeID FROM wuos_visittypelist WHERE VT_ID = '".$item->VT_ID."'"); 

		$row = array(
			$vtCode[0]->VT_VisitTypeID,
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
	function findVTID($code)
	{
		global $wpdb;
		$findVisit = $wpdb->get_results("SELECT VT_ID FROM wuos_visittypelist WHERE VT_VisitTypeID = '".$code."'"); 

		$returnID = 0;
		if(@count($findVisit) > 0)
		{
			$returnID = $findVisit[0]->VT_ID;
		}

		return $returnID;
	}

	function findDuplicateADxVT($adID, $vtID)
	{
		global $wpdb;
		$findDuplicate = $wpdb->get_results("SELECT COUNT(*) AS FindDuplicate FROM wuos_referenceacademicxvisit rxv INNER JOIN wuos_visittypelist vt on vt.VT_ID = rxv.VT_ID INNER JOIN wuos_academicdepartment ad on ad.AD_ID = rxv.AD_ID WHERE vt.VT_ID = ".$vtID." AND ad.AD_ID = ".$adID);

		$returnCount = $findDuplicate[0]->FindDuplicate;

		return $returnCount;
	}

    function page_manage_visit_type_to_academic()
	{
	    ?>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="<?php echo plugins_url('sortable-0.8.0/css/sortable-theme-dark.css', dirname(__FILE__)); ?>" />
		<script src="<?php echo plugins_url('sortable-0.8.0/js/sortable.min.js', dirname(__FILE__)); ?>"></script>

		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<h1>Manage Visit Type to Academic Departments</h1>
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
							$vtCode = $filesop[0];
							$adCode = $filesop[1];

							if($vtCode != "" && $adCode != "")
							{
								$vtID = findVTID($vtCode);
								$adID = findADID($adCode);
								
								if($vtID > 0 && $adID > 0)
								{
									if(findDuplicateADxVT($adID, $vtID) < 1)
									{
										$data = array(
											'VT_ID' => $vtID,
											'AD_ID' => $adID,
										);
										$wpdb->insert('wuos_referenceacademicxvisit' , $data);
									}
								}
							}
						}
						$c++;
					}
				}
			}

			if($_GET['update_list'] != "true"){ 
                $visitTypeOptions = $wpdb->get_results( "SELECT DISTINCT VT_Name, VT_ID FROM wuos_visittypelist GROUP BY(VT_ID)"); 
                $academicOptions = $wpdb->get_results( "SELECT DISTINCT AD_Name, AD_ID FROM wuos_academicdepartment  GROUP BY(AD_ID)"); 
                $visitTypeOptionsHTML = ""; 
                $academicOptionsHTML = ""; 
                foreach($visitTypeOptions as $row){
                    $visitTypeOptionsHTML .= "<option value='". $row->VT_ID . "'>".  $row->VT_Name . "</option>";
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
                        <form action="" class="crosstable-ad-vt-form" id="crosstable-ad-vt-form" method="post"  >
						<label for="ad-id" className="col-sm-4 col-form-label">Academic Department:</label>
							<div class="col-sm-8 smallMarginBottom">
								<select class="form-select" id="ad-id" name="ad-id">
									<?php echo $academicOptionsHTML ?>
								</select>
							</div>
							<label for="visit-type" className="col-sm-4 col-form-label">Visit Type:</label>
							<div class="col-sm-8 smallMarginBottom">
                            	<select class="form-select" id="vt-id" name="vt-id" multiple>
									<?php echo $visitTypeOptionsHTML ?>
								</select>
							</div>					
                            <input type="hidden" name="action" value="send_form" style="display: none; visibility: hidden; opacity: 0;">
                            <input type="submit" value="Add Visit Type Connection" class="btn btn-primary">
                        </form>
                    </div>

                </div>

                <div class="col-sm-6">
					<form class="crosstable-ad-vt-upload-form backend-upload-form" method="post" enctype="multipart/form-data">
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
						<a href="/wp-content/plugins/washu-open_scheduling/SampleFiles/Sample_Academic_To_Visit_Connection.csv">Download</a>
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
        $results = $wpdb->get_results( "SELECT * FROM wuos_referenceacademicxvisit"); 

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
					<input type="text" id="searchTable" onkeyup="searchTable()" placeholder="Search by Visit Type" title="Search by Visit Type" class="form-control">
				</div>
				<div class="col-sm">
					<select id="departmentDropdown" oninput="filterTable()" class="form-select"  style="margin-top:30px;">
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

					<table class="crosstable-ad-vt-info-table backend-info-table sortable-theme-light" id="crosstable-ad-vt-info-table" width='100%' data-sortable>
						<thead>
							<tr><th class="hidden-id">Row ID</th><th data-sortable-type="alpha">Department ID</th><th>Academic Dept</th><th >Visit Type ID</th><th>Visit Type</th><th>Delete</th><th>Select</th></tr>
						</thead>
						<tbody>					
							<?php      
							foreach($results as $row){
								$visitTypeID = $row->VT_ID;
								$academicID = $row->AD_ID;
								$academicDepartment = $wpdb->get_results("SELECT * FROM wuos_academicdepartment WHERE AD_ID = '$academicID'"); 
								$visitType = $wpdb->get_results("SELECT * FROM wuos_visittypelist WHERE VT_ID = '$visitTypeID'"); 
								?>
								<tr class="row-<?php echo $row->RAV_ID ?>">
									<td class="hidden-id"><?php echo $row->RAV_ID ?></td>
									<td><?php echo $academicDepartment[0]->AD_Code ?></td> 
									<td><?php echo $academicDepartment[0]->AD_Name ?></td> 
									<td ><?php echo $visitType[0]->VT_VisitTypeID ?></td> 
									<td><?php echo $visitType[0]->VT_Name ?></td>
									<td><button class="btn btn-danger delete-btn" id="rav-id-<?php echo $row->RAV_ID ?>">D</button></td>
									<td><input type="checkbox" class="select-item" id="select-<?php echo $row->RAV_ID ?>" name="select-item"></td>
									<span class="delete-notification-overlay" id="delete-<?php echo $row->RAV_ID ?>">
									<span class="delete-notification">
										<h2>You are deleting the record for <?php echo $academicDepartment[0]->AD_Name . ' -> ' . $visitType[0]->VT_Name ?></h2>
										<h2>Are you sure you want to delete?</h2>
										<span>
											<button class="btn btn-primary record-delete-btn" id="rav-id-<?php echo $row->RAV_ID ?>">Yes</button>
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
			table = document.getElementById("crosstable-ad-vt-info-table");
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
			table = document.getElementById("crosstable-ad-vt-info-table");
			tr = table.getElementsByTagName("tr");
			for (i = 0; i < tr.length; i++) {
				td = tr[i].getElementsByTagName("td")[4];
				if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					if(!tr[i].classList.contains('cat-filter-active')){
							tr[i].style.display = "";
					}
				} else {
					tr[i].style.display = "none";
				}
				}       
			}
		}
        //Ajax function that takes the academic department form data and utilizes it within PHP function
		jQuery( 'form#crosstable-ad-vt-form' ).on( 'submit', function(event) {
			event.preventDefault();
			event.stopPropagation();

			var adID = jQuery('#ad-id').val();
			var vtID = jQuery('#vt-id').val();

			if(vtID.length != 0){

				if(vtID.length > 1){
					var vtArray = vtID;
				}
				else{
					var singleVT = vtID[0];
				}
				
				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'add_new_connection_ad_vt',
						'ad-id': adID,
						'vt-id': singleVT,
						'vt-id-array': vtArray	
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
				alert('You must select at least one visit type.');
			}

		});

		jQuery( document ).ready(function() {

			//Shows modal to confirm deletion of academic department record
			jQuery( '.delete-btn' ).click(function(event) {
				var ravID = event.target.id.replace('rav-id-', '');
				var deleteNotification = document.querySelector("#delete-" + ravID)
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
				var ravID = event.target.id.replace('rav-id-', '');
				var removeRow = document.querySelector(".row-" + ravID);
				removeRow.remove();
				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'delete_connection_ad_vt',
						'rav-id': ravID
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
					'action' : 'delete_selected_axv_connections',
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

add_action( 'wp_ajax_nopriv_add_new_connection_ad_vt', 'add_new_connection_ad_vt' );
add_action( 'wp_ajax_add_new_connection_ad_vt', 'add_new_connection_ad_vt' );
function add_new_connection_ad_vt(){
	global $wpdb;
    $adID= sanitize_text_field($_POST["ad-id"]);
	$vtID= sanitize_text_field($_POST["vt-id"]);
    $vtIDArray = $_POST["vt-id-array"];

	if($vtID != "")
	{
		if(findDuplicateADxVT($adID, $vtID) < 1)
		{
			/// Query to fetch academic department data from database table and storing in $results
			$sqlInsert = "INSERT INTO wuos_referenceacademicxvisit (AD_ID, VT_ID) 
			VALUES ('$adID', '$vtID');";
			$wpdb->query($sqlInsert);
			return;
			wp_die();
		}
	}
	else{
		foreach($vtIDArray as $id){
			if(findDuplicateADxVT($adID, $id) < 1)
			{
				/// Query to fetch academic department data from database table and storing in $results
				$sqlInsert = "INSERT INTO wuos_referenceacademicxvisit (AD_ID, VT_ID) 
				VALUES ('$adID', '$id');";
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

add_action( 'wp_ajax_nopriv_delete_connection_ad_vt', 'delete_connection_ad_vt' );
add_action( 'wp_ajax_delete_connection_ad_vt', 'delete_connection_ad_vt' );
function delete_connection_ad_vt(){
	global $wpdb;
	$ravID = sanitize_text_field($_POST["rav-id"]);

    /// Query to fetch academic department data from database table and storing in $results
    $sqlDelete = "DELETE from wuos_referenceacademicxvisit 
    WHERE RAV_ID = '$ravID'";
    $wpdb->query($sqlDelete);
	return;
    wp_die();

}

/**
 * This function deletes selected connection data
 */

add_action( 'wp_ajax_nopriv_delete_selected_axv_connections', 'delete_selected_axv_connections' );
add_action( 'wp_ajax_delete_selected_axv_connections', 'delete_selected_axv_connections' );
function delete_selected_axv_connections(){
	global $wpdb;
	$selectedConnections = sanitize_text_field($_POST["selectedConnections"]);

	$selectedArray = explode(",",$selectedConnections);

	foreach($selectedArray as $id){
		/// Query to fetch academic department data from database table and storing in $results
		$sqlDelete = "DELETE from wuos_referenceacademicxvisit 
		WHERE RAV_ID = '$id'";
		$wpdb->query($sqlDelete);
	}
	return;
	wp_die();

}