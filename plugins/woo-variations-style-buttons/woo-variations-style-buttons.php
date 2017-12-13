<?php
/*
Plugin Name: Woo Variations Style Buttons
Plugin URI: https://edatastyle.com/product/woo-variations-style-buttons/
Description: Variations Link, Color, Images, Radio, Select Buttons for WooCommerce. product variations using buttons instead of dropdowns. 
Version: 1.4 
Author: eDataStyle
Author URI: http://edatastyle.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

defined( 'WC_VAR_PATH' )    or  define( 'WC_VAR_PATH',    plugin_dir_path( __FILE__ ) );
defined( 'WC_VAR_URL' )    or  define( 'WC_VAR_URL',    plugin_dir_url( __FILE__ ) );
defined( 'WC_PREFIX' )    or  define( 'WC_PREFIX','ed_wc');
defined( 'WC_VAR_SETTINGS' )    or  define( 'WC_VAR_SETTINGS','WC_VAR_SETTINGS');
defined( 'WC_VAR_FILE' )    or  define( 'WC_VAR_FILE', plugin_basename( __FILE__ ) );


load_plugin_textdomain( 'WC_VAR_LANG', false, plugin_dir_path(__FILE__) . 'languages/' ); 

// Check if WooCommerce is active

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	
	class WC_Variations_Style_Buttons {
		const VERSION = '1.4';
		private $plugin_path;
		private $plugin_url;

		public function __construct() {
			add_filter( 'woocommerce_locate_template', array( $this, 'locate_template' ), 10, 3 );

			//js scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ), 999 );
			
		}

		public function get_plugin_path() {

			if ( $this->plugin_path ) {
				return $this->plugin_path;
			}

			return $this->plugin_path = plugin_dir_path( __FILE__ );
		}

		public function get_plugin_url() {

			if ( $this->plugin_url ) {
				return $this->plugin_url;
			}

			return $this->plugin_url = plugin_dir_url( __FILE__ );
		}
		public function locate_template( $template, $template_name, $template_path ) {
			global $woocommerce;

			$_template = $template;

			if ( ! $template_path ) {
				$template_path = $woocommerce->template_url;
			}

			$plugin_path = $this->get_plugin_path() . 'templates/';

			// Look within passed path within the theme - this is priority
			$template = locate_template( array(
				$template_path . $template_name,
				$template_name
			) );

			// Modification: Get the template from this plugin, if it exists
			if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
				$template = $plugin_path . $template_name;
			}

			// Use default template
			if ( ! $template ) {
				$template = $_template;
			}

			return $template;
		}

		function load_scripts() {
			wp_enqueue_style( 'wc-variations-style-buttons',$this->get_plugin_url() . 'assets/wc-variations-style-buttons.css' );
			
			wp_deregister_script( 'wc-add-to-cart-variation' );
wp_register_script( 'wc-add-to-cart-variation', $this->get_plugin_url() . 'assets/add-to-cart-variation.js', array( 'jquery', 'wp-util' ), self::VERSION );

		}
	}
//wc-variations-style-buttons.css
	new WC_Variations_Style_Buttons();


	if ( file_exists( WC_VAR_PATH . '/inc/options.php' )) {
		require_once WC_VAR_PATH . '/inc/options.php';
	}
	if ( file_exists( WC_VAR_PATH . '/inc/function.php' )) {
		require_once WC_VAR_PATH . '/inc/function.php';
	}
	if ( file_exists( WC_VAR_PATH . '/inc/welcome.php' )) {
		require_once WC_VAR_PATH . '/inc/welcome.php';
	}
	if ( file_exists( WC_VAR_PATH . '/inc/tabs.php' )) {
		//require_once WC_VAR_PATH . '/inc/tabs.php';
	}

	
}


