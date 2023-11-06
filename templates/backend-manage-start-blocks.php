<?php
	ob_start();
	ob_implicit_flush();

	if(isset($_GET['SB_ID']))
	{
		header_remove(); 

		header('Content-Type: text/csv');  
		header('Content-Disposition: attachment; filename="startblockdata.csv"'); 
		global $wpdb;

		$header_row = array(
			'SB_Name',
			'Overwrite',
			'SB_JSON'
		);

		$data_rows = array();

		$paramID = $_GET['SB_ID'];
		$results = $wpdb->get_results("SELECT * FROM wuos_startblock WHERE SB_ID = $paramID");
		foreach ( $results as $item ) 
		{
			$row = array(
				$item->SB_Name,
				"No",
				addslashes($item->SB_JSON)
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

	if(isset($_POST["Export"]) && $_GET['page'] == "manage-start-blocks")
	{
		header_remove(); 

		header('Content-Type: text/csv');  
		header('Content-Disposition: attachment; filename="startblockdata.csv"'); 

		global $wpdb;

		$header_row = array(
			'SB_Name',
			'Overwrite',
			'SB_JSON'
		);
		$data_rows = array();
		
		
		$results = $wpdb->get_results("SELECT * FROM wuos_startblock");
		foreach ( $results as $item ) 
		{
			$row = array(
				$item->SB_Name,
				"No",
				addslashes($item->SB_JSON)
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

	function findSBID($name)
	{
		global $wpdb;
		$findStartBlock = $wpdb->get_results("SELECT SB_ID FROM wuos_startblock WHERE SB_Name = '".$name."'"); 

		$returnID = 0;
		if(@count($findStartBlock) > 0)
		{
			$returnID = $findStartBlock[0]->SB_ID;
		}

		return $returnID;
	}

    /** 
     * A form for inserting new visit type information into the database and a table to display existing visit type information
     **/
    function page_manage_start_blocks()
	{
	    ?>
		<!--<link href="<?php echo plugins_url('react-backend/build/static/css/2.chunk.css', dirname(__FILE__)); ?>" rel="stylesheet">-->
		<link href="<?php echo plugins_url('react-backend/build/static/css/main.chunk.css', dirname(__FILE__)); ?>" rel="stylesheet">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
		<script src="https://cdn.tiny.cloud/1/tfkw9s9lx599ui1r6dzhnpuilqc33ezccmz71hnodxejuf6d/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
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
							$json = stripslashes($filesop[2]);
	

							if($name != "" && $name != "" && $json != "" )
							{
								$sbID = findSBID($name);

								$data = array(
									'SB_Name' => $name,
									'SB_JSON' => $json
								);

								if($sbID < 1)
								{
									$wpdb->insert('wuos_startblock', $data);
								}
								else if(strtolower(trim($overwrite)) == "yes")
								{
									$wpdb->update('wuos_startblock', $data, array('SB_ID' => $sbID));
								}	
							}						
						}
						$c++;
					}
				}
			}
		?>
		<div class="plugin-body">
			<h1 style="margin-bottom:50px;">Manage Start Blocks</h1>
			<h2 style="margin-bottom:50px;">Add/Update Start Block</h2>
		<div class="container">
		<?php
			if($_GET['page'] == "manage-start-blocks")
			{ 
		?>  
				<div id="backendApp" data-url="<?php echo site_url(); ?>"></div>

		<?php
			}
		?>
		</div>
		<div class="container margin-top-25">
			<div class="row">
				<div class="col-sm-12">
					<h2>Export All Start Blocks</h2>
				</div>
			</div>
		</div>
		<div class="container margin-top-25">
			<div class="row">
				<div class="col-sm-12">
				<form class="form-horizontal" action="" method="post" name="upload_excel"   
                      enctype="multipart/form-data">
					<div class="form-group">
								<div class="col-md-4 col-md-offset-4">
									<input type="submit" name="Export" class="btn btn-success" style="margin-bottom:20px;" value="Export data as CSV"/>
								</div>
					</div>                    
				</form>
				<form class="provider-upload-form backend-upload-form" method="post" enctype="multipart/form-data" style="margin-bottom:30px;">
					<div class="file-upload">
						<div class="file-select">
							<div class="file-select-button" id="fileName">Import Start Blocks</div>
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
	}




