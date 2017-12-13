<?php
/**
* Plugin Name: WooCommerce added to cart popup (Ajax) 
* Plugin URI: http://xootix.com
* Author: XootiX
* Version: 1.3
* Text Domain: added-to-cart-popup-woocommerce
* Domain Path: /languages
* Author URI: http://xootix.com
* Description: WooCommerce add to cart popup displays popup when item is added to cart without refreshing page.
**/

//Exit if accessed directly
if(!defined('ABSPATH')){
	return; 	
}

$xoo_cp_version = 1.3;

define("XOO_CP_PATH", plugin_dir_path(__FILE__));
define("XOO_CP_URL", plugins_url('',__FILE__));
define("XOO_CP_VERSION",1.3);


//Admin Settings
include_once XOO_CP_PATH.'/admin/xoo-cp-admin.php';

//Init plugin
function xoo_cp_rock_the_world(){
	global $xoo_cp_gl_atcem_value;
	
	//If mobile
	if(!$xoo_cp_gl_atcem_value){
		if(wp_is_mobile()){
			return;
		}
	}
	require_once XOO_CP_PATH.'/includes/class-xoo-cp.php';
	//Start the plugin
	Xoo_CP::get_instance();
}
add_action('plugins_loaded','xoo_cp_rock_the_world');
