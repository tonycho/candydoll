<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! function_exists( 'ed_print_attribute_link_button' ) ) {
	function ed_print_attribute_link_button( $stock, $checked_value, $value, $label, $name,$color = NULL ) {
		// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
		$checked = sanitize_title( $checked_value ) === $checked_value ? checked( $checked_value, sanitize_title( $value ), false ) : checked( $checked_value, $value, false );

		$input_name = 'attribute_' . esc_attr( $name ) ;
		$esc_value = esc_attr( $value );
		$id = esc_attr( $name . '_v_' . $value );
		$filtered_label = apply_filters( 'woocommerce_variation_option_name', $label );
		$color = (isset($color) && $color !="")? 'style="background:'.$color.'"' : '';
		$color_btn = (isset($color) && $color !="")? 'color_btn' : '';
		$color = sanitize_title( $value ) === $checked_value ? 'class="active" '.$color : $color;


		printf( '<div class="ed__variation__button__wrp '.$color_btn.' '.$stock.'"><input type="radio" name="%1$s" value="%2$s" id="%3$s" %4$s><label for="%3$s" %6$s><i>%5$s</i></label></div>', $input_name, $esc_value, $id, $checked, $filtered_label, $color);
	}
}

if ( ! function_exists( 'ed_print_attribute_image_button' ) ) {
	function ed_print_attribute_image_button($stock, $checked_value, $value, $label, $name) {
		// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
		$checked = sanitize_title( $checked_value ) === $checked_value ? checked( $checked_value, sanitize_title( $value ), false ) : checked( $checked_value, $value, false );

		$input_name = 'attribute_' . esc_attr( $name ) ;
		$esc_value = esc_attr( $value );
		$id = esc_attr( $name . '_v_' . $value );
		$filtered_label = apply_filters( 'woocommerce_variation_option_name', $label );
		$color = (isset($color))? $color : '';
		$image_btn = (isset($label) && $label !="")? 'image_btn' : '';

		$color = sanitize_title( $value ) === $checked_value ? 'class="active" '.$color : $color;

		printf( '<div class="ed__variation__button__wrp '.$image_btn.' '.$stock.'"><input type="radio" name="%1$s" value="%2$s" id="%3$s" %4$s><label for="%3$s" %6$s><img src="%5$s" /></label></div>', $input_name, $esc_value, $id, $checked, $filtered_label, $color);

	}
}

if ( ! function_exists( 'ed_print_attribute_radio_button' ) ) {
	function ed_print_attribute_radio_button($stock, $checked_value, $value, $label, $name) {
		// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
		$checked = sanitize_title( $checked_value ) === $checked_value ? checked( $checked_value, sanitize_title( $value ), false ) : checked( $checked_value, $value, false );

		$input_name = 'attribute_' . esc_attr( $name ) ;
		$esc_value = esc_attr( $value );
		$id = esc_attr( $name . '_v_' . $value );
		$filtered_label = apply_filters( 'woocommerce_variation_option_name', $label );
		$checked_value = sanitize_title( $checked_value ) === $checked_value ? 'active' : '';

		$color = sanitize_title( $value ) === $checked_value ? 'class="active" ' : '';

		if($stock == 'inactive'){
			printf( '<div class="ed__variation__radio__wrp '.$stock.'"><input type="radio" name="%1$s" value="%2$s" id="%3$s" %4$s disabled="disabled"><label for="%3$s" %6$s>%5$s</label></div>', $input_name, $esc_value, $id, $checked, $filtered_label, $color);
		}else{
			printf( '<div class="ed__variation__radio__wrp '.$stock.'"><input type="radio" name="%1$s" value="%2$s" id="%3$s" %4$s><label for="%3$s" %6$s>%5$s</label></div>', $input_name, $esc_value, $id, $checked, $filtered_label, $color);
		}


	}
}




if( !function_exists('ed_wc_variation_stock') ){
	function ed_wc_variation_stock( $available_variations = array()  ) {
		$ed__return__arr = array();
	  if(count($available_variations) > 0 ){

			foreach ( $available_variations as $variation ) :
			//echo $variation['id'];

			//$var 		= wc_get_product( $variation['variation_id'] );
			if ( 'outofstock' == $variation["stock_status"] ) {

				foreach ( $variation['attributes'] as $attr => $value ) :
					$term = get_term_by( 'slug', $value, str_replace( 'attribute_', '', $attr ) );

					if ( isset( $term->term_id ) ) :
						$stock 		= get_post_meta( $variation['variation_id'], '_stock', true ).'<br/>';
						$percentage = round($stock);
						if($percentage <= 0){
							$ed__return__arr[$term->term_id] = 'inactive';
						}
					endif;
				endforeach;
			}else{
				foreach ( $variation['attributes'] as $attr => $value ) :
					$term = get_term_by( 'slug', $value, str_replace( 'attribute_', '', $attr ) );
					if ( isset( $term->term_id ) ) :
						if($variation['is_in_stock'] != 1){$ed__return__arr[$term->term_id] = 'inactive';}
					endif;
				endforeach;

			}

			endforeach;
			return $ed__return__arr;

	  } else {
		return false;
	  }
	}
}


if( !function_exists('ed_image_form_variation') ){
	function ed_image_form_variation( $available_variations = array()  ) {

	  if(count($available_variations) > 0 ){
			$array_img = array();
			foreach ( $available_variations as $variation ) :

				foreach ( $variation['attributes'] as $attr => $value ) :
					$term = get_term_by( 'slug', $value, str_replace( 'attribute_', '', $attr ) );
					if ( isset( $term->term_id ) && isset( $variation['image_src']) &&  $variation['image_src'] != "" ) :
						$array_img[$term->term_id] = $variation['image_src'];

					endif;
				endforeach;
			endforeach;
			return $array_img;
	  } else {
		return false;
	  }
	}
}




if( !function_exists('ed__woo_var_layout_show') ){

function ed__woo_var_layout_show( $product, $attributes, $selected_attributes){
	$attribute_keys = array_keys( $attributes );
	$ed_options = get_option( WC_VAR_SETTINGS );
	$meta_class = '';
?>
<table class="variations" cellspacing="0">
			<tbody>

				<?php
				$meta_value = maybe_unserialize( get_post_meta( get_the_ID() ,'__ed_woo_meta_settings', true ));
				if ( $product->is_type( 'variable' ) ) {
					//$ed_stock = (ed_wc_variation_stock($product->get_available_variations()) != null ) ? ed_wc_variation_stock($product->get_available_variations()): array();
				}

				$ed_img_from_variation = (ed_image_form_variation($product->get_available_variations()) != null ) ? ed_image_form_variation($product->get_available_variations()): array();

				foreach ( $attributes as $name => $options ) :;

				 ?>

                 <?php  if( isset($ed_options) && $ed_options != "" && ($ed_options[WC_PREFIX.'_'.$name]['button_type'] == 1 || $ed_options[WC_PREFIX.'_'.$name]['button_type'] == 2 || $ed_options[WC_PREFIX.'_'.$name]['button_type'] == 3 || $ed_options[WC_PREFIX.'_'.$name]['button_type'] == 4) ):

					 	 $color_set = $ed_options['color_button_'.$name];

						 $image_set = $ed_options['ed_images_button_'.$name];
					   ?>

					<tr>
						<td class="label"><label for="<?php echo sanitize_title( $name ); ?>"><?php echo wc_attribute_label( $name ); ?></label></td>
						<?php

						$sanitized_name = sanitize_title( $name );
						if ( isset( $_REQUEST[ 'attribute_' . $sanitized_name ] ) ) {
							$checked_value = $_REQUEST[ 'attribute_' . $sanitized_name ];
						} elseif ( isset( $selected_attributes[ $sanitized_name ] ) ) {

							$checked_value = $selected_attributes[ $sanitized_name ];

						}else {
							$checked_value = '';
						}

						$button_style = (isset($ed_options[WC_PREFIX.'_'.$name]['button_style'] ) && $ed_options[WC_PREFIX.'_'.$name]['button_style']==2)?'ed_round':'';
						//echo $sanitized_name;
						if(isset($meta_value[$sanitized_name])){
							if($meta_value[$sanitized_name]['woo_meta_button_type']==1 || $meta_value[$sanitized_name]['woo_meta_button_type']==2){
								if($meta_value[$sanitized_name]['button_style'] == 2){
									$button_style ='ed_round';
								}
							}
							if($meta_value[$sanitized_name]['woo_meta_button_type']==1 || $meta_value[$sanitized_name]['woo_meta_button_type']==2 ){
								$meta_class = 'meta_box_button_load';
							}
						}

						?>
						<td class="value <?php echo $button_style;?> <?php echo $meta_class;?> " id="<?php echo WC_PREFIX.'_'.$name;?>">

							<?php



							if ( isset($options) && $options !="" ) {
								if ( taxonomy_exists( $name ) ) {
									// Get terms if this is a taxonomy - ordered. We need the names too.
									$terms = wc_get_product_terms( $product->get_id(), $name, array( 'fields' => 'all' ) );
									$s = 0 ;
									foreach ( $terms as $term ) {
										$s++;
										if ( ! in_array( $term->slug, $options ) ) {
											continue;
										}
// 										if($s > 3){
// echo '<span style="color:#F00; display:block; font-size:12px; font-weight:inherit"><a href="https://edatastyle.com/product/woo-variations-style-buttons/" target="_blank" style="color:#F00">upgrade pro</a> to unlock all variations </span>';
// 
// 										break;
// 										}
										$stock = ( isset($ed_stock[$term->term_id]) && $ed_stock[$term->term_id]  == 'inactive' ) ? 'inactive':'';

										$ed_woo_button_style = $ed_options[WC_PREFIX.'_'.$name];
										if(isset($meta_value[$sanitized_name])){
											if($meta_value[$sanitized_name]['woo_meta_button_type']==1 || $meta_value[$sanitized_name]['woo_meta_button_type']==2){
												$ed_woo_button_style['button_type'] = $meta_value[$sanitized_name]['woo_meta_button_type']+1;

											}
										}

										if($ed_woo_button_style['button_type'] == 2){
											$color = $color_set[$term->term_id];
										if(isset($meta_value[$sanitized_name])){
											if($meta_value[$sanitized_name]['woo_meta_button_type']==1 && $meta_value[$sanitized_name]['color__'.$term->term_id] != ""){
												$color = $meta_value[$sanitized_name]['color__'.$term->term_id];
											}
										}

											ed_print_attribute_link_button($stock, $checked_value, $term->slug, $term->name, $sanitized_name,$color );

										}else if($ed_woo_button_style['button_type'] == 3){

											//$ed_img_from_variation[$term->term_id]

											if($ed_woo_button_style['image_form'] == 2){
												$img_src = ($image_set[$term->term_id] != '')? esc_url($image_set[$term->term_id]) :'';
												$img = ($ed_img_from_variation[$term->term_id] != "")?$ed_img_from_variation[$term->term_id]:$img_src;
											}else{
												$img = ($image_set[$term->term_id] != '')? esc_url($image_set[$term->term_id]) :'';
											}
											if(isset($meta_value[$sanitized_name])){
											if($meta_value[$sanitized_name]['woo_meta_button_type']==2 && $meta_value[$sanitized_name]['img__'.$term->term_id] != ""){
												$image = wp_get_attachment_image_src($meta_value[$sanitized_name]['img__'.$term->term_id], 'thumbnail' );
												$img = $image[0];
											}
											}

											ed_print_attribute_image_button($stock, $checked_value, $term->slug, $img, $sanitized_name);

										}else if($ed_woo_button_style['button_type'] == 4){

											ed_print_attribute_radio_button($stock, $checked_value, $term->slug, $term->name, $sanitized_name);
										}else{
											ed_print_attribute_link_button($stock, $checked_value, $term->slug, $term->name, $sanitized_name);
										}

									}
								} else {
									foreach ( $options as $option ) {

										ed_print_attribute_link_button( $checked_value, $option, $option, $sanitized_name,$color );
									}
								}
							}

							echo end( $attribute_keys ) === $name ? apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . __( 'Clear', 'woocommerce' ) . '</a>' ) : '';
							?>

						</td>
					</tr>
                    <?php else:?>

                        <tr>
                            <td class="label"><label for="<?php echo sanitize_title( $name ); ?>"><?php echo wc_attribute_label( $name ); ?></label></td>
                            <td class="value">
                                <?php
                                    $selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $name ) ] ) ? wc_clean( urldecode( $_REQUEST[ 'attribute_' . sanitize_title( $name ) ] ) ) : $product->get_variation_default_attribute( $name );
                                    wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $name, 'product' => $product, 'selected' => $selected ) );
                                    echo end( $attribute_keys ) === $name ? apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . __( 'Clear', 'woocommerce' ) . '</a>' ) : '';
                                ?>
                            </td>
                        </tr>

               		<?php endif;?>
				<?php endforeach; ?>



			</tbody>
		</table>
<?php
}
add_action('ed__woo_var_layout_show', 'ed__woo_var_layout_show', 10, 3);
}
