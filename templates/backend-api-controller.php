<?php

function add_cors_http_header()
{
    header("Access-Control-Allow-Origin: *");
}
add_action('init','add_cors_http_header');

function getAdminAPIKey()
{
	global $wpdb;

	$sql = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Admin API'";
	$data = $wpdb->get_results($sql);

	return $data[0]->PS_SettingValue;
}

//Retrieve Admin API Key
function get_backend_api_key(WP_REST_Request $request)
{
	$apiKey = getAdminAPIKey();

	$results = array(
		'API' => $apiKey
	);

	return $results;
}

//Register Wordpress API route for retrieving decision tree data
add_action( 'rest_api_init', function () {
	register_rest_route( 'washu-open-scheduling/v1', '/getAdminAPI', array(
	  'methods' => "POST",
	  'callback' => 'get_backend_api_key',
	  'permission_callback' => '__return_true',
	) );
} );

function getFrontendAPIKey()
{
	global $wpdb;

	$sql = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Frontend API'";
	$data = $wpdb->get_results($sql);

	return $data[0]->PS_SettingValue;
}

//Retrieve Frontend API Key
function get_frontend_api_key(WP_REST_Request $request)
{
	$apiKey = getFrontendAPIKey();
    
	$results = array(
		'API' => $apiKey
	);

	return $results;
}

//Register Wordpress API route for retrieving decision tree data
add_action( 'rest_api_init', function () {
	register_rest_route( 'washu-open-scheduling/v1', '/getFrontendAPI', array(
	  'methods' => "POST",
	  'callback' => 'get_frontend_api_key',
	  'permission_callback' => '__return_true',
	) );
} );

function getUserJourneyAPIKey()
{
	global $wpdb;

	$sql = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'User Journey API'";
	$data = $wpdb->get_results($sql);

	return $data[0]->PS_SettingValue;
}

//Retrieve Admin API Key
function get_user_journey_api_key(WP_REST_Request $request)
{
	$apiKey = getUserJourneyAPIKey();

	$results = array(
		'API' => $apiKey
	);

	return $results;
}

//Register Wordpress API route for retrieving decision tree data
add_action( 'rest_api_init', function () {
	register_rest_route( 'washu-open-scheduling/v1', '/getUserJourneyAPI', array(
	  'methods' => "POST",
	  'callback' => 'get_user_journey_api_key',
	  'permission_callback' => '__return_true',
	) );
} );

function checkAdminAccess($apiKey)
{
	$apiAdminKey = getAdminAPIKey();

	if($apiKey == $apiAdminKey)
	{
		return true;
	}

	return false;
}

function checkFrontendAdminAccess($apiKey)
{
	$apiAdminKey = getAdminAPIKey();
	$apiFrontendKey = getFrontendAPIKey();

	if(($apiKey == $apiAdminKey) || ($apiKey == $apiFrontendKey))
	{
		return true;
	}

	return false;
}

function checkUserJourneyAccess($apiKey)
{
	$userJourneyKey = getUserJourneyAPIKey();

	if(($apiKey == $userJourneyKey))
	{
		return true;
	}

	return false;
}

//Retrieve decision tree data from database
function get_decision_tree_data(WP_REST_Request $request)
{
	global $wpdb;

    $parameters = $request->get_params();
	$treeId = $parameters['treeId'];

	$apiKey = $parameters['apiKey'];
	$access = checkFrontendAdminAccess($apiKey);
	if(!$access)
	{
		return "Access Denied";
	}

	$resultArray = [];

	$sql = "SELECT DT_Name, DT_JSON FROM  wuos_decisiontree WHERE DT_ID = $treeId";
	$existingTrees = $wpdb->get_results($sql);

	$sqlEpic = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Epic Url'";
	$epicData = $wpdb->get_results($sqlEpic);

	$sqlEpicAPI = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Epic API Key'";
	$epicAPIData = $wpdb->get_results($sqlEpicAPI);
    
	$results = array(
		'TreeName' => $existingTrees[0]->DT_Name,
		'TreeData' => $existingTrees[0]->DT_JSON,
		'EpicUrl' => $epicData[0]->PS_SettingValue,
		'EpicAPI' => $epicAPIData[0]->PS_SettingValue,
	);

	return $results;
}

//Register Wordpress API route for retrieving decision tree data
add_action( 'rest_api_init', function () {
	register_rest_route( 'washu-open-scheduling/v1', '/treegetdata', array(
	  'methods' => "POST",
	  'callback' => 'get_decision_tree_data',
	  'permission_callback' => '__return_true',
	) );
} );

//Retrieve all decision tree data from database
function get_all_decision_tree_data(WP_REST_Request $request)
{
	$parameters = $request->get_params();
	$apiKey = $parameters['apiKey'];
	$access = checkAdminAccess($apiKey);
	if(!$access)
	{
		return "Access Denied";
	}

	global $wpdb;

	$sql = "SELECT 	DT_ID, 
					DT_Name, 
					DT_JSON
			FROM  	wuos_decisiontree";
	
	$existingTrees = $wpdb->get_results($sql);

	return $existingTrees;
}

//Register Wordpress API route for retrieving decision tree data
add_action( 'rest_api_init', function () {
	register_rest_route( 'washu-open-scheduling/v1', '/treegetalldata', array(
	  'methods' => "POST",
	  'callback' => 'get_all_decision_tree_data',
	  'permission_callback' => '__return_true',
	) );
} );

//Retrieve dropdown data for terminaton point from database
function get_all_dropdown_data(WP_REST_Request $request)
{
	$parameters = $request->get_params();
	$apiKey = $parameters['apiKey'];
	$access = checkFrontendAdminAccess($apiKey);
	if(!$access)
	{
		return "Access Denied";
	}

	global $wpdb;

	$sqlPL = "SELECT 	DISTINCT PL_NPI, PL_ProviderName, ad.AD_Code
			FROM  	wuos_providerlist pl
					JOIN wuos_referenceacademicxprovider rap ON rap.PL_ID = pl.PL_ID
					JOIN wuos_academicdepartment ad ON ad.AD_ID = rap.AD_ID
			ORDER BY PL_ProviderName";
	$resultsPL = $wpdb->get_results($sqlPL);

	$sqlVT = "SELECT 	DISTINCT VT_VisitTypeID, VT_Name, ad.AD_Code
			FROM  	wuos_visittypelist vt
					JOIN wuos_referenceacademicxvisit rav ON rav.VT_ID = vt.VT_ID
					JOIN wuos_academicdepartment ad ON ad.AD_ID = rav.AD_ID
			ORDER BY VT_Name";
	$resultsVT = $wpdb->get_results($sqlVT);

	$sqlED = "SELECT 	DISTINCT ED_Code, ED_Name, ad.AD_Code
			FROM  	wuos_epicdepartment ed
					JOIN wuos_referenceacademicxepic rae ON rae.ED_ID = ed.ED_ID
					JOIN wuos_academicdepartment ad ON ad.AD_ID = rae.AD_ID
			ORDER BY ED_Name";
	$resultsED = $wpdb->get_results($sqlED);

	$sqlAD = "SELECT 	DISTINCT AD_Code, AD_Name, AD_ID 
			FROM  	wuos_academicdepartment 
			ORDER BY AD_Name";
	$resultsAD = $wpdb->get_results($sqlAD);

	$sqlEpic = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Epic Url'";
	$epicData = $wpdb->get_results($sqlEpic);
	
	$sqlEpicAPI = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Epic API Key'";
	$epicAPI = $wpdb->get_results($sqlEpicAPI);

	$results = array(
		'ResultsPL' => $resultsPL,
		'ResultsVT' => $resultsVT,
		'ResultsED' => $resultsED,
		'ResultsAD' => $resultsAD,
		'EpicUrl' => $epicData[0]->PS_SettingValue,
		'EpicAPI' => $epicAPI[0]->PS_SettingValue,
	);

	return $results;
}

//Register Wordpress API route for retrieving decision tree data
add_action( 'rest_api_init', function () {
	register_rest_route( 'washu-open-scheduling/v1', '/getdropdowndata', array(
	  'methods' => "POST",
	  'callback' => 'get_all_dropdown_data',
	  'permission_callback' => '__return_true',
	) );
} );

//Add data to database
function post_decision_tree_data(WP_REST_Request $request)
{
	
	global $wpdb;
	
	$parameters = $request->get_params();
	
	$treeId = $parameters['treeId'];
	$treeName = $parameters['treeName'];
	$treeJson = $parameters['treeJson'];

	$apiKey = $parameters['apiKey'];
	$access = checkAdminAccess($apiKey);
	if(!$access)
	{
		return "Access Denied";
	}

	$createdtime = current_time('mysql');
	$createdBy= get_current_user_id();

	$data = array(
		'DT_Name' => $treeName,
		'DT_JSON' => $treeJson,
		'DT_CreatedOn' => $createdtime,
		'DT_CreatedBy' => $createdBy
	);

	$dbResponse = array();
	
	if($treeId < 1)
	{
		$wpdb->insert('wuos_decisiontree', $data);
		$dtid = $wpdb->insert_id;

		$dbResponse = array('DT_ID'  => $dtid);
	}
	else
	{
		$wpdb->update('wuos_decisiontree', $data, array('DT_ID' => $treeId));
		$dbResponse = array('DT_ID'  => $treeId);
	}

	return $dbResponse;
}

//Register Wordpress API route to add decision tree data
add_action( 'rest_api_init', function () {
	register_rest_route( 'washu-open-scheduling/v1', '/treepostdata', array(
	  'methods' => "POST",
	  'callback' => 'post_decision_tree_data',
	  'permission_callback' => '__return_true',
	) );
} );

//Add data to database
function post_start_block_data(WP_REST_Request $request)
{
	global $wpdb;
	
	$parameters = $request->get_params();
	
	$id = ($parameters['id'] ? $parameters['id'] : 0);


	$name = $parameters['name'];
	$blockJson = $parameters['json'];
	$treeId = $parameters['treeId'];

	$apiKey = $parameters['apiKey'];
	$access = checkAdminAccess($apiKey);
	if(!$access)
	{
		return "Access Denied";
	}

	$data = array(
		'SB_Name' => $name,
		'SB_JSON' => $blockJson,
		'SB_TreeID'=> $treeId
	);
	
	if($id > 0)
	{
		$wpdb->update('wuos_startblock', $data, array('SB_ID' => $id));
		$dbResponse = array('SB_ID'  => $id);
	}
	else 
	{
		$wpdb->insert('wuos_startblock', $data);
		$sbid = $wpdb->insert_id;

		$dbResponse = array('SB_ID'  => $sbid);
	}

	return $dbResponse;
}

//Register Wordpress API route to add decision tree data
add_action( 'rest_api_init', function () {
	register_rest_route( 'washu-open-scheduling/v1', '/poststartblockdata', array(
	  'methods' => "POST",
	  'callback' => 'post_start_block_data',
	  'permission_callback' => '__return_true',
	) );
} );

//Retrieve decision tree data from database
function get_all_start_block_data(WP_REST_Request $request)
{
	$parameters = $request->get_params();
	$apiKey = $parameters['apiKey'];
	$access = checkAdminAccess($apiKey);
	if(!$access)
	{
		return "Access Denied";
	}

	global $wpdb;

	$sql = "SELECT * FROM  wuos_startblock";
	
	$existingBlocks = $wpdb->get_results($sql);

	return $existingBlocks;
}

//Register Wordpress API route for retrieving decision tree data
add_action( 'rest_api_init', function () {
	register_rest_route( 'washu-open-scheduling/v1', '/startblockgetalldata', array(
	  'methods' => "POST",
	  'callback' => 'get_all_start_block_data',
	  'permission_callback' => '__return_true',
	) );
} );

//Delete decision tree data by ID from database - I had to fix his because it just would not work the way it is. 
function delete_decision_tree_data(WP_REST_Request $request)
{
	global $wpdb;
	$parameters = $request->get_params();

	$treeId = $parameters['id'];
	
	$apiKey = $parameters['apiKey'];
	$access = checkAdminAccess($apiKey);
	if(!$access)
	{
		return "Access Denied";
	}

	$data = array('DT_ID' => $treeId);
	
	$wpdb->delete('wuos_decisiontree', $data);
	
	$sql = "SELECT DT_ID, DT_Name, DT_JSON FROM  wuos_decisiontree";
	
	$existingTrees = $wpdb->get_results($sql);

	return $existingTrees;
}

//Register Wordpress API route to update decision tree data
add_action( 'rest_api_init', function () {
	register_rest_route( 'washu-open-scheduling/v1', '/treedeletedata', array(
	  'methods' => "POST",
	  'callback' => 'delete_decision_tree_data',
	) );
} );


//Delete decision tree data by ID from database - I had to fix his because it just would not work the way it is. 
function delete_start_block_data(WP_REST_Request $request)
{
	global $wpdb;
	$parameters = $request->get_params();

	$blockId = $parameters['blockId'];

	$apiKey = $parameters['apiKey'];
	$access = checkAdminAccess($apiKey);
	if(!$access)
	{
		return "Access Denied";
	}

	$data = array('SB_ID' => $blockId);
	
	$wpdb->delete('wuos_startblock', $data);

	$results = array('message' => 'Deleted');
	return "";
}

//Register Wordpress API route to update decision tree data
add_action( 'rest_api_init', function () {
	register_rest_route( 'washu-open-scheduling/v1', '/startblockdeletedata', array(
	  'methods' => "POST",
	  'callback' => 'delete_start_block_data',
	) );
} );

//Retrieve decision tree data from database
function get_start_block_data(WP_REST_Request $request)
{
	global $wpdb;

    $parameters = $request->get_params();
    $id = $parameters['startBlockId'];

	$apiKey = $parameters['apiKey'];
	$access = checkAdminAccess($apiKey);
	if(!$access)
	{
		return "Access Denied";
	}
    
	$sql = "SELECT SB_JSON FROM  wuos_startblock WHERE SB_ID = $id"; 
	
	$startBlockData = $wpdb->get_results($sql);
    
	$results = array(
		'SBJSON' => $startBlockData[0]->SB_JSON,
	);

	return $results;
}

//Register Wordpress API route for retrieving decision tree data
add_action( 'rest_api_init', function () {
	register_rest_route( 'washu-open-scheduling/v1', '/startblockgetdata', array(
	  'methods' => "POST",
	  'callback' => 'get_start_block_data',
	  'permission_callback' => '__return_true',
	) );
} );

//Retrieve decision tree data from database
function get_start_block_by_tree(WP_REST_Request $request)
{
    $parameters = $request->get_params();
    $id = $parameters['treeId'];

	$apiKey = $parameters['apiKey'];
	$access = checkFrontendAdminAccess($apiKey);
	if(!$access)
	{
		return "Access Denied";
	}

	global $wpdb;
    
	$sql = "SELECT SB_ID, SB_Name, SB_JSON FROM  wuos_startblock WHERE SB_TreeID = $id"; 
	
	$startBlockData = $wpdb->get_results($sql);
    
	if (!empty($startBlockData) && is_array($startBlockData)) {
		$firstResult = $startBlockData[0];
	
		$SBName = isset($firstResult->SB_Name) ? $firstResult->SB_Name : 'No Result';
		$SBJSON = isset($firstResult->SB_JSON) ? $firstResult->SB_JSON : 'No Result';
		$SBID = isset($firstResult->SB_ID) ? $firstResult->SB_ID : 'No Result';
	
		$results = array(
			'SBName' => $SBName,
			'SBJSON' => $SBJSON,
			'SBID' => $SBID
		);
	} else {
		// Handle the case when no result is returned from the query
		$results = array(
			'SBName' => 'No Result',
			'SBJSON' => 'No Result',
			'SBID' => 'No Result'
		);
	}
	
	return $results;
	


	/*
	$results = array(
		'SBName' => $startBlockData[0]->SB_Name,
		'SBJSON' => $startBlockData[0]->SB_JSON,
		'SBID' => $startBlockData[0]->SB_ID
	);
	*/
	return $results;
}

//Register Wordpress API route for retrieving decision tree data
add_action( 'rest_api_init', function () {
	register_rest_route( 'washu-open-scheduling/v1', '/startblockgetbytree', array(
	  'methods' => "POST",
	  'callback' => 'get_start_block_by_tree',
	  'permission_callback' => '__return_true',
	) );
} );

//User Tracking Endpoint
function track_users(WP_REST_Request $request)
{
	$parameters = $request->get_params();
	$apiKey = $parameters['apiKey'];
	$access = checkFrontendAdminAccess($apiKey);
	if(!$access)
	{
		return "Access Denied";
	}

	global $wpdb;

	/*
    $parameters = $request->get_params();
    $sessionID = $parameters['sessionID'];
	$country = $parameters['country'];
	$countryName = $parameters['countryName'];
	$city = '';
	$state = $parameters['state'];
	$postal = '';
	$longitude = '';
	$latitude = '';
	$ipAddress = '';
	$browserInfo = $parameters['browserInfo'];
	$startBlockID = $parameters['startBlockID'];
	$startBlock = $parameters['startBlock'];
	$decisionTreeID = $parameters['decisionTreeID'];
	$decisionTree = $parameters['decisionTree'];
	$question = $parameters['question'];
	$answer = $parameters['answer'];
	$termination = $parameters['termination'];
	*/

	$parameters = $request->get_params();

	$sessionID       = isset($parameters['sessionID']) ? $parameters['sessionID'] : '';
	$country         = isset($parameters['country']) ? $parameters['country'] : '';
	$countryName     = isset($parameters['countryName']) ? $parameters['countryName'] : '';
	$city = '';
	$state           = isset($parameters['state']) ? $parameters['state'] : '';
	$postal = '';
	$longitude = '';
	$latitude = '';
	$ipAddress = '';
	$browserInfo     = isset($parameters['browserInfo']) ? $parameters['browserInfo'] : '';
	$startBlockID    = isset($parameters['startBlockID']) ? $parameters['startBlockID'] : '';
	$startBlock      = isset($parameters['startBlock']) ? $parameters['startBlock'] : '';
	$decisionTreeID  = isset($parameters['decisionTreeID']) ? $parameters['decisionTreeID'] : '';
	$decisionTree    = isset($parameters['decisionTree']) ? $parameters['decisionTree'] : '';
	$question        = isset($parameters['question']) ? $parameters['question'] : '';
	$answer          = isset($parameters['answer']) ? $parameters['answer'] : '';
	$termination     = isset($parameters['termination']) ? $parameters['termination'] : '';

	
	$data = array(
		'UJ_SessionID' => $sessionID,
		'UJ_Country' => $country,
		'UJ_CountryName' => $countryName,
		'UJ_City' => $city,
		'UJ_State' => $state,
		'UJ_Postal' => $postal,
		'UJ_Latitude' => $latitude,
		'UJ_Longitude' => $longitude,
		'UJ_IPAddress' => $ipAddress,
		'UJ_BrowserInfo'=> $browserInfo,
		'UJ_StartBlockID'=> $startBlockID,
		'UJ_StartBlock'=> $startBlock,
		'UJ_DecisionTreeID'=> $decisionTreeID,
		'UJ_DecisionTree'=> $decisionTree,
		'UJ_Question'=> $question,
		'UJ_Answer'=> $answer,
		'UJ_Termination'=> $termination,
		'UJ_ActionDateTime' => date("Y-m-d H:i:s")
	);

	$wpdb->insert('wuos_userjourney', $data);
	$ujid = $wpdb->insert_id;

	$dbResponse = array('UJ_ID'  => $ujid, 'Status' => 'Recorded');

	return $dbResponse;
}

//Register Wordpress API route for retrieving decision tree data
add_action( 'rest_api_init', function () {
	register_rest_route( 'washu-open-scheduling/v1', '/track_users', array(
	  'methods' => "POST",
	  'callback' => 'track_users',
	  'permission_callback' => '__return_true',
	) );
} );


//Retrieve decision tree data from database
function get_all_user_journey_data(WP_REST_Request $request)
{
	$parameters = $request->get_params();
	$apiKey = $parameters['apiKey'];
	$startDate = $parameters['startDate'];
	$endDate = $parameters['endDate'];
	$access = checkUserJourneyAccess($apiKey);
	if(!$access)
	{
		return "Access Denied";
	}

	global $wpdb;

	$data_rows = array();

	$query = "SELECT * FROM wuos_userjourney ";
	
	if($startDate || $endDate)
	{
		$query .= " WHERE ";
	}

	if($startDate)
	{
		$query .= " UJ_ActionDateTime >= '$startDate' ";
	}

	if($endDate)
	{
		if($startDate)
		{
			$query .= " AND ";
		}

		$query .= " UJ_ActionDateTime <= '$endDate' ";
	}


	$results = $wpdb->get_results($query);

	foreach ( $results as $item ) 
	{
		$data_rows[] = array(
			'UJ_ID' => $item->UJ_ID,
			'UJ_SessionID' => $item->UJ_SessionID,
			'UJ_Country' => $item->UJ_Country,
			'UJ_CountryName' => $item->UJ_CountryName,
			'UJ_City' => $item->UJ_City,
			'UJ_State' => $item->UJ_State,
			'UJ_Postal' => $item->UJ_Postal,
			'UJ_Latitude' => $item->UJ_Latitude,
			'UJ_Longitude' => $item->UJ_Longitude,
			'UJ_IPAddress' => $item->UJ_IPAddress,
			'UJ_BrowserInfo' => $item->UJ_BrowserInfo,
			'UJ_StartBlockID' => $item->UJ_StartBlockID,
			'UJ_StartBlock' => $item->UJ_StartBlock,
			'UJ_DecisionTreeID' => $item->UJ_DecisionTreeID,
			'UJ_DecisionTree' => $item->UJ_DecisionTree,
			'UJ_Question' => $item->UJ_Question,
			'UJ_Answer' => $item->UJ_Answer,
			'UJ_Termination' => $item->UJ_Termination,
			'UJ_ActionDateTime' => $item->UJ_ActionDateTime 
		);
	}
     
	return $data_rows;
}

//Register Wordpress API route for retrieving decision tree data
add_action( 'rest_api_init', function () {
	register_rest_route( 'washu-open-scheduling/v1', '/userjourneygetalldata', array(
	  'methods' => "POST",
	  'callback' => 'get_all_user_journey_data',
	  'permission_callback' => '__return_true',
	) );
} );
