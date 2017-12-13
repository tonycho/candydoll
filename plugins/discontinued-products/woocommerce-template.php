<?php
/**
 * WooCommerce Discontinued Products Template Functions
 *
 * @package woocommerce
 * @since 1.0.0
 */

if ( ! function_exists( 'dpis_customed_discontinued' ) ) {

	/**
	 * Is discontiued.
	 * Check if product is discontinued.
	 *
	 * @since 1.0.0
	 * @param int|null $product_id Optional. ID of the product to check.
	 */
	function dpis_customed_discontinued( $product_id = null ) {

		global $post;
		if ( $post || $product_id !== null ) {
			$product_id      = $product_id !== null ? $product_id : $post->ID;
			$is_customed_discontinued = get_post_meta( $product_id, 'is_customed_discontinued', true );
			return $is_customed_discontinued === 'yes';
		}
		return false;
	}
}

if ( ! function_exists( 'dp_alt_products' ) ) {

	/**
	 * Alternative Products.
	 * Output buttons to alternative products.
	 *
	 * @since 1.0.0
	 */
	function dp_alt_products() {

		global $post;
		$alt_products = get_post_meta( $post->ID, '_alt_products', true );
		$alt_products = is_array( $alt_products ) ? $alt_products : array();
		$notice       = dp_alt_products_notice( $post->ID, empty( $alt_products ) );
		?>
		<h4><?php echo esc_html( $notice ); ?></h4>
		<?php
		foreach ( $alt_products as $alt_product ) {
			?>
			<a href="<?php echo esc_url( get_permalink( $alt_product ) ); ?>" class="button"><?php echo get_the_title( $alt_product ); ?></a>
			<?php
		}
	}
}

if ( ! function_exists( 'dp_alt_products_notice' ) ) {

	/**
	 * Alternative Products Notice.
	 * Determin notice output for discontinued products based on settings.
	 *
	 * @since 1.1.0
	 * @param int     $product_id ID of the product to check.
	 * @param boolean $no_alt true or false if there are no alternative products.
	 */
	function dp_alt_products_notice( $product_id, $no_alt ) {

		$prod_text_option = get_post_meta( $product_id, '_discontinued_product_text', true );
		$prod_alt_option = get_post_meta( $product_id, '_alt_product_text', true );
		$text_option  = get_option( 'dc_discontinued_text' );
		$alt_option   = get_option( 'dc_alt_text' );
		$text         = dp_alt_products_text( $prod_text_option, $text_option, _( 'This product has been discontinued.' ) );
		$alt          = dp_alt_products_text( $prod_alt_option, $alt_option, _( 'You may be interested in:' ) );
		$notice       = $no_alt ? $text : $text . ' ' . $alt;
		return $notice;
	}
}

if ( ! function_exists( 'dp_alt_products_text' ) ) {

	/**
	 * Alternative Products Text.
	 * Determin text for discontinued products based on settings.
	 *
	 * @since 1.1.0
	 * @param string $product_text product meta text.
	 * @param string $option_text options settings text.
	 * @param string $default_text default text.
	 */
	function dp_alt_products_text( $product_text, $option_text, $default_text ) {

		$text = $product_text ? $product_text : ( $option_text ? $option_text : $default_text );
		return $text;
	}
}
