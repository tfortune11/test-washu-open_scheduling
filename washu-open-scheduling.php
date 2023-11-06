<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              
 * @since             2.2.0
 * @package           Washu_Open_Scheduling
 *
 * @wordpress-plugin
 * Plugin Name:       WashU Open Scheduling
 * Plugin URI:        
 * Description:       WashU Open Scheduling Plugin. Decision Tree and Start Block Builder. 
 * Version:           2.2.0
 * Author:            WashU Development Team
 * Author URI:        
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       washu-open-scheduling
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 2.2.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WASHU_OPEN_SCHEDULING_VERSION', '2.2.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-washu-open-scheduling-activator.php
 */
function activate_washu_open_scheduling() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-washu-open-scheduling-activator.php';

	Washu_Open_Scheduling_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-washu-open-scheduling-deactivator.php
 */
function deactivate_washu_open_scheduling() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-washu-open-scheduling-deactivator.php';
	Washu_Open_Scheduling_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_washu_open_scheduling' );
register_deactivation_hook( __FILE__, 'deactivate_washu_open_scheduling' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-washu-open-scheduling.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_washu_open_scheduling() {

	$plugin = new Washu_Open_Scheduling();
	$plugin->run();

}
run_washu_open_scheduling();



add_action('admin_menu', 'open_scheduling_admin_setup_menu');  
function open_scheduling_admin_setup_menu()
{    
	//Main Plugin Page - Prefix: main_
	$main_page_title = 'WashU Opening Scheduling Page';   
	$main_menu_title = 'WashU Open Scheduling';   
	$main_capability = 'manage_options';   
	$main_menu_slug  = 'decision-tree-builder';   
	$main_function   = 'page_decision_tree_builder';   
	$main_icon_url   = 'dashicons-media-code';   
	$main_position   = 4;    
	add_menu_page($main_page_title, $main_menu_title, $main_capability, $main_menu_slug, $main_function, $main_icon_url, $main_position); 

	//Decision Tree Page - Prefix: dtb_
	$dtb_page_title = "Decision Tree Builder";
	$dtb_menu_title = "Decision Tree Builder";
	$dtb_capability = 0;
	$dtb_menu_slug = "decision-tree-builder";
	$dtb_function = "page_decision_tree_builder";
	add_submenu_page($main_menu_slug, $dtb_page_title, $dtb_menu_title, $dtb_capability, $dtb_menu_slug, $dtb_function);

	//Manage Location Page - Prefix: sb_
	$sb_page_title = "Manage Start Blocks";
	$sb_menu_title = "Manage Start Blocks";
	$sb_capability = 0;
	$sb_menu_slug = "manage-start-blocks";
	$sb_function = "page_manage_start_blocks";
	add_submenu_page($main_menu_slug, $sb_page_title, $sb_menu_title, $sb_capability, $sb_menu_slug, $sb_function);

	//Manage Location Page - Prefix: ad_
	$ad_page_title = "Manage Academic Departments";
	$ad_menu_title = "Manage Academic Departments";
	$ad_capability = 0;
	$ad_menu_slug = "manage-academic-departments";
	$ad_function = "page_manage_academic_departments";
	add_submenu_page($main_menu_slug, $ad_page_title, $ad_menu_title, $ad_capability, $ad_menu_slug, $ad_function);

	//Manage Provider Page - Prefix: md_
	$md_page_title = "Manage Provider";
	$md_menu_title = "Manage Provider";
	$md_capability = 0;
	$md_menu_slug = "manage-provider";
	$md_function = "page_manage_provider";
	add_submenu_page($main_menu_slug, $md_page_title, $md_menu_title, $md_capability, $md_menu_slug, $md_function);

	//Manage Location Page - Prefix: ll_
	$ll_page_title = "Manage Epic Departments";
	$ll_menu_title = "Manage Epic Departments";
	$ll_capability = 0; 
	$ll_menu_slug = "manage-epic-departments";
	$ll_function = "page_manage_epic_departments";
	add_submenu_page($main_menu_slug, $ll_page_title, $ll_menu_title, $ll_capability, $ll_menu_slug, $ll_function);
		
	//Manage Location Page - Prefix: sp_ //Removing this one
	/*$sp_page_title = "Manage Specialty";
	$sp_menu_title = "Manage Specialty";
	$sp_capability = 0;
	$sp_menu_slug = "manage-specialty";
	$sp_function = "page_manage_specialty";
	add_submenu_page($main_menu_slug, $sp_page_title, $sp_menu_title, $sp_capability, $sp_menu_slug, $sp_function);*/

	

	//Manage Vist Type Page - Prefix: vt_
	$vt_page_title = "Manage Visit Type";
	$vt_menu_title = "Manage Visit Type";
	$vt_capability = 0;
	$vt_menu_slug = "manage-visit-type";
	$vt_function = "page_manage_visit_type";
	add_submenu_page($main_menu_slug, $vt_page_title, $vt_menu_title, $vt_capability, $vt_menu_slug, $vt_function);

	//Manage Connection Academic Department and Provider List - Prefix: ap_
	$ap_page_title = "Manage Connection Academic To Provider";
	$ap_menu_title = "Manage Connection Academic To Provider";
	$ap_capability = 0;
	$ap_menu_slug = "manage-connection-academic-provider";
	$ap_function = "page_manage_provider_to_academic";
	add_submenu_page($main_menu_slug, $ap_page_title, $ap_menu_title, $ap_capability, $ap_menu_slug, $ap_function);


	//Manage Connection Academic Department and Visit Type - Prefix: av_
	$av_page_title = "Manage Connection Academic To Visit";
	$av_menu_title = "Manage Connection Academic To Visit";
	$av_capability = 0;
	$av_menu_slug = "manage-connection-academic-visit";
	$av_function = "page_manage_visit_type_to_academic";
	add_submenu_page($main_menu_slug, $av_page_title, $av_menu_title, $av_capability, $av_menu_slug, $av_function);

	//Manage Connection Academic Department and Epic Department - Prefix: ae_
	$ae_page_title = "Manage Connection Academic To Epic";
	$ae_menu_title = "Manage Connection Academic To Epic";
	$ae_capability = 0;
	$ae_menu_slug = "manage-connection-academic-epic";
	$ae_function = "page_manage_epic_department_to_academic";
	add_submenu_page($main_menu_slug, $ae_page_title, $ae_menu_title, $ae_capability, $ae_menu_slug, $ae_function);

	//Backend settings - Prefix: st_
	$st_page_title = "Settings";
	$st_menu_title = "Settings";
	$st_capability = 0;
	$st_menu_slug = "settings";
	$st_function = "page_backend_settings";
	add_submenu_page($main_menu_slug, $st_page_title, $st_menu_title, $st_capability, $st_menu_slug, $st_function);
} 



/**
 * Register and enqueue a custom stylesheet in the WordPress admin.
 */
function wpdocs_enqueue_custom_admin_style() {
	wp_register_style( 'custom_wp_admin_css', plugin_dir_url( __FILE__ ) . 'style.css', false, '1.0.0' );
	wp_enqueue_style( 'custom_wp_admin_css' );
}

add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_style' ); 


// Register scripts and stylesheets for react app
function washu_react_init() {
	global $wpdb;

	$sql = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Epic JS Url'"; 
	$sqlValue = $wpdb->get_results($sql);
	$loadEpicJSUrl = "";
	if($sqlValue)
	{
		$loadEpicJSUrl = $sqlValue[0]->PS_SettingValue;
	}

	$sql = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Epic CSS Url'"; 
	$sqlValue = $wpdb->get_results($sql);
	$loadEpicCSSUrl = "";
	if($sqlValue)
	{
		$loadEpicCSSUrl = $sqlValue[0]->PS_SettingValue;
	}

	wp_register_script("washu_react_js", plugins_url("react-frontend/build/static/js/main.chunk.js", __FILE__), array(), null, true);
    wp_register_script("washu_react_js_2", plugins_url("react-frontend/build/static/js/2.chunk.js", __FILE__), array(), null, true);
    wp_register_style("washu_react_css", plugins_url("react-frontend/build/static/css/main.chunk.css", __FILE__), array(), null, "all");

	wp_register_script("washu_callReact_js", plugins_url("includes/callReact.js", __FILE__), array(), null, true);

	wp_register_style("washu_epic_css", $loadEpicCSSUrl, array(), null, "all");
	wp_register_script("washu_epic_js", $loadEpicJSUrl, array(), null, true);
}
add_action( 'init', 'washu_react_init' );

// Function for the short code that calls the React app
function washu_react($atts) 
{	
    wp_enqueue_script("washu_react_js", '2.0', true);
	wp_enqueue_script("washu_react_js_2", '3.0', true);
    wp_enqueue_style("washu_react_css");

	wp_enqueue_script("washu_callReact_js", '1.0', true);
	
	wp_enqueue_style("washu_epic_css");
	wp_enqueue_script("washu_epic_js", '2.0', true);
	
	ob_start();
	$treeId = $atts['id'];
	?>
	
	<!--<script type="text/javascript">!function(e){function r(r){for(var n,l,a=r[0],p=r[1],f=r[2],c=0,s=[];c<a.length;c++)l=a[c],Object.prototype.hasOwnProperty.call(o,l)&&o[l]&&s.push(o[l][0]),o[l]=0;for(n in p)Object.prototype.hasOwnProperty.call(p,n)&&(e[n]=p[n]);for(i&&i(r);s.length;)s.shift()();return u.push.apply(u,f||[]),t()}function t(){for(var e,r=0;r<u.length;r++){for(var t=u[r],n=!0,a=1;a<t.length;a++){var p=t[a];0!==o[p]&&(n=!1)}n&&(u.splice(r--,1),e=l(l.s=t[0]))}return e}var n={},o={1:0},u=[];function l(r){if(n[r])return n[r].exports;var t=n[r]={i:r,l:!1,exports:{}};return e[r].call(t.exports,t,t.exports,l),t.l=!0,t.exports}l.m=e,l.c=n,l.d=function(e,r,t){l.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:t})},l.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},l.t=function(e,r){if(1&r&&(e=l(e)),8&r)return e;if(4&r&&"object"==typeof e&&e&&e.__esModule)return e;var t=Object.create(null);if(l.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:e}),2&r&&"string"!=typeof e)for(var n in e)l.d(t,n,function(r){return e[r]}.bind(null,n));return t},l.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return l.d(r,"a",r),r},l.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},l.p="/";var a=this["webpackJsonpwashu-poc"]=this["webpackJsonpwashu-poc"]||[],p=a.push.bind(a);a.push=r,a=a.slice();for(var f=0;f<a.length;f++)r(a[f]);var i=p;t()}([])</script>-->
    <div id='frontendApp' data-id="<?php echo $treeId ?>" data-url="<?php echo site_url(); ?>"><img style="max-height: 200px; max-width: 200px;" src="<?php echo plugins_url("images/loading.gif", __FILE__); ?>" /></div>
	<?php
	return ob_get_clean();
}
add_shortcode('WashUOS', 'washu_react');

function move_to_header() {
	global $wpdb;

	$sqlJSEpic = "SELECT PS_SettingValue FROM  wuos_pluginsettings WHERE PS_SettingName = 'Epic JS Url'";
	$epicJSData = $wpdb->get_results($sqlJSEpic);

	?>
	<script src="<?php echo $epicJSData[0]->PS_SettingValue; ?>" type="text/javascript"></script> 
	<?php 

}
add_action('admin_head', 'move_to_header');

include 'templates/backend-main-open-scheduling.php';
include 'templates/backend-manage-start-blocks.php';
include 'templates/backend-decision-tree-builder.php';
include 'templates/backend-manage-provider.php';
include 'templates/backend-manage-epic-department.php';
include 'templates/backend-manage-academic-department.php';
include 'templates/backend-manage-visit-type.php';
include 'templates/backend-database-table.php';
include 'templates/backend-crosstable-pl-ad.php';
include 'templates/backend-crosstable-ed-ad.php';
include 'templates/backend-crosstable-vt-ad.php';
include 'templates/backend-api-controller.php';
include 'templates/backend-settings.php';

register_activation_hook(__FILE__, 'create_epic_department_database_table');
register_activation_hook(__FILE__, 'create_replace_epic_department_database_table');
register_activation_hook(__FILE__, 'create_academic_department_database_table');
register_activation_hook(__FILE__, 'create_replace_academic_department_database_table');
register_activation_hook(__FILE__, 'create_visit_type_list_database_table');
register_activation_hook(__FILE__, 'create_replace_visit_type_list_database_table');
//register_activation_hook(__FILE__, 'create_plugin_database_table'); //Function no longer exists
register_activation_hook(__FILE__, 'create_audit_database_table');
//register_activation_hook(__FILE__, 'create_question_answer_store_database_table'); //Function no longer exists
register_activation_hook(__FILE__, 'create_decision_tree_database_table');
register_activation_hook(__FILE__, 'create_provider_list_database_table');
register_activation_hook(__FILE__, 'create_replace_provider_list_database_table');
register_activation_hook(__FILE__, 'create_crosstable_pl_ad_database_table');
register_activation_hook(__FILE__, 'create_crosstable_ed_ad_database_table');
register_activation_hook(__FILE__, 'create_crosstable_vt_ad_database_table');
register_activation_hook(__FILE__, 'create_start_block_database_table');
register_activation_hook(__FILE__, 'create_settings_database_table');
register_activation_hook(__FILE__, 'create_user_journey_database_table');