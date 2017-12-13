<?php
$acive_template = get_option('wf_invoice_active_key');

$main_data = get_option($acive_template);
if(get_option($acive_template.'value'))
	{
		$main_data_value = get_option($acive_template.'value');
	}
else{
$main_data_value = get_option('wf_invoice_active_value');
}

$main_data_array = explode('|',$main_data_value);

$main_data = str_replace('[wf link]', WF_INVOICE_MAIN_ROOT_PATH,$main_data);
$main_data = str_replace("[invoice main height and width]", 'width:97vw;',$main_data);
if($this->wf_packinglist_get_logo($action) != '') {
	if ($main_data_array[3] === 'logo')
	{
		$main_data = str_replace('[image url for company logo]', $this->wf_packinglist_get_logo($action), $main_data);
		$main_data = str_replace('[logo width]', $main_data_array[0], $main_data);
		$main_data = str_replace('[logo height]', $main_data_array[1], $main_data);
		$main_data = str_replace('[company text show hide]','display:none;', $main_data);
	}
	else{
		$main_data = str_replace('[image url for company logo]','', $main_data);
		$main_data = str_replace('[logo width]', '', $main_data);
		$main_data = str_replace('[logo height]', '', $main_data);
		$main_data = str_replace('[company name]', __($this->wf_packinglist_get_companyname(), 'wf-woocommerce-packing-list'), $main_data);
		$main_data = str_replace('[company text show hide]','', $main_data);
	}
	$main_data = str_replace('[company1 name]', '', $main_data);

}else{
	if($this->wf_packinglist_get_logo($action) == '') {
		$main_data = str_replace('[image url for company logo]', '', $main_data);
		$main_data = str_replace('[logo height]', '', $main_data);
		$main_data = str_replace('[logo width]', '', $main_data);
		$main_data = str_replace('[company name]', __($this->wf_packinglist_get_companyname(), 'wf-woocommerce-packing-list'), $main_data);
		$main_data = str_replace('[company text show hide]','', $main_data);
	}
	else
	{
		$main_data = str_replace('[image url for company logo]', '', $main_data);
		$main_data = str_replace('[logo height]', '', $main_data);
		$main_data = str_replace('[logo width]', '', $main_data);
		$main_data = str_replace('[company name]', '', $main_data);
		$main_data = str_replace('[company text show hide]','display:none;', $main_data);
	}
}
if ($main_data_array[2] === 'no')
	{$main_data 		= str_replace('[company logo visible]','display:none !important;', $main_data);}
else
	{$main_data 		= str_replace('[company logo visible]','', $main_data);}
if ($main_data_array[4] === 'no')
	{$main_data 		= str_replace('[invoice number switch]','display:none !important;', $main_data);}
else
	{$main_data 		= str_replace('[invoice number switch]','', $main_data);}

$main_data 		= str_replace('[invoice number prob]','font-size:'.$main_data_array[5].'px !important;', $main_data);
$main_data 		= str_replace('[invoice date font size]','font-size:'.$main_data_array[11].'px !important;', $main_data);
$main_data 		= str_replace('[invoice font weight]','font-weight:'.$main_data_array[6].' !important;', $main_data);



if($main_data_array[8] != 'default')
	{$main_data 		= str_replace('[invoice_number_color]','color:#'.$main_data_array[8].' !important;', $main_data);}
else
	{$main_data 		= str_replace('[invoice_number_color]','', $main_data);}

if($main_data_array[9] === 'no'  )
	{$main_data 		= str_replace('[invoice date show hide]','display:none !important;', $main_data);}
else
	{$main_data 		= str_replace('[invoice date show hide]','', $main_data);}


if($main_data_array[15] === 'no' )
	{$main_data 		= str_replace('[order date show hide]','display:none !important;', $main_data);}
else
	{$main_data 		= str_replace('[order date show hide]','', $main_data);}

$main_data 		= str_replace('[invoice created date]',date($main_data_array[10],strtotime('now')), $main_data);
$main_data 		= str_replace('[invoice name]', $main_data_array[7], $main_data);
$invoice_number = $this->wf_generate_invoice_number($order);
$main_data 		= str_replace('[invoice number]', $invoice_number , $main_data);
$main_data 		= str_replace('[invoice Date label text]',__($main_data_array[12], 'wf-woocommerce-packing-list'), $main_data);

$main_data 		= str_replace('[invoice date label font weight]','font-weight:'.$main_data_array[13].' !important;', $main_data);

if($main_data_array[14] != 'default')
	{$main_data 		= str_replace('[invoice date color code]','color:#'.$main_data_array[14].' !important;', $main_data);}
else
	{$main_data 		= str_replace('[invoice date color code]','', $main_data);}

$main_data 		= str_replace('[order date title size]', $title_size, $main_data);
$main_data 		= str_replace('[order date label]', __($main_data_array[18], 'wf-woocommerce-packing-list'), $main_data);
$order_date 	= date($main_data_array[16], strtotime($order->order_date));
$order_date 	= apply_filters('wf_pklist_modify_order_date',$order_date, $order, $action);
$main_data 		= str_replace('[order date]', $order_date, $main_data);
$main_data 		= str_replace('[order date font size]','font-size:'.$main_data_array[17].'px !important;', $main_data);
$main_data 		= str_replace('[order date label font weight]','font-weight:'.$main_data_array[19].';', $main_data);

if($main_data_array[20] != 'default')
	{$main_data 		= str_replace('[order date color code]','color:#'.$main_data_array[20].' !important;', $main_data);}
else
	{$main_data 		= str_replace('[order date color code]','', $main_data);}

$main_data 		= str_replace('[from address font size]', $content_size, $main_data);

$faddress=$this->wf_shipment_label_get_from_address();
$from_address_data = '';
foreach ($faddress as $key => $value) {
	if (!empty($value)) {
		$from_address_data .= $value . ' <br>';
	}
}

if(empty($from_address_data))
{
	$main_data 		= str_replace('[from address show hide]','display:none !important;', $main_data);
	$main_data 		= str_replace('[from address]', '', $main_data);
}
else
{

$main_data 		= str_replace('[from address]', rtrim($from_address_data,'<br>'), $main_data);

}
if($main_data_array[21] === 'no' )
	{$main_data 		= str_replace('[from address show hide]','display:none !important;', $main_data);}
else
	{$main_data 		= str_replace('[from address show hide]','', $main_data);}
$main_data 		= str_replace('[from address label]', __($main_data_array[22], 'wf-woocommerce-packing-list'), $main_data);

$main_data 		= str_replace('[from address left right]','text-align:'.$main_data_array[23].' !important;', $main_data);

if($main_data_array[24] != 'default')
	{$main_data 		= str_replace('[from address text color]','color:#'.$main_data_array[24].' !important;', $main_data);}
else
	{$main_data 		= str_replace('[from address text color]','', $main_data);}

$main_data 		= str_replace('[billing address title size]', $title_size, $main_data);

if($main_data_array[25] === 'no' )
	{$main_data 		= str_replace('[billing address show hide]','display:none !important;', $main_data);
}
else
{
	$main_data 		= str_replace('[billing address show hide]','', $main_data);

}

$main_data 		= str_replace('[billing address left right]','text-align:'.$main_data_array[27].' !important;', $main_data);

if($main_data_array[28] != 'default')
{
	$main_data 		= str_replace('[billing address text color]','color:#'.$main_data_array[28].' !important;', $main_data);
}
else
{
	$main_data 		= str_replace('[billing address text color]','', $main_data);

}

$main_data 		= str_replace('[billing address label]', __($main_data_array[26], 'wf-woocommerce-packing-list'), $main_data);
$main_data 		= str_replace('[billing address font size]', $content_size, $main_data);

$billing_address = $this->get_billing_address($order);

$billing_data_address = $billing_address['first_name'] . ' ' . $billing_address['last_name'] . '<br>';
if($billing_address['company'] != '') {
	$billing_data_address .= $billing_address['company'] . '<br>';
}
$billing_data_address .= $billing_address['address_1'] . '<br>';
if($billing_address['address_2'] != '') {
	$billing_data_address .= $billing_address['address_2'] . '<br>';
}
$billing_data_address .= $billing_address['city'].', '.$billing_address['state'] . ' ' . $billing_address['postcode'] . '<br>';
$billing_data_address .= $billing_address['country'] . '<br>';

$main_data 		= str_replace('[billing address data]', rtrim($billing_data_address,'<br>'), $main_data);


$main_data 		= str_replace('[extra field1 size]', $content_size, $main_data);


$main_data 		= str_replace('[shipping address title size]', $title_size, $main_data);


if($main_data_array[29] === 'no' )
{
	$main_data 		= str_replace('[shipping address show hide]','display:none !important;', $main_data);

}
else
{
	$main_data 		= str_replace('[shipping address show hide]','', $main_data);

}

$main_data 		= str_replace('[shipping address left right]','text-align:'.$main_data_array[31].' !important;', $main_data);

if($main_data_array[32] != 'default')
{
	$main_data 		= str_replace('[shipping address text color]','color:#'.$main_data_array[32].' !important;', $main_data);
}
else
{
	$main_data 		= str_replace('[shipping address text color]','', $main_data);

}

$main_data 		= str_replace('[shipping address title]', $main_data_array[30], $main_data);



$main_data 		= str_replace('[shipping address content size]', $content_size, $main_data);

$shipping_address = $this->get_shipping_address($order);
$shipping_address_data = $shipping_address['first_name'] . ' ' . $shipping_address['last_name'] . '<br>';
if($shipping_address['company'] != '') {
	$shipping_address_data .= $shipping_address['company'] . '<br>';
}
$shipping_address_data .= $shipping_address['address_1'] . '<br>';
if($shipping_address['address_2'] != '') {
	$shipping_address_data .= $shipping_address['address_2'] . '<br>';
}
$shipping_address_data .= $shipping_address['city'].', '.$shipping_address['state'] . ' ' . $shipping_address['postcode'] . '<br>';
$shipping_address_data .= $shipping_address['country'] . '<br>';

$main_data 		= str_replace('[shipping address data]', $shipping_address_data, $main_data);



	if ($order->billing_email){

		if($main_data_array[33] === 'no' )
		{
			$main_data 		= str_replace('[wf email show hide]','display:none;', $main_data);

		}
		else
		{
			$main_data 		= str_replace('[wf email show hide]','', $main_data);
			$main_data 		= str_replace('[wf email font size]', 'font-size:'.$main_data_array[34].'px !important;', $main_data);

			$main_data 		= str_replace('[email label]', $main_data_array[35], $main_data);
			$main_data 		= str_replace('[wf email position set]','text-align:'.$main_data_array[36].' !important;', $main_data);

			$main_data 		= str_replace('[email address]', $order->billing_email, $main_data);

			if($main_data_array[37] != 'default')
			{
				$main_data 		= str_replace('[wf_email color code default]','color:#'.$main_data_array[37].' !important;', $main_data);
			}
			else
			{
				$main_data 		= str_replace('[wf_email color code default]','', $main_data);

			}
		}
	}
	else
	{
		$main_data 		= str_replace('[wf email show hide]','display:none;', $main_data);

	}

	if ($order->billing_phone){

		if($main_data_array[38] === 'no' )
		{
			$main_data 		= str_replace('[wf tel show hide]','display:none !important;', $main_data);

		}
		else
		{
			$main_data 		= str_replace('[wf tel show hide]','', $main_data);
			$main_data 		= str_replace('[wf tel font size]', 'font-size:'.$main_data_array[39].'px !important;', $main_data);

			$main_data 		= str_replace('[mobile label]', $main_data_array[40], $main_data);
			$main_data 		= str_replace('[wf tel position set]','text-align:'.$main_data_array[41].' !important;', $main_data);

			$main_data 		= str_replace('[mobile number]', $order->billing_phone, $main_data);

			if($main_data_array[42] != 'default')
			{
				$main_data 		= str_replace('[wf_tel color code default]','color:#'.$main_data_array[42].' !important;', $main_data);
			}
			else
			{
				$main_data 		= str_replace('[wf_tel color code default]','', $main_data);

			}
		}
	}
	else
	{
		$main_data 		= str_replace('[wf tel show hide]','display:none !important;', $main_data);

	}

$main_data 		= str_replace('[invoice extra field font size]','font-size:'.$main_data_array[81].'px;', $main_data);
if($main_data_array[80] != 'none')
{
	$main_data 		= str_replace('[Extra data below logo]',str_replace('-*-','|',$main_data_array[80]) , $main_data);
	$main_data 		= str_replace('[wf extra filed show hide]','', $main_data);
}
else
{
	$main_data 		= str_replace('[wf extra filed show hide]','display:none;', $main_data);

	$main_data 		= str_replace('[Extra data below logo]','' , $main_data);
}


if (((WC()->version < '2.7.0') ? $order->billing_SSN : get_post_meta($order_id,'_billing_SSN',true))){

		if($main_data_array[48] === 'no' )
		{
			$main_data 		= str_replace('[wf ssn show hide]','display:none;', $main_data);

		}
		else
		{
			$main_data 		= str_replace('[wf ssn show hide]','', $main_data);
			$main_data 		= str_replace('[wf ssn font size]', 'font-size:'.$main_data_array[49].'px !important;', $main_data);

			$main_data 		= str_replace('[SSN label]', $main_data_array[50], $main_data);
			$main_data 		= str_replace('[wf ssn position set]','text-align:'.$main_data_array[51].' !important;', $main_data);
			$main_data 		= str_replace('[SSN data]', ((WC()->version < '2.7.0') ? $order->billing_SSN : get_post_meta($order_id,'_billing_SSN',true)), $main_data);

			if($main_data_array[52] != 'default')
			{
				$main_data 		= str_replace('[wf_ssn color code default]','color:#'.$main_data_array[52].' !important;', $main_data);
			}
			else
			{
				$main_data 		= str_replace('[wf_ssn color code default]','', $main_data);

			}
		}
	}
	else
	{
		$main_data 		= str_replace('[wf ssn show hide]','display:none !important;', $main_data);
	}

if (((WC()->version < '2.7.0') ? $order->billing_VAT : get_post_meta($order_id,'_billing_VAT',true))){

		if($main_data_array[43] === 'no' )
		{
			$main_data 		= str_replace('[wf vat show hide]','display:none !important;', $main_data);

		}
		else
		{
			$main_data 		= str_replace('[wf vat show hide]','', $main_data);
			$main_data 		= str_replace('[wf vat font size]', 'font-size:'.$main_data_array[44].'px !important;', $main_data);

			$main_data 		= str_replace('[VAT label]', $main_data_array[45], $main_data);
			$main_data 		= str_replace('[wf vat position set]','text-align:'.$main_data_array[46].' !important;', $main_data);

			$main_data 		= str_replace('[VAT data]', ((WC()->version < '2.7.0') ? $order->billing_VAT : get_post_meta($order_id,'_billing_VAT',true)), $main_data);

			if($main_data_array[47] != 'default')
			{
				$main_data 		= str_replace('[wf_vat color code default]','color:#'.$main_data_array[47].' !important;', $main_data);
			}
			else
			{
				$main_data 		= str_replace('[wf_vat color code default]','', $main_data);

			}
		}
	}
	else
	{
		$main_data 		= str_replace('[wf vat show hide]','display:none;', $main_data);

	}

$main_data =	str_replace('[invoice extra firlds import]','',$main_data);
$main_data =	str_replace('[invoice extra firlds import old one]','',$main_data);

if (get_post_meta($order_id, '_tracking_provider', true)){

	if($main_data_array[53] === 'no' )
	{
		$main_data 		= str_replace('[wf tp show hide]','display:none !important;', $main_data);

	}
	else
	{
		$main_data 		= str_replace('[wf tp show hide]','', $main_data);
		$main_data 		= str_replace('[wf tp font size]', 'font-size:'.$main_data_array[54].'px !important;', $main_data);

		$main_data 		= str_replace('[tracking label]', $main_data_array[55], $main_data);
		$main_data 		= str_replace('[wf tp position set]','text-align:'.$main_data_array[56].' !important;', $main_data);

		$main_data 		= str_replace('[tracking data]', get_post_meta($order_id, '_tracking_provider', true), $main_data);

		if($main_data_array[57] != 'default')
		{
			$main_data 		= str_replace('[wf_tp color code default]','color:#'.$main_data_array[57].' !important;', $main_data);
		}
		else
		{
			$main_data 		= str_replace('[wf_tp color code default]','', $main_data);

		}
	}
}
else
{
	$main_data 		= str_replace('[wf tp show hide]','display:none;', $main_data);
}

if (get_post_meta($order_id, get_option('woocommerce_wf_tracking_number') != '' ? get_option('woocommerce_wf_tracking_number') : '_tracking_number' , true)){

	if($main_data_array[58] === 'no' )
	{
		$main_data 		= str_replace('[wf tn show hide]','display:none !important;', $main_data);

	}
	else
	{
		$main_data 		= str_replace('[wf tn show hide]','', $main_data);
		$main_data 		= str_replace('[wf tn font size]', 'font-size:'.$main_data_array[59].'px !important;', $main_data);

		$main_data 		= str_replace('[tracking number label]', $main_data_array[60], $main_data);
		$main_data 		= str_replace('[wf tn position set]','text-align:'.$main_data_array[61].';', $main_data);

		$main_data 		= str_replace('[tracking number data]', get_post_meta($order_id,get_option('woocommerce_wf_tracking_number') != '' ? get_option('woocommerce_wf_tracking_number') : '_tracking_number' , true), $main_data);

		if($main_data_array[62] != 'default')
		{
			$main_data 		= str_replace('[wf_tn color code default]','color:#'.$main_data_array[62].' !important;', $main_data);
		}
		else
		{
			$main_data 		= str_replace('[wf_tn color code default]','', $main_data);

		}
	}
}
else
{
	$main_data 		= str_replace('[wf tn show hide]','display:none;', $main_data);
}


if ($action === 'print_invoice')
{
	$table_column_sizes = $this->get_table_column_sizes($order);

	if($main_data_array[63] === 'no' )
	{
		$main_data 		= str_replace('[wf product table show hide]','display:none !important;', $main_data);

	}
	else
	{
		$main_data 		= str_replace('[wf product table show hide]','', $main_data);
		if($main_data_array[64] != 'default')
		{
			$main_data 		= str_replace('[wf product table head color]','background:#'.$main_data_array[64].' !important;', $main_data);
			$main_data 		= str_replace('[border-base-theme-color]',$main_data_array[64], $main_data);
			$main_data 		= str_replace('[table border top color]', $main_data_array[64], $main_data);
			$main_data 		= str_replace('[table background color]', $main_data_array[64], $main_data);
			$main_data 		= str_replace('[table header font size]', $title_size, $main_data);
			$main_data 		= str_replace('[table coloum brand color]', $main_data_array[64], $main_data);

		}
		else
		{
			$main_data 		= str_replace('[table border top color]', '#1e73be', $main_data);
			$main_data 		= str_replace('[wf product table head color]','', $main_data);
			$main_data 		= str_replace('[border-base-theme-color]','66BDA9', $main_data);
			$main_data 		= str_replace('[table background color]', '#1e73be', $main_data);
			$main_data 		= str_replace('[table header font size]', $title_size, $main_data);
			$main_data 		= str_replace('[table coloum brand color]', '#1e73be', $main_data);

		}
		if($main_data_array[65] != 'default')
		{
			$main_data 		= str_replace('[wf product table head text color]','color:#'.$main_data_array[65].' !important;', $main_data);
		}
		else
		{
			$main_data 		= str_replace('[wf product table head text color]','', $main_data);
		}

		$main_data 		= str_replace('[wf product table text align]','text-align:'.$main_data_array[66].' !important;', $main_data);

		if($main_data_array[67] != 'default')
		{
			$main_data 		= str_replace('[wf product table text color main]','color:#'.$main_data_array[67].' !important;', $main_data);
		}
		else
		{
			$main_data 		= str_replace('[wf product table text color main]','', $main_data);
		}

		$main_data 		= str_replace('[wf product table body text align]','text-align:'. $main_data_array[68].' !important;', $main_data);

		$main_data 		= str_replace('[product label text]', $main_data_array[70], $main_data);

			$main_data 		= str_replace('[sku label text]', $main_data_array[69], $main_data);
			$main_data 		= str_replace('[table colum span]','1', $main_data);

			$main_data 		= str_replace('[table colum span hide]','', $main_data);
			$main_data 		= str_replace('[table quantity text]', $main_data_array[71], $main_data);
			$main_data 		= str_replace('[table toatl price text]', $main_data_array[72], $main_data);


	}



	$order = new WC_Order($order_id);



	$main_data 		= str_replace('[table tfoot content size]', $content_size, $main_data);
	$main_data 		= str_replace('[table subtotal label]', $main_data_array[73], $main_data);
	$main_data 		= str_replace('[table subtotal value]', $order->get_subtotal_to_display(), $main_data);
	$main_data 		= str_replace('[table shipping select text]', $main_data_array[74], $main_data);

	$main_data 		= str_replace('[table cart discount text]', $main_data_array[75], $main_data);
	$main_data 		= str_replace('[table Order discount text]', $main_data_array[76], $main_data);
	$main_data 		= str_replace('[table Total Tax text]', $main_data_array[77], $main_data);

	$main_data 		= str_replace('[table invoice total label]', $main_data_array[78], $main_data);
	$main_data 		= str_replace('[table tax item value]', wc_price($order->get_total_tax()), $main_data);


	if (get_option('woocommerce_calc_shipping') === 'yes'){
		$main_data 		= str_replace('[wf shipping show hide]','', $main_data);

		$Shippingdetials= $order->get_items('shipping' );
		if(!empty($Shippingdetials)){
			foreach($Shippingdetials as $key){
				$Shipping=get_woocommerce_currency_symbol().' '.$key['cost'].' via '.$key['name'];
			}
		}
		else {
			$Shipping=$order->get_shipping_to_display();
		}
		$main_data 		= str_replace('[table shipping select value]', $Shipping, $main_data);
	}
	else
	{
		$main_data 		= str_replace('[wf shipping show hide]','display:none !important;', $main_data);


	}

	if (((WC()->version < '2.7.0') ? $order->cart_discount : get_post_meta($order_id,'_cart_discount',true)) > 0){
		$main_data 		= str_replace('[wf cd show hide]','', $main_data);

		$main_data 		= str_replace('[table cart discount value]', wc_price(((WC()->version < '2.7.0') ? $order->cart_discount : get_post_meta($order_id,'_cart_discount',true))), $main_data);
	}
	else
	{
		$main_data 		= str_replace('[wf cd show hide]','display:none !important;', $main_data);

	}

	if (((WC()->version < '2.7.0' ) ? $order->order_discount : get_post_meta($order_id,'_order_discount',true)) > 0){
		$main_data 		= str_replace('[wf od show hide]','', $main_data);

		$main_data 		= str_replace('[table Order discount value]', wc_price(((WC()->version < '2.7.0' ) ? $order->order_discount : get_post_meta($order_id,'_order_discount',true))), $main_data);
	}
	else
	{
		$main_data 		= str_replace('[wf od show hide]','display:none !important;', $main_data);
		$main_data 		= str_replace('[table Order discount value]', '', $main_data);

	}
					// If there is more than one tax
	$tax_items = $order->get_tax_totals();
	$sub_loop_data =0;
	$template_data = get_option($acive_template.'from') !=false ? get_option($acive_template.'from') : $acive_template ;
	if (count($tax_items) > 1){

			$main_data 		= str_replace('[wf tt show hide]','display:none;', $main_data);

		foreach($tax_items as $tax_item)
		{

					$sub_loop_data += $tax_item->amount;

		}

			$main_data 		= str_replace('[table tax items]', '', $main_data);

	}
	else
	{

		$main_data 		= str_replace('[wf tt show hide]','display:none;', $main_data);

		foreach($tax_items as $tax_item){

					$sub_loop_data += $tax_item->amount;




		}


			$main_data 		= str_replace('[table tax items]', '', $main_data);

	}

	$total_price_final='';
	$refund_amount ='';

	if(wc_price((WC()->version < '2.7.0') ? $order->order_total : get_post_meta( $order_id, '_order_total', true )))
	{
		$total_price_final = (WC()->version < '2.7.0') ? $order->order_total : get_post_meta( $order_id, '_order_total', true );


		$refund_data_array = $order->get_refunds();
		if(!empty($refund_data_array))
		{
			foreach ($refund_data_array as  $refund) {

				$total_price_final += get_post_meta($refund->id,'_order_total',true);
				$refund_amount -= get_post_meta($refund->id,'_order_total',true);
			}

		}

	}

		$data =  ' (incl. tax  '.wc_price($sub_loop_data).')';
		$main_data 		= str_replace('[table invoice total value]', wc_price((WC()->version < '2.7.0') ? $order->order_total : get_post_meta( $order_id, '_order_total', true )).$data, $main_data);


	$main_data 		= str_replace('[table payment info label]', $main_data_array[79], $main_data);
	$main_data 		= str_replace('[table payment info value]', ucwords(((WC()->version < '2.7.0' ) ? $order->payment_method_title : $order->get_payment_method_title())), $main_data);

		$main_data 		= str_replace('[payment method show hide]','',$main_data);


	$coupon_data = '';


		$main_data = str_replace('[table coupon show hide]','display:none;',$main_data);
		$main_data = str_replace('[table coupon info label]','',$main_data);
		$main_data = str_replace('[table coupon info value]','',$main_data);



		$main_data 		= str_replace('[table payment info value]', '', $main_data);
		$main_data 		= str_replace('[payment method show hide]','display:none;',$main_data);


	$main_data 		= str_replace('[table tbody content label]', $content_size, $main_data);

	$main_data 		= str_replace('[table tbody content value]', $this->woocommerce_invoice_order_items_table($order, true) , $main_data);

	$main_data 		= str_replace('[invoice barcode data]', '' , $main_data);

}


	$main_data 		= str_replace('[invoice return policy data]', '' , $main_data);
	$main_data 		= str_replace('[invoice return policy hide]','visibility:hidden;',$main_data);

$main_data = str_replace('[wffootor style]','bottom: 1px;',$main_data);
$main_data 		= str_replace('[invoice footor data]', '' , $main_data);
$main_data 		= str_replace('[footor content size]', $content_size , $main_data);


echo $main_data; ?>


<script>
	function document_options(print_preview)
	{

		var height = screen.height;

		document.body.style.height = height;

		if(print_preview) {
			window.print();
		}
	}
</script>
