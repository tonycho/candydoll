<?php
/**
 * Loop Add to Cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;
?>

<?php if ( ! $product->is_in_stock() ) : ?>

	<a href="<?php echo apply_filters( 'out_of_stock_add_to_cart_url', get_permalink( /*$product->id  woo v3 deprecated */ wc_get_product(get_the_ID()) ) ); ?>" class="post_name"><span class="icon-info2"></span><?php echo apply_filters( 'out_of_stock_add_to_cart_text', __( 'Details', 'jawtemplates' ) ); ?></a>

<?php else : ?>

	<?php
		$link = array(
			'url'   => '',
			'label' => '',
			'class' => ''
		);

		$handler = apply_filters( 'woocommerce_add_to_cart_handler', /*$product->product_type woo v3 deprecated*/$product->get_type(), $product );

		switch ( $handler ) {
			case "variable" :
				$link['url'] 	= apply_filters( 'variable_add_to_cart_url', get_permalink( /*$product->id  woo v3 deprecated */ wc_get_product(get_the_ID()) ) );
				$link['label'] 	= apply_filters( 'variable_add_to_cart_text', __( 'Select options', 'jawtemplates' ) );
			break;
			case "grouped" :
				$link['url'] 	= apply_filters( 'grouped_add_to_cart_url', get_permalink( /*$product->id  woo v3 deprecated */ wc_get_product(get_the_ID()) ) );
				$link['label'] 	= apply_filters( 'grouped_add_to_cart_text', __( 'View options', 'jawtemplates' ) );
			break;
			case "external" :
				$link['url'] 	= apply_filters( 'external_add_to_cart_url', get_permalink( /*$product->id  woo v3 deprecated */ wc_get_product(get_the_ID()) ) );
				$link['label'] 	= apply_filters( 'external_add_to_cart_text', __( 'Read More', 'jawtemplates' ) );
			break;
			default :
				if ( $product->is_purchasable() ) {
					$link['url'] 	= apply_filters( 'add_to_cart_url', esc_url( $product->add_to_cart_url() ) );
					$link['label'] 	= apply_filters( 'add_to_cart_text', __( 'Add to cart', 'jawtemplates' ) );
					$link['class']  = apply_filters( 'add_to_cart_class', 'add_to_cart_button' );
				} else {
					$link['url'] 	= apply_filters( 'not_purchasable_url', get_permalink( /*$product->id  woo v3 deprecated */ wc_get_product(get_the_ID()) ) );
					$link['label'] 	= apply_filters( 'not_purchasable_text', __( 'Read More', 'jawtemplates' ) );
				}
			break;
		}

		echo apply_filters( 'woocommerce_loop_add_to_cart_link', sprintf('<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="%s post_name button product_type_%s"><span class="icon-plus-circle2"></span><span class="icon-cart3"></span>%s</a>', esc_url( $link['url'] ), esc_attr( /*$product->id  woo v3 deprecated */ wc_get_product(get_the_ID()) ), esc_attr( $product->get_sku() ), esc_attr( $link['class'] ), esc_attr( /*$product->product_type woo v3 deprecated*/$product->get_type() ), esc_html( $link['label'] ) ), $product, $link );

	?>

<?php endif; ?>
