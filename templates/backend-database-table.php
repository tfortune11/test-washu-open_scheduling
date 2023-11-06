<?php
/** 
 * This page creates all of the database tables needed for this plugin
 */

function create_provider_list_database_table()
{
	global $wpdb;

	$sql = "CREATE TABLE wuos_providerlist ( 
		PL_ID INT NOT NULL AUTO_INCREMENT,
		PL_NPI VARCHAR(100) NOT NULL,
		PL_ProviderName VARCHAR(200) NOT NULL,
		PL_FirstName VARCHAR(100) NOT NULL,
		PL_LastName VARCHAR(100) NOT NULL, 
		PL_PostID VARCHAR(100) NOT NULL,
		PRIMARY KEY (PL_ID)
	) ENGINE = InnoDB;";

	if ($wpdb->get_var("SHOW TABLES LIKE 'wuos_providerlist'") !='wuos_providerlist') 
	{
		$charset_collate = $wpdb->get_charset_collate();

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql);
	}
	else
	{
		$existingTableSql = "SHOW CREATE TABLE wuos_providerlist";
		$existingTable = $wpdb->get_var($existingTableSql);

		if($existingTable != "")
		{
			$row1 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_providerlist' AND column_name = 'PL_NPI'");

			if(empty($row1))
			{
				$wpdb->query("ALTER TABLE wuos_providerlist ADD PL_NPI VARCHAR(100) NOT NULL");
			}

			$row2 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_providerlist' AND column_name = 'PL_ProviderName'");

			if(empty($row2))
			{
				$wpdb->query("ALTER TABLE wuos_providerlist ADD PL_ProviderName VARCHAR(200) NOT NULL");
			}

			$row3 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_providerlist' AND column_name = 'PL_FirstName'");

			if(empty($row3))
			{
				$wpdb->query("ALTER TABLE wuos_providerlist ADD PL_FirstName VARCHAR(100) NOT NULL");
			}
			
			$row4 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_providerlist' AND column_name = 'PL_LastName'");

			if(empty($row4))
			{
				$wpdb->query("ALTER TABLE wuos_providerlist ADD PL_LastName VARCHAR(100) NOT NULL");
			}

			$row5 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_providerlist' AND column_name = 'PL_PostID'");

			if(empty($row5))
			{
				$wpdb->query("ALTER TABLE wuos_providerlist ADD PL_PostID VARCHAR(100) NOT NULL");
			}
		}
	}
}

function create_replace_provider_list_database_table()
{
	global $wpdb;
 
	if ($wpdb->get_var("SHOW TABLES LIKE 'wuos_replaceproviderlist'") !='wuos_replaceproviderlist') 
	{
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE wuos_replaceproviderlist ( 
            PL_ID INT NOT NULL AUTO_INCREMENT,
            PL_NPI VARCHAR(100) NOT NULL,
            PL_ProviderName VARCHAR(200) NOT NULL,
            PL_FirstName VARCHAR(100) NOT NULL,
            PL_LastName VARCHAR(100) NOT NULL,
            PL_PostID VARCHAR(100) NOT NULL,
            PRIMARY KEY (PL_ID)
        ) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql);
	}
	else
	{
		$existingTableSql = "SHOW CREATE TABLE wuos_replaceproviderlist";
		$existingTable = $wpdb->get_var($existingTableSql);

		if($existingTable != ""){

			$row1 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_replaceproviderlist' AND column_name = 'PL_NPI'");

			if(empty($row1))
			{
				$wpdb->query("ALTER TABLE wuos_replaceproviderlist ADD PL_NPI VARCHAR(100) NOT NULL");
			}

			$row2 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_replaceproviderlist' AND column_name = 'PL_ProviderName'");

			if(empty($row2))
			{
				$wpdb->query("ALTER TABLE wuos_replaceproviderlist ADD PL_ProviderName VARCHAR(200) NOT NULL");
			}

			$row3 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_replaceproviderlist' AND column_name = 'PL_FirstName'");

			if(empty($row3))
			{
				$wpdb->query("ALTER TABLE wuos_replaceproviderlist ADD PL_FirstName VARCHAR(100) NOT NULL");
			}
			
			$row4 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_replaceproviderlist' AND column_name = 'PL_LastName'");

			if(empty($row4))
			{
				$wpdb->query("ALTER TABLE wuos_replaceproviderlist ADD PL_LastName VARCHAR(100) NOT NULL");
			}

			$row5 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_replaceproviderlist' AND column_name = 'PL_PostID'");

			if(empty($row5))
			{
				$wpdb->query("ALTER TABLE wuos_replaceproviderlist ADD PL_PostID VARCHAR(100) NOT NULL");
			}
		}
	}
}

function create_decision_tree_database_table()
{
	global $wpdb;
 
	if ($wpdb->get_var("SHOW TABLES LIKE 'wuos_decisiontree'") !='wuos_decisiontree') 
	{
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE wuos_decisiontree( 
            DT_ID INT NOT NULL AUTO_INCREMENT,
            DT_Name VARCHAR(200) NOT NULL,
            DT_Description VARCHAR(65535) NOT NULL,
            DT_JSON LONGTEXT NOT NULL,
            DT_CreatedOn datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            DT_CreatedBy INT NOT NULL,
            DT_LastModifiedOn datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            DT_LastModifiedBy INT NOT NULL,
            DT_DeletedOn datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            DT_DeletedBy INT NOT NULL,
            PRIMARY KEY (DT_ID)
        ) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql);
	}
	else
	{
		$existingTableSql = "SHOW CREATE TABLE wuos_decisiontree";
		$existingTable = $wpdb->get_var($existingTableSql);

		if($existingTable != "")
		{
			$row1 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_decisiontree' AND column_name = 'DT_Name'");

			if(empty($row1))
			{
				$wpdb->query("ALTER TABLE wuos_decisiontree ADD DT_Name VARCHAR(200) NOT NULL");
			}

			$row2 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_decisiontree' AND column_name = 'DT_Description'");

			if(empty($row2))
			{
				$wpdb->query("ALTER TABLE wuos_decisiontree ADD  DT_Description VARCHAR(65535) NOT NULL");
			}

			$row3 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_decisiontree' AND column_name = 'DT_JSON'");

			if(empty($row3))
			{
				$wpdb->query("ALTER TABLE wuos_decisiontree ADD DT_JSON LONGTEXT NOT NULL");
			}

			$row4 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_decisiontree' AND column_name = 'DT_CreatedOn'");

			if(empty($row4))
			{
				$wpdb->query("ALTER TABLE wuos_decisiontree ADD DT_CreatedOn DATETIME NULL");
			}

			$row5 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_decisiontree' AND column_name = 'DT_CreatedBy'");

			if(empty($row5))
			{
				$wpdb->query("ALTER TABLE wuos_decisiontree ADD DT_CreatedBy INT NULL");
			}
			
			$row6 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_decisiontree' AND column_name = 'DT_LastModifiedOn'");

			if(empty($row6))
			{
				$wpdb->query("ALTER TABLE wuos_decisiontree ADD DT_LastModifiedOn DATETIME NULL");
			}

			$row7 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_decisiontree' AND column_name = 'DT_LastModifiedBy'");

			if(empty($row7))
			{
				$wpdb->query("ALTER TABLE wuos_decisiontree ADD DT_LastModifiedBy INT NULL");
			}

			$row8 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_decisiontree' AND column_name = 'DT_DeletedOn'");

			if(empty($row8))
			{
				$wpdb->query("ALTER TABLE wuos_decisiontree ADD DT_DeletedOn DATETIME NULL");
			}
			
			$row9 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_decisiontree' AND column_name = 'DT_DeletedBy'");

			if(empty($row9))
			{
				$wpdb->query("ALTER TABLE wuos_decisiontree ADD DT_DeletedBy INT NULL");
			}
		}
	}
}

function create_audit_database_table()
{
	global $wpdb;
 
	if ($wpdb->get_var("SHOW TABLES LIKE 'wuos_auditlog'") !='wuos_auditlog') 
	{	
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE wuos_auditlog ( 
            AL_ID INT NOT NULL AUTO_INCREMENT,
            AL_QAS_ID INT NOT NULL,
            AL_ActionType VARCHAR(100) NOT NULL,
            AL_ActionItem VARCHAR(200) NOT NULL,
            AL_ActionDate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            AL_ActionBy INT NOT NULL,
            PRIMARY KEY (AL_ID)
        ) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql);
	}
	else
	{
		$existingTableSql = "SHOW CREATE TABLE wuos_auditlog";
		$existingTable = $wpdb->get_var($existingTableSql);

		if($existingTable != "")
		{
			$row1 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_auditlog' AND column_name = 'AL_QAS_ID'");

			if(empty($row1))
			{
				$wpdb->query("ALTER TABLE wuos_auditlog ADD AL_QAS_ID INT NULL");
			}

			$row2 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_auditlog' AND column_name = 'AL_ActionType'");

			if(empty($row2))
			{
				$wpdb->query("ALTER TABLE wuos_auditlog ADD AL_ActionType VARCHAR(100) NULL");
			}

			$row3 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_auditlog' AND column_name = 'AL_ActionItem'");

			if(empty($row3))
			{
				$wpdb->query("ALTER TABLE wuos_auditlog ADD AL_ActionItem VARCHAR(200) NULL");
			}

			$row4 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_auditlog' AND column_name = 'AL_ActionDate'");

			if(empty($row4))
			{
				$wpdb->query("ALTER TABLE wuos_auditlog ADD AL_ActionDate DATETIME NULL");
			}

			$row5 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_auditlog' AND column_name = 'AL_ActionBy'");

			if(empty($row5))
			{
				$wpdb->query("ALTER TABLE wuos_auditlog ADD AL_ActionBy INT NULL");
			}
		}
	}
}

function create_user_journey_database_table()
{
	global $wpdb;
 
	if ($wpdb->get_var("SHOW TABLES LIKE 'wuos_userjourney'") !='wuos_userjourney') 
	{
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE wuos_userjourney ( 
            UJ_ID INT NOT NULL AUTO_INCREMENT,
            UJ_SessionID VARCHAR(100),
            UJ_Country VARCHAR(100) NOT NULL,
			UJ_CountryName VARCHAR(100) NOT NULL,
			UJ_City VARCHAR(100) NOT NULL,
			UJ_State VARCHAR(100) NOT NULL,
			UJ_Postal VARCHAR(100) NOT NULL,
			UJ_Latitude VARCHAR(100) NOT NULL,
			UJ_Longitude VARCHAR(100) NOT NULL,
			UJ_IPAddress VARCHAR(100) NOT NULL,
			UJ_BrowserInfo VARCHAR(200) NOT NULL,
			UJ_StartBlockID INT NULL,
			UJ_StartBlock VARCHAR(100) NULL,
			UJ_DecisionTreeID INT NULL,
			UJ_DecisionTree VARCHAR(100) NULL,
			UJ_Question VARCHAR(200) NULL,
			UJ_Answer VARCHAR(200) NULL,
			UJ_Termination VARCHAR(200) NULL,
            UJ_ActionDateTime DATETIME NULL,
            PRIMARY KEY (UJ_ID)
        ) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql);
	}
	else
	{
		$existingTableSql = "SHOW CREATE TABLE wuos_userjourney";
		$existingTable = $wpdb->get_var($existingTableSql);

		if($existingTable != "")
		{
			$row1 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_SessionID'");

			if(empty($row1))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_SessionID VARCHAR(100)");
			}

			$row2 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_Country'");

			if(empty($row2))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_Country VARCHAR(100) NOT NULL");
			}

			$row3 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_CountryName'");

			if(empty($row3))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_CountryName VARCHAR(100) NOT NULL");
			}

			$row4 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_City'");

			if(empty($row4))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_City VARCHAR(100) NOT NULL");
			}

			$row5 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_State'");

			if(empty($row5))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_State VARCHAR(100) NOT NULL");
			}

			$row6 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_Postal'");

			if(empty($row6))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_Postal VARCHAR(100) NOT NULL");
			}

			$row7 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_Latitude'");

			if(empty($row7))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_Latitude VARCHAR(100) NOT NULL");
			}

			$row8 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_Longitude'");

			if(empty($row8))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_Longitude VARCHAR(100) NOT NULL");
			}

			$row9 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_BrowserInfo'");

			if(empty($row9))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_BrowserInfo VARCHAR(200) NOT NULL");
			}

			$row11 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_StartBlockID'");

			if(empty($row11))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_StartBlockID INT NULL");
			}

			$row12 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_StartBlock'");

			if(empty($row12))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_StartBlock VARCHAR(100) NULL");
			}

			$row13 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_DecisionTreeID'");

			if(empty($row13))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_DecisionTreeID INT NULL");
			}

			$row14 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_DecisionTree'");

			if(empty($row14))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_DecisionTree VARCHAR(200) NULL");
			}

			$row15 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_Question'");

			if(empty($row15))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_Question VARCHAR(200) NULL");
			}

			$row16 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_Answer'");

			if(empty($row16))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_Answer VARCHAR(200) NULL");
			}

			$row17 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_Termination'");

			if(empty($row17))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_Termination VARCHAR(200) NULL");
			}

			$row18 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_userjourney' AND column_name = 'UJ_ActionDateTime'");

			if(empty($row18))
			{
				$wpdb->query("ALTER TABLE wuos_userjourney ADD UJ_ActionDateTime DATETIME NULL");
			}
		}
	}
}

function create_epic_department_database_table()
{
	global $wpdb;
 
	if ($wpdb->get_var("SHOW TABLES LIKE 'wuos_epicdepartment'") !='wuos_epicdepartment') 
	{
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE wuos_epicdepartment( 
            ED_ID INT NOT NULL AUTO_INCREMENT,
            ED_Code VARCHAR(250) NOT NULL,
            ED_Name VARCHAR(250) NOT NULL,
            PRIMARY KEY (ED_ID)
        ) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql);
	}
	else
	{
		$existingTableSql = "SHOW CREATE TABLE wuos_epicdepartment";
		$existingTable = $wpdb->get_var($existingTableSql);

		if($existingTable != "")
		{
			$row1 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_epicdepartment' AND column_name = 'ED_Code'");

			if(empty($row1))
			{
				$wpdb->query("ALTER TABLE wuos_epicdepartment ADD ED_Code VARCHAR(250) NOT NULL");
			}

			$row2 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_epicdepartment' AND column_name = 'ED_Name'");

			if(empty($row2))
			{
				$wpdb->query("ALTER TABLE wuos_epicdepartment ADD ED_Name VARCHAR(250) NOT NULL");
			}
		}
	}
}

function create_replace_epic_department_database_table()
{
	global $wpdb;

	if ($wpdb->get_var("SHOW TABLES LIKE 'wuos_replaceepicdepartment'") !='wuos_replaceepicdepartment') 
	{
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE wuos_replaceepicdepartment( 
            ED_ID INT NOT NULL AUTO_INCREMENT,
            ED_Code VARCHAR(250) NOT NULL,
            ED_Name VARCHAR(250) NOT NULL,
            PRIMARY KEY (ED_ID)
        ) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql);
	}
	else
	{
		$existingTableSql = "SHOW CREATE TABLE wuos_replaceepicdepartment";
		$existingTable = $wpdb->get_var($existingTableSql);

		if($existingTable != "")
		{
			$row1 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_replaceepicdepartment' AND column_name = 'ED_Code'");

			if(empty($row1))
			{
				$wpdb->query("ALTER TABLE wuos_replaceepicdepartment ADD ED_Code VARCHAR(250) NOT NULL");
			}

			$row2 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_replaceepicdepartment' AND column_name = 'ED_Name'");

			if(empty($row2))
			{
				$wpdb->query("ALTER TABLE wuos_replaceepicdepartment ADD ED_Name VARCHAR(250) NOT NULL");
			}
		}
	}
}

function create_academic_department_database_table()
{
	global $wpdb;
 
	if ($wpdb->get_var("SHOW TABLES LIKE 'wuos_academicdepartment'") !='wuos_academicdepartment') 
	{
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE wuos_academicdepartment( 
            AD_ID INT NOT NULL AUTO_INCREMENT,
            AD_Code VARCHAR(250) NOT NULL,
            AD_Name VARCHAR(250) NOT NULL,
            PRIMARY KEY (AD_ID)
        ) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql);
	}
	else
	{
		$existingTableSql = "SHOW CREATE TABLE wuos_academicdepartment";
		$existingTable = $wpdb->get_var($existingTableSql);

		if($existingTable != "")
		{

			$row1 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_academicdepartment' AND column_name = 'AD_Code'");

			if(empty($row1))
			{
				$wpdb->query("ALTER TABLE wuos_academicdepartment ADD AD_Code VARCHAR(250) NOT NULL");
			}

			$row2 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_academicdepartment' AND column_name = 'AD_Name'");

			if(empty($row2))
			{
				$wpdb->query("ALTER TABLE wuos_academicdepartment ADD AD_Name VARCHAR(250) NOT NULL");
			}
		}
	}
}
function create_replace_academic_department_database_table()
{
	global $wpdb;
 
	if ($wpdb->get_var("SHOW TABLES LIKE 'wuos_replaceacademicdepartment'") !='wuos_replaceacademicdepartment') 
	{
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE wuos_replaceacademicdepartment( 
            AD_ID INT NOT NULL AUTO_INCREMENT,
            AD_Code VARCHAR(250) NOT NULL,
            AD_Name VARCHAR(250) NOT NULL,
            PRIMARY KEY (AD_ID)
        ) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql);
	}
	else
	{
		$existingTableSql = "SHOW CREATE TABLE wuos_replaceacademicdepartment";
		$existingTable = $wpdb->get_var($existingTableSql);

		if($existingTable != "")
		{

			$row1 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_replaceacademicdepartment' AND column_name = 'AD_Code'");

			if(empty($row1))
			{
				$wpdb->query("ALTER TABLE wuos_replaceacademicdepartment ADD AD_Code VARCHAR(250) NOT NULL");
			}

			$row2 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_replaceacademicdepartment' AND column_name = 'AD_Name'");

			if(empty($row2))
			{
				$wpdb->query("ALTER TABLE wuos_replaceacademicdepartment ADD AD_Name VARCHAR(250) NOT NULL");
			}
		}
	}
}

function create_visit_type_list_database_table()
{
	global $wpdb;
 
	if ($wpdb->get_var("SHOW TABLES LIKE 'wuos_visittypelist'") !='wuos_visittypelist') 
	{
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE wuos_visittypelist ( 
            VT_ID INT NOT NULL AUTO_INCREMENT,
            VT_VisitTypeID VARCHAR(100) NOT NULL,
            VT_Name VARCHAR(100) NOT NULL,
            PRIMARY KEY (VT_ID)
        ) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql);
	}
	else
	{
		$existingTableSql = "SHOW CREATE TABLE wuos_visittypelist ";
		$existingTable = $wpdb->get_var($existingTableSql);

		if($existingTable != ""){

			$row1 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_visittypelist ' AND column_name = 'VT_VisitTypeID'");

			if(empty($row1))
			{
				$wpdb->query("ALTER TABLE wuos_visittypelist  ADD VT_VisitTypeID VARCHAR(100) NOT NULL");
			}

			$row2 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_visittypelist ' AND column_name = 'VT_Name'");

			if(empty($row2))
			{
				$wpdb->query("ALTER TABLE wuos_visittypelist  ADD  VT_Name VARCHAR(100) NOT NULL");
			}
		}
	}
}

function create_replace_visit_type_list_database_table()
{
	global $wpdb;
 
	if ($wpdb->get_var("SHOW TABLES LIKE 'wuos_replacevisittypelist'") !='wuos_replacevisittypelist') 
	{
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE wuos_replacevisittypelist ( 
            VT_ID INT NOT NULL AUTO_INCREMENT,
            VT_VisitTypeID VARCHAR(100) NOT NULL,
            VT_Name VARCHAR(100) NOT NULL,
            PRIMARY KEY (VT_ID)
        ) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql);
	}
	else
	{
		$existingTableSql = "SHOW CREATE TABLE wuos_replacevisittypelist ";
		$existingTable = $wpdb->get_var($existingTableSql);

		if($existingTable != "")
		{
			$row1 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_replacevisittypelist ' AND column_name = 'VT_VisitTypeID'");

			if(empty($row1))
			{
				$wpdb->query("ALTER TABLE wuos_replacevisittypelist  ADD VT_VisitTypeID VARCHAR(100) NOT NULL");
			}

			$row2 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_replacevisittypelist ' AND column_name = 'VT_Name'");

			if(empty($row2))
			{
				$wpdb->query("ALTER TABLE wuos_replacevisittypelist  ADD  VT_Name VARCHAR(100) NOT NULL");
			}
		}
	}
}

function create_crosstable_pl_ad_database_table()
{
	global $wpdb;
 
	if ($wpdb->get_var("SHOW TABLES LIKE 'wuos_referenceacademicxprovider'") !='wuos_referenceacademicxprovider') 
	{
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE wuos_referenceacademicxprovider( 
            RAP_ID INT NOT NULL AUTO_INCREMENT,
            AD_ID INT NOT NULL,
            PL_ID INT NOT NULL,
            PRIMARY KEY (RAP_ID)
        ) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql);
	}
	else
	{
		$existingTableSql = "SHOW CREATE TABLE wuos_referenceacademicxprovider";
		$existingTable = $wpdb->get_var($existingTableSql);

		if($existingTable != "")
		{

			$row1 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_referenceacademicxprovider' AND column_name = 'AD_ID'");

			if(empty($row1))
			{
				$wpdb->query("ALTER TABLE wuos_referenceacademicxprovider ADD AD_ID INT NOT NULL");
			}

			$row2 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_referenceacademicxprovider' AND column_name = 'PL_ID'");

			if(empty($row2))
			{
				$wpdb->query("ALTER TABLE wuos_referenceacademicxprovider ADD PL_ID INT NOT NULL");
			}
		}
	}
}

function create_crosstable_ed_ad_database_table()
{
	global $wpdb;
 
	if ($wpdb->get_var("SHOW TABLES LIKE 'wuos_referenceacademicxepic'") !='wuos_referenceacademicxepic') 
	{
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE wuos_referenceacademicxepic( 
            RAE_ID INT NOT NULL AUTO_INCREMENT,
            AD_ID INT NOT NULL,
            ED_ID INT NOT NULL,
            PRIMARY KEY (RAE_ID)
        ) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql);
	}
	else
	{
		$existingTableSql = "SHOW CREATE TABLE wuos_referenceacademicxepic";
		$existingTable = $wpdb->get_var($existingTableSql);

		if($existingTable != "")
		{
			$row1 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_referenceacademicxepic' AND column_name = 'AD_ID'");

			if(empty($row1))
			{
				$wpdb->query("ALTER TABLE wuos_referenceacademicxepic ADD AD_ID INT NOT NULL");
			}

			$row2 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_referenceacademicxepic' AND column_name = 'ED_ID'");

			if(empty($row2))
			{
				$wpdb->query("ALTER TABLE wuos_referenceacademicxepic ADD ED_ID INT NOT NULL");
			}
		}
	}
}


function create_crosstable_vt_ad_database_table()
{
	global $wpdb;
 
	if ($wpdb->get_var("SHOW TABLES LIKE 'wuos_referenceacademicxvisit'") !='wuos_referenceacademicxvisit') 
	{
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE wuos_referenceacademicxvisit( 
            RAV_ID INT NOT NULL AUTO_INCREMENT,
            AD_ID INT NOT NULL,
            VT_ID INT NOT NULL,
            PRIMARY KEY (RAV_ID)
        ) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql);
	}
	else
	{
		$existingTableSql = "SHOW CREATE TABLE wuos_referenceacademicxvisit";
		$existingTable = $wpdb->get_var($existingTableSql);

		if($existingTable != "")
		{
			$row1 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_referenceacademicxvisit' AND column_name = 'AD_ID'");

			if(empty($row1))
			{
				$wpdb->query("ALTER TABLE wuos_referenceacademicxvisit ADD AD_ID INT NOT NULL");
			}

			$row2 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_referenceacademicxvisit' AND column_name = 'VT_ID'");

			if(empty($row2))
			{
				$wpdb->query("ALTER TABLE wuos_referenceacademicxvisit ADD VT_ID INT NOT NULL");
			}
		}
	}
}

function create_start_block_database_table()
{
	global $wpdb;
 
	if ($wpdb->get_var("SHOW TABLES LIKE 'wuos_startblock'") !='wuos_startblock') 
	{
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE wuos_startblock ( 
            SB_ID INT NOT NULL AUTO_INCREMENT,
            SB_Name VARCHAR(100) NULL,
			SB_JSON VARCHAR(65535) NULL,
			SB_TreeID INT NULL,
            PRIMARY KEY (SB_ID)
        ) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql);
	}
	else
	{
		$existingTableSql = "SHOW CREATE TABLE wuos_startblock";
		$existingTable = $wpdb->get_var($existingTableSql);

		if($existingTable != "")
		{
			$row1 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_startblock' AND column_name = 'SB_Name'");

			if(empty($row1))
			{
				$wpdb->query("ALTER TABLE wuos_startblock ADD SB_Name VARCHAR(100) NULL");
			}

			$row2 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_startblock' AND column_name = 'SB_JSON'");

			if(empty($row2))
			{
				$wpdb->query("ALTER TABLE wuos_startblock ADD SB_JSON VARCHAR(65535) NULL");
			}

			$row3 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_startblock' AND column_name = 'SB_TreeID'");

			if(empty($row3))
			{
				$wpdb->query("ALTER TABLE wuos_startblock ADD SB_TreeID INT NULL");
			}
		}
	}
}


function create_settings_database_table()
{
	global $wpdb;
 
	if ($wpdb->get_var("SHOW TABLES LIKE 'wuos_pluginsettings'") !='wuos_pluginsettings') 
	{
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE wuos_pluginsettings ( 
            PS_ID INT NOT NULL AUTO_INCREMENT,
            PS_SettingName VARCHAR(100) NULL,
			PS_SettingValue  VARCHAR(65535) NULL,
            PRIMARY KEY (PS_ID)
        ) ENGINE = InnoDB;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql);
	}
	else
	{
		$existingTableSql = "SHOW CREATE TABLE wuos_pluginsettings";
		$existingTable = $wpdb->get_var($existingTableSql);

		if($existingTable != "")
		{

			$row1 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_pluginsettings' AND column_name = 'PS_SettingName'");

			if(empty($row1))
			{
				$wpdb->query("ALTER TABLE wuos_pluginsettings ADD PS_SettingName VARCHAR(100)NULL");
			}

			$row2 = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = 'wuos_pluginsettings' AND column_name = 'PS_SettingValue'");

			if(empty($row2))
			{
				$wpdb->query("ALTER TABLE wuos_pluginsettings ADD PS_SettingValue VARCHAR(65535) NULL");
			}
		}
	}
}

