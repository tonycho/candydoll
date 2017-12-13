<?php

// define the woocommerce_cart_item_quantity callback 
// add the + and - buttons
add_filter( 'woocommerce_cart_item_quantity', 'filter_woocommerce_cart_item_quantity', 10, 3 );
function filter_woocommerce_cart_item_quantity( $inputDiv, $cart_item_key, $cart_item = null ) { 

    // check config
    if ( get_option('wac_show_qty_buttons') == 'no' ) {
        return $inputDiv;
    }

    // some users related duplication problem, so it avoid this
    if ( preg_match('/wac-qty-button/', $inputDiv) ) {
        return $inputDiv;
    }

    // add plus and minus buttons
    $minus = wac_button_div('-', 'sub');
    $plus = wac_button_div('+', 'inc');

    $input = str_replace(array('<div class="quantity">', '</div>'), array('', ''), $inputDiv);
    $newDiv = '<div class="quantity wac-quantity">' . $minus . $input . $plus . '</div>';

    return $newDiv;
}; 


function wac_button_div($label, $identifier) {
    $link = '<b><a href="" class="wac-btn-'.$identifier.'">'.$label.'</a></b>';
    $div = '<div class="wac-qty-button">' . $link . '</div>';

    return $div;
}
