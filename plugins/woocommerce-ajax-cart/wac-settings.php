<?php

add_filter('plugin_action_links_' . WAC_PLUGIN, 'wac_settings_link' );
add_action('admin_menu', 'wac_settings_register', 20);


function wac_settings_link( $links ) {
	$action_links = array(
		'settings' => sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=wac-settings'), __('Settings')),
	);

	return array_merge( $action_links, $links );
}

function wac_settings_register() {
    add_management_page(__('Woo Ajax Cart'), __('Woo Ajax Cart'), 'administrator', 'wac-settings', 'wac_settings_page' );
}


function wac_settings_save() {
    update_option('wac_show_qty_buttons', ($_POST['wac_show_qty_buttons'] ? 'yes' : 'no'));
    update_option('wac_confirmation_zero_qty', ($_POST['wac_confirmation_zero_qty'] ? 'yes' : 'no'));
    update_option('wac_qty_as_select', ($_POST['wac_qty_as_select'] ? 'yes' : 'no'));
    update_option('wac_select_items', $_POST['wac_select_items']);
}

function wac_settings_page() {
    // Save settings if data has been posted
    if ( ! empty( $_POST ) ) {
        if ( $_POST['save'] == __( 'Save settings' ) ) {
            wac_settings_save();
        }
        else if ( $_POST['save'] == __( 'Reset all settings' ) ) {
            //
        }
    }

    ?>
    <div class="wrap">
        <div class="icon32">
        <br />
        </div>
        <h2 class="nav-tab-wrapper"><?= __('Woocommerce Ajax Cart Settings') ?></h2>
        <br/>
        <form method="post" id="mainform" action="" enctype="multipart/form-data">
            <table class="form-table">
                <tbody>
                    <tr>
                        <td>
                            <b><?= __('Quantity buttons') ?>:</b>
                        </td>
                        <td>
                            <label>
                                <input type="checkbox"
                                    <?php if (get_option('wac_show_qty_buttons') != 'no'): ?>checked<?php endif ?> name="wac_show_qty_buttons" id="wac_show_qty_buttons">
                                <?= __('Show -/+ buttons around item quantity on cart page') ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b><?= __('Quantity select') ?>:</b>
                        </td>
                        <td class="">
                            <label>
                                <input type="checkbox"
                                    <?php if (get_option('wac_qty_as_select') == 'yes'): ?>checked<?php endif ?> name="wac_qty_as_select" id="wac_qty_as_select">
                                <?= __('Show item quantity as select instead numeric field') ?>
                            </label>
                            <div id="qty_select_div" style="margin-left: 30px">
                                <?= __('Items to show on select') ?>:
                                <input type="number" size="4" min="1" max="50"
                                        value="<?php echo setting_wac_select_items(); ?>"
                                        id="wac_select_items"
                                        name="wac_select_items">

                                <br/>
                                <!--<label>
                                    <input type="checkbox"
                                        <?php if (get_option('wac_qty_select_on_product') == 'yes'): ?>checked<?php endif ?> name="wac_qty_select_on_product" id="wac_qty_select_on_product">
                                    <?= __('Convert the Quantity field on product page into select also') ?>
                                </label>-->
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b><?= __('Quantity confirmation') ?>:</b>
                        </td>
                        <td class="">
                            <label>
                                <input type="checkbox"
                                    <?php if (get_option('wac_confirmation_zero_qty') != 'no'): ?>checked<?php endif ?> name="wac_confirmation_zero_qty" id="wac_confirmation_zero_qty">
                                <?= __('Show user confirmation when change item quantity to zero') ?>
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr/>
            <input name="save" class="button-primary" type="submit" value="<?= __( 'Save settings' ); ?>" />
            <!--<input name="save" class="button" type="submit" value="<?= __( 'Reset all settings' ); ?>" onclick="return confirm('Are you sure?')"/>-->
        </form>
    </div>
    <script>
        (function($){

            checkQtySelectDiv = function() {
                if ( $('#wac_qty_as_select').is(':checked') ) {
                    $('#qty_select_div').show();
                }
                else {
                    $('#qty_select_div').hide();
                }
            };

            $('#wac_qty_as_select').click(function(){ checkQtySelectDiv(); });
            checkQtySelectDiv();

        })(jQuery);
    </script>
    <?php
}

function setting_wac_select_items() {
    return get_option('wac_select_items', 5);
}
