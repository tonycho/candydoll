<?php

// trigger qty input on shop page
add_filter( 'wc_get_template', 'wac_get_template', 10, 5 );
function wac_get_template( $located, $template_name, $args, $template_path, $default_path ) {    

    // ignore if select disabled
    if ( get_option('wac_qty_as_select') != 'yes' ) {
        return $located;
    }

    // modify input template to use select
    if ( 'global/quantity-input.php' == $template_name ) {
        $located = plugin_dir_path( __FILE__ ) . '/wac-qty-select-template.php';
    }
    
    return $located;
}
