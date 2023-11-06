<?php
	ob_start();
	ob_implicit_flush();
	if(isset($_GET['DT_ID']))
	{
		header_remove(); 

		header('Content-Type: text/csv');  
		header('Content-Disposition: attachment; filename="decisiontreedata.csv"'); 
		global $wpdb;

		$header_row = array(
			'DT_Name',
			'Overwrite',
			'DT_Description',
			'DT_JSON'
		);

		$data_rows = array();

		$paramID = $_GET['DT_ID'];
		$results = $wpdb->get_results("SELECT DT_Name, DT_Description, DT_JSON FROM wuos_decisiontree WHERE DT_ID = $paramID");
		foreach ($results as $item) 
		{
			$row = array(
				$item->DT_Name,
				"No",
				$item->DT_Description,
				addslashes($item->DT_JSON)
			);

			$data_rows[] = $row;
		}

		$fh = fopen('php://output', 'w');
		fputcsv($fh, $header_row);
		foreach ($data_rows as $data_row) 
		{
			fputcsv($fh, $data_row);
		}

		die();
		ob_end_flush();
		fclose($fh);
	}
	
	if(isset($_POST["Export"]) && $_GET['page'] == "decision-tree-builder")
	{
		header_remove(); 

		header('Content-Type: text/csv');  
		header('Content-Disposition: attachment; filename="decisiontreedata.csv"'); 
		global $wpdb;

		$header_row = array(
			'DT_Name',
			'Overwrite',
			'DT_Description',
			'DT_JSON'
		);

		$data_rows = array();
	
		$results = $wpdb->get_results("SELECT DT_Name, DT_Description, DT_JSON FROM wuos_decisiontree");
		foreach ($results as $item) 
		{
			$row = array(
				$item->DT_Name,
				"No",
				$item->DT_Description,
				addslashes($item->DT_JSON)
			);

			$data_rows[] = $row;
		}

		$fh = fopen('php://output', 'w');
		fputcsv($fh, $header_row);
		foreach ($data_rows as $data_row) 
		{
			fputcsv($fh, $data_row);
		}
	
		
		die();
		ob_end_flush();
		fclose($fh);
	}

	function findDTID($name)
	{
		global $wpdb;
		$findTree = $wpdb->get_results("SELECT DT_ID FROM wuos_decisiontree WHERE DT_Name = '".$name."'"); 

		$returnID = 0;
		if(@count($findTree) > 0)
		{
			$returnID = $findTree[0]->DT_ID;
		}

		return $returnID;
	}


    function page_decision_tree_builder()
	{
		global $wpdb;
		
		$sqlJSEpic = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Epic JS Url'";
		$epicJSData = $wpdb->get_results($sqlJSEpic);

		$sqlCSSEpic = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Epic CSS Url'";
		$epicCSSData = $wpdb->get_results($sqlCSSEpic);

		$sqlBaseEpic = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Epic Base Url'";
		$epicBaseData = $wpdb->get_results($sqlBaseEpic);
		
		?>
		<link rel="manifest" href="<?php echo plugins_url('react-backend/build/manifest.json', dirname(__FILE__)); ?>"/>

		<link href="<?php echo plugins_url('react-backend/build/static/css/2.chunk.css', dirname(__FILE__)); ?>" rel="stylesheet">
		<link href="<?php echo plugins_url('react-backend/build/static/css/main.chunk.css', dirname(__FILE__)); ?>" rel="stylesheet">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
		<script src="https://cdn.tiny.cloud/1/tfkw9s9lx599ui1r6dzhnpuilqc33ezccmz71hnodxejuf6d/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

		<script>!function(e){function r(r){for(var n,l,a=r[0],p=r[1],f=r[2],c=0,s=[];c<a.length;c++)l=a[c],Object.prototype.hasOwnProperty.call(o,l)&&o[l]&&s.push(o[l][0]),o[l]=0;for(n in p)Object.prototype.hasOwnProperty.call(p,n)&&(e[n]=p[n]);for(i&&i(r);s.length;)s.shift()();return u.push.apply(u,f||[]),t()}function t(){for(var e,r=0;r<u.length;r++){for(var t=u[r],n=!0,a=1;a<t.length;a++){var p=t[a];0!==o[p]&&(n=!1)}n&&(u.splice(r--,1),e=l(l.s=t[0]))}return e}var n={},o={1:0},u=[];function l(r){if(n[r])return n[r].exports;var t=n[r]={i:r,l:!1,exports:{}};return e[r].call(t.exports,t,t.exports,l),t.l=!0,t.exports}l.m=e,l.c=n,l.d=function(e,r,t){l.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:t})},l.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},l.t=function(e,r){if(1&r&&(e=l(e)),8&r)return e;if(4&r&&"object"==typeof e&&e&&e.__esModule)return e;var t=Object.create(null);if(l.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:e}),2&r&&"string"!=typeof e)for(var n in e)l.d(t,n,function(r){return e[r]}.bind(null,n));return t},l.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return l.d(r,"a",r),r},l.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},l.p="/";var a=this["webpackJsonpwashu-poc"]=this["webpackJsonpwashu-poc"]||[],p=a.push.bind(a);a.push=r,a=a.slice();for(var f=0;f<a.length;f++)r(a[f]);var i=p;t()}([])</script>




		<style>
			.ReactVirtualized__Grid{
				overflow:visible !important;
				height: 100% !important;
			}
		
			.btn-primary
			{
				color: #a51417;
				background-color: #fff;
				border: 2px solid #a51417;
			}

			.btn-primary:hover
			{
				color: #fff;
				background-color: #a51417;
				border: 2px solid #fff;
			}

			.btn-danger
			{
				color: #fff;
				background-color: #a51417;
			}

			

			.ReactVirtualized__Grid__innerScrollContainer .rst__node {
				/*height: 510px !important;*/
			}




			.rst__node:not(:first-child) {
				top: 460px !important;
				position: relative !important;
			}

			.rst__row {
				height: 445px !important;
			}


		</style>
		<script>!function(e){function r(r){for(var n,l,a=r[0],p=r[1],f=r[2],c=0,s=[];c<a.length;c++)l=a[c],Object.prototype.hasOwnProperty.call(o,l)&&o[l]&&s.push(o[l][0]),o[l]=0;for(n in p)Object.prototype.hasOwnProperty.call(p,n)&&(e[n]=p[n]);for(i&&i(r);s.length;)s.shift()();return u.push.apply(u,f||[]),t()}function t(){for(var e,r=0;r<u.length;r++){for(var t=u[r],n=!0,a=1;a<t.length;a++){var p=t[a];0!==o[p]&&(n=!1)}n&&(u.splice(r--,1),e=l(l.s=t[0]))}return e}var n={},o={1:0},u=[];function l(r){if(n[r])return n[r].exports;var t=n[r]={i:r,l:!1,exports:{}};return e[r].call(t.exports,t,t.exports,l),t.l=!0,t.exports}l.m=e,l.c=n,l.d=function(e,r,t){l.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:t})},l.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},l.t=function(e,r){if(1&r&&(e=l(e)),8&r)return e;if(4&r&&"object"==typeof e&&e&&e.__esModule)return e;var t=Object.create(null);if(l.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:e}),2&r&&"string"!=typeof e)for(var n in e)l.d(t,n,function(r){return e[r]}.bind(null,n));return t},l.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return l.d(r,"a",r),r},l.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},l.p="/";var a=this["webpackJsonpwashu-poc"]=this["webpackJsonpwashu-poc"]||[],p=a.push.bind(a);a.push=r,a=a.slice();for(var f=0;f<a.length;f++)r(a[f]);var i=p;t()}([])</script>
		<?php
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
							$name = $filesop[0];
							$overwrite = $filesop[1];
							$description = $filesop[2];
							$json = stripslashes($filesop[3]);

							if($name != "" && $json != "" )
							{
								$dtID = findDTID($name);

								$data = array(
									'DT_Name' => $name,
									'DT_Description' => $description,
									'DT_JSON' => $json,
									'DT_CreatedOn' => date("Y-m-d"),
									'DT_CreatedBy' => get_current_user_id(),
								);

								if($dtID < 1)
								{
									$wpdb->insert('wuos_decisiontree', $data);
								}
								else if(strtolower(trim($overwrite)) == "yes")
								{
									$wpdb->update('wuos_decisiontree', $data, array('DT_ID' => $dtID));
								}	
							}						
						}
						$c++;
					}
				}
			}
		?>
		<div class="container mediumMarginTop mediumMarginBottom">
			<div class="row">
				<div class="col-sm-12">
					<h1>Decision Tree Builder</h1>
				</div>				
			</div>
		</div>

		<div class="container">
			<div id="backendApp" style="margin-left: 100px;" data-url="<?php echo site_url(); ?>"></div>		
		</div>

		<div class="container margin-top-25">
			<div class="row">
				<div class="col-sm-12">
					<h2>Export All Trees</h2>
				</div>
			</div>
		</div>
		<div class="container margin-top-25">
			<div class="row">
				<div class="col-sm-12" style="margin-left:100px;">
					<form class="form-horizontal" action="" method="post" name="upload_excel"   
                      enctype="multipart/form-data">
						<div class="form-group">
									<div class="col-md-4 col-md-offset-4">
										<input type="submit" name="Export" class="btn btn-success" style="margin-bottom:20px;" value="Export data as CSV"/>
									</div>
						</div>                    
            		</form>
					<form class="provider-upload-form backend-upload-form" method="post" enctype="multipart/form-data">
						<div class="file-upload">
							<div class="file-select">
								<div class="file-select-button" id="fileName">Import Decision Trees</div>
								<div class="file-select-name" id="noFile">No file chosen...</div> 
								<input type="file" name="chooseFile" id="chooseFile" required>
						
							</div>
						</div>	
						<div class="form-group" style="margin-top: 10px;">
							<input type="submit" name="upload" class="btn btn-primary">
						</div>
					</form>
				</div>
			</div>
		</div>

		
		
		<script src="<?php echo plugins_url('react-backend/build/static/js/2.chunk.js', dirname(__FILE__)); ?>"></script>
		<script src="<?php echo plugins_url('react-backend/build/static/js/main.chunk.js', dirname(__FILE__)); ?>"></script>
		<?php
		/**
		 * This function takes the decision tree form data after the Ajax call and stores it within the WordPress database
		 */

		add_action( 'wp_ajax_nopriv_add_new_decision_tree', 'add_new_decision_tree' );
		add_action( 'wp_ajax_add_new_decision_tree', 'add_new_decision_tree' );
		function add_new_decision_tree(){
			global $wpdb;
			$code = sanitize_text_field($_POST["code"]);
			$name = sanitize_text_field($_POST["name"]);

			/// Query to fetch decision tree data from database table and storing in $results
			$rowExists = $wpdb->get_var("SELECT COUNT(AD_Code) FROM wuos_decisiontree WHERE AD_Code = '$code'"); 

			if($rowExists > 0){
				$sqlInsert = "INSERT INTO wuos_replaceacademicdepartment (AD_Code, AD_Name) 
				VALUES ('$code', '$name');";
				$wpdb->query($sqlInsert);
			}
			else{
				$sqlInsert = "INSERT INTO wuos_decisiontree (AD_Code, AD_Name) 
				VALUES ('$code', '$name');";
				$wpdb->query($sqlInsert);
			}
		wp_die();

		}



		/**
		 * This function takes the decision tree update form data after the Ajax call and stores it within the WordPress database
		 */

		add_action( 'wp_ajax_nopriv_edit_decision_tree', 'edit_decision_tree' );
		add_action( 'wp_ajax_edit_decision_tree', 'edit_decision_tree' );
		function edit_decision_tree(){
			global $wpdb;
			$code = sanitize_text_field($_POST["code"]);
			$name = sanitize_text_field($_POST["name"]);

			/// Query to fetch decision tree data from database table and storing in $results
			$rowExists = $wpdb->get_var("SELECT COUNT(AD_Code) FROM wuos_decisiontree WHERE AD_ID = $code"); 

			if($rowExists > 0){
				$sqlUpdate = "UPDATE wuos_decisiontree 
				SET   AD_Name = '$name'
				WHERE AD_ID = $code";
				$wpdb->query($sqlUpdate);
			}
		wp_die();

		}

		/**
		 * This function deletes existing decision tree data
		 */

		add_action( 'wp_ajax_nopriv_delete_decision_tree', 'delete_decision_tree' );
		add_action( 'wp_ajax_delete_decision_tree', 'delete_decision_tree' );
		function delete_decision_tree(){
			global $wpdb;
			$code = sanitize_text_field($_POST["code"]);
			$name = sanitize_text_field($_POST["name"]);


			/// Query to fetch decision tree data from database table and storing in $results
			$rowExists = $wpdb->get_var("SELECT COUNT(AD_Code) FROM wuos_decisiontree WHERE AD_Code = '$code'"); 

			if($rowExists > 0){
				$sqlDelete = "DELETE from wuos_decisiontree 
				WHERE AD_Code = '$code'";
				$wpdb->query($sqlDelete);
			}

		wp_die();

		}


		/**
		 * This function accepts updated decision tree data from import
		 */

		add_action( 'wp_ajax_nopriv_accept_updated_decision_tree', 'accept_updated_decision_tree' );
		add_action( 'wp_ajax_accept_updated_decision_tree', 'accept_updated_decision_tree' );
		function accept_updated_decision_tree(){
			global $wpdb;
			$code = sanitize_text_field($_POST["code"]);

			/// Query to fetch decision tree data from database table and storing in $results
			$updatedRow = $wpdb->get_results("SELECT * FROM wuos_replaceacademicdepartment WHERE AD_Code = '$code'"); 
			$updatedName = $updatedRow[0]->AD_Name;
			$sqlUpdate = "UPDATE wuos_decisiontree 
			SET   AD_Name = '$updatedName'
			WHERE AD_Code = '$code'";
			$result = $wpdb->query($sqlUpdate);
			$wpdb->query("DELETE from wuos_replaceacademicdepartment WHERE AD_Code = '$code'");

			wp_die();

		}

		/**
		 * This function deletes updated decision tree data from import
		 */

		add_action( 'wp_ajax_nopriv_delete_updated_decision_tree', 'delete_updated_decision_tree' );
		add_action( 'wp_ajax_delete_updated_decision_tree', 'delete_updated_decision_tree' );
		function delete_updated_decision_tree(){
			global $wpdb;
			$code = sanitize_text_field($_POST["code"]);
			$wpdb->query("DELETE from wuos_replaceacademicdepartment WHERE AD_Code = '$code'");

			wp_die();

		}
	}