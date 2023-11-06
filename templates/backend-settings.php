<?php
    ob_start();
    ob_implicit_flush();
    if(isset($_POST["Export-User-Journey"]) && $_GET['page'] == "settings"){
        header_remove(); 

        header('Content-Type: text/csv');  
        header('Content-Disposition: attachment; filename="userjourneydata.csv"'); 
        global $wpdb;

        $header_row = array(
            'UJ_ID' ,
            'UJ_SessionID' ,
            'UJ_Country' ,
			'UJ_CountryName' ,
			'UJ_City' ,
			'UJ_State' ,
			'UJ_Postal' ,
			'UJ_Latitude' ,
			'UJ_Longitude',
			'UJ_IPAddress' ,
			'UJ_BrowserInfo' ,
			'UJ_StartBlockID' ,
			'UJ_StartBlock' ,
			'UJ_DecisionTreeID' ,
			'UJ_DecisionTree' ,
			'UJ_Question' ,
			'UJ_Answer',
			'UJ_Termination' ,
            'UJ_ActionDateTime' 
        );
        $data_rows = array();

        $results = $wpdb->get_results("SELECT * FROM wuos_userjourney");
        foreach ( $results as $item ) 
        {
            $row = array(
                $item->UJ_ID ,
                $item->UJ_SessionID,
                $item->UJ_Country,
                $item->UJ_CountryName ,
                $item->UJ_City ,
                $item->UJ_State,
                $item->UJ_Postal ,
                $item->UJ_Latitude ,
                $item->UJ_Longitude,
                $item->UJ_IPAddress ,
                $item->UJ_BrowserInfo ,
                $item->UJ_StartBlockID ,
                $item->UJ_StartBlock,
                $item->UJ_DecisionTreeID ,
                $item->UJ_DecisionTree ,
                $item->UJ_Question ,
                $item->UJ_Answer,
                $item->UJ_Termination ,
                $item->UJ_ActionDateTime 
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


    
    // We need to clear IDable user data from tables to obey HIPAA
                
    function clearIdentifiableUserData() {
        global $wpdb;
    
        // Table name
        $table_name = 'wuos_userjourney';
    
        // Check if the columns exist in the table
        $column_names = $wpdb->get_col("DESC $table_name");

    
        if (in_array('UJ_Latitude', $column_names) && in_array('UJ_Longitude', $column_names) && in_array('UJ_IPAddress', $column_names) && in_array('UJ_Postal', $column_names)) {
            // Columns exist, update them to null
            $wpdb->query("UPDATE wuos_userjourney SET UJ_City = '', UJ_Latitude = '', UJ_Longitude = '', UJ_IPAddress = '', UJ_Postal = '';");

    
            // Debugging: Print the last SQL query executed
            //echo $wpdb->last_query;
    
            // Fetch the first row of the table
            //$firstRow = $wpdb->get_row("SELECT * FROM $table_name LIMIT 1");
    
            // Debugging: Print the first row
            //print_r($firstRow);
    
            // Return true to indicate success
            return true;
        } else {
            // Handle the case where one or more columns don't exist
            return false;
        }
    }
    
    

    function page_backend_settings()
	{
        global $wpdb;

        $loadEpicUrl = "";
        $loadEpicCSSUrl = "";
        $loadEpicAPIKey = "";
        $loadEpicJSUrl = "";
        $loadEpicBaseUrl = "";
        $loadAdminAPI = "";
        $loadFrontendAPI = "";


        $sql = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Epic Url'"; 
        $sqlValue = $wpdb->get_results($sql);
        if($sqlValue)
        {
            $loadEpicUrl = $sqlValue[0]->PS_SettingValue;
        }

        $sql = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Epic CSS Url'"; 
        $sqlValue = $wpdb->get_results($sql);
        if($sqlValue)
        {
            $loadEpicCSSUrl = $sqlValue[0]->PS_SettingValue;
        }

        $sql = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Epic API Key'"; 
        $sqlValue = $wpdb->get_results($sql);
        if($sqlValue)
        {
            $loadEpicAPIKey = $sqlValue[0]->PS_SettingValue;
        }

        $sql = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Epic JS Url'"; 
        $sqlValue = $wpdb->get_results($sql);
        if($sqlValue)
        {
            $loadEpicJSUrl = $sqlValue[0]->PS_SettingValue;
        }

        $sql = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Epic Base Url'"; 
        $sqlValue = $wpdb->get_results($sql);
        if($sqlValue)
        {
            $loadEpicBaseUrl = $sqlValue[0]->PS_SettingValue;
        }

        $sql = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Admin API'"; 
        $sqlValue = $wpdb->get_results($sql);
        if($sqlValue)
        {
            $loadAdminAPI = $sqlValue[0]->PS_SettingValue;
        }

        $sql = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Frontend API'"; 
        $sqlValue = $wpdb->get_results($sql);
        if($sqlValue)
        {
            $loadFrontendAPI = $sqlValue[0]->PS_SettingValue;
        }
        
        $sql = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'User Journey API'"; 
        $sqlValue = $wpdb->get_results($sql);
        if($sqlValue)
        {
            $loadUserJourneyAPI = $sqlValue[0]->PS_SettingValue;
        }
        
	    ?>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
		
		<div class="container">
            
            <h1 style="margin-bottom:0px;">Settings</h1>
            <div class="settings-tabs">
                <a class="<?php  echo (isset($_GET['tab']) == "main" || !isset($_GET['tab']) ? 'active' : '')?>" href="<?php echo site_url('/wp-admin/admin.php?page=settings&tab=main'); ?>">General</a>
                <a class="<?php echo (isset($_GET['tab']) == "user-journey" ? 'active' :  '') ?>" href="<?php echo site_url('/wp-admin/admin.php?page=settings&tab=user-journey'); ?>">User Journey</a>
                <a class="<?php echo (isset($_GET['tab']) == "drop-tables"  ?  'active' : '') ?>" href="<?php echo site_url('/wp-admin/admin.php?page=settings&tab=drop-tables'); ?>">Drop Tables</a>
                <a class="<?php echo (isset($_GET['tab']) == "truncate-tables"  ?  'active' : '') ?>" href="<?php echo site_url('/wp-admin/admin.php?page=settings&tab=truncate-tables'); ?>">Truncate Tables</a>
                <a class="<?php echo (isset($_GET['tab']) == "create-tables"  ?  'active' : '') ?>" href="<?php echo site_url('/wp-admin/admin.php?page=settings&tab=create-tables'); ?>">Create Tables</a>

            </div>

            <?php
            if($_GET['tab'] == "main" || !$_GET['tab']){
            ?>
            <form action="" class="epic-url-form" id="epic-url-form" method="post"  >
                <label for="epic-base-input" className="col-sm-4 col-form-label">Epic Base URL:</label>
                <div class="col-sm-8 smallMarginBottom">
                    <input id="epic-base" type="text" name="epic-base-input" class="form-control" value="<?php echo $loadEpicBaseUrl; ?>">
                </div>
                <label for="epic-url-input" className="col-sm-4 col-form-label">Epic Endpoint URL:</label>
                <div class="col-sm-8 smallMarginBottom">
                    <input id="epic-url" type="text" name="epic-url-input" class="form-control" value="<?php echo $loadEpicUrl; ?>">
                </div>
                <label for="epic-css-input" className="col-sm-4 col-form-label">Epic CSS URL:</label>
                <div class="col-sm-8 smallMarginBottom">
                    <input id="epic-css" type="text" name="epic-css-input" class="form-control" value="<?php echo $loadEpicCSSUrl; ?>">
                </div>
                <label for="epic-js-input" className="col-sm-4 col-form-label">Epic JS URL:</label>
                <div class="col-sm-8 smallMarginBottom">
                    <input id="epic-js" type="text" name="epic-js-input" class="form-control" value="<?php echo $loadEpicJSUrl; ?>">
                </div>
                <label for="epic-api-input" className="col-sm-4 col-form-label">Epic API Key:</label>
                <div class="col-sm-8 smallMarginBottom">
                    <input id="epic-api" type="text" name="epic-api-input" class="form-control" value="<?php echo $loadEpicAPIKey; ?>">
                </div>
                <label for="admin-api-key" className="col-sm-4 col-form-label">Admin Endpoint API Key:</label>
                <div class="col-sm-8 smallMarginBottom">
                    <input id="admin-api-key" type="text" name="admin-api-key-input" class="form-control" value="<?php echo $loadAdminAPI; ?>">
                </div>
                <label for="frontend-api-key" className="col-sm-4 col-form-label">Frontend Endpoint API Key:</label>
                <div class="col-sm-8 smallMarginBottom">
                    <input id="frontend-api-key" type="text" name="frontend-api-key-input" class="form-control" value="<?php echo $loadFrontendAPI; ?>">
                </div>
                <label for="user-journey-api-key" className="col-sm-4 col-form-label">User Journey API Key:</label>
                <div class="col-sm-8 smallMarginBottom">
                    <input id="user-journey-api-key" type="text" name="user-journey-api-key-input" class="form-control" value="<?php echo $loadUserJourneyAPI; ?>">
                </div>
                <div class="col-sm-12 smallMarginBottom">
                    <input type="hidden" name="action" value="send_form" style="display: none; visibility: hidden; opacity: 0;">
                    <input type="submit" value="Save" class="btn btn-primary">
                </div>
            </form>
            <?php
            }
            if($_GET['tab'] == "user-journey"){
            ?>
            <div class="row">
                <h2>User Journey</h2>
                <form class="form-horizontal" action="" method="post" name="upload_excel"   
                      enctype="multipart/form-data">
					<div class="form-group">
								<div class="col-md-4 col-md-offset-4">
									<input type="submit" name="Export-User-Journey" class="btn btn-success" style="margin-bottom:20px;" value="Export User Journey Data"/>
								</div>
					</div>                    
				</form>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    To access the API, POST: <?php echo site_url(); ?>/wp-json/washu-open-scheduling/v1/userjourneygetalldata/ <br />
                    Send the User Journey API Key in the JSON Body<br />
                    <p>
                        Example: <br />
                        {<br />
                            <span style="margin-left:15px;">"apiKey": "[User Journey API Key]"<br /></span>
                            <span style="margin-left:15px;">"startDate": "[Start Date Format YYYY-MM-DD Example: 2021-12-01]"<br /></span>
                            <span style="margin-left:15px;">"endDate": "[End Date Format YYYY-MM-DD Example: 2022-01-01]"<br /></span>
                        }<br />
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <form method="POST">
                        <button type="submit" name="clearUserData" class="reset-tables btn btn-primary">Clear User Identifiers Data</button>
                    </form>
                </div>
            </div>
            <?php
                // Include the clearIdentifiableUserData function here or in a separate PHP file

                // Check if the form was submitted
                if (isset($_POST['clearUserData'])) {
                    // Call the clearIdentifiableUserData function
                    $result = clearIdentifiableUserData();

                    if ($result) {
                        echo 'UserData Cleared Successfully'; // Return a success message
                    } else {
                        echo 'Error Clearing UserData'; // Return an error message if needed
                    }
                }
            ?>
            <?php
            }
            if($_GET['tab'] == "drop-tables")
            {
            ?>
            <div class="row">
                <hr />
                <h2>Drop Tables</h2>
                <div clsss="col-sm-12"  style="margin-bottom:20px;">Warning, this will erase all data in the dropped table. </div>
            </div>
            <div class="row">
                <div class="col-sm-12 ">
                <div class="row">
                    <div class="col-sm settings-btn-col">
                        <button class="reset-tables btn btn-primary" id="drop-all">Drop All Tables</button>
                        <button class="reset-tables btn btn-primary" id="drop-PL">Drop Provider Table</button>
                        <button class="reset-tables btn btn-primary" id="drop-VT">Drop Visit Type Table</button>
                        <button class="reset-tables btn btn-primary" id="drop-UJ">Drop User Journey Table</button>
                        <button class="reset-tables btn btn-primary" id="drop-SE">Drop Settings Table</button>
                    </div>
                    <div class="col-sm settings-btn-col">
                        <button class="reset-tables btn btn-primary" id="drop-ED">Drop Epic Department Table</button>
                        <button class="reset-tables btn btn-primary" id="drop-AD">Drop Academic Department Table</button>
                        <button class="reset-tables btn btn-primary" id="drop-SB">Drop Start Block Table</button>
                        <button class="reset-tables btn btn-primary" id="drop-DT">Drop Decision Tree Table</button>
                    </div>
                    <div class="col-sm settings-btn-col">         
                        <button class="reset-tables btn btn-primary" id="drop-AU">Drop Audit Table</button>
                        <button class="reset-tables btn btn-primary" id="drop-AXE">Drop Academic x Epic Table</button>
                        <button class="reset-tables btn btn-primary" id="drop-AXP">Drop Academic x Provider Table</button>
                        <button class="reset-tables btn btn-primary" id="drop-AXV">Drop Academic x Visit Type Table</button>
                    </div>
                </div>             
                    <span class="delete-notification-overlay">
                    <span class="delete-notification">
                        <h2>Are you sure you want to drop <span class="reset-selected"></span>?</h2>
                        <span>
                            <button class="btn btn-primary tables-drop-btn" >Yes</button>
                            <button class="btn btn-danger cancel-btn">Cancel</button>
                        </span> 
                    </span>
                    </span> 
                </div>
            </div>
            <?php
            }
            if($_GET['tab'] == "truncate-tables")
            {
                ?>
                <div class="row">
                    <hr />
                    <h2>Truncate Tables</h2>
                    <div clsss="col-sm-12" style="margin-bottom:20px;">Warning, this will truncate all data inside the table. </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 ">
                    <div class="row">
                        <div class="col-sm settings-btn-col">
                            <button class="truncate-tables btn btn-primary" id="truncate-all">Truncate All Tables</button>
                            <button class="truncate-tables btn btn-primary" id="truncate-PL">Truncate Provider Table</button>
                            <button class="truncate-tables btn btn-primary" id="truncate-VT">Truncate Visit Type Table</button>
                            <button class="truncate-tables btn btn-primary" id="truncate-UJ">Truncate User Journey Table</button>
                            <button class="truncate-tables btn btn-primary" id="truncate-SE">Truncate Settings Table</button>
                        </div>
                        <div class="col-sm settings-btn-col">
                            <button class="truncate-tables btn btn-primary" id="truncate-ED">Truncate Epic Department Table</button>
                            <button class="truncate-tables btn btn-primary" id="truncate-AD">Truncate Academic Department Table</button>
                            <button class="truncate-tables btn btn-primary" id="truncate-SB">Truncate Start Block Table</button>
                            <button class="truncate-tables btn btn-primary" id="truncate-DT">Truncate Decision Tree Table</button>
                        </div>
                        <div class="col-sm settings-btn-col">         
                            <button class="truncate-tables btn btn-primary" id="truncate-AU">Truncate Audit Table</button>
                            <button class="truncate-tables btn btn-primary" id="truncate-AXE">Truncate Academic x Epic Table</button>
                            <button class="truncate-tables btn btn-primary" id="truncate-AXP">Truncate Academic x Provider Table</button>
                            <button class="truncate-tables btn btn-primary" id="truncate-AXV">Truncate Academic x Visit Type Table</button>
                        </div>
                    </div>             
                        <span class="truncate-notification-overlay">
                        <span class="truncate-notification">
                            <h2>Are you sure you want to Truncate <span class="reset-selected"></span>?</h2>
                            <span>
                                <button class="btn btn-primary tables-truncate-btn" >Yes</button>
                                <button class="btn btn-danger cancel-btn">Cancel</button>
                            </span> 
                        </span>
                        </span> 
                    </div>
            </div>
            <?php
            }
            if($_GET['tab'] == "create-tables")
            {
                ?>
                <div class="row">
                    <hr />
                    <h2>Create Tables</h2>
                    <div clsss="col-sm-12" style="margin-bottom:20px;">These buttons will create/recreate data tables. </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 ">
                    <div class="row">
                        <div class="col-sm settings-btn-col">
                            <button class="create-tables btn btn-primary" id="create-all">Create All Tables</button>
                            <button class="create-tables btn btn-primary" id="create-PL">Create Provider Table</button>
                            <button class="create-tables btn btn-primary" id="create-VT">Create Visit Type Table</button>
                            <button class="create-tables btn btn-primary" id="create-UJ">Create User Journey Table</button>
                            <button class="create-tables btn btn-primary" id="create-SE">Create Settings Table</button>
                        </div>
                        <div class="col-sm settings-btn-col">
                            <button class="create-tables btn btn-primary" id="create-ED">Create Epic Department Table</button>
                            <button class="create-tables btn btn-primary" id="create-AD">Create Academic Department Table</button>
                            <button class="create-tables btn btn-primary" id="create-SB">Create Start Block Table</button>
                            <button class="create-tables btn btn-primary" id="create-DT">Create Decision Tree Table</button>
                        </div>
                        <div class="col-sm settings-btn-col">         
                            <button class="create-tables btn btn-primary" id="create-AU">Create Audit Table</button>
                            <button class="create-tables btn btn-primary" id="create-AXE">Create Academic x Epic Table</button>
                            <button class="create-tables btn btn-primary" id="create-AXP">Create Academic x Provider Table</button>
                            <button class="create-tables btn btn-primary" id="create-AXV">Create Academic x Visit Type Table</button>
                        </div>
                    </div>             
                        <span class="create-notification-overlay">
                        <span class="create-notification">
                            <h2>Are you sure you want to Create <span class="reset-selected"></span>?</h2>
                            <span>
                                <button class="btn btn-primary tables-create-btn" >Yes</button>
                                <button class="btn btn-danger cancel-btn">Cancel</button>
                            </span> 
                        </span>
                        </span> 
                    </div>
            </div>
            <?php
            }
            ?>
        </div>
        <script>
        //Shows modal to confirm deletion of provider record
        jQuery('.reset-tables').click(function(event) {
            var deleteNotification = document.querySelector(".delete-notification-overlay");
            var tableSelected = event.target.id;
            document.querySelector(".tables-drop-btn").id = tableSelected;
            var tableText = event.target.innerText.replace("Reset","").toLowerCase();
            jQuery(".reset-selected").text(tableText);
            deleteNotification.style.display = "block";
            var pluginBody = document.querySelector("body");
            pluginBody.style.overflow = "hidden";
            
        });

        jQuery('.truncate-tables').click(function(event) {
            var deleteNotification = document.querySelector(".truncate-notification-overlay");
            var tableSelected = event.target.id;
            document.querySelector(".tables-truncate-btn").id = tableSelected;
            var tableText = event.target.innerText.replace("Reset","").toLowerCase();
            jQuery(".reset-selected").text(tableText);
            deleteNotification.style.display = "block";
            var pluginBody = document.querySelector("body");
            pluginBody.style.overflow = "hidden";
            
        });

        jQuery('.create-tables').click(function(event) {
            var deleteNotification = document.querySelector(".create-notification-overlay");
            var tableSelected = event.target.id;
            document.querySelector(".tables-create-btn").id = tableSelected;
            var tableText = event.target.innerText.replace("Reset","").toLowerCase();
            jQuery(".reset-selected").text(tableText);
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

        jQuery( 'form#epic-url-form' ).on( 'submit', async function(e) {
			e.preventDefault();
			e.stopPropagation();

			var epicUrl = jQuery('#epic-url').val();
            var epicCSSUrl = jQuery('#epic-css').val();
            var epicJSUrl = jQuery('#epic-js').val();
            var epicAPIKey = jQuery('#epic-api').val();
            var epicBaseUrl = jQuery('#epic-base').val();
            var adminAPI = jQuery('#admin-api-key').val();
            var frontendAPI = jQuery('#frontend-api-key').val();
            var userjourneyAPI = jQuery('#user-journey-api-key').val();

            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo admin_url('admin-ajax.php'); ?>", 
                data: { 
                    'action' : 'add_new_epic_url',
                    'epicUrl': epicUrl,
                    'epicJSUrl': epicJSUrl,
                    'epicCSSUrl': epicCSSUrl,
                    'epicAPIKey': epicAPIKey,
                    'epicBaseUrl': epicBaseUrl,
                    'adminAPI': adminAPI,
                    'frontendAPI': frontendAPI,
                    'userjourneyAPI': userjourneyAPI
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

        jQuery( '.tables-drop-btn' ).on( 'click', async function(e) {
            e.preventDefault();
            e.stopPropagation();

            var tableType = e.target.id.replace("drop-","");

            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo admin_url('admin-ajax.php'); ?>", 
                data: { 
                    'action' : 'reset_data_tables',
                    'table': tableType
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

        jQuery( '.tables-truncate-btn' ).on( 'click', async function(e) {
            e.preventDefault();
            e.stopPropagation();

            var tableType = e.target.id.replace("truncate-","");

            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo admin_url('admin-ajax.php'); ?>", 
                data: { 
                    'action' : 'truncate_data_tables',
                    'table': tableType
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

        jQuery( '.tables-create-btn' ).on( 'click', async function(e) {
            e.preventDefault();
            e.stopPropagation();

            var tableType = e.target.id.replace("create-","");

            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo admin_url('admin-ajax.php'); ?>", 
                data: { 
                    'action' : 'create_data_tables',
                    'table': tableType
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
     * This function takes the epic url form data after the Ajax call and stores it within the WordPress database
     */

    add_action( 'wp_ajax_nopriv_add_new_epic_url', 'add_new_epic_url' );
    add_action( 'wp_ajax_add_new_epic_url', 'add_new_epic_url' );
    function add_new_epic_url()
    {
        global $wpdb;
        $epicUrl = sanitize_text_field($_POST["epicUrl"]);
        $epicCSSUrl = sanitize_text_field($_POST["epicCSSUrl"]);
        $epicJSUrl = sanitize_text_field($_POST["epicJSUrl"]);
        $epicAPIKey = sanitize_text_field($_POST["epicAPIKey"]);
        $epicBaseUrl = sanitize_text_field($_POST["epicBaseUrl"]);
        $adminAPI = sanitize_text_field($_POST["adminAPI"]);
        $frontendAPI = sanitize_text_field($_POST["frontendAPI"]);
        $userjourneyAPI = sanitize_text_field($_POST["userjourneyAPI"]);

        /// Query to fetch provider data from database table and storing in $results
        $rowExists = $wpdb->get_var("SELECT COUNT(PS_SettingName) FROM wuos_pluginsettings WHERE PS_SettingName = 'Epic Url'"); 

        $data = array(
            'PS_SettingName' => 'Epic Url',
            'PS_SettingValue' => $epicUrl
        );
        
        if($rowExists > 0)
        {
            $wpdb->update('wuos_pluginsettings', $data, array('PS_SettingName' => 'Epic Url'));
        }
        else 
        {
            $wpdb->insert('wuos_pluginsettings', $data);
        }

        $rowExists = $wpdb->get_var("SELECT COUNT(PS_SettingName) FROM wuos_pluginsettings WHERE PS_SettingName = 'Epic CSS Url'"); 

        $data = array(
            'PS_SettingName' => 'Epic CSS Url',
            'PS_SettingValue' => $epicCSSUrl
        );

        if($rowExists > 0)
        {
            $wpdb->update('wuos_pluginsettings', $data, array('PS_SettingName' => 'Epic CSS Url'));
        }
        else 
        {
            $wpdb->insert('wuos_pluginsettings', $data);
        }

        $rowExists = $wpdb->get_var("SELECT COUNT(PS_SettingName) FROM wuos_pluginsettings WHERE PS_SettingName = 'Epic JS Url'"); 

        $data = array(
            'PS_SettingName' => 'Epic JS Url',
            'PS_SettingValue' => $epicJSUrl
        );

        if($rowExists > 0)
        {
            $wpdb->update('wuos_pluginsettings', $data, array('PS_SettingName' => 'Epic JS Url'));
        }
        else 
        {
            $wpdb->insert('wuos_pluginsettings', $data);
        }

        $rowExists = $wpdb->get_var("SELECT COUNT(PS_SettingName) FROM wuos_pluginsettings WHERE PS_SettingName = 'Epic API Key'"); 

        $data = array(
            'PS_SettingName' => 'Epic API Key',
            'PS_SettingValue' => $epicAPIKey
        );

        if($rowExists > 0)
        {
            $wpdb->update('wuos_pluginsettings', $data, array('PS_SettingName' => 'Epic API Key'));
        }
        else 
        {
            $wpdb->insert('wuos_pluginsettings', $data);
        }


        $rowExists = $wpdb->get_var("SELECT COUNT(PS_SettingName) FROM wuos_pluginsettings WHERE PS_SettingName = 'Epic Base Url'"); 

        $data = array(
            'PS_SettingName' => 'Epic Base Url',
            'PS_SettingValue' => $epicBaseUrl
        );

        if($rowExists > 0)
        {
            $wpdb->update('wuos_pluginsettings', $data, array('PS_SettingName' => 'Epic Base Url'));
        }
        else 
        {
            $wpdb->insert('wuos_pluginsettings', $data);
        }



        /// Query to fetch provider data from database table and storing in $results
        $rowExists = $wpdb->get_var("SELECT COUNT(PS_SettingName) FROM wuos_pluginsettings WHERE PS_SettingName = 'Admin API'"); 

        $data = array(
            'PS_SettingName' => 'Admin API',
            'PS_SettingValue' => $adminAPI
        );
        
        if($rowExists > 0)
        {
            $wpdb->update('wuos_pluginsettings', $data, array('PS_SettingName' => 'Admin API'));
        }
        else 
        {
            $wpdb->insert('wuos_pluginsettings', $data);
        }

         /// Query to fetch provider data from database table and storing in $results
         $rowExists = $wpdb->get_var("SELECT COUNT(PS_SettingName) FROM wuos_pluginsettings WHERE PS_SettingName = 'Frontend API'"); 

         $data = array(
            'PS_SettingName' => 'Frontend API',
            'PS_SettingValue' => $frontendAPI
        );
        
        if($rowExists > 0)
        {
            $wpdb->update('wuos_pluginsettings', $data, array('PS_SettingName' => 'Frontend API'));
        }
        else 
        {
            $wpdb->insert('wuos_pluginsettings', $data);
        }


         /// Query to fetch provider data from database table and storing in $results
         $rowExists = $wpdb->get_var("SELECT COUNT(PS_SettingName) FROM wuos_pluginsettings WHERE PS_SettingName = 'User Journey API'"); 

         $data = array(
            'PS_SettingName' => 'User Journey API',
            'PS_SettingValue' => $userjourneyAPI
        );
        
        if($rowExists > 0)
        {
            $wpdb->update('wuos_pluginsettings', $data, array('PS_SettingName' => 'User Journey API'));
        }
        else 
        {
            $wpdb->insert('wuos_pluginsettings', $data);
        }

        return;
        wp_die();

    }

    

    /**
     * This function handles the drop table button functionality on the settings page
     */
    add_action( 'wp_ajax_nopriv_reset_data_tables', 'reset_data_tables' );
    add_action( 'wp_ajax_reset_data_tables', 'reset_data_tables' );
    function reset_data_tables()
	{
		global $wpdb;

		$tableType = sanitize_text_field($_POST["table"]);

        $dropTableSql = array(
            'AD' => "DROP Table wuos_academicdepartment;",
            'RAD' => "DROP Table wuos_replaceacademicdepartment;",
            'VT' => "DROP Table wuos_visittypelist;",
            'RVT' => "DROP Table wuos_replacevisittypelist;",
            'ED' => "DROP Table wuos_epicdepartment;",
            'RED' => "DROP Table wuos_replaceepicdepartment;",
            'PL' => "DROP Table wuos_providerlist;",
            'RPL' => "DROP Table wuos_replaceproviderlist;",
            'AXE' =>"DROP Table wuos_referenceacademicxepic;",
            'AXP' =>"DROP Table wuos_referenceacademicxprovider;",
            'AXV' =>"DROP Table wuos_referenceacademicxvisit;",
            'SB' =>"DROP Table wuos_startblock;",               
            'DT' =>"DROP Table wuos_decisiontree;",
            'AU' =>"DROP Table wuos_auditlog;",
            'SE' =>"DROP Table wuos_pluginsettings;",
            'UJ' =>"DROP Table wuos_userjourney;"
        );

        if($tableType == "all"){
            foreach ($dropTableSql as $key => $value) {
                $wpdb->query($value);
            }
        }
        else{
            if($tableType == 'AD' || $tableType == 'VT' || $tableType == 'ED' || $tableType == 'PL'){
                $wpdb->query($dropTableSql["R" . $tableType]);
                $wpdb->query($dropTableSql[$tableType]);
            }
            else{
                $wpdb->query($dropTableSql[$tableType]);
            }
            
        }
        
        
        return;
        wp_die();
	}
    
    /**
* This function handles the truncate table button functionality on the settings page
*/
add_action( 'wp_ajax_nopriv_truncate_data_tables', 'truncate_data_tables' );
add_action( 'wp_ajax_truncate_data_tables', 'truncate_data_tables' );
function truncate_data_tables()
{
   global $wpdb;

   $tableType = sanitize_text_field($_POST["table"]);

   $truncateTableSql = array(
       'AD' => "TRUNCATE Table wuos_academicdepartment;",
       'RAD' => "TRUNCATE Table wuos_replaceacademicdepartment;",
       'VT' => "TRUNCATE Table wuos_visittypelist;",
       'RVT' => "TRUNCATE Table wuos_replacevisittypelist;",
       'ED' => "TRUNCATE Table wuos_epicdepartment;",
       'RED' => "TRUNCATE Table wuos_replaceepicdepartment;",
       'PL' => "TRUNCATE Table wuos_providerlist;",
       'RPL' => "TRUNCATE Table wuos_replaceproviderlist;",
       'AXE' =>"TRUNCATE Table wuos_referenceacademicxepic;",
       'AXP' =>"TRUNCATE Table wuos_referenceacademicxprovider;",
       'AXV' =>"TRUNCATE Table wuos_referenceacademicxvisit;",
       'SB' =>"TRUNCATE Table wuos_startblock;",               
       'DT' =>"TRUNCATE Table wuos_decisiontree;",
       'AU' =>"TRUNCATE Table wuos_auditlog;",
       'SE' =>"TRUNCATE Table wuos_pluginsettings;",
       'UJ' =>"TRUNCATE Table wuos_userjourney;"
   );

   if($tableType == "all"){
       foreach ($truncateTableSql as $key => $value) {
           $wpdb->query($value);
       }
   }
   else{
       if($tableType == 'AD' || $tableType == 'VT' || $tableType == 'ED' || $tableType == 'PL'){
           $wpdb->query($truncateTableSql["R" . $tableType]);
           $wpdb->query($truncateTableSql[$tableType]);
       }
       else{
           $wpdb->query($truncateTableSql[$tableType]);
       }
       
   }
   
   
   return;
   wp_die();
}


add_action( 'wp_ajax_nopriv_create_data_tables', 'create_data_tables' );
add_action( 'wp_ajax_create_data_tables', 'create_data_tables' );
function create_data_tables()
{
   global $wpdb;

    $tableType = sanitize_text_field($_POST["table"]);
    
    if($tableType == "all"){
        create_provider_list_database_table();
        create_replace_provider_list_database_table();
        create_decision_tree_database_table();
        create_audit_database_table();
        create_user_journey_database_table();
        create_epic_department_database_table();
        create_replace_epic_department_database_table();
        create_academic_department_database_table();
        create_replace_academic_department_database_table();
        create_visit_type_list_database_table();
        create_replace_visit_type_list_database_table();
        create_crosstable_pl_ad_database_table();
        create_crosstable_ed_ad_database_table();
        create_crosstable_vt_ad_database_table();
        create_start_block_database_table();
        create_settings_database_table();
    }
    else{
        if($tableType == 'AD'){
            create_academic_department_database_table();
            create_replace_academic_department_database_table();
        }
        if($tableType == 'VT'){
            create_visit_type_list_database_table();
            create_replace_visit_type_list_database_table();
        }
        if($tableType == 'ED'){
            create_epic_department_database_table();
            create_replace_epic_department_database_table();
        }
        if($tableType == 'PL'){
            create_provider_list_database_table();
            create_replace_provider_list_database_table();
        }
        if($tableType == 'AXE'){
            create_crosstable_ed_ad_database_table();
        }
        if($tableType == 'AXP'){
            create_crosstable_pl_ad_database_table();
        }
        if($tableType == 'AXV'){
            create_crosstable_vt_ad_database_table();
        }
        if($tableType == 'SB'){
            create_start_block_database_table();
        }
        if($tableType == 'DT'){
            create_decision_tree_database_table();
        }
        if($tableType == 'AU'){
            create_audit_database_table();
        }
        if($tableType == 'SE'){
            create_settings_database_table();
        }
        if($tableType == 'UJ'){
            create_user_journey_database_table();
        }
        
    }

    return;
    wp_die();  

}
