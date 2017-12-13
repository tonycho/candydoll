<?php

//Exit if accessed directly
if(!defined('ABSPATH')){
	return; 	
}

class Xoo_CP_Public{

	protected static $instance = null;

	public function __construct(){
		add_action('wp_enqueue_scripts',array($this,'enqueue_scripts'));
		add_action('plugins_loaded',array($this,'load_txt_domain'),99);
		add_action('wp_footer',array($this,'get_popup_markup'));
	}

	//Get class instance
	public static function get_instance(){
		if(self::$instance === null){
			self::$instance = new self();
		}	
		return self::$instance; 
	}

	//Inline styles from cart popup settings
	public static function get_inline_styles(){
		global $xoo_cp_sy_pw_value,$xoo_cp_sy_imgw_value,$xoo_cp_sy_btnbg_value,$xoo_cp_sy_btnc_value,$xoo_cp_sy_btns_value,$xoo_cp_sy_btnbr_value,$xoo_cp_sy_tbc_value,$xoo_cp_sy_tbs_value,$xoo_cp_gl_ibtne_value,$xoo_cp_gl_vcbtne_value,$xoo_cp_gl_chbtne_value,$xoo_cp_gl_qtyen_value,$xoo_cp_gl_spinen_value;

		$style = '';

		if(!$xoo_cp_gl_vcbtne_value){
			$style .= 'a.xoo-cp-btn-vc{
				display: none;
			}';
		}

		if(!$xoo_cp_gl_ibtne_value){
			$style .= 'span.xcp-chng{
				display: none;
			}';
		}

		if(!$xoo_cp_gl_chbtne_value){
			$style .= 'a.xoo-cp-btn-ch{
				display: none;
			}';
		}

		if($xoo_cp_gl_qtyen_value && $xoo_cp_gl_ibtne_value){
			$style .= 'td.xoo-cp-pqty{
			    min-width: 120px;
			}';
		}
		else{
			
		}

		if(!$xoo_cp_gl_spinen_value){
			$style .= '.xoo-cp-adding,.xoo-cp-added{display:none!important}';
		}

		$style.= "
			.xoo-cp-container{
				max-width: {$xoo_cp_sy_pw_value}px;
			}
			.xcp-btn{
				background-color: {$xoo_cp_sy_btnbg_value};
				color: {$xoo_cp_sy_btnc_value};
				font-size: {$xoo_cp_sy_btns_value}px;
				border-radius: {$xoo_cp_sy_btnbr_value}px;
				border: 1px solid {$xoo_cp_sy_btnbg_value};
			}
			.xcp-btn:hover{
				color: {$xoo_cp_sy_btnc_value};
			}
			td.xoo-cp-pimg{
				width: {$xoo_cp_sy_imgw_value}%;
			}
			table.xoo-cp-pdetails , table.xoo-cp-pdetails tr{
				border: 0!important;
			}
			table.xoo-cp-pdetails td{
				border-style: solid;
				border-width: {$xoo_cp_sy_tbs_value}px;
				border-color: {$xoo_cp_sy_tbc_value};
			}";

			return $style;
	}


	//enqueue stylesheets & scripts
	public function enqueue_scripts(){
		global $xoo_cp_gl_resetbtn_value;

		wp_enqueue_style('xoo-cp-style',XOO_CP_URL.'/assets/css/xoo-cp-style.css',null,XOO_CP_VERSION);
		wp_enqueue_script('xoo-cp-js',XOO_CP_URL.'/assets/js/xoo-cp-js.min.js',array('jquery'),XOO_CP_VERSION,true);

		wp_localize_script('xoo-cp-js','xoo_cp_localize',array(
			'adminurl'     		=> admin_url().'admin-ajax.php',
			'homeurl' 			=> get_bloginfo('url'),
			'wc_ajax_url' 		=> WC_AJAX::get_endpoint( "%%endpoint%%" ),
			'reset_cart'		=> $xoo_cp_gl_resetbtn_value
		));

		wp_add_inline_style('xoo-cp-style',self::get_inline_styles());

	}

	//Load text domain
	public function load_txt_domain(){
		$domain = 'added-to-cart-popup-woocommerce';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, WP_LANG_DIR . '/'.$domain.'-' . $locale . '.mo' ); //wp-content languages
		load_plugin_textdomain( $domain, FALSE, basename(XOO_CP_PATH) . '/languages/' ); // Plugin Languages
	}


	//Get popup markup
	public function get_popup_markup(){
		if(is_cart() || is_checkout()){return;}
		wc_get_template('xoo-cp-popup-template.php','','',XOO_CP_PATH.'/templates/');
	}


}

?>