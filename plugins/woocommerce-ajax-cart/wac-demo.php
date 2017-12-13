<?php

add_action('woocommerce_before_cart_contents', 'wac_cart_demo');
add_action('woocommerce_archive_description', 'wac_shop_demo');


function wac_cart_demo() {
    if ( defined('IS_DEMO')) {
        wac_demo_msg('Change the product quantity to see the AJAX update made by WooAjaxCart plugin');
    }
}

function wac_shop_demo() {
    if ( defined('IS_DEMO')) {
        wac_demo_msg('Add some items to cart and go to the "Cart" page to see this plugin in action');
    }
}

function wac_demo_msg($text) {
    echo sprintf('<div style="background-color: lightgreen; padding: 5px; margin: 5px; border-radius: 3px">* %s</div>', $text);
}

