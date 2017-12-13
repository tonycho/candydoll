<?php

// on submit AJAX form of the cart
// after calculate cart form items
add_action('woocommerce_cart_updated', 'wac_update');
function wac_update() {
    // is_wac_ajax: flag defined on wooajaxcart.js
    
    if ( !empty($_POST['is_wac_ajax'])) {
        $resp = array();
        $resp['update_label'] = __( 'Update Cart', 'woocommerce' );
        $resp['checkout_label'] = __( 'Proceed to Checkout', 'woocommerce' );
        $resp['price'] = 0;
        
        // render the cart totals (cart-totals.php)
        ob_start();
        do_action( 'woocommerce_after_cart_table' );
        do_action( 'woocommerce_cart_collaterals' );
        do_action( 'woocommerce_after_cart' );
        $resp['html'] = ob_get_clean();
        $resp['price'] = 0;
        
        // calculate the item price
        if ( !empty($_POST['cart_item_key']) ) {
            $items = WC()->cart->get_cart();
            $cart_item_key = $_POST['cart_item_key'];
            
            if ( array_key_exists($cart_item_key, $items)) {
                $cart_item = $items[$cart_item_key];
                $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                $price = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
                $resp['price'] = $price;
            }
        }

        echo json_encode($resp);
        exit;
    }
}
