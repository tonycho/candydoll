jQuery(document).ready(function($){

    wacChange = function(element) {
        form = element.closest('form');

        // emulates button Update cart click
        $("<input type='hidden' name='update_cart' id='update_cart' value='1'>").appendTo(form);
        
        // plugin flag
        $("<input type='hidden' name='is_wac_ajax' id='is_wac_ajax' value='1'>").appendTo(form);

        el_qty = element;
        matches = element.attr('name').match(/cart\[(\w+)\]/);
        cart_item_key = matches[1];
        form.append( $("<input type='hidden' name='cart_item_key' id='cart_item_key'>").val(cart_item_key) );

        // ask user if they really want to remove this product
        if ( !wacZeroQuantityCheck(el_qty) ) {
            return false;
        }

        // when qty is set to zero, then fires default woocommerce remove link
        if ( el_qty.val() == 0 ) {
            removeLink = element.closest('.cart_item').find('.product-remove a');
            removeLink.click();

            return false;
        }

        // get the form data before disable button...
        formData = form.serialize();
        
        $("input[name='update_cart']").val('Updating…').prop('disabled', true);

        $("a.checkout-button.wc-forward").addClass('disabled').html('Updating…');

        $.post( form.attr('action'), formData, wacPostCallback, 'json');

        return true;
    };

    wacPostCallback = function(resp) {
        // ajax response
        $('.cart-collaterals').html(resp.html);
        
        el_qty.closest('.cart_item').find('.product-subtotal').html(resp.price);
        
        $('#update_cart').remove();
        $('#is_wac_ajax').remove();
        $('#cart_item_key').remove();
        
        $("input[name='update_cart']").val(resp.update_label).prop('disabled', false);

        $("a.checkout-button.wc-forward").removeClass('disabled').html(resp.checkout_label);

        // when changes to 0, remove the product from cart
        if ( el_qty.val() == 0 ) {
            el_qty.closest('tr.cart_item').remove();
        }

        // fix to update "Your order" totals when cart is inside Checkout page (thanks @vritzka)
        if ( $( '.woocommerce-checkout' ).length ) {
            $( document.body ).trigger( 'update_checkout' );
        }

        $( document.body ).trigger( 'updated_cart_totals' );
    };

    // overrided by wac-js-calls.php
    wacZeroQuantityCheck = function(el_qty) {
        if ( el_qty.val() == 0 ) {

            if ( !confirm('Are you sure you want to remove this item from cart?') ) {
                el_qty.val(1);
                return false;
            }
        }

        return true;
    };

    wacListenChange = function() {
        $(document).on('change','.qty', {} ,function(e){
            return wacChange( $(this) );
        });
    };

    wacQtyButtons = function() {
        $(document).on('click','.wac-btn-inc', {} ,function(e){
            inputQty = $(this).parent().parent().parent().find('.qty');
            inputQty.val( function(i, oldval) { return ++oldval; });
            inputQty.change();
            return false;
        });

        $(document).on('click','.wac-btn-sub', {} ,function(e){
            inputQty = $(this).parent().parent().parent().find('.qty');
            inputQty.val( function(i, oldval) { return oldval > 0 ? --oldval : 0; });
            inputQty.change();
            return false;
        });
    };


    //
    // start calls
    //
    wacListenChange();
    wacQtyButtons();
});
