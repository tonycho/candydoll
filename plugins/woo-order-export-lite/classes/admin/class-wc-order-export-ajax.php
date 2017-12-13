<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Order_Export_Ajax {
	const tempfile_prefix = 'woocommerce-order-file-';

	private static $_wp_using_ext_object_cache_previous;

	public function save_settings() {
		$settings = WC_Order_Export_Manage::make_new_settings( $_POST );
		$id = WC_Order_Export_Manage::save_export_settings( $_POST['mode'], $_POST['id'], $settings );
		echo json_encode( array( 'id' => $id ) );
	}

	public function run_one_job() {
		if( !empty( $_REQUEST[ 'schedule' ] ) )
			$settings = WC_Order_Export_Manage::get( WC_Order_Export_Manage::EXPORT_SCHEDULE, $_REQUEST[ 'schedule' ] );
		elseif($_REQUEST[ 'profile' ] == 'now')
			$settings = WC_Order_Export_Manage::get( WC_Order_Export_Manage::EXPORT_NOW );
		else
			$settings = WC_Order_Export_Manage::get( WC_Order_Export_Manage::EXPORT_PROFILE, $_REQUEST[ 'profile' ] );
		$filename = WC_Order_Export_Engine::build_file_full( $settings );
		WC_Order_Export_Manage::set_correct_file_ext( $settings );
		self::send_headers( $settings[ 'format' ], WC_Order_Export_Engine::make_filename( $settings['export_filename'] ) );
		self::send_contents_delete_file($filename);
	}

	public static function run_cron_jobs() {
		$main_settings = WC_Order_Export_Admin::load_main_settings();
		if( !isset( $_REQUEST['key'] ) OR $_REQUEST['key'] != $main_settings['cron_key'] ) {
			_e( 'Wrong key for cron url!', 'woo-order-export-lite' ) ;
			return;
		}
		WC_Order_Export_Cron::wc_export_cron_global_f();
	}

    public function save_tools() {
		$data = json_decode($_POST['tools-import'], true);
        if ( $data )
			WC_Order_Export_Manage::import_settings( $data );
	}

    public function save_settings_tab() {
		WC_Order_Export_Admin::save_main_settings();
	}

	public function get_products() {
		echo json_encode( WC_Order_Export_Data_Extractor_UI::get_products_like($_REQUEST['q']) );
	}

	public function get_users() {
		echo json_encode( WC_Order_Export_Data_Extractor_UI::get_users_like($_REQUEST['q']) );
	}

	public function get_coupons() {
		echo json_encode( WC_Order_Export_Data_Extractor_UI::get_coupons_like($_REQUEST['q']) );
	}

	public function get_used_custom_order_meta() {
		$settings = WC_Order_Export_Manage::make_new_settings( $_POST );
		$sql = WC_Order_Export_Data_Extractor::sql_get_order_ids( $settings );
		$ret = WC_Order_Export_Data_Extractor_UI::get_all_order_custom_meta_fields( $sql );
		echo json_encode( $ret );
	}

	public function get_used_custom_products_meta() {
		$settings = WC_Order_Export_Manage::make_new_settings( $_POST );
		$sql = WC_Order_Export_Data_Extractor::sql_get_order_ids( $settings );
		$ret = WC_Order_Export_Data_Extractor_UI::get_all_product_custom_meta_fields_for_orders( $sql );
		echo json_encode( $ret );
	}

	public function get_used_custom_coupons_meta() {
		$ret = array();
		echo json_encode( $ret );
	}

	public function get_categories() {
		echo json_encode( WC_Order_Export_Data_Extractor_UI::get_categories_like($_REQUEST['q']) );
	}

	public function get_vendors() {
		self::get_users();
	}

	public function test_destination() {
		$settings = WC_Order_Export_Manage::make_new_settings( $_POST );

		unset( $settings[ 'destination' ][ 'type' ] );
		$settings[ 'destination' ][ 'type' ][ ] = $_POST[ 'destination' ];

		// use unsaved settings

		do_action( 'woe_start_test_job', $_POST['id'], $settings );

		$main_settings = WC_Order_Export_Admin::load_main_settings();

		$result = WC_Order_Export_Engine::build_files_and_export( $settings, '', $main_settings['limit_button_test'] );
		echo $result;
	}

	public function preview() {
		$settings = WC_Order_Export_Manage::make_new_settings( $_POST );
		// use unsaved settings

		do_action( 'woe_start_preview_job', $_POST['id'], $settings );

		WC_Order_Export_Engine::build_file( $settings, 'preview', 'browser', 0, $_POST['limit'] );
	}

	public function estimate() {
		$settings = WC_Order_Export_Manage::make_new_settings( $_POST );
		// use unsaved settings

		$total = WC_Order_Export_Engine::build_file( $settings, 'estimate', 'file', 0, 0, 'test' );

		echo json_encode( array( 'total' => $total ) );
	}

	public function get_order_custom_fields_values() {
		echo json_encode( WC_Order_Export_Data_Extractor_UI::get_order_custom_fields_values($_POST['cf_name']) );
	}

	public function get_product_custom_fields_values() {
		echo json_encode( WC_Order_Export_Data_Extractor_UI::get_product_custom_fields_values($_POST['cf_name']) );
	}

	public function get_products_taxonomies_values() {
		echo json_encode( WC_Order_Export_Data_Extractor_UI::get_products_taxonomies_values($_POST['tax']) );
	}

	public function get_products_attributes_values() {
		echo json_encode( WC_Order_Export_Data_Extractor_UI::get_products_attributes_values($_POST['attr']) );
	}

    public function get_products_itemmeta_values() {
		echo json_encode( WC_Order_Export_Data_Extractor_UI::get_products_itemmeta_values($_POST['item']) );
	}

	public function get_order_shipping_values() {
		echo json_encode( WC_Order_Export_Data_Extractor_UI::get_order_meta_values('_shipping_', $_POST['item']) );
	}

    public function get_order_billing_values() {
		echo json_encode( WC_Order_Export_Data_Extractor_UI::get_order_meta_values('_billing_', $_POST['item']) );
    }

	public function send_headers( $format, $download_name = '') {
		WC_Order_Export_Engine::kill_buffers();
		switch ( $format ) {
			case 'XLSX':
				if( empty( $download_name ) )
				  $download_name = "orders.xlsx";
				header( 'Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
				break;
			case 'XLS':
				if( empty( $download_name ) )
				  $download_name = "orders.xls";
				header( 'Content-type: application/vnd.ms-excel; charset=utf-8' );
				break;
			case 'CSV':
				if( empty( $download_name ) )
				  $download_name = "orders.csv";
				header( 'Content-type: text/csv' );
				break;
			case 'TSV':
				if( empty( $download_name ) )
					$download_name = "orders.tsv";
				header( 'Content-type: text/csv' );
				break;
			case 'JSON':
				if( empty( $download_name ) )
				  $download_name = "orders.json";
				header( 'Content-type: application/json' );
				break;
			case 'XML':
				if( empty( $download_name ) )
				  $download_name = "orders.xml";
				header( 'Content-type: text/xml' );
				break;
		}
		header( 'Content-Disposition: attachment; filename="' . $download_name  .'"' );
	}

	public function start_prevent_object_cache() {
		global $_wp_using_ext_object_cache;

		self::$_wp_using_ext_object_cache_previous	 = $_wp_using_ext_object_cache;
		$_wp_using_ext_object_cache					 = false;
	}

	public function stop_prevent_object_cache() {
		global $_wp_using_ext_object_cache;

		$_wp_using_ext_object_cache = self::$_wp_using_ext_object_cache_previous;
	}

	public function export_start() {
		self::start_prevent_object_cache();
		$settings = WC_Order_Export_Manage::make_new_settings( $_POST );

		$filename = WC_Order_Export_Engine::tempnam( sys_get_temp_dir(), "orders" );
		if( !$filename ) {
			die( __( 'Can\'t create temporary file', 'woo-order-export-lite' ) ) ;
		}

		file_put_contents( $filename, '' );

		do_action( 'woe_start_export_job', $_POST['id'], $settings );

		$total = WC_Order_Export_Engine::build_file( $settings, 'start_estimate', 'file', 0, 0, $filename );
		$file_id = current_time( 'timestamp' );
		set_transient( self::tempfile_prefix . $file_id, $filename, 60 );
		self::stop_prevent_object_cache();
		echo json_encode( array( 'total' => $total, 'file_id' => $file_id ) );
	}

	private function get_temp_file_name() {
		self::start_prevent_object_cache();
		$filename = get_transient( self::tempfile_prefix . $_REQUEST['file_id'] );
		if ( $filename === false ) {
			echo json_encode( array( 'error' => __( 'Can\'t find exported file', 'woo-order-export-lite' ) ) );
			die();
		}
		set_transient( self::tempfile_prefix . $_REQUEST['file_id'], $filename, 60 );
		self::stop_prevent_object_cache();
		return $filename;
	}

	private function delete_temp_file() {
		self::start_prevent_object_cache();
		$filename = get_transient( self::tempfile_prefix . $_REQUEST['file_id'] );
		if ( $filename !== false ) {
			delete_transient( self::tempfile_prefix . $_REQUEST['file_id'] );
			unlink($filename);
		}
		self::stop_prevent_object_cache();
	}

	public function cancel_export() {
		self::delete_temp_file();

		echo json_encode( array() );
	}

	public function export_part() {
		$settings = WC_Order_Export_Manage::make_new_settings( $_POST );
		$main_settings = WC_Order_Export_Admin::load_main_settings();

		WC_Order_Export_Engine::build_file( $settings, 'partial', 'file', intval( $_POST['start'] ), $main_settings['ajax_orders_per_step'],
			self::get_temp_file_name() );
		echo json_encode( array( 'start' => $_POST['start'] + $main_settings['ajax_orders_per_step'] ) );
	}

	public function export_finish() {
		$settings = WC_Order_Export_Manage::make_new_settings( $_POST );
		WC_Order_Export_Engine::build_file( $settings, 'finish', 'file', 0, 0, self::get_temp_file_name() );

		$filename = WC_Order_Export_Engine::make_filename( $settings['export_filename'] );
		set_transient( self::tempfile_prefix . 'download_filename', $filename, 60 );
		echo json_encode( array( 'done' => true ) );
	}

	public function export_download() {
		self::start_prevent_object_cache();
		$format   = basename( $_GET['format'] );
		$filename = self::get_temp_file_name();
		delete_transient( self::tempfile_prefix . $_GET['file_id'] );

		$download_name = get_transient( self::tempfile_prefix . 'download_filename');
		self::send_headers( $format ,$download_name);
		self::send_contents_delete_file( $filename );
		self::stop_prevent_object_cache();
	}

	public function plain_export() {
		parse_str($_POST['settings'], $_POST['settings']);
		$_POST['settings'] = array_map('stripslashes_deep', $_POST['settings']);
		$_POST['settings']['mode'] = $_POST['mode'];
		$_POST['settings']['id']   = $_POST['id'];
		$settings = WC_Order_Export_Manage::make_new_settings( $_POST['settings'] );
		// use unsaved settings

		do_action( 'woe_start_export_job', $_POST['id'], $settings );

		// custom export worked for plain
		if( apply_filters( 'woe_plain_export_custom_func', false, $_POST['id'], $settings ) )
			return ;

		$file = WC_Order_Export_Engine::build_file_full( $settings );

		if ( $file !== false ) {
			$file_id = current_time( 'timestamp' );
			self::start_prevent_object_cache();
			set_transient( self::tempfile_prefix . $file_id, $file, 600 );
			self::stop_prevent_object_cache();

			WC_Order_Export_Manage::set_correct_file_ext( $settings );

			$_GET['format']  = $settings[ 'format' ];
			$_GET['file_id'] = $_REQUEST['file_id'] = $file_id;

			$filename = WC_Order_Export_Engine::make_filename( $settings['export_filename'] );
			set_transient( self::tempfile_prefix . 'download_filename', $filename, 60 );

			self::export_download();
		} else {
			_e( 'Nothing to export. Please, adjust your filters', 'woo-order-export-lite' );
		}
	}

	function export_download_bulk_file() {
		if($_REQUEST[ 'export_bulk_profile' ] == 'now')
			$settings	 = WC_Order_Export_Manage::get( WC_Order_Export_Manage::EXPORT_NOW );
		else
			$settings = WC_Order_Export_Manage::get( WC_Order_Export_Manage::EXPORT_PROFILE, $_REQUEST[ 'export_bulk_profile' ] );
		$filename = WC_Order_Export_Engine::build_file_full( $settings, '', 0, explode(",",$_REQUEST[ 'ids' ]) );
		WC_Order_Export_Manage::set_correct_file_ext( $settings );
		self::send_headers( $settings[ 'format' ], WC_Order_Export_Engine::make_filename( $settings['export_filename'] ) );
		self::send_contents_delete_file( $filename );
	}
	
	private static function send_contents_delete_file( $filename ) {
		if( !empty($filename) ) {
			readfile( $filename );
			unlink( $filename );
		}	
	}
}
?>