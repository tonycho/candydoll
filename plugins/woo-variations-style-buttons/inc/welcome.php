<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class WC_VAR_Welcome_Info {
	
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'admin_notices' ), 99 );
		add_action('admin_head', array( $this, 'our_logo_icon' ));
		add_filter( 'plugin_action_links', array( $this, 'go_pro' ), 10, 2 );
		//add_action( 'admin_init', array( $this, 'welcome' ) );
	}
	public function welcome() {
		$activated = get_option( WC_VAR_SETTINGS, false );
		if ( !$activated ) {
			wp_safe_redirect('index.php?admin.php?page=ed_wc_var');
		}
	}
	public function admin_notices() {
		if ( isset($_GET['page'])  && $_GET['page']== 'ed_wc_var' ) {
			echo '<div id="dwqa-message" class="notice is-dismissible"><p>To support Woo Variations Style Buttons plugin and get All features, <a href="https://edatastyle.com/product/woo-variations-style-buttons/" target="_blank">upgrade to Pro &rarr;</a></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
			
		}
		
	}
	public function our_logo_icon() {
		echo '<style>
		#toplevel_page_ed_wc_var .dashicons-admin-generic:before{
		content:""!important;
		background:url('.WC_VAR_URL.'/assets/logo.svg) no-repeat center center;	
		}
		#EDSramework_form .eds-element,.eds-field-notice .eds-notice{
			padding:15px;	
		}
		</style>';
	}
	
	public function go_pro( $actions, $file ) {
		
		if ( $file == WC_VAR_FILE) {
			$actions['eds_tes'] = '<a href="https://edatastyle.com/product/woo-variations-style-buttons/" style="color: red; font-weight: bold">Go Pro!</a>';
			$action = $actions['eds_tes'];
			unset( $actions['eds_tes'] );
			array_unshift( $actions, $action );
			$actions['eds_woo_settings'] = '<a href="'.get_admin_url().'/admin.php?page=ed_wc_var">Settings</a>';
			$action = $actions['eds_woo_settings'];
			unset( $actions['eds_woo_settings'] );
			array_unshift( $actions, $action );
		}
		return $actions;
	}


}
new WC_VAR_Welcome_Info();
?>
