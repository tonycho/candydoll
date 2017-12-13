<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Order_Export_Data_Extractor {
	static $statuses;
	static $countries;
	static $prices_include_tax;
	static $decimal_separator;
	static $decimals;
	static $thousands_separator;
	static $current_order;
	static $date_format;
	static $object_type = 'shop_order';
	const  HUGE_SHOP_ORDERS = 1000;// more than 1000 orders
	const  HUGE_SHOP_PRODUCTS = 1000;// more than 1000 products


	//Common
	public static function get_order_custom_fields() {
		global $wpdb;
		$transient_key = 'woe_get_order_custom_fields_result';

		$fields = get_transient( $transient_key );
		if($fields  === false) {
			$total_orders = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts}  WHERE post_type = '" . self::$object_type . "'" );
			//small shop , take all orders
			if( $total_orders < self::HUGE_SHOP_ORDERS )
				$fields = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->posts} INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id WHERE post_type = '" . self::$object_type . "'" );
			else { // we have a lot of orders, take last good orders, upto 1000
			   	$order_ids = $wpdb->get_col( "SELECT  ID FROM {$wpdb->posts} WHERE post_type = '" . self::$object_type . "' AND post_status IN('wc-on-hold','wc-processing','wc-completed')  ORDER BY post_date DESC LIMIT 1000");
				$order_ids[] = 0; // add fake zero
				$order_ids = join( ",", $order_ids);
				$fields = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->postmeta}  WHERE post_id IN ($order_ids)" );
			}
			sort( $fields );
			set_transient( $transient_key, $fields, 60 ); //valid for a minute
		}
		return apply_filters( 'woe_get_order_custom_fields', $fields );
	}

	public static function get_product_attributes() {
		global $wpdb;

		$attrs = array();

		// WC internal table , skip hidden and attributes
		$wc_fields = $wpdb->get_results( "SELECT attribute_name,attribute_label FROM {$wpdb->prefix}woocommerce_attribute_taxonomies" );
		foreach ( $wc_fields as $f ) {
			$attrs[ 'pa_' . $f->attribute_name ] = $f->attribute_label;
		}


		// WP internal table, take all attributes
		$wp_fields = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} INNER JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
                                            WHERE meta_key LIKE 'attribute\_%' AND post_type = 'product_variation'" );
		foreach ( $wp_fields as $attr ) {
			$attr = str_replace( "attribute_", "", $attr );
			if ( substr( $attr, 0, 3 ) == 'pa_' ) // skip attributes from WC table
			{
				continue;
			}
			$name           = str_replace( "-", " ", $attr );
			$name           = ucwords( $name );
			$attrs[ $attr ] = $name;
		}
		asort( $attrs );

		return apply_filters( 'woe_get_product_attributes', $attrs );
	}

    public static function get_product_itemmeta() {
		global $wpdb;
		$transient_key = 'woe_get_product_itemmeta_result';

		$metas = get_transient( $transient_key );
		if($metas  === false) {
			// WP internal table, take all metas
			$metas = $wpdb->get_col( "SELECT DISTINCT meta.meta_key FROM {$wpdb->prefix}woocommerce_order_itemmeta meta inner join {$wpdb->prefix}woocommerce_order_items item on item.order_item_id=meta.order_item_id and item.order_item_type = 'line_item' " );
			sort($metas);
			set_transient( $transient_key, $metas, 60 ); //valid for a minute
		}
		return apply_filters( 'woe_get_product_itemmeta', $metas );
	}

	public static function get_product_taxonomies() {
		global $wpdb;

		$attrs = array();

		// WP internal table, take all taxonomies for products
		$wpdb->show_errors( true );
		$wp_fields = $wpdb->get_col( "SELECT DISTINCT taxonomy FROM {$wpdb->term_relationships}
					JOIN {$wpdb->term_taxonomy} ON {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id
					WHERE {$wpdb->term_relationships}.object_id IN  (SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_type = 'product' OR post_type='product_variation')" );
		foreach ( $wp_fields as $attr ) {
			$attrs[ $attr ] = $attr;
		}
		asort( $attrs );

		return apply_filters( 'woe_get_product_taxonomies', $attrs );
	}

	public static function get_product_custom_fields() {
		global $wpdb;
		$transient_key = 'woe_get_product_custom_fields_result';

		$fields = get_transient( $transient_key );
		if($fields  === false) {
			//rewrite for huge # of products
			$total_products = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts}  WHERE  post_type = 'product' OR post_type='product_variation' " );
			//small shop , take all orders
			if( $total_products < self::HUGE_SHOP_PRODUCTS )
				$fields = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->posts} INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id WHERE post_type = 'product' OR post_type='product_variation' " );
			else { // we have a lot of orders, take last good orders, upto 1000
			   	$product_ids = $wpdb->get_col( "SELECT  ID FROM {$wpdb->posts} WHERE post_type IN('product','product_variation')  ORDER BY post_date DESC LIMIT 1000");
				$product_ids[] = 0; // add fake zero
				$product_ids = join( ",", $product_ids);
				$fields = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->postmeta}  WHERE post_id IN ($product_ids)" );
			}
			sort( $fields );
			set_transient( $transient_key, $fields, 60 ); //valid for a minute
		}
		return apply_filters( 'woe_get_product_custom_fields', $fields );
	}

	//For ENGINE
	private static function parse_pairs( $pairs, $valid_types, $mode = '' ) {
		$pair_types = array();
		foreach ( $pairs as $pair ) {
			list( $filter_type, $filter_value ) = array_map( 'trim', explode( "=", trim( $pair ) ) );
			if ( $mode == 'lower_filter_label' ) {
				$filter_type = strtolower( $filter_type );
			} // Country=>country for locations
			if ( ! in_array( $filter_type, $valid_types ) ) {
				continue;
			}
			if ( ! isset( $pair_types[ $filter_type ] ) ) {
				$pair_types[ $filter_type ] = array();
			}
			$pair_types[ $filter_type ][] = $filter_value;
		}

		return $pair_types;
	}

	private static function parse_complex_pairs( $pairs, $valid_types, $mode = '' ) {
		$pair_types = array();
		$delimiters = array(
			'=' => 'IN',
			'<>' => 'NOT IN',
			'LIKE' => 'LIKE',
		);
		foreach ( $pairs as $pair ) {
			$pair = trim( $pair );
			$op = '';
			foreach($delimiters as $delim=>$op_seek) {
				$t = explode( $delim, $pair );
				if(count($t) == 2) {
					$op = $op_seek;
					break;
				}
			}
			if( !$op )
				continue;

			list( $filter_type,  $filter_value ) = array_map("trim", $t);

			if ( $mode == 'lower_filter_label' ) {
				$filter_type = strtolower( $filter_type );
			} // Country=>country for locations
			if ( ! in_array( $filter_type, $valid_types ) ) {
				continue;
			}
			$filter_type = addslashes($filter_type);
			if ( ! isset( $pair_types[ $op ] ) ) {
				$pair_types[ $op ] = array();
			}
			if ( ! isset( $pair_types[ $op ] [ $filter_type ] ) ) {
				$pair_types[ $op ] [ $filter_type ] = array();
			}
			$pair_types[ $op ][ $filter_type ][] = addslashes($filter_value);
		}
		return $pair_types;
	}

	private static function sql_subset( $arr_values ) {
		$values = array();
		foreach ( $arr_values as $s ) {
			$values[] = "'$s'";
		}

		return join( ",", $values );
	}


	public static function sql_get_order_ids( $settings ) {
		//$settings['product_categories'] = array(119);
		//$settings['products'] = array(4554);
		//$settings['shipping_locations'] = array("city=cityS","city=alex","postcode=12345");
		//$settings['product_attributes'] = array("pa_material=glass");
		return self::sql_get_order_ids_Ver1( $settings );
	}

	public static function sql_get_product_ids( $settings ) {
		global $wpdb;

		$product_where = self::sql_build_product_filter($settings);

		$wc_order_items_meta        = "{$wpdb->prefix}woocommerce_order_itemmeta";
		$left_join_order_items_meta = $order_items_meta_where = array();

		// filter by product
		if ( $product_where ) {
			$left_join_order_items_meta[] = "LEFT JOIN $wc_order_items_meta  AS orderitemmeta_product ON orderitemmeta_product.order_item_id = order_items.order_item_id";
			$order_items_meta_where[]     = " (orderitemmeta_product.meta_key IN ('_variation_id', '_product_id')  AND orderitemmeta_product.meta_value IN $product_where)";
		} else {
			$left_join_order_items_meta[] = "LEFT JOIN $wc_order_items_meta  AS orderitemmeta_product ON orderitemmeta_product.order_item_id = order_items.order_item_id";
			$order_items_meta_where[]     = " orderitemmeta_product.meta_key IN ('_variation_id', '_product_id')";
		}

		//by attrbutes in woocommerce_order_itemmeta
		if ( $settings['product_attributes'] ) {
			$attrs        = self::get_product_attributes();
			$names2fields = array_flip( $attrs );
			$filters      = self::parse_complex_pairs( $settings['product_attributes'], $attrs );
			foreach ( $filters as $operator => $fields) {
				foreach ( $fields as $field => $values ) {
					$field  = $names2fields[ $field ];
					if ( $values ) {
						$left_join_order_items_meta[] = "LEFT JOIN $wc_order_items_meta  AS `orderitemmeta_{$field}` ON `orderitemmeta_{$field}`.order_item_id = order_items.order_item_id";
						if( $operator == 'IN' OR $operator == 'NOT IN' ) {
							$values = self::sql_subset( $values );
							$order_items_meta_where[]     = " (`orderitemmeta_{$field}`.meta_key='$field'  AND `orderitemmeta_{$field}`.meta_value $operator  ($values) ) ";
						} elseif( $operator =='LIKE' ) {
							$pairs = array();
							foreach($values as $v)
								$pairs[] = " `orderitemmeta_{$field}`.meta_value LIKE '$v' ";
							$pairs = join("OR", $pairs);
							$order_items_meta_where[]     = " (`orderitemmeta_{$field}`.meta_key='$field'  AND  ($pairs) ) ";
						}
					}
				}// values
			}// operators
		}

        //by attrbutes in woocommerce_order_itemmeta
		if ( $settings['product_itemmeta'] ) {
            foreach($settings['product_itemmeta'] as $value) {
                $settings['product_itemmeta'][] = esc_html($value);
            }

			$itemmeta        = self::get_product_itemmeta();
			$filters      = self::parse_complex_pairs( $settings['product_itemmeta'], $itemmeta );
            foreach ( $filters as $operator => $fields) {
				foreach ( $fields as $field => $values ) {;
					if ( $values ) {
						$left_join_order_items_meta[] = "LEFT JOIN $wc_order_items_meta  AS `orderitemmeta_{$field}` ON `orderitemmeta_{$field}`.order_item_id = order_items.order_item_id";
						if( $operator == 'IN' OR $operator == 'NOT IN' ) {
							$values = self::sql_subset( $values );
							$order_items_meta_where[]     = " (`orderitemmeta_{$field}`.meta_key='$field'  AND `orderitemmeta_{$field}`.meta_value $operator  ($values) ) ";
						} elseif( $operator =='LIKE' ) {
							$pairs = array();
							foreach($values as $v)
								$pairs[] = " `orderitemmeta_{$field}`.meta_value LIKE '$v' ";
							$pairs = join("OR", $pairs);
							$order_items_meta_where[]     = " (`orderitemmeta_{$field}`.meta_key='$field'  AND  ($pairs) ) ";
						}
					}// values
				}
			}// operators
		}

		$orders_where = array();
		self::apply_order_filters_to_sql( $orders_where, $settings );
		if( $orders_where  ) {
			$left_join_order_items_meta[] = "LEFT JOIN {$wpdb->posts}  AS `orders` ON `orders`.ID  = order_items.order_id";
			$order_items_meta_where[] = "( " . join(" AND ", $orders_where) . " )";
		}

		$order_items_meta_where = join( " AND ", $order_items_meta_where );
		if ( $order_items_meta_where ) {
			$order_items_meta_where = " AND " . $order_items_meta_where;
		}
		$left_join_order_items_meta = join( "  ", $left_join_order_items_meta );


		// final sql from WC tables
		if ( !$order_items_meta_where )
			return false;

		$sql= "SELECT DISTINCT p_id FROM
						(SELECT order_items.order_item_id as order_item_id, MAX(CONVERT(orderitemmeta_product.meta_value ,UNSIGNED INTEGER)) as p_id FROM {$wpdb->prefix}woocommerce_order_items as order_items
							$left_join_order_items_meta
							WHERE order_item_type='line_item' $order_items_meta_where GROUP BY order_item_id
						) AS temp";
		return $sql;
	}


	public static function sql_get_filtered_product_list( $settings ) {
		global $wpdb;

		// has exact products?
		if( $settings['products'] ) {
			;// do nothing
		} elseif( empty( $settings['product_vendors'] )  AND empty( $settings['product_custom_fields'] ) ) {
			$settings['products'] = array();
		} else {
			$product_where = array("1");

			//by owners
			$settings['product_vendors'] = apply_filters('woe_sql_get_product_vendor_ids', $settings['product_vendors'], $settings);
			if ( $settings['product_vendors'] ) {
				$values = self::sql_subset( $settings['product_vendors'] );
				$product_where[] =" products.post_author in ($values)";
			}

			//by custom fields in Product
			$product_meta_where = "";
			$left_join_product_meta = "";
			if ( $settings['product_custom_fields'] ) {
				$left_join_product_meta = $product_meta_where = array();
				$cf_names = self::get_product_custom_fields();
				$filters  = self::parse_complex_pairs( $settings['product_custom_fields'], $cf_names);
				$pos=1;
				foreach ( $filters as $operator => $fields) {
					foreach ( $fields as $field => $values ) {
						if ( $values ) {
							$left_join_product_meta[] = "LEFT JOIN {$wpdb->postmeta} AS productmeta_cf_{$pos} ON productmeta_cf_{$pos}.post_id = products.ID";
							if( $operator == 'IN' OR $operator == 'NOT IN' ) {
								$values = self::sql_subset( $values );
								$product_meta_where[]    = " (productmeta_cf_{$pos}.meta_key='$field'  AND productmeta_cf_{$pos}.meta_value $operator ($values)) ";
							} elseif( $operator =='LIKE' ) {
								$pairs = array();
								foreach($values as $v)
									$pairs[] = " productmeta_cf_{$pos}.meta_value LIKE '$v' ";
								$pairs = join("OR", $pairs);
								$product_meta_where[]     = " (productmeta_cf_{$pos}.meta_key='$field'  AND  ($pairs) ) ";
							}
							$pos++;
						}//if values
					}
				}

				if( $filters ) {
					$product_where[] = join(" AND ", $product_meta_where);
					$left_join_product_meta = join( "  ", $left_join_product_meta );
				}
			}

			//done
			$product_where = join(" AND ", $product_where);
			$sql = "SELECT DISTINCT ID FROM {$wpdb->posts} AS products $left_join_product_meta  WHERE products.post_type in ('product','product_variation') AND products.post_status<>'trash' AND $product_where ";
			$settings['products'] = $wpdb->get_col($sql);
		}

		//  we have to use variations , if user sets product attributes
		if ( $settings['products'] AND $settings['product_attributes'] ) {
		    $values = self::sql_subset( $settings['products'] );
			$sql = "SELECT DISTINCT ID FROM {$wpdb->posts} AS products WHERE products.post_type in ('product','product_variation') AND products.post_status<>'trash' AND post_parent IN ($values)";
			$settings['products'] = $wpdb->get_col($sql);
		}

		return apply_filters('woe_sql_adjust_products', $settings['products'] , $settings);
	}


	public static function sql_build_product_filter( $settings ) {
		global $wpdb;

		//custom taxonomies
		$taxonomy_where = "";
		if ( $settings['product_taxonomies'] ) {
			$attrs        = self::get_product_taxonomies();
			$names2fields = array_flip( $attrs );
			$filters      = self::parse_pairs( $settings['product_taxonomies'], $attrs );
			//print_r($filters );die();
			foreach ( $filters as $label => $values ) {
				$field  = $names2fields[ $label ];
				$values = self::sql_subset( $values );
				if ( $values ) {
					$taxonomy_where_object_id = $taxonomy_where ? "AND object_id IN ($taxonomy_where)" : "";
					$taxonomy_where           = "(SELECT  object_id FROM {$wpdb->term_relationships} AS `{$field}_rel`
						INNER JOIN {$wpdb->term_taxonomy} AS `{$field}_cat` ON `{$field}_cat`.term_taxonomy_id = `{$field}_rel`.term_taxonomy_id
						WHERE `{$field}_cat`.term_id IN (SELECT term_id FROM {$wpdb->terms} WHERE name IN($values) ) $taxonomy_where_object_id
					)";
				}
			}
		}

		$product_category_where = $taxonomy_where;
		if ( $settings['product_categories'] ) {
			$cat_ids = array( 0 );
			foreach ( $settings['product_categories'] as $cat_id ) {
				$cat_ids[] = $cat_id;
				foreach ( get_term_children( $cat_id, 'product_cat' ) as $child_id ) {
					$cat_ids[] = $child_id;
				}
			}
			$cat_ids                  = join( ',', $cat_ids );
			$taxonomy_where_object_id = $taxonomy_where ? "AND object_id IN ($taxonomy_where)" : "";
			$product_category_where   = "SELECT  DISTINCT object_id FROM {$wpdb->term_relationships} AS product_in_cat
						LEFT JOIN {$wpdb->term_taxonomy} AS product_category ON product_category.term_taxonomy_id = product_in_cat.term_taxonomy_id
						WHERE product_category.term_id IN ($cat_ids) $taxonomy_where_object_id
					";
			// get products and variations!
			$product_category_where = "
				(
					SELECT DISTINCT ID FROM {$wpdb->posts} AS product_category_variations WHERE post_parent IN ($product_category_where)
					UNION
					$product_category_where
				)
				";
		}

		$settings['products'] = self::sql_get_filtered_product_list($settings);

		// deep level still
		$product_where = '';
		if ( $settings['products'] ) {
			$values = self::sql_subset( $settings['products'] );
			if ( $values ) {
				$product_where          = "($values)";
				$product_category_where = "";
			}
		}
		if ( $product_category_where ) {
			$product_where = $product_category_where;
		}
		return $product_where ;
	}

	public static function sql_get_order_ids_Ver1( $settings ) {
		global $wpdb;

		// deep level !
		$product_where = self::sql_build_product_filter($settings);

		$wc_order_items_meta        = "{$wpdb->prefix}woocommerce_order_itemmeta";
		$left_join_order_items_meta = $order_items_meta_where = array();

		// filter by product
		if ( $product_where ) {
			$left_join_order_items_meta[] = "LEFT JOIN $wc_order_items_meta  AS orderitemmeta_product ON orderitemmeta_product.order_item_id = order_items.order_item_id";
			$order_items_meta_where[]     = " (orderitemmeta_product.meta_key IN ('_variation_id', '_product_id')  AND orderitemmeta_product.meta_value IN $product_where)";
		}


		//by attrbutes in woocommerce_order_itemmeta
		if ( $settings['product_attributes'] ) {
			$attrs        = self::get_product_attributes();
			$names2fields = @array_flip( $attrs );
			$filters      = self::parse_complex_pairs( $settings['product_attributes'], $attrs );
			foreach ( $filters as $operator => $fields) {
				foreach ( $fields as $field => $values ) {
					$field  = $names2fields[ $field ];
					if ( $values ) {
						$left_join_order_items_meta[] = "LEFT JOIN $wc_order_items_meta  AS `orderitemmeta_{$field}` ON `orderitemmeta_{$field}`.order_item_id = order_items.order_item_id";
						if( $operator == 'IN' OR $operator == 'NOT IN' ) {
							$values = self::sql_subset( $values );
							$order_items_meta_where[]     = " (`orderitemmeta_{$field}`.meta_key='$field'  AND `orderitemmeta_{$field}`.meta_value $operator  ($values) ) ";
						} elseif( $operator =='LIKE' ) {
							$pairs = array();
							foreach($values as $v)
								$pairs[] = " `orderitemmeta_{$field}`.meta_value LIKE '$v' ";
							$pairs = join("OR", $pairs);
							$order_items_meta_where[]     = " (`orderitemmeta_{$field}`.meta_key='$field'  AND  ($pairs) ) ";
						}
					}// values
				}
			}// operators
		}

        //by attrbutes in woocommerce_order_itemmeta
		if ( $settings['product_itemmeta'] ) {
            foreach($settings['product_itemmeta'] as $value) {
                $settings['product_itemmeta'][] = esc_html($value);
            }

			$itemmeta        = self::get_product_itemmeta();
			$filters      = self::parse_complex_pairs( $settings['product_itemmeta'], $itemmeta );
            foreach ( $filters as $operator => $fields) {
				foreach ( $fields as $field => $values ) {;
					if ( $values ) {
						$left_join_order_items_meta[] = "LEFT JOIN $wc_order_items_meta  AS `orderitemmeta_{$field}` ON `orderitemmeta_{$field}`.order_item_id = order_items.order_item_id";
						if( $operator == 'IN' OR $operator == 'NOT IN' ) {
							$values = self::sql_subset( $values );
							$order_items_meta_where[]     = " (`orderitemmeta_{$field}`.meta_key='$field'  AND `orderitemmeta_{$field}`.meta_value $operator  ($values) ) ";
						} elseif( $operator =='LIKE' ) {
							$pairs = array();
							foreach($values as $v)
								$pairs[] = " `orderitemmeta_{$field}`.meta_value LIKE '$v' ";
							$pairs = join("OR", $pairs);
							$order_items_meta_where[]     = " (`orderitemmeta_{$field}`.meta_key='$field'  AND  ($pairs) ) ";
						}
					}// values
				}
			}// operators
		}

		$order_items_meta_where = join( " AND ", $order_items_meta_where );
		if ( $order_items_meta_where ) {
			$order_items_meta_where = " AND " . $order_items_meta_where;
		}
		$left_join_order_items_meta = join( "  ", $left_join_order_items_meta );


		// final sql from WC tables
		$order_items_where = "";
		if ( $order_items_meta_where ) {
			$order_items_where = " AND orders.ID IN (SELECT DISTINCT order_items.order_id FROM {$wpdb->prefix}woocommerce_order_items as order_items
				$left_join_order_items_meta
				WHERE order_item_type='line_item' $order_items_meta_where )";
		}

		// by coupons
		if ( ! empty( $settings['any_coupon_used'] ) ) {
			$order_items_where .= " AND orders.ID IN (SELECT DISTINCT order_coupons.order_id FROM {$wpdb->prefix}woocommerce_order_items as order_coupons
					WHERE order_coupons.order_item_type='coupon')";
		}
		elseif ( ! empty( $settings['coupons'] ) ) {
			$values = self::sql_subset( $settings['coupons'] );
			$order_items_where .= " AND orders.ID IN (SELECT DISTINCT order_coupons.order_id FROM {$wpdb->prefix}woocommerce_order_items as order_coupons
					WHERE order_coupons.order_item_type='coupon'  AND order_coupons.order_item_name in ($values) )";
		}
		// shipping methods
		if ( ! empty( $settings['shipping_methods'] ) ) {
			$like_values = array();
			foreach($settings['shipping_methods'] as $value)
				$like_values[] = "(shipping_itemmeta.meta_value  LIKE '$value%') ";
			$like_values = join( " OR ", $like_values);
			$order_items_where .= " AND orders.ID IN (SELECT order_shippings.order_id FROM {$wpdb->prefix}woocommerce_order_items as order_shippings
						LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS shipping_itemmeta ON  shipping_itemmeta.order_item_id = order_shippings.order_item_id
						WHERE order_shippings.order_item_type='shipping' AND  shipping_itemmeta.meta_key='method_id' AND ( $like_values ) )";
		}

		// pre top
		$left_join_order_meta = $order_meta_where = array();
		//add filter by custom fields in order

		if( $settings[ 'export_unmarked_orders' ] ) {
			$pos = "export_unmarked_orders";
			$field = "woe_order_exported";
			$left_join_order_meta[] = "LEFT JOIN {$wpdb->postmeta} AS ordermeta_cf_{$pos} ON ordermeta_cf_{$pos}.post_id = orders.ID AND ordermeta_cf_{$pos}.meta_key='$field'";
			$order_meta_where []    = " ( ordermeta_cf_{$pos}.meta_value IS NULL ) ";
		}

		if ( $settings['order_custom_fields'] ) {
			$cf_names = self::get_order_custom_fields();
			$filters  = self::parse_complex_pairs( $settings['order_custom_fields'], $cf_names);
			$pos=1;
			foreach ( $filters as $operator => $fields) {
				foreach ( $fields as $field => $values ) {
					if ( $values ) {
						$left_join_order_meta[] = "LEFT JOIN {$wpdb->postmeta} AS ordermeta_cf_{$pos} ON ordermeta_cf_{$pos}.post_id = orders.ID";
						if( $operator == 'IN' OR $operator == 'NOT IN' ) {
							$values = self::sql_subset( $values );
							$order_meta_where []    = " (ordermeta_cf_{$pos}.meta_key='$field'  AND ordermeta_cf_{$pos}.meta_value $operator ($values)) ";
						} elseif( $operator =='LIKE' ) {
							$pairs = array();
							foreach($values as $v)
								$pairs[] = " ordermeta_cf_{$pos}.meta_value LIKE '$v' ";
							$pairs = join("OR", $pairs);
							$order_meta_where[]     = " (ordermeta_cf_{$pos}.meta_key='$field'  AND  ($pairs) ) ";
						}
						$pos++;
					}//if values
				}
			}
		}
		if ( $settings['shipping_locations'] ) {
			$filters              = self::parse_complex_pairs( $settings['shipping_locations'],
				array( 'city', 'state', 'postcode', 'country' ), 'lower_filter_label' );
			foreach ( $filters as $operator => $fields) {
				foreach ( $fields as $field => $values ) {
					$values = self::sql_subset( $values );
					if ( $values ) {
						$left_join_order_meta[] = "LEFT JOIN {$wpdb->postmeta} AS ordermeta_{$field} ON ordermeta_{$field}.post_id = orders.ID";
						$order_meta_where []    = " (ordermeta_{$field}.meta_key='_shipping_$field'  AND ordermeta_{$field}.meta_value $operator ($values)) ";
					}
				}
			}
		}
        if ( $settings['billing_locations'] ) {
            $filters              = self::parse_complex_pairs( $settings['billing_locations'],
                array( 'city', 'state', 'postcode', 'country' ), 'lower_filter_label' );
            foreach ( $filters as $operator => $fields) {
                foreach ( $fields as $field => $values ) {
                    $values = self::sql_subset( $values );
                    if ( $values ) {
                        $left_join_order_meta[] = "LEFT JOIN {$wpdb->postmeta} AS ordermeta_{$field} ON ordermeta_{$field}.post_id = orders.ID";
                        $order_meta_where []    = " (ordermeta_{$field}.meta_key='_billing_$field'  AND ordermeta_{$field}.meta_value $operator ($values)) ";
                    }
                }
            }
        }

		// users
		$user_ids = array();
		if ( !empty($settings['user_names'] ) )
			$user_ids = array_filter( array_map( "intval", $settings['user_names'] ) );
		//roles
		if ( !empty($settings['user_roles'] ) ) {
			foreach( $settings['user_roles'] as $role) {
				//seek by role
				foreach( get_users( 'fields=ID&role=' . $role ) as $user_id )
					$user_ids[] = intval( $user_id );
				$user_ids = array_unique( $user_ids );
			}
		}
		$user_ids = apply_filters("woe_sql_get_customer_ids", $user_ids, $settings);
		//apply filter
		if ( $user_ids ) {
			$field = 'customer_user';
			$values = self::sql_subset( $user_ids );
			if( $values ) {
				$left_join_order_meta[] = "LEFT JOIN {$wpdb->postmeta} AS ordermeta_{$field} ON ordermeta_{$field}.post_id = orders.ID";
				$order_meta_where []    = " (ordermeta_{$field}.meta_key='_customer_user'  AND ordermeta_{$field}.meta_value in ($values)) ";
			}
		}

		// payment methods
		if ( ! empty( $settings['payment_methods'] ) ) {
			$field  = 'payment_method';
			$values = self::sql_subset( $settings['payment_methods'] );

			$left_join_order_meta[] = "LEFT JOIN {$wpdb->postmeta} AS ordermeta_{$field} ON ordermeta_{$field}.post_id = orders.ID";
			$order_meta_where []    = " (ordermeta_{$field}.meta_key='_{$field}'  AND ordermeta_{$field}.meta_value in ($values)) ";
		}

		$order_meta_where = join( " AND ", apply_filters( "woe_sql_get_order_ids_order_meta_where" , $order_meta_where ) );
		if ( $order_meta_where ) {
			$order_meta_where = " AND " . $order_meta_where;
		}
		$left_join_order_meta = join( "  ", apply_filters( "woe_sql_get_order_ids_left_joins" , $left_join_order_meta ) );


		//top_level
		$where = array( 1 );
		self::apply_order_filters_to_sql( $where, $settings );
		$where     = apply_filters( 'woe_sql_get_order_ids_where', $where, $settings );
		$order_sql = join( " AND ", $where );

		//setup order types to work with
		$order_types = array( "'" . self::$object_type . "'" );
		if($settings['export_refunds'])
			$order_types[] =  "'shop_order_refund'";
		$order_types  = join( ",", apply_filters( "woe_sql_order_types", $order_types ) );

		$sql = "SELECT " . apply_filters( "woe_sql_get_order_ids_fields", "ID AS order_id" ) . " FROM {$wpdb->posts} AS orders
			{$left_join_order_meta}
			WHERE orders.post_type in ( $order_types) AND $order_sql $order_meta_where $order_items_where";
		//die($sql);
		return $sql;
	}

	private static function add_date_filter(&$where, &$where_meta, $date_field, $value) {
		if( $date_field == 'date_paid' OR $date_field == 'date_completed') // 3.0+ uses timestamp
			$where_meta[] = "(order_$date_field.meta_value>0 AND FROM_UNIXTIME(order_$date_field.meta_value) " . $value.")";
		elseif( $date_field == 'paid_date' OR $date_field == 'completed_date') // previous versions use mysql datetime
			$where_meta[] = "(order_$date_field.meta_value<>'' AND order_$date_field.meta_value " . $value.")";
		else
			$where[] = "orders.post_" . $date_field . $value;
	}

	private static function apply_order_filters_to_sql(&$where,$settings) {
		global $wpdb;
		//default filter by date
		if( ! isset($settings[ 'export_rule_field' ]) )
			$settings[ 'export_rule_field' ] = 'modified';

		$date_field = $settings[ 'export_rule_field' ];
		//rename this field for 2.6 and less
		if( !method_exists( 'WC_Order', "get_date_completed") ) {
			if( $date_field == 'date_paid')
				$date_field = 'paid_date';
			elseif( $date_field == 'date_completed')
				$date_field = 'completed_date';
		}
		$where_meta = array();


		if ( $settings['from_date'] ) {
			$from_date = date( 'Y-m-d', strtotime( $settings['from_date'] ) );
			if ( $from_date ) {
				self::add_date_filter($where, $where_meta, $date_field, ">='$from_date 00:00:00'" );
			}
		}
		if ( $settings['to_date'] ) {
			$to_date = date( 'Y-m-d', strtotime( $settings['to_date'] ) );
			if ( $to_date ) {
				self::add_date_filter($where, $where_meta, $date_field, "<='$to_date 23:59:59'" );
			}
		}
		if ( $settings['statuses'] ) {
			$values = self::sql_subset( $settings['statuses'] );
			if ( $values ) {
				$where[] = "orders.post_status in ($values)";
			}
		}

		//export rule
		$_time = current_time( "timestamp", 0 );

		//skip rules if data range defined
		if( ! isset($settings[ 'export_rule' ])/* OR $settings['from_date'] OR $settings['to_date']*/ )
			$settings[ 'export_rule' ] = '';

		switch ( $settings[ 'export_rule' ] ) {
			case "last_run":
				if ( !empty( $settings[ 'schedule' ][ 'last_run' ] ) ) {
					self::add_date_filter($where, $where_meta, $date_field, ' >= "' . date( 'Y-m-d H:i:s', $settings[ 'schedule' ][ 'last_run' ] ) . '"' );
				}
				break;
			case "today":
				$_date		 = date( 'Y-m-d', $_time );
				self::add_date_filter($where, $where_meta, $date_field,  ">='$_date 00:00:00'" );
				self::add_date_filter($where, $where_meta, $date_field,  "<='$_date 23:59:59'" );
				break;
			case "this_week":
				$day		 = date( 'w', $_time );
				$_date		 = date( 'Y-m-d', $_time );
				$week_start	 = date( 'Y-m-d', strtotime( $_date . ' -' . $day . ' days' ) );
				$week_end	 = date( 'Y-m-d', strtotime( $_date . ' +' . (6 - $day) . ' days' ) );
				self::add_date_filter($where, $where_meta, $date_field,  ">='$week_start 00:00:00'" );
				self::add_date_filter($where, $where_meta, $date_field,  "<='$week_end 23:59:59'" );
				break;
			case "this_month":
				$month_start = date( 'Y-m-01', $_time );
				$month_end	 = date( 'Y-m-t', $_time );
				self::add_date_filter($where, $where_meta, $date_field,  ">='$month_start 00:00:00'" );
				self::add_date_filter($where, $where_meta, $date_field,  "<='$month_end 23:59:59'" );
				break;
			case "last_day":
				$_date		 = date( 'Y-m-d', $_time );
				$last_day    = strtotime($_date." -1 day");
				$_date		 = date( 'Y-m-d', $last_day );
				self::add_date_filter($where, $where_meta, $date_field,  ">='$_date 00:00:00'" );
				self::add_date_filter($where, $where_meta, $date_field,  "<='$_date 23:59:59'" );
				break;
			case "last_week":
				$day		 = date( 'w', $_time );
				$_date		 = date( 'Y-m-d', $_time );
				$last_week    = strtotime($_date." -1 week");
				$week_start	 = date( 'Y-m-d', strtotime( date( 'Y-m-d', $last_week ) . ' -' . $day . ' days' ) );
				$week_end	 = date( 'Y-m-d', strtotime( date( 'Y-m-d', $last_week ) . ' +' . (6 - $day) . ' days' ) );
				self::add_date_filter($where, $where_meta, $date_field,  ">='$week_start 00:00:00'" );
				self::add_date_filter($where, $where_meta, $date_field,  "<='$week_end 23:59:59'" );
				break;
			case "last_month":
				$_date		 = date( 'Y-m-d', $_time );
				$last_month    = strtotime($_date." -1 month");
				$month_start = date( 'Y-m-01', $last_month );
				$month_end	 = date( 'Y-m-t', $last_month );
				self::add_date_filter($where, $where_meta, $date_field,  ">='$month_start 00:00:00'" );
				self::add_date_filter($where, $where_meta, $date_field,  "<='$month_end 23:59:59'" );
				break;
			case "last_quarter":
				$_date		 = date( 'Y-m-d', $_time );
				$last_month    = strtotime($_date." -3 month");
				$quarter_start = date( 'Y-'.  self::get_quarter_month($last_month).'-01', $last_month );
				$quarter_end = date( 'Y-'.  (self::get_quarter_month($last_month)+2).'-31', $last_month );
				self::add_date_filter($where, $where_meta, $date_field,  ">='$quarter_start 00:00:00'" );
				self::add_date_filter($where, $where_meta, $date_field,  "<='$quarter_end 23:59:59'" );
				break;
			case "this_year":
				$year_start  = date( 'Y-01-01', $_time );
				self::add_date_filter($where, $where_meta, $date_field,  ">='$year_start 00:00:00'" );
				break;
			case "custom":
				if ( isset( $settings[ 'export_rule_custom' ] ) && ($settings[ 'export_rule_custom' ]) ) {
					$day_start	 = date( 'Y-m-d', strtotime( date( 'Y-m-d', $_time ) . ' -' . intval( $settings[ 'export_rule_custom' ] ) . ' days' ) );
					$day_end	 = date( 'Y-m-d', $_time );
					self::add_date_filter($where, $where_meta, $date_field,  ">='$day_start 00:00:00'" );
					self::add_date_filter($where, $where_meta, $date_field,  "<='$day_end 23:59:59'" );
				}
				break;
			default:
				break;
		}
		//end export rule

		//for date_paid or date_completed
		if( $where_meta ) {
			$where_meta  = join( " AND ", $where_meta );
			$where[] = "orders.id  IN ( SELECT post_id FROM {$wpdb->postmeta} AS order_$date_field WHERE order_$date_field.meta_key ='_$date_field' AND $where_meta)";
		}

		// skip child orders?
		if( $settings['skip_suborders'] AND !$settings['export_refunds'])
			$where[] = "orders.post_parent=0";

		// Skip drafts and deleted
		$where[] = "orders.post_status NOT in ('auto-draft','trash')";
	}

	public static function get_quarter_month( $time ) {
		$month = date( "m", $time );
		if ( $month <= 3 )
			return 1;
		if ( $month <= 6 )
			return 4;
		if ( $month <= 9 )
			return 7;

		return 10;
	}

	public static function prepare_for_export() {
		self::$statuses  = wc_get_order_statuses();
		self::$countries = WC()->countries->countries;
		self::$prices_include_tax = get_option('woocommerce_prices_include_tax') == 'yes' ? true : false;
		self::$decimal_separator = wc_get_price_decimal_separator();
		self::$thousands_separator = apply_filters( 'woe_thousands_separator', '' );
		self::$decimals = wc_get_price_decimals();
	}

	public static function get_max_order_items( $type, $ids ) {
		global $wpdb;

		$ids[] = 0; // for safe
		$ids   = join( ",", $ids );

		$sql = "SELECT COUNT( * ) AS t
			FROM  `{$wpdb->prefix}woocommerce_order_items`
			WHERE order_item_type =  '$type'
			AND order_id
			IN ( $ids)
			GROUP BY order_id
			ORDER BY t DESC
			LIMIT 1";

		$max = $wpdb->get_var( $sql );
		if ( ! $max ) {
			$max = 1;
		}

		return $max;
	}

	public static function fetch_order_coupons(
		$order,
		$labels,
		$format,
		$filters_active,
		$get_coupon_meta,
		$static_vals,
		$format_number_fields
	) {
		global $wpdb;
		$coupons = array();
		foreach ( $order->get_items( 'coupon' ) as $item ) {
			$coupon_meta = array();
			if ( $get_coupon_meta ) {
				$recs = $wpdb->get_results( $wpdb->prepare( "SELECT meta_value,meta_key FROM {$wpdb->postmeta} AS meta
					JOIN {$wpdb->posts} AS posts ON posts.ID = meta.post_id
					WHERE posts.post_title=%s", $item['name'] ) );
				foreach ( $recs as $rec ) {
					$coupon_meta[ $rec->meta_key ] = $rec->meta_value;
				}
			}

			$row = array();
			foreach ( $labels as $field => $label ) {
				if ( isset( $item[ $field ] ) ) {
					$row[ $field ] = $item[ $field ];
				} elseif ( $field == 'code' ) {
					$row['code'] = $item["name"];
				} elseif ( $field == 'discount_amount_plus_tax' ) {
					$row['discount_amount_plus_tax'] = $item["discount_amount"] +  $item["discount_amount_tax"];
				} elseif ( isset( $coupon_meta[ $field ] ) ) {
					$row[ $field ] = $coupon_meta[ $field ];
				} elseif ( isset( $static_vals[ $field ] ) ) {
					$row[ $field ] = $static_vals[ $field ];
				} else {
					$row[ $field ] = '';
				}

				if ( isset( $filters_active[ $field ] ) ) {
					$row[ $field ] = apply_filters( "woe_get_order_coupon_value_{$field}", $row[ $field ], $order, $item );
					$row[ $field ] = apply_filters( "woe_get_order_coupon_{$format}_value_{$field}", $row[ $field ], $order, $item );
				}

                if ($field == 'excerpt') {
                    $post = get_page_by_title( $item['name'], OBJECT, 'shop_' . $item['type'] );
                    $row[ $field ] = $post? $post->post_excerpt : '';
                }
				if( $format_number_fields )
					$row[ $field ] = self::format_numbers('order_coupon', $row[ $field ], $field);
			}
			$row = apply_filters('woe_fetch_order_coupon', $row, $item, $coupon_meta);
			if( $row )
				$coupons[] = $row;
		}

		return apply_filters( "woe_fetch_order_coupons", $coupons, $order, $labels, $format, $static_vals );
	}


	/**
	 * @param WC_Order $order
	 * @param $labels
	 * @param $format
	 * @param $filters_active
	 * @param $static_vals
	 * @param $export_only_products
	 * @param $export_refunds
	 *
	 * @return array
	 */
	public static function fetch_order_products( $order, $labels, $format, $filters_active, $static_vals , $export_only_products, $export_refunds, $skip_refunded_items, $strip_tags_product_fields, $format_number_fields ) {
		$product_fields_with_tags = array( 'product_variation', 'post_content', 'post_excerpt');
		$products = array();
		$i = 0;
		foreach ( $order->get_items('line_item') as $item_id=>$item ) {
			do_action( "woe_get_order_product_item", $item );
			if( $export_refunds  AND $item['qty'] == 0 ) // skip zero items, when export refunds
				continue;
			// we export only matched products?
			if( $export_only_products AND !in_array($item['product_id'], $export_only_products ) AND !in_array($item['variation_id'], $export_only_products ) )
				continue;

			$product   = $order->get_product_from_item( $item );
			$product = apply_filters( "woe_get_order_product", $product );
			$item_meta = get_metadata( 'order_item', $item_id );
            foreach($item_meta as $key=>$value) {
                $clear_key = wc_sanitize_taxonomy_name( $key );
                if ( taxonomy_exists( $clear_key ) ) {
					$term               = get_term_by( 'slug', $value[0], $clear_key );
                    $item_meta[$key][0] = isset( $term->name ) ? $term->name : $value[0];
                    if (strpos($key, 'attribute_') === false)
                        $item_meta['attribute_' . $key][0] = isset( $term->name ) ? $term->name : $value[0];
				}
            }
			$item_meta = apply_filters( "woe_get_order_product_item_meta", $item_meta );
			$product = apply_filters( "woe_get_order_product_and_item_meta", $product , $item_meta );
			if( $product ) {
				if( method_exists($product,'get_id') ) {
					if ( $product->is_type( 'variation' ) )
						$product_id = method_exists($product,'get_parent_id') ? $product->get_parent_id() : $product->parent->id;
					else
						$product_id = $product->get_id();
					$post = get_post( $product_id );
				} else {	// legacy
					$product_id  =  $product->id;
					$post   = $product->post;
				}
			} else {
				$product_id = 0;
				$post  = false;
			}

			// skip based on products/items/meta
			if( apply_filters('woe_skip_order_item', false, $product, $item, $item_meta, $post) )
				continue;

			if( $skip_refunded_items ) {
				$qty_minus_refund = $item_meta["_qty"][0] + $order->get_qty_refunded_for_item( $item_id ); // Yes we add negative! qty
				if( $qty_minus_refund <= 0 )
					continue;
			}

			$i++;
			$row       = array();
			foreach ( $labels as $field => $label ) {
				if ( strpos( $field, '__' ) !== false && $taxonomies = wc_get_product_terms( $item['product_id'],
						substr( $field, 2 ), array( 'fields' => 'names' ) )
				) {
					$row[ $field ] = implode( ', ', $taxonomies );
				} else if ( $field == 'product_shipping_class' ) {
					if( $taxonomies = wc_get_product_terms( $item['product_id'], $field, array( 'fields' => 'names' ) ) )
						$row[ $field ] = implode( ', ', $taxonomies );
					else
						$row[ $field ] = ""; // unknown class
				} elseif ( isset( $item_meta[ $field ] ) ) {    //meta from order
					$row[ $field ] = $item_meta[ $field ][0];
				} elseif ( isset( $item_meta[ "_" . $field ] ) ) {// or hidden field
					$row[ $field ] = $item_meta[ "_" . $field ][0];
				} elseif ( isset( $item['item_meta'][ $field ] ) ) {  // meta from item line
					$row[ $field ] = $item['item_meta'][ $field ][0];
				} elseif ( isset( $item['item_meta'][ "_" . $field ] ) ) { // or hidden field
					$row[ $field ] = $item['item_meta'][ "_" . $field ][0];
				} elseif ( $field == 'name' ) {
					$row['name'] = $item["name"];
				} elseif ( $field == 'product_variation' ) {
					$row['product_variation'] = self::get_product_variation( $item, $order, $item_id, $product );
				} elseif ( $field == 'seller' ) {
					$row[ $field ] = '';
					if( $post ) {
						$user = get_userdata( $post->post_author );
						$row[ $field ] = ! empty( $user->display_name ) ? $user->display_name : '';
					}
				} elseif ( $field == 'post_content' ) {
					$row[ $field ] = $post ? $post->post_content : '';
				} elseif ( $field == 'post_excerpt' ) {
					$row[ $field ] = $post ? $post->post_excerpt: '';
				} elseif ( $field == 'type' ) {
					$row[ $field ] = '';
					if( $product )
						$row[ $field ] =  method_exists($product,'get_type') ? $product->get_type() : $product->product_type;
				} elseif ( $field == 'tags' ) {
					$terms       = get_the_terms( $product_id, 'product_tag' );
					$row['tags'] = array();
					if ( $terms ) {
						foreach ( $terms as $term ) {
							$row['tags'][] = $term->name;
						}
					}
					$row['tags'] = join( ",", $row['tags'] );
				} elseif ( $field == 'category' ) {
					$terms           = get_the_terms( $product_id, 'product_cat' );
					$row['category'] = array();
					if ( $terms ) {
						foreach ( $terms as $term ) {
							$row['category'][] = $term->name;
						}
					}
					$row['category'] = join( ",", $row['category'] );// hierarhy ???
				} elseif ( $field == 'line_no_tax' ) {
					$row['line_no_tax'] = self::$prices_include_tax? ($item_meta["_line_total"][0] - $item_meta["_line_tax"][0]) : $item_meta["_line_total"][0];
				//item refund
				} elseif ( $field == 'line_total_refunded' ) {
					$row['line_total_refunded'] = $order->get_total_refunded_for_item( $item_id );
				} elseif ( $field == 'line_total_minus_refund' ) {
					$row['line_total_minus_refund'] = $item_meta["_line_total"][0] - $order->get_total_refunded_for_item( $item_id );
				} elseif ( $field == 'qty_minus_refund' ) {
					$row['qty_minus_refund'] = $item_meta["_qty"][0] + $order->get_qty_refunded_for_item( $item_id ); // Yes we add negative! qty
				//tax refund
				} elseif ( $field == 'line_tax_refunded' ) {
					$row['line_tax_refunded'] = self::get_order_item_taxes_refund($order, $item_id );
				} elseif ( $field == 'line_tax_minus_refund' ) {
					$row['line_tax_minus_refund'] = $item_meta["_line_tax"][0] - self::get_order_item_taxes_refund($order, $item_id );
				} elseif ( $field == 'line_id' ) {
					$row[ $field ] = $i;
				} elseif ( $field == 'item_price' ) {
					$row[ $field ] = $order->get_item_total( $item, false, true ); // YES we have to calc item price
				} elseif ( $field == 'download_url' ) {
					$row[ $field ] = '';
					if ( $product AND $product->is_downloadable() ) {
						$files = get_post_meta( $product_id, '_downloadable_files', true );
						$links = array();
						if( $files  ) {
							foreach ( $files as $file )
								$links[] = $file['file'];
						}
						$row[ $field ] = implode( "\n", $links );
					}
				} elseif ( $field == 'image_url' ) {
					$row[ $field ] =  ($product AND $product->get_image_id() ) ? current( wp_get_attachment_image_src( $product->get_image_id(), 'full') ) : ''; // make full url
				} elseif ( isset( $static_vals[ $field ] ) ) {
					$row[ $field ] = $static_vals[ $field ];
				} else {
					$row[ $field ]  = '';
					if( !empty( $item['variation_id'] ) )
						$row[ $field ] = get_post_meta( $item['variation_id'], $field, true );
					if($row[ $field ] === '' ) // empty value ? try get custom!
						$row[ $field ] = get_post_meta( $product_id, $field, true );
					if($row[ $field ] === '' ) // empty value ?
						$row[ $field ] = method_exists($product,'get_'.$field) ? $product->{'get_'.$field}() : get_post_meta( $product_id, '_' . $field, true );
					if($row[ $field ] === '' AND empty( $item['variation_id'] ) ) // empty value ? try get attribute for !variaton
						$row[ $field ] = $product ? $product->get_attribute( $field ) : '';
				}

				if($strip_tags_product_fields AND in_array($field,$product_fields_with_tags) ) {
					$row[$field] = strip_tags( $row[$field] );
				}

				if( $format_number_fields )
					$row[ $field ] = self::format_numbers('order_product', $row[ $field ], $field);

				if ( isset($row[ $field ] ) ) {
					$row[ $field ] = apply_filters( "woe_get_order_product_value_{$field}", $row[ $field ] , $order, $item, $product, $item_meta);
					$row[ $field ] = apply_filters( "woe_get_order_product_{$format}_value_{$field}", $row[ $field ] , $order, $item, $product, $item_meta);
				}
			}
			$row = apply_filters( 'woe_fetch_order_product', $row, $order, $item, $product, $item_meta);
			if( $row )
				$products[] = $row;
		}
		return apply_filters( "woe_fetch_order_products", $products, $order, $labels, $format, $static_vals );
	}

	public static function get_order_item_taxes_refund($order, $item_id) {
		$tax_refund = 0;
		$order_taxes = $order->get_taxes();
		foreach ( $order_taxes as $tax_item ) {
			$tax_item_id       = $tax_item['rate_id'];
			$tax_refund += $order->get_tax_refunded_for_item( $item_id, $tax_item_id );
		}
		return $tax_refund;
	}

	public static function fetch_order_data(
		$order_id,
		$labels,
		$format,
		$filters_active,
		$csv_max,
		$export,
		$get_coupon_meta,
		$static_vals,
		$options
	) {
		global $wpdb;
		global $wp_roles;

		$extra_rows = array();
		$row        = array();
		$user_lang = get_post_meta( $order_id, '_user_language', true );

		//$order_id = 390;

		// get order meta
		$order_meta = array();
		$recs       = $wpdb->get_results( "SELECT meta_value,meta_key FROM {$wpdb->postmeta} WHERE post_id=$order_id" );
		foreach ( $recs as $rec ) {
			$order_meta[ $rec->meta_key ] = $rec->meta_value;
		}

		// take order
		self::$current_order = $order = new WC_Order( $order_id );

		if ( $export['products'] OR isset( $labels['order']['count_unique_products'] ) OR isset( $labels['order']['total_weight_items'] ) ) {
			$temp = $labels['products'];
			$temp['qty'] = '';
			$temp['weight'] = '';
			$data['products'] = self::fetch_order_products( $order, $temp, $format,
				$filters_active['products'], $static_vals['products'], $options['include_products'],
				$options['export_refunds'] , $options['skip_refunded_items'], $options['strip_tags_product_fields'], $options['format_number_fields'] );
		}
		if ( $export['coupons'] OR isset( $labels['order']['coupons_used'] ) ) {
			$data['coupons'] = self::fetch_order_coupons( $order, $labels['coupons'], $format,
				$filters_active['coupons'], $get_coupon_meta, $static_vals['coupons'],$options['format_number_fields'] );
		}

		$must_adjust_extra_rows = array();

		$date_fields = self::get_order_fields_as_type( $labels['order'], 'date' );
		self::$date_format = $date_format = trim( $options['date_format'] . ' ' . $options['time_format'] );

		// add fields for WC 3.0
		foreach( array( "billing_country","billing_state","shipping_country","shipping_state") as $field_30 ) {
			$$field_30 = method_exists($order,'get_'.$field_30) ? $order->{'get_'.$field_30}() : $order->$field_30;
		}

		$parent_order_id = method_exists($order,'get_parent_id') ? $order->get_parent_id() : $order->post->post_parent;
		$parent_order = $parent_order_id ? new WC_Order($parent_order_id) : false;
		$post   = method_exists($order,'get_id') ? get_post($order->get_id()) : $order->post;

		// correct meta for child orders
		if( $parent_order_id ) {
			// overwrite child values for refunds
			$is_refund = ($post->post_type == 'shop_order_refund') ;
			$overwrite_child_order_meta = apply_filters( 'woe_overwrite_child_order_meta',  $is_refund ) ;
			$recs       = $wpdb->get_results( "SELECT meta_value,meta_key FROM {$wpdb->postmeta} WHERE post_id=$parent_order_id" );
			foreach ( $recs as $rec ) {
				if( $overwrite_child_order_meta OR !isset( $order_meta[ $rec->meta_key ] ) )
					$order_meta[ $rec->meta_key ] = $rec->meta_value;
			}

			//refund rewrites it
			if ( $overwrite_child_order_meta ) {
				foreach( array( "billing_country","billing_state","shipping_country","shipping_state") as $field_30 ) {
					$$field_30 = method_exists($parent_order,'get_'.$field_30) ? $parent_order->{'get_'.$field_30}() : $parent_order->$field_30;
				}
			}
			//refund status
			if( $is_refund )
				$order_status = 'refunded';
		}

		// extra WP_User
		$user = ! empty( $order_meta['_customer_user'] ) ? get_userdata( $order_meta['_customer_user'] ) : false;
		// setup missed fields for full addresses
		foreach( array( '_billing_address_2', '_shipping_address_2' ) as $optional_field) {
			if( !isset($order_meta[$optional_field]) )
				$order_meta[$optional_field]  = '';
		}

		// fill as it must
		foreach ( $labels['order'] as $field => $label ) {
			if(substr($field,0,5) == "USER_") { //user field
				$key = substr($field,5);
				$row[ $field ] = $user ? $user->get($key) : '';
			} elseif ( $field == 'order_id' ) {
				$row['order_id'] = $order_id;
			} elseif ( $field == 'order_date' ) {
				$row['order_date'] = !method_exists( $order, "get_date_created") ? $order->order_date : ( $order->get_date_created() ? gmdate( 'Y-m-d H:i:s', $order->get_date_created()->getOffsetTimestamp() ) : '' ) ;
			} elseif ( $field == 'modified_date' ) {
				$row['modified_date'] = !method_exists( $order, "get_date_modified") ? $order->modified_date : ( $order->get_date_modified() ? gmdate( 'Y-m-d H:i:s', $order->get_date_modified()->getOffsetTimestamp() ) : '' ) ;
			} elseif ( $field == 'completed_date' ) {
				$row['completed_date'] = !method_exists( $order, "get_date_completed") ? $order->completed_date : ( $order->get_date_completed() ? gmdate( 'Y-m-d H:i:s', $order->get_date_completed()->getOffsetTimestamp() ) : '' ) ;
			} elseif ( $field == 'paid_date' ) {
				$row['paid_date'] = !method_exists( $order, "get_date_paid") ? $order->paid_date : ( $order->get_date_paid() ? gmdate( 'Y-m-d H:i:s', $order->get_date_paid()->getOffsetTimestamp() )  : '' ) ;
			} elseif ( $field == 'order_number' ) {
				$row['order_number'] = $parent_order ? $parent_order->get_order_number() : $order->get_order_number(); // use parent order number
			} elseif ( $field == 'order_subtotal' ) {
				$row['order_subtotal'] = wc_format_decimal( $order->get_subtotal(), 2);
			} elseif ( $field == 'order_subtotal_refunded' ) {
				$row['order_subtotal_refunded'] = wc_format_decimal( self::get_order_subtotal_refunded($order), 2);
			} elseif ( $field == 'order_subtotal_minus_refund' ) {
				$row['order_subtotal_minus_refund'] = wc_format_decimal( $order->get_subtotal()  - self::get_order_subtotal_refunded($order), 2);
			//order total
			} elseif ( $field == 'order_total' ) {
				$row['order_total'] = $order->get_total();
			} elseif ( $field == 'order_total_no_tax' ) {
				$row['order_total_no_tax'] = $order->get_total() - $order->get_total_tax();
			} elseif ( $field == 'order_refund' ) {
				$row['order_refund'] = $order->get_total_refunded();
			} elseif ( $field == 'order_total_inc_refund' ) {
				$row['order_total_inc_refund'] = $order->get_total() - $order->get_total_refunded();
			//shipping
			} elseif ( $field == 'order_shipping' ) {
				$row['order_shipping'] = $order->get_total_shipping();
			} elseif ( $field == 'order_shipping_refunded' ) {
				$row['order_shipping_refunded'] = $order->get_total_shipping_refunded();
			} elseif ( $field == 'order_shipping_minus_refund' ) {
				$row['order_shipping_minus_refund'] = $order->get_total_shipping() - $order->get_total_shipping_refunded();
			//shipping tax
			} elseif ( $field == 'order_shipping_tax_refunded' ) {
				$row['order_shipping_tax_refunded'] = self::get_order_shipping_tax_refunded( $order_id );
			} elseif ( $field == 'order_shipping_tax_minus_refund' ) {
				$row['order_shipping_tax_minus_refund'] = $order->get_shipping_tax() - self::get_order_shipping_tax_refunded( $order_id );
			//order tax
			} elseif ( $field == 'order_tax' ) {
				$row['order_tax'] = wc_round_tax_total( $order->get_cart_tax() );
			} elseif ( $field == 'order_total_tax' ) {
				$row['order_total_tax'] = wc_round_tax_total( $order->get_total_tax() );
			} elseif ( $field == 'order_total_tax_refunded' ) {
				$row['order_total_tax_refunded'] = wc_round_tax_total( $order->get_total_tax_refunded() );
			} elseif ( $field == 'order_total_tax_minus_refund' ) {
				$row['order_total_tax_minus_refund'] = wc_round_tax_total( $order->get_total_tax() - $order->get_total_tax_refunded() );
			} elseif ( $field == 'order_status' ) {
				$status              = empty($order_status) ? $order->get_status() : $order_status ;
				$status              = 'wc-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;
				$row['order_status'] = isset( self::$statuses[ 'wc-' . $status ] ) ? self::$statuses[ 'wc-' . $status ] : $status;
			} elseif ( $field == 'user_login' OR $field == 'user_email' ) {
				$row[ $field ] = $user ? $user->$field : "";
			} elseif ( $field == 'user_role' ) {
				$row[ $field ] = ( isset($user->roles[0]) && isset($wp_roles->roles[$user->roles[0]]) ) ? $wp_roles->roles[$user->roles[0]]['name'] : ""; // take first role Name
			} elseif ( $field == 'billing_address' ) {
				$row[ $field ] = join(", ", array_filter( array( $order_meta["_billing_address_1"] ,  $order_meta["_billing_address_2"] ) ) );
			} elseif ( $field == 'shipping_address' ) {
				$row[ $field ] = join(", ", array_filter( array( $order_meta["_shipping_address_1"] ,  $order_meta["_shipping_address_2"] ) ) );
			} elseif ( $field == 'billing_full_name' ) {
				$row[ $field ] = trim( $order_meta["_billing_first_name"] . ' ' . $order_meta["_billing_last_name"] );
			} elseif ( $field == 'shipping_full_name' ) {
				$row[ $field ] = trim( $order_meta["_shipping_first_name"] . ' ' . $order_meta["_shipping_last_name"] );
			} elseif ( $field == 'billing_country_full' ) {
				$row[ $field ] = isset( self::$countries[ $billing_country ] ) ? self::$countries[ $billing_country ] : $billing_country;
			} elseif ( $field == 'shipping_country_full' ) {
				$row[ $field ] = isset( self::$countries[ $shipping_country ] ) ? self::$countries[ $shipping_country ] : $shipping_country;
			} elseif ( $field == 'billing_state_full' ) {
				$country_states = WC()->countries->get_states( $billing_country );
				$row[ $field ] = isset( $country_states[ $billing_state ]) ? html_entity_decode( $country_states[ $billing_state ] ) : $billing_state;
			} elseif ( $field == 'shipping_state_full' ) {
				$country_states = WC()->countries->get_states( $shipping_country );
				$row[ $field ] = isset( $country_states[ $shipping_state ]) ? html_entity_decode( $country_states[ $shipping_state ] ) : $shipping_state;
			} elseif ( $field == 'products' OR $field == 'coupons' ) {
				if ( $format == 'xls' OR $format == 'csv' OR $format == 'tsv' ) {
					if ( $csv_max[ $field ] == 1 ) {
						//print_r(array_values($row));die();
						// don't refill columns from parent row!
						//echo count($row)."-".(count($row)+count($labels[$field])-1)."|";
						if ( @$options['populate_other_columns_product_rows'] ) {
							$must_adjust_extra_rows = array_merge( $must_adjust_extra_rows,
								range( count( $row ), count( $row ) + count( $labels[ $field ] ) - 1 ) );
						}
						$items = apply_filters('woe_get_'. $field .'_items_for_' . $format.'_rows', $data[ $field ],$order);
						//print_r($items);						die('woe_get_'. $field .'_items_for_' . $format.'_rows');
						self::csv_process_multi_rows( $row, $extra_rows, $items, $labels[ $field ], $options['item_rows_start_from_new_line'] );
					} else {
						$items = apply_filters('woe_get_'. $field .'_items_for_' . $format.'_cols', $data[ $field ],$order);
						self::csv_process_multi_cols( $row, $items, $labels[ $field ], $csv_max[ $field ] );
					}
				} else {
					$row[ $field ] = $data[ $field ];
				}
			} elseif ( $field == 'shipping_method_title' ) {
				$row[ $field ] = $order->get_shipping_method();
			} elseif ( $field == 'shipping_method' ) {
				$shipping_methods = $order->get_items( 'shipping' );
				$shipping_method = reset($shipping_methods); // take first entry
				$row[ $field ] =  !empty($shipping_method) ?  $shipping_method['method_id'] : '' ;
			} elseif ( $field == 'coupons_used' ) {
				$row[ $field ] = count( $data['coupons'] );
			} elseif ( $field == 'total_weight_items' ) {
				$row[$field] = 0;
				foreach($data['products'] as $product) {
					$row[$field] += $product['qty'] * $product['weight'];
				}
			} elseif ( $field == 'count_total_items' ) {
				$row[ $field ] = $order->get_item_count();
			} elseif ( $field == 'count_unique_products' ) { // speed! replace with own counter ?
				$row[ $field ] = count( $data['products'] );
			} elseif ( $field == 'customer_note' ) {
				$notes =  array( $post->post_excerpt );
				if( $options['export_refund_notes'] ) {
						$refunds = $order->get_refunds();
						foreach($refunds  as $refund) {
							// added get_reason for WC 3.0
							$notes[] = method_exists($refund, 'get_reason') ? $refund->get_reason() : $refund->get_refund_reason();
						}
				}
				$row[ $field ] = implode("\n", array_filter($notes) );
			} elseif ( isset( $static_vals['order'][ $field ] ) ) {
				$row[ $field ] = $static_vals['order'][ $field ];
			} elseif ( $field == 'order_notes' ) {
				remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10 );
				$args = array(
						'post_id' 	=> $order_id,
						'approve' 	=> 'approve',
						'type' 		=> 'order_note',
				);
				$notes = get_comments( $args );
				add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
				$comments = array();
				if ( $notes ) {
					foreach( $notes as $note ) {
						if ( ! empty( $options['export_all_comments'] ) || $note->comment_author !== 'WooCommerce' ) {
							$comments[] = apply_filters( 'woe_get_order_notes', $note->comment_content, $note, $order);
						}
					}
				}
				$row[ $field ] = implode("\n", $comments);
			} elseif ( isset( $order_meta[ $field ] ) ) {
				$field_data = array();
				do_action( 'woocommerce_order_export_add_field_data', $field_data, $order_meta[ $field ], $field );
				if ( empty( $field_data ) ) {
					$field_data[ $field ] = $order_meta[ $field ];
				}
				$row = array_merge( $row, $field_data );
			} elseif ( isset( $order_meta[ "_" . $field ] ) ) { // or hidden field
				$row[ $field ] = $order_meta[ "_" . $field ];
			} else { // order_date...
				$row[ $field ] = method_exists($order,'get_'.$field) ? $order->{'get_'.$field}() : get_post_meta( $order_id, '_' . $field, true );
				//print_r($field."=".$label); echo "debug static!\n\n";
			}

			//if ( isset( $filters_active['order'][ $field ] ) ) {
			if ( isset( $row[ $field ] ) ) {
				if ( in_array( $field, $date_fields ) ) {
					$row[ $field ] = self::try_to_convert_date_to_format( $row[ $field ], $date_format );
				}
				if( $options['format_number_fields'] )
					$row[ $field ] = self::format_numbers('order', $row[ $field ], $field);

				$row[ $field ] = apply_filters( "woe_get_order_value_{$field}", $row[ $field ] , $order, $field);
				$row[ $field ] = apply_filters( "woe_get_order_{$format}_value_{$field}", $row[ $field ] , $order, $field);
			}
		}

		// fill child cells
		if ( !$options['item_rows_start_from_new_line'] AND $must_adjust_extra_rows AND $extra_rows ) {
			$must_adjust_extra_rows = array_unique( $must_adjust_extra_rows );
			$row_vals               = array_values( $row );
			//print_r($must_adjust_extra_rows);//die();
			foreach ( $extra_rows as $id => $extra_row ) {
				foreach ( $row_vals as $pos => $val ) {
					//add missed columns if no coupon in 2nd row
					if ( ! isset( $extra_rows[ $id ][ $pos ] ) ) {
						$extra_rows[ $id ][ $pos ] = $val;
					}
					if ( ! in_array( $pos, $must_adjust_extra_rows ) ) {
						$extra_rows[ $id ][ $pos ] = $val;
					}
				}
			}
		}


		if ( $extra_rows ) {
			array_unshift( $extra_rows, $row );
		} else {
			$extra_rows = array( $row );
		}

		//don't encode products& coupons
		foreach($extra_rows as $k => $extra_row) {
			if($k != 'products'  AND $k != 'coupons')
 			$extra_rows[$k] = array_map( function( $elem ) {
 				return is_array( $elem ) ? json_encode( $elem ) : $elem;
 			}, $extra_row );
		}

		return apply_filters("woe_fetch_order_data",qtranxf_use($user_lang, $extra_rows));
	}

	public static function get_order_fields_as_type( $fields, $type ) {
		$type_fields = array();
		foreach ( $fields as $field => $label ) {
			if (preg_match('/' . $type . '/', $field)) {
				$type_fields[] = $field;
			}
		}

		return apply_filters("woe_get_order_fields_as_{$type}", $type_fields);
	}

	public static function try_to_convert_date_to_format( $value, $date_format ) {
		$new_value = strtotime( $value );

		if ( $new_value ) {
			return date( $date_format, $new_value );
		}
		else {
			return $value;
		}
	}


	public static function csv_process_multi_rows( &$row, &$extra_rows, $items, $labels, $item_rows_start_from_new_line ) {
		// to support
		// order row
		// item1 row
		// item1 row
		if( $item_rows_start_from_new_line ) {
			foreach($items as $item)
				$extra_rows[] = $item;
			return;//done
		}

		$row_size = count( $row );
		// must add one record at least, if no coupons for example
		if ( empty( $items ) ) {
			foreach ( $labels as $field => $label ) {
				$row[] = "";
			}

			return;
		}

		foreach ( $items as $pos => $data ) {
			if ( $pos == 0 ) { //current row
				foreach ( $labels as $field => $label ) {
					$row[] = $data[ $field ];
				}
			} else {
				if ( ! isset( $extra_rows[ $pos - 1 ] ) ) {
					$extra_rows[ $pos - 1 ] = $row_size ? array_fill( 0, $row_size, "" ) : array() ;
				}
				// if we adds 1-2 coupons after we added some products	, so $extra_rows ALREADY exists
				while ( count( $extra_rows[ $pos - 1 ] ) < $row_size ) {
					$extra_rows[ $pos - 1 ][] = "";
				}
				foreach ( $labels as $field => $label ) {
					$extra_rows[ $pos - 1 ][] = $data[ $field ];
				}
			}
		}
	}

	public static function csv_process_multi_cols( &$row, $data, $labels, $csv_max ) {
		for ( $i = 0; $i < $csv_max; $i ++ ) {
			if ( empty( $data[ $i ] ) ) {
				foreach ( $labels as $field => $label ) {
					$row[] = "";
				}
			} else {
				foreach ( $labels as $field => $label ) {
					$row[] = $data[ $i ][ $field ];
				}
			}
		}
	}

	public static function get_order_shipping_tax_refunded($order_id) {
		global $wpdb;
		$refund_ship_taxes  = $wpdb->get_var( $wpdb->prepare( "
			SELECT SUM( order_itemmeta.meta_value )
			FROM {$wpdb->prefix}woocommerce_order_itemmeta AS order_itemmeta
			INNER JOIN $wpdb->posts AS posts ON ( posts.post_type = 'shop_order_refund' AND posts.post_parent = %d )
			INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON ( order_items.order_id = posts.ID AND order_items.order_item_type = 'tax' )
			WHERE order_itemmeta.order_item_id = order_items.order_item_id
			AND order_itemmeta.meta_key IN ( 'shipping_tax_amount')
		", $order_id ) );

		return   abs( $refund_ship_taxes  );
	}

	public static function get_order_subtotal_refunded($order) {
		$subtotal_refund = 0 ;
		foreach($order->get_refunds() as $refund){
			$subtotal_refund += $refund->get_subtotal();
		}
		return abs($subtotal_refund);
	}

	/**
	 * @return string
	 */
	public static function get_product_variation( $item, $order, $item_id, $product ) {
		global $wpdb;
		$hidden_order_itemmeta = apply_filters( 'woocommerce_hidden_order_itemmeta', array(
		'_qty',
		'_tax_class',
		'_product_id',
		'_variation_id',
		'_line_subtotal',
		'_line_subtotal_tax',
		'_line_total',
		'_line_tax',
		'method_id',
		'cost',
		) );

		$result = array();

		$value_delimiter = apply_filters('woe_fetch_item_meta_value_delimiter', ': ');

		// pull meta directly
		$meta_data = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value, meta_id, order_item_id
			FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = %d
			ORDER BY meta_id", $item_id ), ARRAY_A );
		foreach( $meta_data  as $meta) {
			if ( in_array( $meta['meta_key'], $hidden_order_itemmeta ) )
				continue;
			if ( is_serialized( $meta['meta_value'] ) )
				continue;

			//known attribute?
			if ( taxonomy_exists( wc_sanitize_taxonomy_name( $meta['meta_key'] ) ) ) {
				$term               = get_term_by( 'slug', $meta['meta_value'], wc_sanitize_taxonomy_name( $meta['meta_key'] ) );
				$meta['meta_key']   = wc_attribute_label( wc_sanitize_taxonomy_name( $meta['meta_key'] ) );
				$meta['meta_value'] = isset( $term->name ) ? $term->name : $meta['meta_value'];
			} else {
				$meta['meta_key']   = apply_filters( 'woocommerce_attribute_label', wc_attribute_label( $meta['meta_key'], $product ), $meta['meta_key'] );
			}

			$value = wp_kses_post( $meta['meta_key'] ) . $value_delimiter . wp_kses_post( force_balance_tags( $meta['meta_value'] ) );
			$result[]  = apply_filters('woe_fetch_item_meta', $value,  $meta, $item , $product );
		}
		//list to string!
		return join( apply_filters('woe_fetch_item_meta_lines_delimiter', ' | '), $result);
	}

	/**
	 * @return array
	 */
	public static function get_shipping_methods() {

		if( !class_exists("WC_Shipping_Zone") )
			return array();

		if( !method_exists("WC_Shipping_Zone", "get_shipping_methods") )
			return array();

		$shipping_methods = array();

		$zone    = new WC_Shipping_Zone( 0 );
		$methods = $zone->get_shipping_methods();
		/** @var WC_Shipping_Method $method */
		foreach ( $methods as $method ) {
			$shipping_methods[ $method->get_rate_id() ] = __('[Rest of the World]', 'woo-order-export-lite' ) .' '. $method->get_title();
		}

		foreach ( WC_Shipping_Zones::get_zones() as $zone ) {
			$methods = $zone['shipping_methods'];
			/** @var WC_Shipping_Method $method */
			foreach ( $methods as $method ) {
				$shipping_methods[ $method->get_rate_id() ] =  '[' . $zone['zone_name'] . '] ' . $method->get_title();
			}
		}
		return $shipping_methods;
	}

	public static function format_numbers($object, $value, $field) {
		$option = WC_Order_Export_Engine::$current_job_settings[$object.'_fields'][$field];
		if( isset($option['format'])  AND ($option['format']=='money'  OR $option['format']=='number' ) ) {
			$new_value = number_format( floatval($value), self::$decimals, self::$decimal_separator, self::$thousands_separator );
			$value = apply_filters( 'woe_format_numbers', $new_value, $value);
		}
		return $value;
	}

}
