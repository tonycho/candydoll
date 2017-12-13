<?php
/*
  Plugin Name: Woo AJAX Cart
  Plugin URI: http://ragob.com/wooajaxcart
  Description: Change the default behavior of WooCommerce Cart page, making AJAX requests when quantity field changes
  Version: 1.2
  Author: Moises Heberle
  Author URI: http://codecanyon.net/user/moiseh
 */

define('WAC_PLUGIN', plugin_basename( __FILE__ ));

require_once 'wac-settings.php';
require_once 'wac-demo.php';
require_once 'wac-qty-buttons.php';
require_once 'wac-qty-select.php';
require_once 'wac-cart-update.php';
require_once 'wac-js-calls.php';


add_action('init', 'wac_init');

function wac_init() {
    // force to make is_cart() returns true, to make right calculations on class-wc-cart.php (WC_Cart::calculate_totals())

    // this define fix a bug that not show Shipping calculator when is WAC ajax request

    if ( !empty($_POST['is_wac_ajax']) && !defined( 'WOOCOMMERCE_CART' ) ) {
        define( 'WOOCOMMERCE_CART', true );
    }

    wac_enqueue_cart_js();
}

// this is custom code to cart page ajax work in pages like "Woocommerce Shop page"
function wac_enqueue_cart_js() {
    $path = 'assets/js/frontend/cart.js';
    $src = str_replace( array( 'http:', 'https:' ), '', plugins_url( $path, WC_PLUGIN_FILE ) );

    $deps = array( 'jquery', 'wc-country-select', 'wc-address-i18n');
    wp_enqueue_script( 'wc-cart', $src, $deps, WC_VERSION, true );
}
