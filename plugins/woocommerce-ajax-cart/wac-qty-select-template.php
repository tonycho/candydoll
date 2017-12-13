<div class="quantity">
    <label class="quantity" for="qty">
      <?php
        _e("[:en]QUANTITY[:zh]數量[:hk]数量");
      ?>
    </label>
    <select id="qty" name="<?php echo esc_attr( $input_name ); ?>"
            title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) ?>"
            class="input-text qty text">
        <?php for ( $i=0; $i <= setting_wac_select_items(); $i++ ): ?>
            <option <?php if ( esc_attr( $input_value ) == $i ): ?>selected="selected"<?php endif; ?>
                    value="<?php echo $i; ?>">
                <?php echo $i; ?>
            </option>
        <?php endfor; ?>
    </select>
</div>
