<?php

if(!defined('ABSPATH')){
	return;
}

class Xoo_CP_Core{

	protected static $instance = null;

	public $action = null;

	//Get instance
	public static function get_instance(){
		if(self::$instance === null){
			self::$instance = new self();
		}
		return self::$instance;
	}


	public function __construct(){
		add_action('wc_ajax_xoo_cp_add_to_cart',array($this,'xoo_cp_add_to_cart'));
		add_action('wc_ajax_xoo_cp_update_cart',array($this,'xoo_cp_update_cart'));
		add_filter('woocommerce_add_to_cart_fragments',array($this,'set_ajax_fragments'),10,1);
		add_action('woocommerce_add_to_cart',array($this,'set_last_added_cart_item_key'),10,6);
	}


	//Get cart Content
	public function get_cart_content(){
		global $xoo_cp_gl_pden_value;

		//Get last cart item key
		$cart_item_key = get_option('xoo_cp_added_cart_key');

		if(!$cart_item_key || !$this->action)
			return;

		//Remove from the database
		delete_option('xoo_cp_added_cart_key');

		$notice = $this->get_notice_html();

		if($this->action == 'remove' || !$xoo_cp_gl_pden_value){
			return $notice;
		}

		$args = array(
			'cart_item_key' => $cart_item_key,
			'action' 		=> $this->action
		);

		ob_start();
		wc_get_template('xoo-cp-content.php',$args,'',XOO_CP_PATH.'/templates/');
		return $notice.ob_get_clean();
	}

	public function get_notice_html(){

		if(!$this->action) return;

		switch ($this->action) {
			case 'add':
				$notice = __('Product successfully added to your cart','added-to-cart-popup-woocommerce');
				break;

			case 'update':
				$notice = __('Product updated successfully','added-to-cart-popup-woocommerce');
				break;

			case 'remove':
				$notice = __('Product removed from your cart','added-to-cart-popup-woocommerce');
				break;

		}

		return '<div class="xoo-cp-atcn xoo-cp-success"><span class="xoo-cp-icon-check"></span>'.$notice.'</div>';
	}


	//add to cart ajax on single product page
	public function xoo_cp_add_to_cart(){
		global $woocommerce,$xoo_cp_gl_qtyen_value,$xoo_cp_gl_ibtne_value;

		if(!isset($_POST['action']) || $_POST['action'] != 'xoo_cp_add_to_cart' || !isset($_POST['add-to-cart'])){
			die();
		}

		// get woocommerce error notice
		$error = wc_get_notices( 'error' );
		$html = '';

		if( $error ){
			// print notice
			ob_start();
			foreach( $error as $value ) {
				wc_print_notice( $value, 'error' );
			}

			$js_data =  array(
				'error' => ob_get_clean()
			);

			wc_clear_notices(); // clear other notice
			wp_send_json($js_data);
		}
		else {
			// trigger action for added to cart in ajax
			do_action( 'woocommerce_ajax_added_to_cart', intval( $_POST['add-to-cart'] ) );

			wc_clear_notices(); // clear other notice
			WC_AJAX::get_refreshed_fragments();
		}

		die();
	}


	// Set ajax fragments
	public function set_ajax_fragments($fragments){

		$cart_content = $this->get_cart_content();

		//Cart content
		$fragments['div.xoo-cp-content'] = '<div class="xoo-cp-content">'.$cart_content.'</div>';

		return $fragments;
	}


	//Store last added cart item key
	public function set_last_added_cart_item_key($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data){
		$this->action = 'add';
		update_option('xoo_cp_added_cart_key',$cart_item_key);
	}


	//Update cart quantity
	public function xoo_cp_update_cart(){

		//Form Input Values
		$cart_key = sanitize_text_field($_POST['cart_key']);
		$new_qty = (float) $_POST['new_qty'];

		if(!is_numeric($new_qty) || $new_qty < 0 || !$cart_key){
			wp_send_json(array('error' => __('Something went wrong','side-cart-woocommerce')));
		}


		$cart_success = $new_qty == 0 ? WC()->cart->remove_cart_item($cart_key) : WC()->cart->set_quantity($cart_key,$new_qty);

		if($cart_success){
			$this->action = $new_qty == 0 ? 'remove' : 'update';
			update_option('xoo_cp_added_cart_key',$cart_key);
			WC_AJAX::get_refreshed_fragments();
		}
		else{
			if(wc_notice_count('error') > 0){
	    		echo wc_print_notices();
			}
		}
		die();
	}


}


?>
