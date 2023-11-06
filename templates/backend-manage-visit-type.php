<?php
ob_start();
ob_implicit_flush();
	if(isset($_POST["Export"]) && $_GET['page'] == "manage-visit-type")
	{
		header_remove(); 

		header('Content-Type: text/csv');  
		header('Content-Disposition: attachment; filename="visittypedata.csv"'); 
		global $wpdb;

		$header_row = array(
			'VT_VisitTypeID',
			'VT_Name'
		);
		$data_rows = array();

		$results = $wpdb->get_results("SELECT * FROM wuos_visittypelist");
		foreach ( $results as $item ) 
		{
			$row = array(
				$item->VT_VisitTypeID,
				$item->VT_Name
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
     * A page for managing Visit Type data
     **/
	function getVT($code)
	{
		global $wpdb;
		$findVTInfo = $wpdb->get_results("SELECT VT_VisitTypeID, VT_Name FROM wuos_visittypelist WHERE VT_VisitTypeID = '$code'"); 

		$returnData = null;
		if(@count($findVTInfo) > 0)
		{
			$returnData = $findVTInfo[0];
		}

		return $returnData;
	}

    function page_manage_visit_type()
	{
	    ?>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="<?php echo plugins_url('sortable-0.8.0/css/sortable-theme-dark.css', dirname(__FILE__)); ?>" />
		<script src="<?php echo plugins_url('sortable-0.8.0/js/sortable.min.js', dirname(__FILE__)); ?>"></script>
		
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<h1>Manage Visit Types</h1>
				</div>
			</div>

		<?php
		/** 
		 * Adds CSV data to Visit Type table on import
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
								$vtID = findVTID($code);

								$data = array(
									'VT_VisitTypeID' => $code,
									'VT_Name' => $name
								);
								
								if($vtID > 0)
								{
									$vtInfo = getVT($code);
									if($name != $vtInfo->VT_Name)
									{
										$wpdb->insert('wuos_replacevisittypelist' , $data);
									}
								}
								else 
								{
									$wpdb->insert( 'wuos_visittypelist' , $data );
								}
							}
						}
						$c++;
					}
				}
			}

			// Query to fetch duplicate Visit Type data from imported file
			$updateResults = $wpdb->get_results( "SELECT * FROM wuos_replacevisittypelist"); 

			// Checks if duplicate data exists
			if(!empty($updateResults))                        
			{
				if($_GET['update_list'] != "true"){ 
			?>  
				<h2>Matching Records were found from the imported data</h2>
				<a href="/wp-admin/admin.php?page=manage-visit-type&update_list=true" class="btn btn-warning matching-records-link">View Matching Records</a>
				<?php
				}
			} 
			if($_GET['update_list']== "true"){ 
				?>
				<a href="/wp-admin/admin.php?page=manage-visit-type" class="matching-records-link">Back to previous page</a>
				<?php
				if(!empty($updateResults))                        
				{
				?>
					<h2>Update or decline existing records</h2>
					<input type="text" id="searchTable" onkeyup="searchTable()" placeholder="Search for Vist Type ID.." title="Type in a name">
					<table class="visit-type-info-table backend-info-table sortable-theme-light" id="visit-type-info-table" width='100%' data-sortable>
					<thead>
							<tr><th>Existing Record</th>
							<th>Updated Record</th>
							<th>Accept Changes</th>
							<th>Decline Changes</th></tr>
						</thead>
						<tbody>
							
							<?php      
							foreach($updateResults as $row){
								$existingRow = $wpdb->get_results("SELECT * FROM wuos_visittypelist WHERE VT_VisitTypeID = '$row->VT_VisitTypeID'"); 
								?>
							<tr>
									<td class="existing-visitID-id">
									<strong>Vist Type ID:</strong> <?php echo $existingRow[0]->VT_VisitTypeID ?></br>
									<strong>Vist Type Name:</strong> <?php echo $existingRow[0]->VT_Name ?></br>
									</td> 
									<td class="updated-visitID-id"> 
									<strong>Vist Type ID: </strong><?php echo $row->VT_VisitTypeID ?></br>
									<strong>Vist Type Name:</strong> <?php echo $row->VT_Name ?></br>
									</td> 
									<td><button class="btn btn-primary accept-btn" id="accept-<?php echo $row->VT_VisitTypeID ?>">Accept</button></td>
									<td><button class="btn btn-danger deny-btn" id="visitID-<?php echo $row->VT_VisitTypeID ?>">Decline</button></td>      
							</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				<?php
				}
			}

			if($_GET['update_list'] != "true")
			{ 
			?>
			<div class="row">
				<div class="col-sm-12">
					<hr class="blackLine" />
				</div>
				<div class="col-sm-6">

					<div class="row">
						<form action="" class="visit-type-form" id="visit-type-form" method="post">
							<label for="visitID" className="col-sm-4 col-form-label">Visit Type ID:</label>
							<div class="col-sm-8 smallMarginBottom">
								<input id= "visitID" type="text" name="visitID-input" class="form-control">
							</div>
							<label for="name" className="col-sm-4 col-form-label">Visit Type Name:</label>
							<div class="col-sm-8 smallMarginBottom">
								<input id="name" type="text" name="name" class="form-control">
							</div>
							<input type="hidden" name="action" value="send_form" style="display: none; visibility: hidden; opacity: 0;">
							<input type="submit" value="Submit" class="btn btn-primary">
						</form>
					</div>

					<div class="visit-type-form-container backend-form-container update-form-container">
						<div class="row">
						
							<form action="" class="visit-type-form update-form" id="update-form" method="post">
								<div class="row">
									<div class="col-sm-12">
										<h2>Update Visit Type Record</h2>
									</div>
								</div>

							
								<label for="name" className="col-sm-4 col-form-label">Visit Type Name:</label>
								<div class="col-sm-12 smallMarginBottom">
									<input id= "visitID" type="hidden"  name="visitID-input">
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
					<form class="visit-type-upload-form backend-upload-form" method="post" enctype="multipart/form-data">
					<div class="file-upload">
						<div class="file-select">
						<div class="file-select-button" id="fileName">Import Visit Type List</div>
						<div class="file-select-name" id="noFile">No file chosen...</div> 
						<input type="file" name="chooseFile" id="chooseFile" required>
						</div>
					</div>
					<div class="form-group" style="margin-top: 10px;">
						<input type="submit" name="upload" class="btn btn-primary">
					</div>
					<div style="margin-top: 50px;">
						<strong>Download Sample Import CSV file</strong><br />
						<a href="/wp-content/plugins/washu-open_scheduling/SampleFiles/Sample_Visit_Type.csv">Download</a>
					</div>
					</form>
				</div>
			</div>
        <?php
		}

        // Query to fetch visit type data from database table and storing in $results
        $results = $wpdb->get_results( "SELECT * FROM wuos_visittypelist"); 

        // Checks if visit type data exists in database and displays data in table if it exists
        if(!empty($results) && $_GET['update_list'] != "true")                        
        {
        ?>  
			
			<div class="row">
				<div class="col-sm">
					<input type="text" id="searchTable" onkeyup="searchTable()" placeholder="Search Visit Type Name" title="Type in a name" class="form-control">
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
					<table class="visit-type-info-table backend-info-table sortable-theme-light" id="visit-type-info-table" width='100%' data-sortable>
					<thead>
							<tr><th class="hidden-id">Row ID</th><th data-sortable-type="alpha">Visit Type ID</th><th>Visit Type Name</th><th>Edit</th><th>Delete</th><th>Select</th></tr>
						</thead>
						<tbody>
							
							<?php      
							foreach($results as $row){
								?>
							<tr class="row-<?php echo $row->VT_VisitTypeID ?>">
									<td class="hidden-id"><?php echo $row->VT_ID ?></td> 
									<td class="visitID-id"><?php echo $row->VT_VisitTypeID ?></td> 
									<td><?php echo $row->VT_Name ?></td>
									<td><button class="btn btn-primary record-edit-btn" id="edit-<?php echo $row->VT_VisitTypeID ?>">+</button></td>
									<td><button class="btn btn-danger delete-btn" id="visitID-<?php echo $row->VT_VisitTypeID ?>">D</button></td>
									<td><input type="checkbox" class="select-item" id="select-<?php echo $row->VT_VisitTypeID ?>" name="select-item"></td>
									<span class="delete-notification-overlay" id="delete-<?php echo $row->VT_VisitTypeID ?>">
									<span class="delete-notification">
									<h2>You are deleting the record for <?php echo $row->VT_Name?></h2>
										<h2>Are you sure you want to delete?</h2>
										<span>
											<button class="btn btn-primary record-delete-btn" id="visitID-<?php echo $row->VT_VisitTypeID ?>">Yes</button>
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

		//Filter values within HTML table based on visitID
		function searchTable() {
			var input, filter, table, tr, td, i, txtValue;
			input = document.getElementById("searchTable");
			filter = input.value.toUpperCase();
			table = document.getElementById("visit-type-info-table");
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
        //Ajax function that takes the visit type form data and utilizes it within PHP function
		jQuery( 'form#visit-type-form' ).on( 'submit', async function(e) {
			e.preventDefault();
			e.stopPropagation();

			var visitID = jQuery('#visitID').val();
			var name = jQuery('#name').val();

			if(visitID != "" && name != "" ){
				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'add_new_visit_type',
						'visitID': visitID,
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
			else
			{
				alert('You must enter both a visit id and name.');
			}

		});

		//Ajax function that updates visit type data based on the popup modal form
		jQuery( 'form#update-form' ).on( 'submit', async function(e) {
			e.preventDefault();
			e.stopPropagation();

			var updateModal = document.querySelector(".update-form-container");
			updateModal.style.display = "none";
			var visitID = jQuery('.update-form #visitID').val();
			var name = jQuery('.update-form #name').val();
			jQuery.ajax({
				type: 'POST',
				dataType: 'json',
				url: "<?php echo admin_url('admin-ajax.php'); ?>", 
				data: { 
					'action' : 'edit_visit_type',
					'visitID': visitID,
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

		//Shows the edit visit type record popup modal
		jQuery('.record-edit-btn').on( 'click', function(event) {
			var visitID = event.target.id.replace('edit-', '');
			var formVals = event.target.parentNode.parentNode.children;
			jQuery(".update-form #visitID").val(formVals[1].innerText);
			jQuery(".update-form #name").val(formVals[2].innerText);
			
			var updateModal = document.querySelector(".update-form-container");
			updateModal.id = "event-" + visitID;
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

			//Shows modal to confirm deletion of visit type record
			jQuery( '.delete-btn' ).click(function(event) {
				var visitID = event.target.id.replace('visitID-', '');
				var deleteNotification = document.querySelector("#delete-" + visitID)
				deleteNotification.style.display = "block";
				var pluginBody = document.querySelector("body");
				pluginBody.style.overflow = "hidden";
				
			});

			//Cancel delete visit type record
			jQuery( '.cancel-btn' ).click(function(event) {
				var deleteNotification = event.target.parentNode.parentNode.parentNode;
				deleteNotification.style.display = "none";
				var pluginBody = document.querySelector("body");
				pluginBody.style.overflow = "unset";
				
			});

			//Deletes visit type record from table and database after confirmation 
			jQuery( '.record-delete-btn' ).click(function(event) {
				var deleteNotification = event.target.parentNode.parentNode.parentNode;
				deleteNotification.style.display = "none";
				var pluginBody = document.querySelector("body");
				pluginBody.style.overflow = "unset";
				var visitID = event.target.id.replace('visitID-', '');
				var removeRow = document.querySelector(".row-" + visitID);
				removeRow.remove();
				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'delete_visit_type',
						'visitID': visitID
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

			//Shows modal to confirm deletion of visit type records
			jQuery('.delete-all-btn').click(function(event) {
				var deleteNotification = document.querySelector(".delete-all-overlay");
				deleteNotification.style.display = "block";
				var pluginBody = document.querySelector("body");
				pluginBody.style.overflow = "hidden";
				
			});

			//Deletes selected visit type records from table and database after confirmation 
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
						'action' : 'delete_selected_visit_types',
						'selectedVisitTypes': idString
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

			//Accepts updated visit type record based on CSV import
			jQuery('.accept-btn').click(function(event) {
				var visitID = event.target.id.replace('accept-', '');
				event.target.parentNode.parentNode.remove();
				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'accept_updated_visit_type',
						'visitID': visitID
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

			//Deny and remove updated visit type record from table and keeps the record with matching visitID unchanged
			jQuery('.deny-btn').click(function(event) {
				var visitID = event.target.id.replace('visitID-', '');
				event.target.parentNode.parentNode.remove();
				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'delete_updated_visit_type',
						'visitID': visitID
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
 * This function takes the visit type form data after the Ajax call and stores it within the WordPress database
 */

add_action( 'wp_ajax_nopriv_add_new_visit_type', 'add_new_visit_type' );
add_action( 'wp_ajax_add_new_visit_type', 'add_new_visit_type' );
function add_new_visit_type(){
	global $wpdb; 
	$code = sanitize_text_field($_POST["visitID"]);
	$name = sanitize_text_field($_POST["name"]);

	if($name != "")
	{
		$vtID = findVTID($code);

		$data = array(
			'VT_VisitTypeID' => $code,
			'VT_Name' => $name
		);

		if($vtID > 0)
		{
			$vtInfo = getVT($code);

			if($name != $vtInfo->VT_Name)
			{
				$wpdb->insert('wuos_replacevisittypelist' , $data);
			}
		}
		else 
		{
			$wpdb->insert('wuos_visittypelist' , $data);
		}
		return;
		wp_die();
	}
}



/**
 * This function takes the visit type update form data after the Ajax call and stores it within the WordPress database
 */

add_action( 'wp_ajax_nopriv_edit_visit_type', 'edit_visit_type' );
add_action( 'wp_ajax_edit_visit_type', 'edit_visit_type' );
function edit_visit_type()
{
	global $wpdb;
	$visitID = sanitize_text_field($_POST["visitID"]);
	$name = sanitize_text_field($_POST["name"]);

	if($name != "")
	{
		$sqlUpdate = "UPDATE wuos_visittypelist SET VT_Name = '$name' WHERE VT_VisitTypeID = '$visitID'";
		$wpdb->query($sqlUpdate);
		return;
		wp_die();
	}
}

/**
 * This function deletes existing visit type data
 */

add_action( 'wp_ajax_nopriv_delete_visit_type', 'delete_visit_type' );
add_action( 'wp_ajax_delete_visit_type', 'delete_visit_type' );
function delete_visit_type(){
	global $wpdb;
	$visitID = sanitize_text_field($_POST["visitID"]);
	$name = sanitize_text_field($_POST["name"]);


    /// Query to fetch visit type data from database table and storing in $results
    $sqlDelete = "DELETE from wuos_visittypelist WHERE VT_VisitTypeID = '$visitID'";
	$sqlDeleteMatches = "DELETE from wuos_replacevisittypelist WHERE VT_VisitTypeID = '$visitID'";

	$rowID = $wpdb->get_var("SELECT VT_ID FROM wuos_visittypelist WHERE VT_VisitTypeID = '$visitID'"); 
	$referenceDelete = "DELETE from wuos_referenceacademicxvisit WHERE VT_ID = '$rowID'";

    $wpdb->query($sqlDelete);
	$wpdb->query($sqlDeleteMatches);
	$wpdb->query($referenceDelete);
	return;
	wp_die();
}


/**
 * This function deletes selected visit type data
 */

add_action( 'wp_ajax_nopriv_delete_selected_visit_types', 'delete_selected_visit_types' );
add_action( 'wp_ajax_delete_selected_visit_types', 'delete_selected_visit_types' );
function delete_selected_visit_types(){
	global $wpdb;
	$selectedVisitTypes = sanitize_text_field($_POST["selectedVisitTypes"]);

	$selectedArray = explode(",",$selectedVisitTypes);

	foreach($selectedArray as $id){
		/// Query to fetch visit type data from database table and storing in $results
		$sqlDelete = "DELETE from wuos_visittypelist WHERE VT_VisitTypeID = '$id'";
		$sqlDeleteMatches = "DELETE from wuos_replacevisittypelist WHERE VT_VisitTypeID = '$id'";
	
		$rowID = $wpdb->get_var("SELECT VT_ID FROM wuos_visittypelist WHERE VT_VisitTypeID = '$id'"); 
		$referenceDelete = "DELETE from wuos_referenceacademicxvisit WHERE VT_ID = '$rowID'";
	
		$wpdb->query($sqlDelete);
		$wpdb->query($sqlDeleteMatches);
		$wpdb->query($referenceDelete);
	}
	return;
	wp_die();

}

/**
 * This function accepts updated visit type data from import
 */

add_action( 'wp_ajax_nopriv_accept_updated_visit_type', 'accept_updated_visit_type' );
add_action( 'wp_ajax_accept_updated_visit_type', 'accept_updated_visit_type' );
function accept_updated_visit_type(){
	global $wpdb;
	$visitID = sanitize_text_field($_POST["visitID"]);

    /// Query to fetch visit type data from database table and storing in $results
    $updatedRow = $wpdb->get_results("SELECT * FROM wuos_replacevisittypelist WHERE VT_VisitTypeID = '$visitID'"); 
	$updatedName = $updatedRow[0]->VT_Name;

	$sqlUpdate = "UPDATE wuos_visittypelist SET VT_Name = '$updatedName' WHERE VT_VisitTypeID = '$visitID'";
	$wpdb->query($sqlUpdate);

	$wpdb->query("DELETE from wuos_replacevisittypelist WHERE VT_VisitTypeID = '$visitID'");
	return;
	wp_die();
}

/**
 * This function deletes updated visit type data from import
 */

add_action( 'wp_ajax_nopriv_delete_updated_visit_type', 'delete_updated_visit_type' );
add_action( 'wp_ajax_delete_updated_visit_type', 'delete_updated_visit_type' );
function delete_updated_visit_type(){
	global $wpdb;
	$visitID = sanitize_text_field($_POST["visitID"]);
	$wpdb->query("DELETE from wuos_replacevisittypelist WHERE VT_VisitTypeID = '$visitID'");
	return;
	wp_die();

}