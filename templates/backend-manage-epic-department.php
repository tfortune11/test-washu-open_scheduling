<?php
ob_start();
ob_implicit_flush();
if(isset($_POST["Export"]) && $_GET['page'] == "manage-epic-departments"){
	header_remove(); 

	header('Content-Type: text/csv');  
	header('Content-Disposition: attachment; filename="epicdepartmentdata.csv"'); 
	global $wpdb;

	$header_row = array(
		'ED_Code',
		'ED_Name'
	);
	$data_rows = array();

	$results = $wpdb->get_results("SELECT * FROM wuos_epicdepartment");
	foreach ( $results as $item ) 
	{
		$row = array(
			$item->ED_Code,
			$item->ED_Name
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
     * A page for managing Epic Department data
     **/
	function getED($code)
	{
		global $wpdb;
		$findEDInfo = $wpdb->get_results("SELECT ED_Code, ED_Name FROM wuos_epicdepartment WHERE ED_Code = '$code'"); 

		$returnData = null;
		if(@count($findEDInfo) > 0)
		{
			$returnData = $findEDInfo[0];
		}

		return $returnData;
	}

    function page_manage_epic_departments()
	{
	    ?>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="<?php echo plugins_url('sortable-0.8.0/css/sortable-theme-dark.css', dirname(__FILE__)); ?>" />
		<script src="<?php echo plugins_url('sortable-0.8.0/js/sortable.min.js', dirname(__FILE__)); ?>"></script>
		
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<h1>Manage Epic Departments</h1>
				</div>
			</div>

		<?php
		/** 
		 * Adds CSV data to Epic Department table on import
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
							$code = $filesop[0];
							$name = $filesop[1];

							
							if($code != "" && $name != "")
							{
								$edID = findEDID($code);

								$data = array(
									'ED_Code' => $code,
									'ED_Name' => $name
								);
								
								if($edID > 0)
								{
									$edInfo = getED($code);
									if($name != $edInfo->ED_Name)
									{
										$wpdb->insert('wuos_replaceepicdepartment' , $data);
									}
								}
								else 
								{
									$wpdb->insert('wuos_epicdepartment' , $data);
								}
							}
						}
						$c++;
					}
				}
			}

			// Query to fetch duplicate epic department data from imported file
			$updateResults = $wpdb->get_results( "SELECT * FROM wuos_replaceepicdepartment"); 

			// Checks if duplicate data exists
			if(!empty($updateResults))                        
			{
				if($_GET['update_list'] != "true"){ 
			?>  
				<h2>Matching Records were found from the imported data</h2>
				<a href="/wp-admin/admin.php?page=manage-epic-departments&update_list=true" class="btn btn-warning matching-records-link">View Matching Records</a>
				<?php
				}
			} 
			if($_GET['update_list']== "true"){ 
				?>
				<a href="/wp-admin/admin.php?page=manage-epic-departments" class="matching-records-link">Back to previous page</a>
				<?php
				if(!empty($updateResults))                        
				{
				?>
					<h2>Update or decline existing records</h2>
					<table class="epic-department-info-table backend-info-table sortable-theme-light" id="epic-department-info-table" width='100%' data-sortable>
					<thead>
							<tr><th>Existing Record</th>
							<th>Updated Record</th>
							<th>Accept Changes</th>
							<th>Decline Changes</th></tr>
						</thead>
						<tbody>
							
							<?php      
							foreach($updateResults as $row){
								$existingRow = $wpdb->get_results("SELECT * FROM wuos_epicdepartment WHERE ED_Code = '$row->ED_Code'"); 
								?>
							<tr>
									<td class="existing-code-id">
									<strong>Department ID:</strong> <?php echo $existingRow[0]->ED_Code ?></br>
									<strong>Department Name:</strong> <?php echo $existingRow[0]->ED_Name ?></br>
									</td> 
									<td class="updated-code-id"> 
									<strong>Department ID: </strong><?php echo $row->ED_Code ?></br>
									<strong>Department Name:</strong> <?php echo $row->ED_Name ?></br>
									</td> 
									<td><button class="btn btn-primary accept-btn" id="accept-<?php echo $row->ED_Code ?>">Accept</button></td>
									<td><button class="btn btn-danger deny-btn" id="code-<?php echo $row->ED_Code ?>">Decline</button></td>      
							</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				<?php
				}
			}
			if($_GET['update_list'] != "true"){ 
			?>

			<div class="row">
				<div class="col-sm-12">
					<hr class="blackLine" />
				</div>
				<div class="col-sm-6">

					<div class="row">
						<form action="" class="epic-department-form" id="epic-department-form" method="post"  >
							<label for="code" className="col-sm-4 col-form-label">Department ID:</label>
							<div class="col-sm-8 smallMarginBottom">
								<input id= "code" type="text" name="code-input" class="form-control">
							</div>
							<label for="name" className="col-sm-4 col-form-label">Department Name:</label>
							<div class="col-sm-8 smallMarginBottom">
								<input id= "name" type="text" name="name" class="form-control">
							</div>
							<input type="hidden" name="action" value="send_form" style="display: none; visibility: hidden; opacity: 0;">
							<input type="submit" value="Submit" class="btn btn-primary">
						</form>
					</div>

					<div class="epic-department-form-container backend-form-container update-form-container">
						<div class="row">
							<form action="" class="epic-department-form update-form" id="update-form" method="post">
								<div class="row">
									<div class="col-sm-12">
										<h2>Update Epic Department Record</h2>
									</div>
								</div>

							
								<label for="name" className="col-sm-4 col-form-label">Department Name:</label>
								<div class="col-sm-12 smallMarginBottom">
									<input id= "code" type="hidden" name="code-input">
									<input id= "name" type="text" name="name" class="form-control">
								</div>
								<div class="col-sm-12">
									<input type="hidden" name="action" value="send_form" style="display: none; visibility: hidden; opacity: 0;">
									<input class="btn btn-primary" type="submit" value="Save">
									<button type="button" class="btn btn-danger btnFix" onClick="hideModal()" style="margin-bottom:20px;">Cancel</button>
								</div>
							</form>
						</div>
					</div>	

				</div>

				<div class="col-sm-6">
					<form class="epic-department-upload-form backend-upload-form" method="post" enctype="multipart/form-data">
						<div class="file-upload">
							<div class="file-select">
							<div class="file-select-button" id="fileName">Import Epic Department List</div>
							<div class="file-select-name" id="noFile">No file chosen...</div> 
							<input type="file" name="chooseFile" id="chooseFile" required >
							</div>
						</div>
						<div class="form-group" style="margin-top: 10px;">
							<input type="submit" name="upload" class="btn btn-primary">
						</div>

						<div style="margin-top: 50px;">
							<strong>Download Sample Import CSV file</strong><br />
							<a href="/wp-content/plugins/washu-open_scheduling/SampleFiles/Sample_Epic_Dept.csv">Download</a>
						</div>
					</form>
			</div>
		</div>
		
        <?php
		}
		
        // Query to fetch epic department data from database table and storing in $results
        $results = $wpdb->get_results( "SELECT * FROM wuos_epicdepartment"); 

        // Checks if epic department data exists in database and displays data in table if it exists
        if(!empty($results) && $_GET['update_list'] != "true")                        
        {
        ?>  
			<div class="row">
				<div class="col-sm">
					<input type="text" id="searchTable" class="form-control" onkeyup="searchTable()" placeholder="Search Department Name" title="Type in a name">
					
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
					<table class="epic-department-info-table backend-info-table sortable-theme-light" id="epic-department-info-table" width='100%' data-sortable>
						<thead>
							<tr><th class="hidden-id">Row ID</th><th data-sortable-type="alpha">Department ID</th><th>Department Name</th><th>Edit</th><th>Delete</th><th>Select</th></tr>
						</thead>
						<tbody>
							
							<?php      
							foreach($results as $row){
								?>
							<tr class="row-<?php echo $row->ED_Code ?>">
									<td class="hidden-id"><?php echo $row->ED_ID ?></td> 
									<td class="code-id"><?php echo $row->ED_Code ?></td> 
									<td><?php echo $row->ED_Name ?></td>
									<td><button class="btn btn-primary record-edit-btn" id="edit-<?php echo $row->ED_Code ?>">+</button></td>
									<td><button class="btn btn-danger delete-btn" id="code-<?php echo $row->ED_Code ?>">D</button></td>
									<td><input type="checkbox" class="select-item" id="select-<?php echo $row->ED_Code ?>" name="select-item"></td>
									<span class="delete-notification-overlay" id="delete-<?php echo $row->ED_Code ?>">
									<span class="delete-notification">
										<h2>You are deleting the record for <?php echo $row->ED_Name ?></h2>
										<h2>Are you sure you want to delete?</h2>
										<span>
											<button class="btn btn-primary record-delete-btn" id="code-<?php echo $row->ED_Code ?>">Yes</button>
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

		//Filter values within HTML table based on Code
		function searchTable() {
			var input, filter, table, tr, td, i, txtValue;
			input = document.getElementById("searchTable");
			filter = input.value.toUpperCase();
			table = document.getElementById("epic-department-info-table");
			tr = table.getElementsByTagName("tr");
			for (i = 0; i < tr.length; i++) {
				td = tr[i].getElementsByTagName("td")[2];
				if (td) {
				txtValue = td.textContent || td.innerText;
				if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
				} else {
					tr[i].style.display = "none";
				}
				}       
			}
		}
        //Ajax function that takes the epic department form data and utilizes it within PHP function
		jQuery( 'form#epic-department-form' ).on( 'submit', async function(e) {
			e.preventDefault();
			e.stopPropagation();
		
			var code = jQuery('#code').val();
			var name = jQuery('#name').val();

			if(name != "" && code != ""){

				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'add_new_epic_department',
						'code': code,
						'name': name	
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
				alert('You must enter a department id and name.');
			}
		});

		//Ajax function that updates epic department data based on the popup modal form
		jQuery( 'form#update-form' ).on( 'submit', async function(e) {
			e.preventDefault();
			e.stopPropagation();
			
			var updateModal = document.querySelector(".update-form-container");
			updateModal.style.display = "none";
			var code = jQuery('.update-form #code').val();
			var name = jQuery('.update-form #name').val();
			jQuery.ajax({
				type: 'POST',
				dataType: 'json',
				url: "<?php echo admin_url('admin-ajax.php'); ?>", 
				data: { 
					'action' : 'edit_epic_department',
					'code': code,
					'name': name	
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

		//Shows the edit epic department record popup modal
		jQuery('.record-edit-btn').on( 'click', function(event) {
			var code = event.target.id.replace('edit-', '');
			var formVals = event.target.parentNode.parentNode.children;
			jQuery(".update-form #code").val(formVals[1].innerText);
			jQuery(".update-form #name").val(formVals[2].innerText);
			
			var updateModal = document.querySelector(".update-form-container");
			updateModal.id = "event-" + code;
			var pluginBody = document.querySelector("body");
			updateModal.style.display = "block";
			pluginBody.style.overflow = "hidden";
		});

		//Hide Update Modal
		function hideModal() {
			var updateModal = document.querySelector(".update-form-container");
			var pluginBody = document.querySelector("body");
			pluginBody.style.overflow = "unset";
			updateModal.style.display = "none";
		}
	
		jQuery( document ).ready(function() {

			//Shows modal to confirm deletion of epic department record
			jQuery( '.delete-btn' ).click(function(event) {
				var code = event.target.id.replace('code-', '');
				var deleteNotification = document.querySelector("#delete-" + code)
				deleteNotification.style.display = "block";
				var pluginBody = document.querySelector("body");
				pluginBody.style.overflow = "hidden";
				
			});

			//Cancel delete epic department record
			jQuery( '.cancel-btn' ).click(function(event) {
				var deleteNotification = event.target.parentNode.parentNode.parentNode;
				deleteNotification.style.display = "none";
				var pluginBody = document.querySelector("body");
				pluginBody.style.overflow = "unset";
				
			});

			//Deletes epic department record from table and database after confirmation 
			jQuery( '.record-delete-btn' ).click(function(event) {
				var deleteNotification = event.target.parentNode.parentNode.parentNode;
				deleteNotification.style.display = "none";
				var pluginBody = document.querySelector("body");
				pluginBody.style.overflow = "unset";
				var code = event.target.id.replace('code-', '');
				var removeRow = document.querySelector(".row-" + code);
				removeRow.remove();
				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'delete_epic_department',
						'code': code
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

			//Shows modal to confirm deletion of epic department records
			jQuery('.delete-all-btn').click(function(event) {
				var deleteNotification = document.querySelector(".delete-all-overlay");
				deleteNotification.style.display = "block";
				var pluginBody = document.querySelector("body");
				pluginBody.style.overflow = "hidden";
				
			});

			//Deletes selected epic department records from table and database after confirmation 
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
						'action' : 'delete_selected_epic_departments',
						'selectedDepartments': idString
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

			//Accepts updated epic department record based on CSV import
			jQuery('.accept-btn').click(function(event) {
				var code = event.target.id.replace('accept-', '');
				event.target.parentNode.parentNode.remove();
				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'accept_updated_epic_department',
						'code': code
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


			//Deny and remove updated epic department record from table and keeps the record with matching code unchanged
			jQuery('.deny-btn').click(function(event) {
				var code = event.target.id.replace('code-', '');
				event.target.parentNode.parentNode.remove();
				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'delete_updated_epic_department',
						'code': code
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
		</script>
	<?php
	}

/**
 * This function takes the epic department form data after the Ajax call and stores it within the WordPress database
 */

add_action('wp_ajax_nopriv_add_new_epic_department', 'add_new_epic_department');
add_action('wp_ajax_add_new_epic_department', 'add_new_epic_department');
function add_new_epic_department(){
	global $wpdb;
	$code = sanitize_text_field($_POST["code"]);
	$name = sanitize_text_field($_POST["name"]);

	if($name != "")
	{
		$edID = findEDID($code);

		$data = array(
			'ED_Code' => $code,
			'ED_Name' => $name
		);

		if($edID > 0)
		{
			$edInfo = getED($code);

			if($name != $edInfo->ED_Name)
			{
				$wpdb->insert('wuos_replaceepicdepartment' , $data);
			}
		}
		else 
		{
			$wpdb->insert('wuos_epicdepartment' , $data);
		}
		return;
		wp_die();
	}
}



/**
 * This function takes the epic department update form data after the Ajax call and stores it within the WordPress database
 */

add_action( 'wp_ajax_nopriv_edit_epic_department', 'edit_epic_department' );
add_action( 'wp_ajax_edit_epic_department', 'edit_epic_department' );
function edit_epic_department(){
	global $wpdb;
	$code = sanitize_text_field($_POST["code"]);
	$name = sanitize_text_field($_POST["name"]);

	if($name != "")
	{
		/// Query to fetch epic department data from database table and storing in $results
		$sqlUpdate = "UPDATE wuos_epicdepartment SET ED_Name = '$name' WHERE ED_Code = '$code'";
		$wpdb->query($sqlUpdate);

		$message = array('message' => 'completed');
		return $message;
		
		wp_die();
	}
}

/**
 * This function deletes existing epic department data
 */

add_action( 'wp_ajax_nopriv_delete_epic_department', 'delete_epic_department' );
add_action( 'wp_ajax_delete_epic_department', 'delete_epic_department' );
function delete_epic_department()
{
	global $wpdb;
	$code = sanitize_text_field($_POST["code"]);
	$name = sanitize_text_field($_POST["name"]);

    /// Query to fetch epic department data from database table and storing in $results
    $sqlDelete = "DELETE from wuos_epicdepartment WHERE ED_Code = '$code'";
	$sqlDeleteMatches = "DELETE from wuos_replaceepicdepartment WHERE ED_Code = '$code'";

	$rowID = $wpdb->get_var("SELECT ED_ID FROM wuos_epicdepartment WHERE ED_Code = '$code'"); 
	$referenceDelete = "DELETE from wuos_referenceacademicxepic WHERE ED_ID = '$rowID'";

    $wpdb->query($sqlDelete);
	$wpdb->query($sqlDeleteMatches);
    $wpdb->query($referenceDelete);
	
    return;
	wp_die();
}

/**
 * This function deletes selected epic department data
 */

add_action( 'wp_ajax_nopriv_delete_selected_epic_departments', 'delete_selected_epic_departments' );
add_action( 'wp_ajax_delete_selected_epic_departments', 'delete_selected_epic_departments' );
function delete_selected_epic_departments(){
	global $wpdb;
	$selectedDepartments = sanitize_text_field($_POST["selectedDepartments"]);

	$selectedArray = explode(",",$selectedDepartments);

	foreach($selectedArray as $id){
		/// Query to fetch provider data from database table and storing in $results
		$sqlDelete = "DELETE from wuos_epicdepartment WHERE ED_Code = '$id'";
		$sqlDeleteMatches = "DELETE from wuos_replaceepicdepartment WHERE ED_Code = '$id'";

		$rowID = $wpdb->get_var("SELECT ED_ID FROM wuos_epicdepartment WHERE ED_Code = '$id'"); 
		$referenceDelete = "DELETE from wuos_referenceacademicxepic WHERE ED_ID = '$rowID'";

		$wpdb->query($sqlDelete);
		$wpdb->query($sqlDeleteMatches);
		$wpdb->query($referenceDelete);
	}
	return;
	wp_die();

}

/**
 * This function accepts updated epic department data from import
 */

add_action( 'wp_ajax_nopriv_accept_updated_epic_department', 'accept_updated_epic_department' );
add_action( 'wp_ajax_accept_updated_epic_department', 'accept_updated_epic_department' );
function accept_updated_epic_department()
{
	global $wpdb;
	$code = sanitize_text_field($_POST["code"]);

    /// Query to fetch epic department data from database table and storing in $results
    $updatedRow = $wpdb->get_results("SELECT * FROM wuos_replaceepicdepartment WHERE ED_Code = '$code'");
	$updatedName = $updatedRow[0]->ED_Name; 

	$sqlUpdate = "UPDATE wuos_epicdepartment SET ED_Name = '$updatedName' WHERE ED_Code = '$code'";
	$wpdb->query($sqlUpdate);

	$wpdb->query("DELETE from wuos_replaceepicdepartment WHERE ED_Code = '$code'");
	return;
	wp_die();
}

/**
 * This function deletes updated epic department data from import
 */

add_action( 'wp_ajax_nopriv_delete_updated_epic_department', 'delete_updated_epic_department' );
add_action( 'wp_ajax_delete_updated_epic_department', 'delete_updated_epic_department' );
function delete_updated_epic_department()
{
	global $wpdb;
	$code = sanitize_text_field($_POST["code"]);
	$wpdb->query("DELETE from wuos_replaceepicdepartment WHERE ED_Code = '$code'");
	return;
	wp_die();

}