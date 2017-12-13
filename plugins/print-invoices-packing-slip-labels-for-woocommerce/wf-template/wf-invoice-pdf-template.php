<?php
include_once('wf-invoice-packingslip-deliverynote-template.php');
class Pdf_Invoice extends Pdf_Invoice_Packingslip_Deliverynote
{
	//Cell with horizontal scaling if text is too wide
	public $invoice_number;
	
	//-------------
	function invoice_num($Id,$document_name){
		$this->SetFontSize($this->title_size);
		$this->Ln(20);
		$this->Cell(40,0,__($document_name.' ','wf-woocommerce-packing-list').$Id,0,1);
		$invoice_number = $Id;
		$this->SetFontSize($this->content_size);
	}
	
	function order_date($date){
		$this->SetFontSize($this->content_size);
		$this->Cell(40,10,__('Order Date: ','wf-woocommerce-packing-list').$date);
	}
	
	function OrderDetails_invoice($order, $wf_pklist_column_sizes){
		$currency_symbol=get_woocommerce_currency();
		$this->setXY(12,115);
		$this->SetFontSize($this->title_size);
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(30, 115, 190);
		$this->SetDrawColor(0,0,0);
		$this->Cell($wf_pklist_column_sizes['sku'],10,__('SKU','wf-woocommerce-packing-list'),0,0,'C',true);
		$this->Cell($wf_pklist_column_sizes['product'],10,__('Product','wf-woocommerce-packing-list'),0,0,'C',true);
		$this->Cell($wf_pklist_column_sizes['quantity'],10,__('Quantity','wf-woocommerce-packing-list'),0,0,'C',true);
		$this->Cell($wf_pklist_column_sizes['price'],10,__('Total Price','wf-woocommerce-packing-list'),0,1,'C',true);
		$this->SetFontSize($this->content_size);
		$this->setFillColor(255,255,255); 
		$this->SetTextColor(0,0,0);
		$counter=1;
		$this->starting_y = $this->getY();
		foreach($order->get_items() as $item) {
			$_product = $order->get_product_from_item($item);
			if($_product) {
				$item_meta = (WC()->version < '3.1.0') ? new WC_Order_Item_Meta($item) : new WC_Order_Item_Product($item);
				$variation = '';
				$variation = (WC()->version < '3.1.0') ? $item_meta->display(true, true) : '';
				if (!$variation && $_product && isset($_product->variation_data)) {
					// otherwise (for an order added through the admin) lets display the formatted variation data so we have something to fall back to
					$variation = wc_get_formatted_variation($_product->variation_data, true);
				}
				if($counter%2==0){
					$this->SetFillColor(192,192,192);
				}else{
					$this->SetFillColor(255,255,255);
				}
			
				$number_of_lines = $this->total_lines($wf_pklist_column_sizes['product'],$item['name'].''.$variation);
				if($number_of_lines > 3) {
					$name_cell_height = 5;
					$cell_height = 5*$number_of_lines;
				} else {
					$name_cell_height = 15/$number_of_lines;
					$cell_height = 15;
				}
				$number_of_lines_sku = $this->total_lines($wf_pklist_column_sizes['sku'], $_product->get_sku());
				if(($number_of_lines > $number_of_lines_sku) && ($number_of_lines_sku > 1)) {
					$sku_cell_height = $cell_height/$number_of_lines_sku;
				} else if ($number_of_lines_sku > 1){
					$sku_cell_height = 15/$number_of_lines_sku;
				} else {
					$sku_cell_height = 15;
				}
				if($this->GetY()+$cell_height>$this->PageBreakTrigger) {
					$this->AddPage($this->CurOrientation);
					$this->starting_y = 10;
					$this->setY(10);
				}
				$x = 12;
				$this->setXY($x,$this->starting_y);
				$this->MultiCell($wf_pklist_column_sizes['sku'],$sku_cell_height,__($_product->get_sku(),'wf-woocommerce-packing-list'),1,'C',true);
				$x += $wf_pklist_column_sizes['sku'];
				$this->setXY($x,$this->starting_y);
				$this->MultiCell($wf_pklist_column_sizes['product'],$name_cell_height,__($item['name'].' '.$variation,'wf-woocommerce-packing-list'),1,'C',true);
				$x += $wf_pklist_column_sizes['product'];
				$this->setXY($x,$this->starting_y);
				$this->MultiCell($wf_pklist_column_sizes['quantity'],$cell_height,__($item['qty'],'wf-woocommerce-packing-list'),1,'C',true);
				$x += $wf_pklist_column_sizes['quantity'];
				$this->setXY($x,$this->starting_y);
				$this->MultiCell($wf_pklist_column_sizes['price'],$cell_height,__($currency_symbol.' '.number_format($order->get_line_subtotal($item),2),'wf-woocommerce-packing-list'),1,'C',true);
				$this->starting_y = $this->getY();
				$counter++;
			}
		}
		$this->setX(12);
		$this->SetFontSize($this->content_size);
		$this->Cell((180 - ($wf_pklist_column_sizes['price'])),10,__('Subtotal', 'wf-woocommerce-packing-list'),1,0,'C');
		$this->Cell($wf_pklist_column_sizes['price'],10,__($currency_symbol.' '.number_format($order->get_subtotal(),2)),1,1,'C');
		if (get_option('woocommerce_calc_shipping') == 'yes'){
			$cell_height = 10;
			
			$this->setX(12);
			//$this->Cell((180 - ($wf_pklist_column_sizes['price'])),10,__('Shipping', 'wf-woocommerce-packing-list'),1,0,'C');
			$Shippingdetials= $order->get_items('shipping' );
			$y_position = $this->getY();
			if(!empty($Shippingdetials)){
				foreach($Shippingdetials as $key){
					$Shipping=$key['cost'].__(' via ', 'wf-woocommerce-packing-list').html_entity_decode($key['name'],ENT_QUOTES,'UTF-8');
				}
				$number_of_lines = $this->total_lines($wf_pklist_column_sizes['price'],$currency_symbol.' '.$Shipping);
				if($number_of_lines > 1) {
					$value_cell_height = 10;
					$name_cell_height = 10*$number_of_lines;
				} else {
					$name_cell_height = 10;
					$value_cell_height = 10;
				}
				$this->MultiCell((180 - ($wf_pklist_column_sizes['price'])),$name_cell_height,__('Shipping', 'wf-woocommerce-packing-list'), 1, 'C', true);
				$this->setXY(12 + (180 - ($wf_pklist_column_sizes['price'])), $y_position);
				$this->MultiCell($wf_pklist_column_sizes['price'],$value_cell_height,__($currency_symbol.' '.$Shipping), 1, 'C', true);
			}else{
				$number_of_lines = $this->total_lines($wf_pklist_column_sizes['product'],$currency_symbol.' '.$Shipping);
				if($number_of_lines > 1) {
					$value_cell_height = 10;
					$name_cell_height = 10*$number_of_lines;
				} else {
					$name_cell_height = 10;
					$value_cell_height = 10;
				}
				$this->MultiCell((180 - ($wf_pklist_column_sizes['price'])),$name_cell_height,__('Shipping', 'wf-woocommerce-packing-list'), 1, 'C', true);
				$this->setXY(12 + (180 - ($wf_pklist_column_sizes['price'])), $y_position);
				$this->MultiCell($wf_pklist_column_sizes['price'],$value_cell_height,__($currency_symbol.' '.number_format($order->order_shipping, 2).' via '.strip_tags($order->shipping_method_title)), 1, 'C', true);
			}
						
		}
		$tax_items = $order->get_tax_totals();  
		if (count($tax_items) >=1){
			foreach($tax_items as $tax_item){ 
				$this->setX(12);
				$this->Cell((180 - ($wf_pklist_column_sizes['price'])),10,__($tax_item->label,'wf-woocommerce-packing-list'),1,0,'C');
				$this->Cell($wf_pklist_column_sizes['price'],10,__($currency_symbol.' '.number_format($tax_item->amount,2),'wf-woocommerce-packing-list'),1,1,'C');
			}  
			if (count($tax_items) >1){
					$this->setX(12);
					$this->Cell((180 - ($wf_pklist_column_sizes['price'])),10,__('Total Tax:', 'wf-woocommerce-packing-list'),1,0,'C');
					$this->Cell($wf_pklist_column_sizes['price'],10,__($currency_symbol.' '.number_format($order->get_total_tax(),2),'wf-woocommerce-packing-list'),1,1,'C');
			}
		}
		if ($order->cart_discount > 0){
			$this->setX(12);
			$this->Cell((180 - ($wf_pklist_column_sizes['price'])),10,__('Cart Discount:', 'wf-woocommerce-packing-list'),1,0,'C');
			$this->Cell($wf_pklist_column_sizes['price'],10,__($currency_symbol.' '.number_format($order->cart_discount,2)),1,1,'C');
		}
		if ($order->order_discount > 0){
			$this->setX(12);
			$this->Cell((180 - ($wf_pklist_column_sizes['price'])),10,__('Order Discount:', 'wf-woocommerce-packing-list'),1,0,'C');
			$this->Cell($wf_pklist_column_sizes['price'],10,__($currency_symbol.' '.number_format($order->order_discount,2)),1,1,'C');
		}
		$this->setX(12);
		$this->Cell((180 - ($wf_pklist_column_sizes['price'])),10,__('Total', 'wf-woocommerce-packing-list'),1,0,'C');
		$this->Cell($wf_pklist_column_sizes['price'],10,__($currency_symbol.' '.number_format($order->order_total,2)),1,1,'C');

		$this->setX(12);
		$this->Cell((180 - ($wf_pklist_column_sizes['price'])),10,__('Payment Method', 'wf-woocommerce-packing-list'),1,0,'C');
		$this->Cell($wf_pklist_column_sizes['price'],10,__(html_entity_decode($order->payment_method_title,ENT_QUOTES,'UTF-8')),1,1,'C');
	}
}
