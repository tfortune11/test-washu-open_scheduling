<?php
ob_start();
ob_implicit_flush();
if(isset($_POST["Export"]) && $_GET['page'] == "manage-provider"){
	header_remove(); 

	header('Content-Type: text/csv');  
	header('Content-Disposition: attachment; filename="providerlist.csv"'); 
	global $wpdb;

	$header_row = array(
		'PL_NPI',
		'PL_FirstName',
		'PL_LastName',
		'PL_ProviderName'
	);
	$data_rows = array();

	$results = $wpdb->get_results("SELECT * FROM wuos_providerlist");
	foreach ( $results as $item ) 
	{
		$row = array(
		$item->PL_NPI,
		$item->PL_FirstName,
		$item->PL_LastName,
		$item->PL_ProviderName
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
	function javascript_variables(){ ?>
		<script type="text/javascript">
			var ajax_url = '<?php echo admin_url( "admin-ajax.php" ); ?>';
			var ajax_nonce = '<?php echo wp_create_nonce( "secure_nonce_name" ); ?>';
		</script><?php
	}
	add_action ( 'wp_head', 'javascript_variables' );

    /** 
     * A page for managing Provider data
     **/

	function getPL($code)
	{
		global $wpdb;
		$findPLInfo = $wpdb->get_results("SELECT PL_NPI, PL_ProviderName, PL_FirstName, PL_LastName FROM wuos_providerlist WHERE PL_NPI = '$code'"); 

		$returnData = null;
		if(@count($findPLInfo) > 0)
		{
			$returnData = $findPLInfo[0];
		}

		return $returnData;
	}

    function page_manage_provider()
	{
		
	    ?>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="<?php echo plugins_url('sortable-0.8.0/css/sortable-theme-dark.css', dirname(__FILE__)); ?>" />
		<script src="<?php echo plugins_url('sortable-0.8.0/js/sortable.min.js', dirname(__FILE__)); ?>"></script>

		<div class="container">
			<h1 style="margin-bottom:50px;">Manage Providers</h1>
		<?php
		/** 
		 * Adds CSV data to Provider table on import
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
							$npi = $filesop[0];
							$fname = $filesop[1];
							$lname = $filesop[2];
							$prov_name = $filesop[3];
							//$post_id = $filesop[4];

							if($npi != "" && $fname != "" && $lname != "" && $prov_name != "")
							{
								$plID = findNPID($npi);

								$data = array(
									'PL_NPI' => $npi,
									'PL_ProviderName' => $prov_name,
									'PL_LastName' => $lname,
									'PL_FirstName' => $fname,
								);

								if($plID > 0)
								{
									$plInfo = getPL($npi);

									$exactMatch = 0;
									if($plInfo)
									{
										$countNPI = (strtolower(trim($plInfo->PL_NPI)) == strtolower(trim($npi)) ? 1 : 0);
										$countPname = (strtolower(trim($plInfo->PL_ProviderName)) == strtolower(trim($prov_name)) ? 1 : 0);
										$countLname = (strtolower(trim($plInfo->PL_LastName)) == strtolower(trim($lname)) ? 1 : 0);
										$countFname = (strtolower(trim($plInfo->PL_FirstName)) == strtolower(trim($fname)) ? 1 : 0);
										$exactMatch = $countNPI + $countPname + $countFname + $countLname;
									}

									if($exactMatch < 1)
									{
										$wpdb->insert('wuos_replaceproviderlist', $data);
									}

									$exactMatch = 0;
								}
								else 
								{
									$wpdb->insert('wuos_providerlist', $data);
								}	
							}						
						}
						$c++;
					}
				}
			}

			// Query to fetch duplicate provider data from imported file
			$updateResults = $wpdb->get_results( "SELECT * FROM wuos_replaceproviderlist"); 

			// Checks if duplicate data exists
			if(!empty($updateResults))                        
			{
				if($_GET['update_list'] != "true"){ 
			?>  
				<h2>Matching Records were found from the imported data</h2>
				<a href="/wp-admin/admin.php?page=manage-provider&update_list=true" class="btn btn-warning matching-records-link">View Matching Records</a>
				<?php
				}
			} 
			if($_GET['update_list']== "true"){ 
				?>
				<a href="/wp-admin/admin.php?page=manage-provider" class="matching-records-link">Back to previous page</a>
				<?php
				if(!empty($updateResults))                        
				{
				?>
					<h2>Update or decline existing records</h2>
					<table class="provider-info-table backend-info-table sortable-theme-light" id="provider-info-table" width='100%' data-sortable>
					<thead>
							<tr><th>Existing Record</th>
							<th>Updated Record</th>
							<th>Accept Changes</th>
							<th>Decline Changes</th></tr>
						</thead>
						<tbody>
							
							<?php      
							foreach($updateResults as $row){
								$existingRow = $wpdb->get_results("SELECT * FROM wuos_providerlist WHERE PL_NPI = '$row->PL_NPI'"); 
								?>
							<tr>
									<td class="existing-npi-id">
									<strong>NPI:</strong> <?php echo $existingRow[0]->PL_NPI ?></br>
									<strong>First Name:</strong> <?php echo $existingRow[0]->PL_FirstName ?></br>
									<strong>Last Name:</strong> <?php echo $existingRow[0]->PL_LastName ?></br>
									<strong>Provider Name:</strong> <?php echo $existingRow[0]->PL_ProviderName ?>
									</td> 
									<td class="updated-npi-id"> 
									<strong>NPI: </strong><?php echo $row->PL_NPI ?></br>
									<strong>First Name:</strong> <?php echo $row->PL_FirstName ?></br>
									<strong>Last Name: </strong><?php echo $row->PL_LastName ?></br>
									<strong>Provider Name: </strong><?php echo $row->PL_ProviderName ?>
									</td> 
									<td><button class="btn btn-primary accept-btn" id="accept-<?php echo $row->PL_NPI ?>">Accept</button></td>
									<td><button class="btn btn-danger deny-btn" id="npi-<?php echo $row->PL_NPI ?>">Decline</button></td>      
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
						<form action="" class="provider-form" id="provider-form" method="post"  >
							<label for="npi" className="col-sm-4 col-form-label">NPI:</label>
							<div class="col-sm-8 smallMarginBottom">
								<input id= "npi" type="text" name="npi-input" class="form-control">
							</div>
							<label for="fname" className="col-sm-4 col-form-label">First Name:</label>
							<div class="col-sm-8 smallMarginBottom">
								<input id= "fname" type="text" name="fname" class="form-control">
							</div>
							<label for="lname" className="col-sm-4 col-form-label">Last Name:</label>
							<div class="col-sm-8 smallMarginBottom">
								<input id= "lname" type="text" name="lname" class="form-control">
							</div>
							<label for="providername" className="col-sm-4 col-form-label">Provider Full Name:</label>
							<div class="col-sm-8 smallMarginBottom">
								<input id= "providername" type="text" name="providername" class="form-control">
							</div>
							<div class="col-sm-12 smallMarginBottom">
								<input type="hidden" name="action" value="send_form" style="display: none; visibility: hidden; opacity: 0;">
								<input type="submit" value="Submit" class="btn btn-primary">
							</div>
						</form>
					</div>

					<div class="provider-form-container backend-form-container update-form-container">
						<div class="row">
							<form action="" class="provider-form update-form" id="update-form" method="post">
								<div class="row">
									<div class="col-sm-12">
									<h2>Update Provider Record</h2>
									</div>
								</div>

								<label for="npi" className="col-sm-4 col-form-label">NPI:</label>
								<div class="col-sm-12 smallMarginBottom">
									<span id="npi-display"></span>
									<input id="npi" type="hidden" name="npi-input" class="form-control">
								</div>

								<label for="fname" className="col-sm-4 col-form-label">First Name:</label>
								<div class="col-sm-12 smallMarginBottom">
									<input id="fname" type="text" name="fname" class="form-control">
								</div>

								<label for="lname" className="col-sm-4 col-form-label">Last Name:</label>
								<div class="col-sm-12 smallMarginBottom">
									<input id= "lname" type="text" name="lname" class="form-control">
								</div>

								<label for="providername" className="col-sm-4 col-form-label">Provider Full Name:</label>
								<div class="col-sm-12 smallMarginBottom">
									<input id= "providername" type="text" name="providername" class="form-control">
								</div>

								<div class="col-sm-12">
									<input type="hidden" name="action" value="send_form" style="display: none; visibility: hidden; opacity: 0;">
									<input type="submit" value="Save" class="btn btn-primary marginRight">
									<button type="button" class="btn btn-danger btnFix" onClick="hideModal()" style="margin-bottom:20px;">Cancel</button>
								</div>
							</form>
						</div>
					</div>
			</div>

			<div class="col-sm-6">
					<form class="provider-upload-form backend-upload-form" method="post" enctype="multipart/form-data">
						<div class="file-upload">
							<div class="file-select">
							<div class="file-select-button" id="fileName">Import Provider List</div>
							<div class="file-select-name" id="noFile">No file chosen...</div> 
							<input type="file" name="chooseFile" id="chooseFile" required>
							</div>
						</div>
						<div class="form-group" style="margin-top: 10px;">
							<input type="submit" name="upload" class="btn btn-primary">
						</div>

						<div style="margin-top: 50px;">
							<strong>Download Sample Import CSV file</strong><br />
							<a href="/wp-content/plugins/washu-open_scheduling/SampleFiles/Sample_Provider.csv">Download</a>
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
        // Query to fetch provider data from database table and storing in $results
        $results = $wpdb->get_results( "SELECT * FROM wuos_providerlist"); 

        // Checks if provider data exists in database and displays data in table if it exists
        if(!empty($results) && $_GET['update_list'] != "true")                        
        {
        ?>  
			<div class="row">
				<div class="col-sm">
					<input type="text" class="form-control" id="searchTable" onkeyup="searchTable()" placeholder="Search for NPI" title="Type in a name">
				</div>
				<div class="col-sm">
				<input type="text" class="form-control" id="searchProvider" onkeyup="searchProvider()" placeholder="Search by Provider Name" title="Search by Provider Name">
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
					<table class="provider-info-table backend-info-table sortable-theme-light" id="provider-info-table" width='100%' data-sortable>
					<thead>
							<tr><th class="hidden-id">Row ID</th><th data-sortable-type="alpha">NPI</th><th>First Name</th><th>Last Name</th><th>Provider Name</th>
							<th>Edit</th><th>Delete</th><th>Select</th></tr>
						</thead>
						<tbody>
							
							<?php      
							foreach($results as $row){
								?>
							<tr class="row-<?php echo $row->PL_NPI ?>">
									<td class="hidden-id"><?php echo $row->PL_ID ?></td> 
									<td class="npi-id"><?php echo $row->PL_NPI ?></td> 
									<td><?php echo $row->PL_FirstName ?></td>
									<td><?php echo $row->PL_LastName ?> </td> 
									<td><?php echo $row->PL_ProviderName ?> </td> 
									<td><button class="btn btn-primary record-edit-btn" id="edit-<?php echo $row->PL_NPI ?>">+</button></td>
									<td><button class="btn btn-danger delete-btn" id="npi-<?php echo $row->PL_NPI ?>">D</button></td>
									<td><input type="checkbox" class="select-item" id="select-<?php echo $row->PL_NPI ?>" name="select-item"></td>
									<span class="delete-notification-overlay" id="delete-<?php echo $row->PL_NPI ?>">
									<span class="delete-notification">
									<h2>You are deleting the record for <?php echo $row->PL_ProviderName ?></h2>
										<h2>Are you sure you want to delete?</h2>
										<span>
											<button class="btn btn-primary record-delete-btn" id="npi-<?php echo $row->PL_NPI ?>">Yes</button>
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
		<script src="<?php echo plugins_url('react-backend/build/static/js/2.chunk.js', dirname(__FILE__)); ?>"></script>
		<script src="<?php echo plugins_url('react-backend/build/static/js/main.chunk.js', dirname(__FILE__)); ?>"></script>
        <script>

		//Filter values within HTML table based on NPI
		function searchTable() {
			var input, filter, table, tr, td, i, txtValue;
			input = document.getElementById("searchTable");
			filter = input.value.toUpperCase();
			table = document.getElementById("provider-info-table");
			tr = table.getElementsByTagName("tr");
			input2 = document.getElementById("searchProvider");
			filter2 = input.value.toUpperCase();
			for (i = 0; i < tr.length; i++) {
				td = tr[i].getElementsByTagName("td")[1];
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

		//Filter values within HTML table based on NPI
		function searchProvider() {
			var input, filter, table, tr, td, i, txtValue;
			input = document.getElementById("searchProvider");
			filter = input.value.toUpperCase();
			table = document.getElementById("provider-info-table");
			tr = table.getElementsByTagName("tr");
			input2 = document.getElementById("searchTable");
			filter2 = input.value.toUpperCase();
			for (i = 0; i < tr.length; i++) {
				td = tr[i].getElementsByTagName("td")[4];
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
        //Ajax function that takes the provider form data and utilizes it within PHP function
		jQuery( 'form#provider-form' ).on( 'submit', async function(e) {
			e.preventDefault();
			e.stopPropagation();

			var npi = jQuery('#npi').val();
			var fname = jQuery('#fname').val();
			var lname = jQuery('#lname').val();
			var providername = jQuery('#providername').val();
			var postid = jQuery('#postid').val();

			if(npi != "" && providername != "" ){

				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'add_new_provider',
						'fname': fname,
						'lname': lname,
						'npi': npi,
						'providername': providername,
						'postid': postid,
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
				alert('You must enter an npi and provider name.');
			}
		});

		//Ajax function that updates provider data based on the popup modal form
		jQuery( 'form#update-form' ).on( 'submit', async function(e) {
			e.preventDefault();
			e.stopPropagation();
		
			var updateModal = document.querySelector(".update-form-container");
			updateModal.style.display = "none";
			var npi = jQuery('.update-form #npi').val();
			var fname = jQuery('.update-form #fname').val();
			var lname = jQuery('.update-form #lname').val();
			var providername = jQuery('.update-form #providername').val();

			jQuery.ajax({
				type: 'POST',
				dataType: 'json',
				url: "<?php echo admin_url('admin-ajax.php'); ?>", 
				data: { 
					'action' : 'edit_provider',
					'fname': fname,
					'lname': lname,
					'npi': npi,
					'providername': providername
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

		//Shows the edit provider record popup modal
		jQuery('.record-edit-btn').on( 'click', function(event) {
			var npi = event.target.id.replace('edit-', '');
			var formVals = event.target.parentNode.parentNode.children;
			jQuery(".update-form #npi").val(formVals[1].innerText);
			jQuery(".update-form #npi-display").val(formVals[1].innerText);
			jQuery(".update-form #providername").val(formVals[4].innerText);
			jQuery(".update-form #fname").val(formVals[2].innerText);
			jQuery(".update-form #lname").val(formVals[3].innerText);
			
			var updateModal = document.querySelector(".update-form-container");
			updateModal.id = "event-" + npi;
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

			//Shows modal to confirm deletion of provider record
			jQuery('.delete-btn').click(function(event) {
				var npi = event.target.id.replace('npi-', '');
				var deleteNotification = document.querySelector("#delete-" + npi)
				deleteNotification.style.display = "block";
				var pluginBody = document.querySelector("body");
				pluginBody.style.overflow = "hidden";
				
			});

			//Shows modal to confirm deletion of provider records
			jQuery('.delete-all-btn').click(function(event) {
				var deleteNotification = document.querySelector(".delete-all-overlay");
				deleteNotification.style.display = "block";
				var pluginBody = document.querySelector("body");
				pluginBody.style.overflow = "hidden";
				
			});

			//Cancel delete provider record
			jQuery('.cancel-btn').click(function(event) {
				var deleteNotification = event.target.parentNode.parentNode.parentNode;
				deleteNotification.style.display = "none";
				var pluginBody = document.querySelector("body");
				pluginBody.style.overflow = "unset";
				
			});

			//Deletes provider record from table and database after confirmation 
			jQuery('.record-delete-btn').click(function(event) {
				var deleteNotification = event.target.parentNode.parentNode.parentNode;
				deleteNotification.style.display = "none";
				var pluginBody = document.querySelector("body");
				pluginBody.style.overflow = "unset";
				var npi = event.target.id.replace('npi-', '');
				var removeRow = document.querySelector(".row-" + npi);
				removeRow.remove();
				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'delete_provider',
						'npi': npi
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

			//Deletes selected provider data from table and database after confirmation 
			jQuery('.record-delete-all-btn').click(function(event) {
				var deleteNotification = event.target.parentNode.parentNode.parentNode;
				deleteNotification.style.display = "none";
				var pluginBody = document.querySelector("body");
				pluginBody.style.overflow = "unset";
				var allNPI = document.querySelectorAll(".select-item");
				var npiString = "";
				allNPI.forEach((element) => {
					if(element.checked == true){
						npiString = npiString + element.id.replace('select-', '') + ",";
						var removeRow = document.querySelector(".row-" + element.id.replace('select-', ''));
						removeRow.remove();
					}
				});
				
				npiString = npiString.slice(0, -1);

				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'delete_selected_providers',
						'selectedProviders': npiString
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

			//Accepts updated Provider record based on CSV import
			jQuery('.accept-btn').click(function(event) {
				var npi = event.target.id.replace('accept-', '');
				event.target.parentNode.parentNode.remove();
				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'accept_updated_provider',
						'npi': npi
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
			
			//Deny and remove updated provider record from table and keeps the record with matching NPI unchanged
			jQuery('.deny-btn').click(function(event) {
				var npi = event.target.id.replace('npi-', '');
				event.target.parentNode.parentNode.remove();
				jQuery.ajax({
					type: 'POST',
					dataType: 'json',
					url: "<?php echo admin_url('admin-ajax.php'); ?>", 
					data: { 
						'action' : 'delete_updated_provider',
						'npi': npi
				},
				success: function(data){
					if (data.res == true){
						alert(data.message);    // success message
					}else{
						alert(data.message);    // fail
					}
				}
				});
			});
		});
		</script>
	<?php
	}

/**
 * This function takes the provider form data after the Ajax call and stores it within the WordPress database
 */

add_action( 'wp_ajax_nopriv_add_new_provider', 'add_new_provider' );
add_action( 'wp_ajax_add_new_provider', 'add_new_provider' );
function add_new_provider(){
	global $wpdb;
	$npi = sanitize_text_field($_POST["npi"]);
	$fname = sanitize_text_field($_POST["fname"]);
	$lname = sanitize_text_field($_POST["lname"]);
	$providerName = sanitize_text_field($_POST["providername"]);

    /// Query to fetch provider data from database table and storing in $results
    $rowExists = $wpdb->get_var("SELECT COUNT(PL_NPI) FROM wuos_providerlist WHERE PL_NPI = '$npi'"); 

    if($rowExists > 0){
		$sqlInsert = "INSERT INTO wuos_replaceproviderlist (PL_NPI, PL_ProviderName, PL_FirstName, PL_LastName, PL_PostID) 
        VALUES ('$npi', '$providerName', '$fname', '$lname', '$postId');";
        $wpdb->query($sqlInsert);
    }
    else{
        $sqlInsert = "INSERT INTO wuos_providerlist (PL_NPI, PL_ProviderName, PL_FirstName, PL_LastName, PL_PostID) 
        VALUES ('$npi', '$providerName', '$fname', '$lname', '$postId');";
        $wpdb->query($sqlInsert);
    }
	return;
	wp_die();

}



/**
 * This function takes the provider update form data after the Ajax call and stores it within the WordPress database
 */

add_action( 'wp_ajax_nopriv_edit_provider', 'edit_provider' );
add_action( 'wp_ajax_edit_provider', 'edit_provider' );
function edit_provider(){
	global $wpdb;
	$npi = sanitize_text_field($_POST["npi"]);
	$fname = sanitize_text_field($_POST["fname"]);
	$lname = sanitize_text_field($_POST["lname"]);
	$providerName = sanitize_text_field($_POST["providername"]);

    /// Query to fetch provider data from database table and storing in $results
    $rowExists = $wpdb->get_var("SELECT COUNT(PL_NPI) FROM wuos_providerlist WHERE PL_NPI = '$npi'"); 

    if($rowExists > 0){
        $sqlUpdate = "UPDATE wuos_providerlist 
        SET PL_ProviderName = '$providerName', 
            PL_FirstName = '$fname', 
            PL_LastName = '$lname'
        WHERE PL_NPI = '$npi'";
        $wpdb->query($sqlUpdate);
    }
	return;
	wp_die();

}

/**
 * This function deletes existing provider data
 */

add_action( 'wp_ajax_nopriv_delete_provider', 'delete_provider' );
add_action( 'wp_ajax_delete_provider', 'delete_provider' );
function delete_provider(){
	global $wpdb;
	$npi = sanitize_text_field($_POST["npi"]);
	$fname = sanitize_text_field($_POST["fname"]);
	$lname = sanitize_text_field($_POST["lname"]);
	$providerName = sanitize_text_field($_POST["providername"]);
	$postId = sanitize_text_field($_POST["postid"]);


    /// Query to fetch provider data from database table and storing in $results
    $rowExists = $wpdb->get_var("SELECT COUNT(PL_NPI) FROM wuos_providerlist WHERE PL_NPI = '$npi'"); 
	$rowID = $wpdb->get_var("SELECT PL_ID FROM wuos_providerlist WHERE PL_NPI = '$npi'"); 

    if($rowExists > 0){
        $sqlDelete = "DELETE from wuos_providerlist 
        WHERE PL_NPI = '$npi'";
        $wpdb->query($sqlDelete);

		$sqlDeleteMatches = "DELETE from wuos_replaceproviderlist 
        WHERE PL_NPI = '$npi'";
        $wpdb->query($sqlDeleteMatches);

		$referenceDelete = "DELETE from wuos_referenceacademicxprovider 
        WHERE PL_ID = '$rowID'";
        $wpdb->query($referenceDelete);
    }
	return;
	wp_die();

}

/**
 * This function deletes selected provider data
 */

add_action( 'wp_ajax_nopriv_delete_selected_providers', 'delete_selected_providers' );
add_action( 'wp_ajax_delete_selected_providers', 'delete_selected_providers' );
function delete_selected_providers(){
	global $wpdb;
	$selectedProviders = sanitize_text_field($_POST["selectedProviders"]);

	$selectedArray = explode(",",$selectedProviders);

	foreach($selectedArray as $id){
		/// Query to fetch provider data from database table and storing in $results
		$rowExists = $wpdb->get_var("SELECT COUNT(PL_NPI) FROM wuos_providerlist WHERE PL_NPI = '$id'"); 
		$rowID = $wpdb->get_var("SELECT PL_ID FROM wuos_providerlist WHERE PL_NPI = '$id'"); 

		if($rowExists > 0){
			$sqlDelete = "DELETE from wuos_providerlist 
			WHERE PL_NPI = '$id'";
			$wpdb->query($sqlDelete);

			$sqlDeleteMatches = "DELETE from wuos_replaceproviderlist 
			WHERE PL_NPI = '$id'";
			$wpdb->query($sqlDeleteMatches);

			$referenceDelete = "DELETE from wuos_referenceacademicxprovider 
			WHERE PL_ID = '$rowID'";
			$wpdb->query($referenceDelete);
		}
	}
	return;
	wp_die();

}


/**
 * This function accepts updated provider data from import
 */

add_action( 'wp_ajax_nopriv_accept_updated_provider', 'accept_updated_provider' );
add_action( 'wp_ajax_accept_updated_provider', 'accept_updated_provider' );
function accept_updated_provider(){
	global $wpdb;
	$npi = sanitize_text_field($_POST["npi"]);

    /// Query to fetch provider data from database table and storing in $results
    $updatedRow = $wpdb->get_results("SELECT * FROM wuos_replaceproviderlist WHERE PL_NPI = '$npi'"); 
	$updatedNPI = $updatedRow[0]->PL_NPI;
	$updatedProvider = $updatedRow[0]->PL_ProviderName;
	$updatedFName = $updatedRow[0]->PL_FirstName;
	$updatedLName = $updatedRow[0]->PL_LastName;
	$updatedPostID = $updatedRow[0]->PL_PostID;
	$sqlUpdate = "UPDATE wuos_providerlist 
	SET PL_ProviderName = '$updatedProvider', 
		PL_FirstName = '$updatedFName', 
		PL_LastName = '$updatedLName', 
		PL_PostID = '$updatedPostID'
	WHERE PL_NPI = '$updatedNPI'";
	$wpdb->query($sqlUpdate);
	$wpdb->query("DELETE from wuos_replaceproviderlist WHERE PL_NPI = '$npi'");
	return;
	wp_die();

}

/**
 * This function deletes updated provider data from import
 */

add_action( 'wp_ajax_nopriv_delete_updated_provider', 'delete_updated_provider' );
add_action( 'wp_ajax_delete_updated_provider', 'delete_updated_provider' );
function delete_updated_provider(){
	global $wpdb;
	$npi = sanitize_text_field($_POST["npi"]);
	$wpdb->query("DELETE from wuos_replaceproviderlist WHERE PL_NPI = '$npi'");
	return;
	wp_die();

}

