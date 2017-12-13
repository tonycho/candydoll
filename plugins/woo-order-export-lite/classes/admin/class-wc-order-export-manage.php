<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Order_Export_Manage {
	const settings_name_now      = 'woocommerce-order-export-now';
	const settings_name_cron     = 'woocommerce-order-export-cron';
	const settings_name_profiles = 'woocommerce-order-export-profiles';
	const settings_name_actions  = 'woocommerce-order-export-actions';

	const EXPORT_NOW          = 'now';
	const EXPORT_PROFILE      = 'profiles';
	const EXPORT_SCHEDULE     = 'cron';
	const EXPORT_ORDER_ACTION = 'order-action';
	
	static function get_days() {
		return array(
			'Sun'=>__( 'Sun', 'woo-order-export-lite' ),
			'Mon'=>__( 'Mon', 'woo-order-export-lite' ),
			'Tue'=>__( 'Tue', 'woo-order-export-lite' ),
			'Wed'=>__( 'Wed', 'woo-order-export-lite' ),
			'Thu'=>__( 'Thu', 'woo-order-export-lite' ),
			'Fri'=>__( 'Fri', 'woo-order-export-lite' ),
			'Sat'=>__( 'Sat', 'woo-order-export-lite' ),
		);
	}

	static function get_settings_name_for_mode( $mode ) {
		$name = '';
		if ( $mode == self::EXPORT_NOW ) {
			$name = self::settings_name_now;
		} elseif ( $mode == self::EXPORT_SCHEDULE ) {
			$name = self::settings_name_cron;
		} elseif ( $mode == self::EXPORT_PROFILE ) {
			$name = self::settings_name_profiles;
		} elseif ( $mode == self::EXPORT_ORDER_ACTION ) {
			$name = self::settings_name_actions;
		}
		return $name;
	}
	
	static function remove_settings() {
		$options = array(
			self::settings_name_now,
			self::settings_name_cron,
			self::settings_name_profiles,
			self::settings_name_actions,
		);	
		
		foreach($options as $option)
			delete_option( $option );
	}

	// arrays
	static function get_export_settings_collection( $mode ) {
		$name = self::get_settings_name_for_mode( $mode );
		return get_option( $name, array() );
	}
	static function save_export_settings_collection( $mode, $jobs ) {
		$name = self::get_settings_name_for_mode( $mode );
		return update_option( $name, $jobs );
	}

	static function make_new_settings( $in ) {
		$new_settings = $in['settings'];
		
		// use old PHP code if no permissions, just to stop trcky persons ;)
		if( !WC_Order_Export_Admin::user_can_add_custom_php() ) {
			 unset($new_settings['custom_php_code']);
		}

		// UI don't pass empty multiselects
		$multiselects = array(
			'from_status',
			'to_status',
			'statuses',
			'order_custom_fields',
			'product_custom_fields',
			'product_categories',
			'product_vendors',
			'products',
			'shipping_locations',
			'shipping_methods',
			'user_roles',
			'user_names',
			'coupons',
            'billing_locations',
			'payment_methods',
			'product_attributes',
            'product_itemmeta',
			'product_taxonomies'
		);
		foreach ( $multiselects as $m_select ) {
			if ( ! isset( $new_settings[ $m_select ] ) ) {
				$new_settings[ $m_select ] = array();
			}
		}

		$settings = self::get( $in['mode'], $in['id'] );
		// setup new values for same keys
		foreach ( $new_settings as $key => $val ) {
			$settings[ $key ] = $val;
		}

		$sections = array(
			'orders'   => 'order_fields',
			'products' => 'order_product_fields',
			'coupons'  => 'order_coupon_fields'
		);
		foreach ( $sections as $section => $fieldset ) {
			$new_order_fields = array();
			$in_sec           = $in[ $section ];

			if ( $in_sec['colname'] ) {
				foreach ( $in_sec['colname'] as $field => $colname ) {
					$opts = array(
						"checked" => $in_sec['exported'][ $field ],
						"colname" => $colname,
						"label"   => $in_sec['label'][ $field ]
					);
					// for products & coupons
					if ( isset( $in_sec['repeat'][ $field ] ) ) {
						$opts["repeat"] = $in_sec['repeat'][ $field ];
					}
					if ( isset( $in_sec['max_cols'][ $field ] ) ) {
						$opts["max_cols"] = $in_sec['max_cols'][ $field ];
					}
					//for orders
					if ( isset( $in_sec['segment'][ $field ] ) ) {
						$opts["segment"] = $in_sec['segment'][ $field ];
					}
					//for static fields
					if ( isset( $in_sec['value'][ $field ] ) ) {
						$opts["value"] = $in_sec['value'][ $field ];
					}
					$new_order_fields[ $field ] = $opts;
				}
			}

			$settings[ $fieldset ] = $new_order_fields;
		}

		return self::apply_defaults( $in['mode'], $settings );
	}

	static function get( $mode, $id = 0 ) {
		$all_jobs = self::get_export_settings_collection( $mode );

		if ( $mode == self::EXPORT_NOW) { // one job
			return self::apply_defaults( $mode, $all_jobs );
		} elseif ( ! $id ) {
			if ( empty( $all_jobs ) OR !is_array($all_jobs) ) return array();
			return array_map( function( $item ) use( $mode ) {
				return WC_Order_Export_Manage::apply_defaults( $mode, $item );
			}, $all_jobs );
		}

		$settings = isset( $all_jobs[ $id ] ) ? $all_jobs[ $id ] : array();
		return self::apply_defaults( $mode, $settings );
	}

	private static function apply_defaults( $mode, $settings ) {
		$defaults = array(
			'mode'                                           => $mode,
			'skip_empty_file'                                => true,
			'from_status'                                    => array(),
			'to_status'                                      => array(),
			'change_order_status_to'                         => '',
			'statuses'                                       => array(),
			'from_date'                                      => '',
			'to_date'                                        => '',
			'shipping_locations'                             => array(),
			'shipping_methods'                               => array(),
			'user_roles'                                     => array(),
			'user_names'                                     => array(),
            'billing_locations'                              => array(),
			'payment_methods'                                => array(),
			'any_coupon_used'                                => 0,
			'coupons'                                        => array(),
			'order_custom_fields'                            => array(),
			'product_categories'                             => array(),
			'product_vendors'                                => array(),
			'products'                                       => array(),
			'product_taxonomies'                             => array(),
			'product_custom_fields'                          => array(),
			'product_attributes'                             => array(),
            'product_itemmeta'                               => array(),
			'format'                                         => 'XLS',
			'format_xls_use_xls_format'		       			 => 0,
			'format_xls_sheet_name'		       			 	 => __( 'Orders', 'woo-order-export-lite' ),
			'format_xls_display_column_names'                => 1,
			'format_xls_auto_width'				             => 1,
			'format_xls_populate_other_columns_product_rows' => 1,
			'format_xls_direction_rtl' 						 => 0,
			'format_csv_enclosure'                           => '"',
			'format_csv_delimiter'                           => ',',
			'format_csv_linebreak'                           => '\r\n',
			'format_csv_display_column_names'                => 1,
			'format_csv_add_utf8_bom'                        => 0,
			'format_csv_populate_other_columns_product_rows' => 1,
			'format_csv_item_rows_start_from_new_line'    => 0,
			'format_csv_encoding'                            => 'UTF-8',
			'format_tsv_linebreak'                           => '\r\n',
			'format_tsv_display_column_names'                => 1,
			'format_tsv_add_utf8_bom'                        => 0,
			'format_tsv_populate_other_columns_product_rows' => 1,
			'format_tsv_encoding'                            => 'UTF-8',
			'format_xml_root_tag'                            => 'Orders',
			'format_xml_order_tag'                           => 'Order',
			'format_xml_product_tag'                         => 'Product',
			'format_xml_coupon_tag'                          => 'Coupon',
			'format_xml_prepend_raw_xml'                     => '',
			'format_xml_append_raw_xml'                      => '',
			'format_xml_self_closing_tags'                   => 1,
			'all_products_from_order'                        => 1,
			'skip_refunded_items'                            => 1,
			'skip_suborders' 	              		         => 0,
			'export_refunds' 	              		         => 0,
			'date_format' 									 => 'Y-m-d',
			'time_format' 									 => 'H:i',
			'sort_direction'                                 => 'DESC',
			'sort'                                           => 'order_id',
			'format_number_fields'                            => 0,
			'export_all_comments'                            => 0,
			'export_refund_notes'                            => 0,
			'strip_tags_product_fields'                      => 0,
			'format_json_start_tag'							 => '[',
			'format_json_end_tag'							 => '\n]',
			'custom_php'                                     => 0,
			'custom_php_code'                                => '',
			'mark_exported_orders'                           => 0,
			'export_unmarked_orders'                         => 0,
			
			'summary_report_by_products'                     => 0,
		);

		if ( ! isset( $settings['format'] ) ) {
			$settings['format'] = 'XLS';
		}

		if ( ! isset( $settings['order_fields'] ) ) {
			$settings['order_fields'] = array();
		}
		self::merge_settings_and_default( $settings['order_fields'], WC_Order_Export_Data_Extractor_UI::get_order_fields( $settings['format'] ) );

		if ( ! isset( $settings['order_product_fields'] ) ) {
			$settings['order_product_fields'] = array();
		}
		self::merge_settings_and_default( $settings['order_product_fields'], WC_Order_Export_Data_Extractor_UI::get_order_product_fields( $settings['format'] ) );

		if ( ! isset( $settings['order_coupon_fields'] ) ) {
			$settings['order_coupon_fields'] = array();
		}
		self::merge_settings_and_default( $settings['order_coupon_fields'], WC_Order_Export_Data_Extractor_UI::get_order_coupon_fields( $settings['format'] ) );
		return array_merge( $defaults, $settings );
	}

	static function merge_settings_and_default(&$opt, $defaults) {
		foreach( $defaults as $k=>$v ) {
			if( isset($opt[$k]) ) {
				//set default attribute OR add to option
				if( isset($v['default']) )
					$opt[$k]['default'] = $v['default'];
				//set default format OR add to option
				if( isset($v['format']) )
					$opt[$k]['format'] = $v['format'];
				// overwrite labels for localization	
				$opt[$k]['label'] = $v['label'];	
			}
			else
				$opt[$k] = $v;
		}
	}

	static function save_export_settings( $mode, $id, $options ) {
		$all_jobs = self::get_export_settings_collection( $mode);
		if ( $mode == self::EXPORT_NOW ) {
			$all_jobs = $options;// just replace
		} elseif ( $mode == self::EXPORT_SCHEDULE ) {
			if ( $id ) {
				$options['schedule']['last_run'] = $all_jobs[ $id ]['schedule']['last_run'];
				$options['schedule']['next_run'] = WC_Order_Export_Cron::next_event_timestamp_for_schedule( $options['schedule'], $id );
				$all_jobs[ $id ]                 = $options;
			} else {
				$options['schedule']['last_run'] = current_time("timestamp",0);
				$options['schedule']['next_run'] = WC_Order_Export_Cron::next_event_timestamp_for_schedule( $options['schedule'] );
				$all_jobs[]                      = $options; // new job
                end( $all_jobs );
                $id = key( $all_jobs );
			}
		} elseif ( $mode == self::EXPORT_PROFILE OR $mode == self::EXPORT_ORDER_ACTION ) {
			if ( $id ) {
				$all_jobs[ $id ] = $options;
			} else {
				$all_jobs[] = $options; // new job
				end( $all_jobs );
				$id = key( $all_jobs );
			}
		}

		self::save_export_settings_collection( $mode, $all_jobs);

		if( $mode == self::EXPORT_SCHEDULE )
			WC_Order_Export_Cron::install_job();
		return $id;
	}

	static function clone_export_settings( $mode, $id ) {
		return self::advanced_clone_export_settings( $id, $mode, $mode );
	}

	static function advanced_clone_export_settings( $id, $mode_in = self::EXPORT_SCHEDULE, $mode_out = self::EXPORT_SCHEDULE ) {
		$all_jobs_in = self::get_export_settings_collection( $mode_in );
		//new settings
		$settings           = $all_jobs_in[ $id ];
		$settings['mode']   = $mode_out;

		if ( $mode_in !== $mode_out ) {
			$all_jobs_out = self::get_export_settings_collection( $mode_out );
		}
		else {
			$mode_out     = $mode_in;
			$all_jobs_out = $all_jobs_in;
			$settings['title'] .= " [cloned]"; //add note
		}

		if ( $mode_in === self::EXPORT_PROFILE && $mode_out === self::EXPORT_SCHEDULE) {
			if ( ! isset( $settings['destination'] ) ) {
				$settings['destination'] = array(
					'type' => 'folder',
					'path' => get_home_path(),
				);
			}

			if ( ! isset( $settings['export_rule'] ) ) {
				$settings['export_rule'] = 'last_run';
			}

			if ( ! isset( $settings['export_rule_field'] ) ) {
				$settings['export_rule_field'] = 'modified';
			}

			if ( ! isset( $settings['schedule'] ) ) {
				$settings['schedule'] = array(
						'type'   => 'schedule-1',
						'run_at' => '00:00',
				);
			}

			unset( $settings['use_as_bulk'] );
		}

		end( $all_jobs_out );
		$next_id				  = key( $all_jobs_out ) + 1;
		$all_jobs_out[ $next_id ] = $settings;

		self::save_export_settings_collection( $mode_out, $all_jobs_out  );
		return $next_id;
	}


	static function set_correct_file_ext( &$settings ) {
		if ( $settings['format'] == 'XLS' AND ! $settings['format_xls_use_xls_format'] ) {
			$settings['format'] = 'XLSX';
		}
	}

	static function import_settings( $data ) {
		$allowed_options = array(
			self::EXPORT_NOW,
			self::EXPORT_SCHEDULE,
			self::EXPORT_PROFILE,
			self::EXPORT_ORDER_ACTION,
		);
		if( isset( $data[ self::EXPORT_NOW ] ) ) { // import ALL
			foreach ( $allowed_options as $key ) {
				if ( isset( $data[ $key ] ) ) {
					$import_type = self::get_settings_name_for_mode(  $key );
					update_option( $setting_name, $data[ $key ] );
				}
			}
		}
		elseif( isset( $data["mode"] )  AND  in_array( $data["mode"], $allowed_options ) ) { // OR import single ?
				$setting_name = self::get_settings_name_for_mode(  $data["mode"] );
				if( $setting_name == self::settings_name_now ) {
					update_option( $setting_name, $data ); // rewrite
				} else { // append!
					$items   = get_option( $setting_name, array() );
					$items[] = $data;
					update_option( $setting_name, $items );
				}
		}// if modes
	}
}
