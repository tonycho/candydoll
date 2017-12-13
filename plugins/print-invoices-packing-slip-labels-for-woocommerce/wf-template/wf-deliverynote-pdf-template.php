<?php
include_once('wf-invoice-packingslip-deliverynote-template.php');
class Pdf_Deliverynote  extends Pdf_Invoice_Packingslip_Deliverynote
{
	
	function packinglist_num($Id){
		$this->SetFontSize($this->title_size);
		$this->Ln(20);
		$this->Cell(40,0,__('Order: ','wf-woocommerce-packing-list').$Id,0,1);
		$this->SetFontSize($this->content_size);
	}

	function order_date($date){
		$this->Cell(40,20,__('Order Date: ','wf-woocommerce-packing-list').$date);
	}
	
	function OrderDetails_packinglist($order, $packinglist){
		$currency_symbol=get_woocommerce_currency();
		$this->setXY(12,115);
		$this->SetFontSize($this->title_size);
		$this->SetTextColor(255,255,255);
		$this->SetFillColor(30, 115, 190);
		$this->SetDrawColor(0,0,0);
		$Include_image=get_option('woocommerce_wf_attach_image_packinglist');
		if($Include_image=='Yes'){
			$this->Cell(30,10,__('Image','wf-woocommerce-packing-list'),0,0,'C',true);
			$this->Cell(30,10,__('SKU','wf-woocommerce-packing-list'),0,0,'C',true);
			$this->Cell(50,10,__('Product','wf-woocommerce-packing-list'),0,0,'C',true);
			$this->Cell(30,10,__('Quantity','wf-woocommerce-packing-list'),0,0,'C',true);
			$this->Cell(40,10,__('Total Weight','wf-woocommerce-packing-list'),0,0,'C',true);
		} else {
			$this->Cell(45,10,__('SKU','wf-woocommerce-packing-list'),0,0,'C',true);
			$this->Cell(65,10,__('Product','wf-woocommerce-packing-list'),0,0,'C',true);
			$this->Cell(30,10,__('Quantity','wf-woocommerce-packing-list'),0,0,'C',true);
			$this->Cell(40,10,__('Total Weight','wf-woocommerce-packing-list'),0,1,'C',true);
		}
		$this->SetFontSize($this->content_size);
		$this->setFillColor(255,255,255); 
		$this->SetTextColor(0,0,0);
		$counter=1;
		$this->starting_y = 125;
		foreach($order as $id => $item) {
			$this->setX(12);
			if($counter%2==0){
				$this->SetFillColor(192,192,192);
			}else{
				$this->SetFillColor(255,255,255);
			}
			if($Include_image=='Yes'){
				$number_of_lines = $this->total_lines(50,$item['name'].$item['variation_data']);
				if($number_of_lines > 3) {
					$name_cell_height = 5;
					$cell_height = 5*$number_of_lines;
				} else {
					$name_cell_height = 15/$number_of_lines;
					$cell_height = 15;
				}
				$number_of_lines_sku = $this->total_lines(30, $item['sku']);
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
				$image_id = ($item['variation_id'] != '') ? get_post_thumbnail_id($item['variation_id']) : get_post_thumbnail_id($item['id']);
				$attachment = wp_get_attachment_image_src($image_id, 'full' );
				if(($item['variation_id'] != '') && empty($attachment[0])) {
					$image_id = get_post_thumbnail_id($item['id']);
					$attachment = wp_get_attachment_image_src($image_id);
				}
				$this->setXY(12,$this->starting_y);
				$this->MultiCell(30,$cell_height,'',1,'C',true);
				if($attachment[0] !='') {
					$image_path = WP_CONTENT_DIR.'/'.strstr($attachment[0],'uploads');
					$image_format = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));
					if(!empty($image_path)){
						$dimensions = $packinglist->wf_pklist_get_new_dimensions($attachment[0], 30, 40);
						$this->Image($image_path,25,$this->starting_y+($cell_height/4),$dimensions['width']*0.264,$dimensions['height']*0.264,$image_format);
					}
				}
				$this->setXY(42,$this->starting_y);
				$this->MultiCell(30,$sku_cell_height,__($item['sku'],'wf-woocommerce-packing-list'),1,'C',true);
				$this->setXY(72,$this->starting_y);
				$this->MultiCell(50,$name_cell_height,__($item['name'].$item['variation_data'],'wf-woocommerce-packing-list'),1,'C',true);
				$this->setXY(122,$this->starting_y);
				$this->MultiCell(30,$cell_height,__($item['quantity'],'wf-woocommerce-packing-list'),1,'C',true);
				$this->setXY(152,$this->starting_y);
				$this->MultiCell(40,$cell_height,($item['weight']) ? $item['weight'] * $item['quantity'] . ' ' . get_option('woocommerce_weight_unit') : __('n/a', 'wf-woocommerce-packing-list'),1,'C',true);
				$this->starting_y += $cell_height;
			} else {
				$number_of_lines = $this->total_lines(65,$item['name'].$item['variation_data']);
				if($number_of_lines > 3) {
					$name_cell_height = 5;
					$cell_height = 5*$number_of_lines;
				} else {
					$name_cell_height = 15/$number_of_lines;
					$cell_height = 15;
				}
				$number_of_lines_sku = $this->total_lines(45, $item['sku']);
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
				$this->setXY(12,$this->starting_y);
				$this->MultiCell(45,$sku_cell_height,__($item['sku'],'wf-woocommerce-packing-list'),1,'C',true);
				$this->setXY(57,$this->starting_y);
				$this->MultiCell(65,$name_cell_height,__($item['name'].$item['variation_data'],'wf-woocommerce-packing-list'),1,'C',true);
				$this->setXY(122,$this->starting_y);
				$this->MultiCell(30,$cell_height,__($item['quantity'],'wf-woocommerce-packing-list'),1,'C',true);
				$this->setXY(152,$this->starting_y);
				$this->MultiCell(40,$cell_height,($item['weight']) ? $item['weight'] * $item['quantity'] . ' ' . get_option('woocommerce_weight_unit') : __('n/a', 'wf-woocommerce-packing-list'),1,'C',true);
				$this->setXY(152,$this->starting_y);
				$this->starting_y += $cell_height;
			}
			$counter++;
		}
	}
}
