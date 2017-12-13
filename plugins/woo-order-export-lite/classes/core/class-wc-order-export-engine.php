<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Order_Export_Engine {
	public static $current_job_settings = '';
	public static $current_job_build_mode = '';
	public static $date_format;

	public static $order_id = '';
	public static $make_separate_orders = false;
	//
	public static function export( $settings, $filepath ) {
		if( empty($settings['destination']['type']) ) {
			return __( "No format selected", 'woo-order-export-lite' );
		}
		
		if( !is_array( $settings[ 'destination' ][ 'type' ] ) ) {
			$settings[ 'destination' ][ 'type' ] = array( $settings[ 'destination' ][ 'type' ] );
		}
		$results = array( );
		foreach( $settings[ 'destination' ][ 'type' ] as $export_type ) {
			$export_type = strtolower( $export_type );
			if ( ! in_array( strtoupper( $export_type ), WC_Order_Export_Admin::$export_types ) ) {
				return __( "Wrong format", 'woo-order-export-lite' );
			}

			include_once dirname( dirname( __FILE__ ) ) . "/exports/abstract-class-woe-export.php";
			include_once dirname( dirname( __FILE__ ) ) . "/exports/class-woe-export-{$export_type}.php";
			$class    = 'WOE_Export_' . $export_type;
			$exporter = new $class( $settings['destination'] );

			$filename = self::make_filename( $settings['export_filename'] );
			$custom_export = apply_filters('woe_custom_export_to_'.$export_type,false, $filename, $filepath, $exporter);
			if( !$custom_export ) {
				$results[] = $exporter->run_export( $filename, $filepath );
			} else {
				$results[] = $custom_export;
			}
		}
		return implode( "<br>\r\n", $results );
	}

	public static function make_filename( $mask ) {
		if ( self::$make_separate_orders && strpos( $mask, '%order_id' ) === false ) {
			$mask_parts = explode( '.', $mask );
			$before_prefix = count( $mask_parts ) >1 ? 2 : 1;
			$mask_parts[ count( $mask_parts ) - $before_prefix ] .= '-%order_id';
			$mask       = implode( '.', $mask_parts );
		}
		$time = apply_filters( 'woe_make_filename_current_time', current_time( 'timestamp' ) );
		$subst = apply_filters( 'woe_make_filename_replacements', array(
			'%d' => date( 'd',$time ),
			'%m' => date( 'm',$time ),
			'%y' => date( 'Y',$time ),
			'%h' => date( 'H',$time ),
			'%i' => date( 'i',$time ),
			'%s' => date( 's',$time ),
			'%order_id' => self::$order_id,
		) );

		return apply_filters( 'woe_make_filename', strtr( $mask, $subst ) );
	}
	
	public static function kill_buffers() {
		while ( ob_get_level() ) 
			ob_end_clean();
	}

	public static function tempnam( $folder, $prefix ) {
		$filename = @tempnam( $folder, $prefix );
		if(! $filename ) {
			$tmp_folder = dirname( dirname( dirname ( __FILE__ ) ) ) . '/tmp';
			// kill expired tmp file
			foreach( glob( $tmp_folder."/*" ) as $f) {
				if( time() - filemtime($f) > 24*3600 ) {
					unlink( $f );
				}	
			}
			$filename = tempnam( $tmp_folder, $prefix );
		}
		return $filename;
	}


	// labels for output columns
	private static function get_labels( $fields, $format, &$static_vals ) {
		$labels = array();
		foreach ( $fields as $key => $field ) {
			if ( preg_match( '#^custom_field_#', $key ) ) { // for static fields
				$static_vals[ $key ] = isset($field['value']) ? $field['value'] : $field['colname'];// FIX BUG here
			}
			if ( $field['checked'] ) {
				$labels[ $key ] = apply_filters( "woe_get_{$format}_label_{$key}", $field['colname'] );
			}
		}

		return $labels;
	}

	// gather columns having filters
	private static function check_filters( $fields, $format, $type ) {
		$filters = array();
		foreach ( $fields as $key => $field ) {
			if ( $field['checked'] AND has_filter( "woe_get_{$type}_{$format}_value_{$key}" ) ) {
				$filters[] = $key;
			}
		}

		return $filters;
	}

	/**
	 * @param string $mode
	 * @param array $settings
	 * @param string $fname
	 * @param null $labels
	 * @param null $static_vals
	 *
	 * @return WOE_Formatter
	 */
	private static function init_formater( $mode, $settings, $fname, &$labels, &$static_vals ) {
		$format = strtolower( $settings['format'] );
		include_once dirname( dirname( __FILE__ ) ) . "/formats/abstract-class-woe-formatter.php";
		if( !apply_filters('woe_load_custom_formatter_'.$format, false) )
			include_once dirname( dirname( __FILE__ ) ) . "/formats/class-woe-formatter-$format.php";

		$format_settings = array( 'global_job_settings' => $settings );
		foreach ( $settings as $key => $val ) {
			if ( preg_match( '#^format_' . $format . '_(.+)$#', $key, $m ) ) {
				$format_settings[ $m[1] ] = $val;
			}
		}

		self::init_labels( $settings, $labels, $static_vals );

		$class = 'WOE_Formatter_' . $format;

		return new $class( $mode, $fname, $format_settings, $format, $labels );
	}

	private static function init_labels( $settings, &$labels, &$static_vals ) {
		$format = strtolower( $settings['format'] );

		$static_vals = array( 'order' => array(), 'products' => array(), 'coupons' => array() );
		$labels      = array(
			'order'    => self::get_labels( $settings['order_fields'], $format, $static_vals['order'] ),
			'products' => self::get_labels( $settings['order_product_fields'], $format, $static_vals['products'] ),
			'coupons'  => self::get_labels( $settings['order_coupon_fields'], $format, $static_vals['coupons'] ),
		);
	}

	private static function _prepare_xls_csv( $settings, $order_ids ) {
		$format = strtolower( $settings['format'] );

		$csv_max['coupons'] = $csv_max['products'] = 1;
		if ( $format == 'xls' OR $format == 'csv' OR $format == 'tsv' ) {
			if ( @$settings['order_fields']['products']['repeat'] == 'columns' ) {
				if(@$settings['order_fields']['products']['max_cols'])
					$csv_max['products'] = $settings['order_fields']['products']['max_cols'];
				else
					$csv_max['products'] = WC_Order_Export_Data_Extractor::get_max_order_items( "line_item", $order_ids );
			}
			if ( @$settings['order_fields']['coupons']['repeat'] == 'columns' ) {
				if(@$settings['order_fields']['coupons']['max_cols'])
					$csv_max['coupons'] = $settings['order_fields']['coupons']['max_cols'];
				else
					$csv_max['coupons'] = WC_Order_Export_Data_Extractor::get_max_order_items( "coupon", $order_ids );
			}
		}

		return $csv_max;
	}

	private static function _optimize_calls( $settings ) {
		$format = strtolower( $settings['format'] );

		$filters_active = array(
				'order'    => self::check_filters( $settings['order_fields'], $format, 'order' ),
				'products' => self::check_filters( $settings['order_product_fields'], $format, 'order_product' ),
				'coupons'  => self::check_filters( $settings['order_coupon_fields'], $format, 'order_coupon' ),
		);

		return $filters_active;
	}

	private static function _check_products_and_coupons_fields( $settings, &$export, &$labels, &$get_coupon_meta ) {
		$export['products'] = $settings['order_fields']['products']['checked'];
		$export['coupons']  = $settings['order_fields']['coupons']['checked'];
		$get_coupon_meta    = ( $export['coupons'] AND array_diff( array_keys( $labels['coupons'] ),
						array( 'code', 'discount_amount', 'discount_amount_tax', 'excerpt' ) ) );
		if ( empty( $labels['products'] ) ) {
			$export['products'] = 0;
			unset( $labels['order']['products'] );
		}
		if ( empty( $labels['coupons'] ) ) {
			$export['coupons'] = 0;
			unset( $labels['order']['coupons'] );
		}
	}

	private static function _make_header( $format, $labels, $csv_max ) {
		$header = ( $format == 'xls' OR $format == 'csv' OR $format == 'tsv' ) ? self::_make_header_csv( $labels, $csv_max ) : '';

		return $header;
	}

	private static function _make_header_csv( $labels, $csv_max ) {
		$header = array();
		foreach ( $labels['order'] as $field => $label ) {
			$field_header = array();
			if ( $field == 'products' OR $field == 'coupons' ) {
				for ( $i = 1; $i <= $csv_max[ $field ]; $i ++ ) {
					foreach ( $labels[ $field ] as $field2 => $label2 ) {
						$field_header[] = $label2 . ( $csv_max[ $field ] > 1 ? ' #' . $i : '' );
					}
				}
			}
			if ( empty( $field_header ) ) {
				$field_header[] = $label;
			}
			$field_header = apply_filters( 'woe_add_csv_headers', $field_header, $field );
			$header = array_merge( $header, $field_header );
		}

		return $header;
	}

	private static function _install_options( $settings ) {
		global $wpdb;

		$format = strtolower( $settings['format'] );

		$options = array();
		
		if ( $format == 'xls' AND @$settings['format_xls_populate_other_columns_product_rows']
		     OR $format == 'csv' AND @$settings['format_csv_populate_other_columns_product_rows']
		     OR $format == 'tsv' AND @$settings['format_tsv_populate_other_columns_product_rows'] ) {
			$options['populate_other_columns_product_rows'] = 1;
		}
		$options['item_rows_start_from_new_line'] = ( $format == 'csv' AND @$settings['format_csv_item_rows_start_from_new_line'] );
		
		if( !empty($settings['all_products_from_order']) )
			$options['include_products'] = false;
		else
			$options['include_products'] =  $wpdb->get_col( WC_Order_Export_Data_Extractor::sql_get_product_ids( $settings ) );

		if ( isset( $settings['date_format'] ) )
			$options['date_format'] = $settings['date_format'];
		else
			$options['date_format'] = 'Y-m-d';

		if ( isset( $settings['time_format'] ) )
			$options['time_format'] = $settings['time_format'];
		else
			$options['time_format'] = 'H:i';

		//as is
		$options['export_refunds'] = $settings['export_refunds'];
		$options['skip_refunded_items'] = $settings['skip_refunded_items'];
		$options['export_all_comments'] = $settings['export_all_comments'];
		$options['export_refund_notes'] = $settings['export_refund_notes'];
		$options['format_number_fields'] = $settings['format_number_fields'];
		$options['strip_tags_product_fields'] = !empty($settings['strip_tags_product_fields']);

		return $options;
	}

	private static function  validate_defaults( $settings ) {
		if( empty($settings['sort']) )
			$settings['sort'] = 'order_id';
		if( empty($settings['sort_direction']) )
			$settings['sort_direction'] = 'DESC';
		if( !isset($settings['skip_empty_file']) )
			$settings['skip_empty_file'] = true;
		if( $settings[ 'custom_php' ] ) {
			ob_start( array( 'WC_Order_Export_Engine', 'code_error_callback' ) );
			$result = eval( $settings[ 'custom_php_code' ] );
			ob_end_clean();
		}
		return apply_filters('woe_settings_validate_defaults', $settings);
	}

	private static function code_error_callback( $out ) {
		$error = error_get_last();

		if ( is_null( $error ) ) {
			return $out;
		}

		$m = '<h2>' . __( "Don't Panic", 'woo-order-export-lite' ) . '</h2>';
		$m .= '<p>' . sprintf( __( 'The code you are trying to save produced a fatal error on line %d:', 'woo-order-export-lite' ), $error['line'] ) . '</p>';
		$m .= '<strong>' . $error['message'] . '</strong>';

		return $m;
	}

	private static function  try_modify_status( $order_id, $settings ) {
		if ( isset( $settings['change_order_status_to'] ) && wc_is_order_status( $settings['change_order_status_to'] ) ) {
			$order = new WC_Order( $order_id );
			$order->update_status( $settings['change_order_status_to'] );
		}
	}

	private static function  try_mark_order( $order_id, $settings ) {
		if ( $settings[ 'mark_exported_orders' ] ) {
			update_post_meta( $order_id, 'woe_order_exported', 1 );
		}
	}

	public static function build_file(
		$settings,
		$make_mode,
		$output_mode,
		$offset = false,
		$limit = false,
		$filename = ''
	) {
		global $wpdb;

		self::kill_buffers();
		$settings = self::validate_defaults( $settings );
		self::$current_job_settings = $settings;
		self::$current_job_build_mode = $make_mode;
		self::$date_format = trim( $settings['date_format'] . ' ' . $settings['time_format'] );

		if ( $output_mode == 'browser' ) {
			$filename = 'php://output';
		} else {
			$filename = ( ! empty( $filename ) ? $filename : self::tempnam( sys_get_temp_dir(), $settings['format'] ) );
		}


		if ( $make_mode !== 'estimate' )
			$formater = self::init_formater( $make_mode, $settings, $filename, $labels, $static_vals );
		$format   = strtolower( $settings['format'] );

		if ( $make_mode == 'finish' ) {
			self::maybe_output_summary_report( $formater );
			$formater->finish();
			return $filename;
		}

		//get IDs
		$sql = WC_Order_Export_Data_Extractor::sql_get_order_ids( $settings );
		if ( $make_mode == 'estimate' ) { //if estimate return total count
			return $wpdb->get_var( str_replace( 'ID AS order_id', 'COUNT(ID) AS order_count', $sql ) );
		} elseif ( $make_mode == 'preview' ) {
			$sql .= apply_filters ( "woe_sql_get_order_ids_order_by", " ORDER BY " . $settings[ 'sort' ] . " " . $settings[ 'sort_direction' ] ). " LIMIT " . ($limit !== false ? $limit : 1);
		} elseif ( $make_mode == 'partial' ) {
			$sql .= apply_filters ( "woe_sql_get_order_ids_order_by", " ORDER BY " . $settings[ 'sort' ] . " " . $settings[ 'sort_direction' ] );
			$offset = intval( $offset );
			$limit  = intval( $limit );
			$sql .= " LIMIT $offset,$limit";
		}

		$order_ids = $wpdb->get_col( $sql );

		// prepare for XLS/CSV
		$csv_max = self::_prepare_xls_csv( $settings, $order_ids );

		// try to optimize calls
		$filters_active = self::_optimize_calls( $settings );

		// check it once
		self::_check_products_and_coupons_fields( $settings, $export, $labels, $get_coupon_meta );

		// make header
		$header = self::_make_header( $format, $labels, $csv_max );

		$options = self::_install_options( $settings );

		if ( $make_mode != 'partial' ) { // Preview or start_estimate
			self::maybe_init_summary_report();
			$formater->start( $header );
			if ( $make_mode == 'start_estimate' ) { //Start return total count
				return $wpdb->get_var( str_replace( 'ID AS order_id', 'COUNT(ID) AS order_count', $sql ) );
			}
		} elseif ( $format == 'json' AND $offset > 0 ) { // json partial
			$formater->prev_added = true;
		}
		self::maybe_start_summary_report();

		WC_Order_Export_Data_Extractor::prepare_for_export();
		foreach ( $order_ids as $order_id ) {
			$order_id = apply_filters( "woe_order_export_started", $order_id);
			if( !$order_id )
				continue;
			self::$order_id = $order_id;
			$rows = WC_Order_Export_Data_Extractor::fetch_order_data( $order_id, $labels, $format, $filters_active,
				$csv_max, $export, $get_coupon_meta, $static_vals, $options );
			foreach ( $rows as $row ) {
				$row=apply_filters( "woe_fetch_order_row", $row, $order_id);
				if ($row) {
					$formater->output( $row );
					do_action( "woe_order_row_exported", $row, $order_id);
				}
			}
			if ( $make_mode != 'preview' ) {
				do_action( "woe_order_exported", $order_id);
				self::try_mark_order( $order_id, $settings );
			}
			else
				do_action( "woe_order_previewed", $order_id);
		}

		// for modes
		if ( $make_mode == 'partial')
			$formater->finish_partial();
		elseif ( $make_mode == 'preview') {
			self::maybe_output_summary_report( $formater );
			$formater->finish();
		}	

		return $filename;
	}

	public static function build_file_full( $settings, $filename = '', $limit = 0, $order_ids = array( ) ) {
		global $wpdb;
		
		//no need self::kill_buffers();
		$settings = self::validate_defaults( $settings );
		self::$current_job_settings = $settings;
		self::$current_job_build_mode = 'full';
		self::$date_format = trim( $settings['date_format'] . ' ' . $settings['time_format'] );
		self::maybe_init_summary_report();
		self::maybe_start_summary_report();	

		$filename = ( ! empty( $filename ) ? $filename : self::tempnam( sys_get_temp_dir(), $settings['format'] ) );

		$formater = self::init_formater( '', $settings, $filename, $labels, $static_vals );
		$format   = strtolower( $settings['format'] );

		//get IDs
		$sql = WC_Order_Export_Data_Extractor::sql_get_order_ids( $settings );
		$sql .= apply_filters ( "woe_sql_get_order_ids_order_by", " ORDER BY " . $settings[ 'sort' ] . " ". $settings[ 'sort_direction' ] );

		if ( $limit ) {
			$sql .= " LIMIT " . intval( $limit );
		}
		if ( !$order_ids )
			$order_ids = $wpdb->get_col( $sql );

		if ( empty( $order_ids )  AND apply_filters( 'woe_schedule_job_skip_empty_file', (bool) $settings['skip_empty_file'] ) ) {
			return false;
		}

		// prepare for XLS/CSV
		$csv_max = self::_prepare_xls_csv( $settings, $order_ids );

		// try to optimize calls
		$filters_active = self::_optimize_calls( $settings );

		// check it once
		self::_check_products_and_coupons_fields( $settings, $export, $labels, $get_coupon_meta );

		// make header
		$header = self::_make_header( $format, $labels, $csv_max );

		$options = self::_install_options( $settings );

		$formater->start( $header );

		WC_Order_Export_Data_Extractor::prepare_for_export();
		foreach ( $order_ids as $order_id ) {
			$order_id = apply_filters( "woe_order_export_started", $order_id);
			if( !$order_id )
				continue;
			self::$order_id = $order_id;
			$rows = WC_Order_Export_Data_Extractor::fetch_order_data( $order_id, $labels, $format, $filters_active,
				$csv_max, $export, $get_coupon_meta, $static_vals, $options );
			foreach ( $rows as $row ) {
				$row=apply_filters( "woe_fetch_order_row", $row, $order_id);
				if ($row) {
					$formater->output( $row );
					do_action( "woe_order_row_exported", $row, $order_id);
				}
			}
			do_action( "woe_order_exported", $order_id);
			self::try_modify_status( $order_id, $settings );
			self::try_mark_order( $order_id, $settings );
		}
		
		self::maybe_output_summary_report( $formater );
		$formater->finish();

		return $filename;
	}

	public static function build_separate_files_and_export( $settings, $filename = '', $limit = 0, $order_ids = array( ) ) {
		global $wpdb;
		
		self::kill_buffers();
		$settings = self::validate_defaults( $settings );
		self::$current_job_settings = $settings;
		self::$current_job_build_mode = 'full';
		self::$date_format = trim( $settings['date_format'] . ' ' . $settings['time_format'] );

		$filename = ( ! empty( $filename ) ? $filename : self::tempnam( sys_get_temp_dir(), $settings['format'] ) );

		self::init_labels( $settings, $labels, $static_vals );
		$format   = strtolower( $settings['format'] );

		//get IDs
		$sql = WC_Order_Export_Data_Extractor::sql_get_order_ids( $settings );
		$sql .= apply_filters ( "woe_sql_get_order_ids_order_by", " ORDER BY " . $settings[ 'sort' ] . " ". $settings[ 'sort_direction' ] );

		if ( $limit ) {
			$sql .= " LIMIT " . intval( $limit );
		}

		if ( !$order_ids )
			$order_ids = $wpdb->get_col( $sql );

		if ( empty( $order_ids ) ) {
			return false;
		}
		// prepare for XLS/CSV
		$csv_max = self::_prepare_xls_csv( $settings, $order_ids );

		// try to optimize calls
		$filters_active = self::_optimize_calls( $settings );

		// check it once
		self::_check_products_and_coupons_fields( $settings, $export, $labels, $get_coupon_meta );

		// make header
		$header = self::_make_header( $format, $labels, $csv_max );

		$options = self::_install_options( $settings );

		$result = false;

		WC_Order_Export_Data_Extractor::prepare_for_export();
		self::$make_separate_orders = true;
		foreach ( $order_ids as $order_id ) {
			$order_id = apply_filters( "woe_order_export_started", $order_id);
			if( !$order_id )
				continue;
			self::$order_id = $order_id;
			$formater       = self::init_formater( '', $settings, $filename, $_labels, $_static_vals );

			$formater->truncate();
			$formater->start( $header );
			$rows = WC_Order_Export_Data_Extractor::fetch_order_data( $order_id, $labels, $format, $filters_active,
					$csv_max, $export, $get_coupon_meta, $static_vals, $options );
			foreach ( $rows as $row ) {
				$row=apply_filters( "woe_fetch_order_row", $row, $order_id);
				if ($row) {
					$formater->output( $row );
					do_action( "woe_order_row_exported", $row, $order_id);
				}
			}
			do_action( "woe_order_exported", $order_id);
			self::try_modify_status( $order_id, $settings );
			self::try_mark_order( $order_id, $settings );
			$formater->finish();

			if ( $filename !== false ) {
				$result = self::export( $settings, $filename );
				//if ($result) {
				//	return $result;
				//}
			}
			self::$order_id = '';
		}

		return $result; //return last result
	}


	public static function build_files_and_export( $settings, $filename = '', $limit = 0, $order_ids = array( ) ) {
		if (!empty($settings['destination']['separate_files'])) {
			$result = self::build_separate_files_and_export( $settings, $filename, $limit, $order_ids );
		}
		else {
			$file = self::build_file_full( $settings, $filename, $limit, $order_ids );
			if ( $file !== false )
				$result = self::export( $settings, $file );
			else
				$result = false;
		}

		if ( $result === false )
			$result  = __( 'Nothing to export. Please, adjust your filters', 'woo-order-export-lite' );
		return $result;
	}
	

	//SUMMARY report starts here 
	private static function check_create_session() {
		if(!session_id()) {
			session_start();
		}	
	}
	//reset data
	private static function maybe_init_summary_report() {
		if( !self::$current_job_settings['summary_report_by_products'] )
			return; 
		self::check_create_session();	
			
		//modify header
		add_filter('woe_' . strtolower(self::$current_job_settings['format']) . '_header_filter', function() {
			return array( 
				__( "Id", 'woocommerce-export-order'),
				__( "SKU", 'woocommerce-export-order'),
				__( "Product", 'woocommerce-export-order'),
				__( "Quantity", 'woocommerce-export-order'),
				__( "Total Amount", 'woocommerce-export-order'),
			);
		});
			
		$_SESSION['woe_summary_products'] = array();	
	}
	
	//get ready to accept data
	private static function maybe_start_summary_report() {
		if( !self::$current_job_settings['summary_report_by_products'] )
			return; 
		self::check_create_session();
		
		//skip orders
		add_filter('woe_fetch_order_row', '__return_false');
		// gather details 
		add_action('woe_order_exported', array('WC_Order_Export_Engine', 'summary_report_add_order_products') );
		add_action('woe_order_previewed', array('WC_Order_Export_Engine', 'summary_report_add_order_products') );
	}
	
	public static function summary_report_add_order_products($order_id) {
		$order  = new WC_Order($order_id);
		foreach($order->get_items( ) as $item) {
			$item_id = !empty($item['variation_id']) ? $item['variation_id'] : $item['product_id'];
			$key = $item['name'] . $item_id;
			if( !isset($_SESSION['woe_summary_products'][$key]) ) {
				$product   = $order->get_product_from_item( $item );
				$_SESSION['woe_summary_products'][$key] = array(
					'id'=> $item_id, 
					'sku'=> method_exists($product,'get_sku') ? $product->get_sku() : $product->sku,
					'name'=> $item['name'],
					'qty'=> 0,
					'total'=> 0,
				);
			}	
				
			$_SESSION['woe_summary_products'][$key]['qty'] += $item['qty'];
			$total = method_exists($item, 'get_total') ? $item->get_total() : $item['line_total'];
			$_SESSION['woe_summary_products'][$key]['total'] += wc_round_tax_total( $total );
		}
	}
	
	private static function maybe_output_summary_report($formatter) {
		if( !self::$current_job_settings['summary_report_by_products'] )
			return ; 
		self::check_create_session();
			
		ksort( $_SESSION['woe_summary_products'] );// by Name+Id
		foreach($_SESSION['woe_summary_products'] as $data) {
			$formatter->output( $data);
		}
	}
}