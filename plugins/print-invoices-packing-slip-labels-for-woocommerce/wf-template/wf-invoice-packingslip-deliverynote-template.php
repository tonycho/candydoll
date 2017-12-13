<?php
require('pdf-templates/tfpdf.php');
class Pdf_Invoice_Packingslip_Deliverynote  extends tFPDF
{
	public $heading_size;
	public $title_size;
	public $content_size;
	function init($font, $font_size)
	{
		$this->AddPage();
		$font_name;
		if($font == 'big5') {
			$font_name = 'big5.ttf';
		} else {
			$font_name = 'arial.ttf';
		}
		$this->AddFont('DejaVu','',$font_name,true);
		$this->SetFont('DejaVu','',8);
		switch($font_size) {
			case 'small':
				$this->heading_size = 23;
				$this->title_size = 13;
				$this->content_size = 8;
				break;
			case 'large':
				$this->heading_size = 27;
				$this->title_size = 17;
				$this->content_size = 12;
				break;
			default:
				$this->heading_size = 25;
				$this->title_size = 15;
				$this->content_size = 10;
				break;
		}
	}
	
	function add_Companyname($companyname){
		$this->SetFontSize($this->heading_size);
		$this->setXY(65,3);
		$this->Cell(50,20,__($companyname,'wf-woocommerce-packing-list'),0,0,'R');
	}
	 
	function from_address($faddress){
		$this->SetFontSize($this->content_size);
		$y_position=7;
		foreach($faddress as $value){
			$this->setXY(-65,$y_position);
			if(!empty($value)){
				$this->Cell(40,10,__($value,'wf-woocommerce-packing-list'),0,1);
				$y_position+=5;
			}
		}
	}
	
	function billing_address($order){
		$N=55;
		$this->SetFontSize($this->title_size);
		$this->setXY(10,55);	
		$this->Cell(40,5,__('Billing address: ','wf-woocommerce-packing-list'),0,1);
		$this->SetFontSize($this->content_size);
		$billingaddress=$order->get_formatted_billing_address();
		$billingaddressarray=explode("<br/>",$billingaddress);
		 foreach ($billingaddressarray as $value){
			$this->setXY(10,$N);				 
			$this->Ln(3);
			$this->Cell(40,10,__($value,'wf-woocommerce-packing-list'),0,1);
			$N+=5;
		 }
			if(!empty($order->billing_SSN)) 
			$this->Cell(40,5,__('SSN: ','wf-woocommerce-packing-list').$order->billing_SSN,0,1);
		
			if(!empty($order->billing_VAT))
			$this->Cell(40,5,__('VAT: ','wf-woocommerce-packing-list').$order->billing_VAT,0,1);
		
			$this->Cell(40,5,__('Email: ','wf-woocommerce-packing-list').$order->billing_email,0,1);
		
			$this->Cell(40,5,__('Phone: ','wf-woocommerce-packing-list').$order->billing_phone,0,1);
	}
	
	function shipping_address($order){
		$X=-100;$Y=58;
		$this->SetFontSize($this->title_size);
		$this->setXY(-100,57);
		$this->Cell(40,2,__('Shipping address: ','wf-woocommerce-packing-list'),0,1);
		$this->SetFontSize($this->content_size);
		$shippingaddress=$order->get_formatted_shipping_address();
		$shipingaddressarray=explode("<br/>",$shippingaddress);
		foreach ($shipingaddressarray as $value){ 
			$this->setXY($X,$Y);
			$this->Cell(40,10,__($value,'wf-woocommerce-packing-list'),0,1);
			$Y+=5;
		}
	}
	
	//function to calculate total number of lines for product name 
	function total_lines($w, $txt, $h=1, $border=0, $align='J', $fill=false)
	{
		//Computes the number of lines a MultiCell of width w will take
		$cw = &$this->CurrentFont['cw'];
		if($w==0)
			$w = $this->w-$this->rMargin-$this->x;
		$wmax = ($w-2*$this->cMargin);
		$s = str_replace("\r",'',$txt);
		if ($this->unifontSubset) {
			$nb=mb_strlen($s, 'utf-8');
			while($nb>0 && mb_substr($s,$nb-1,1,'utf-8')=="\n")	$nb--;
		} else {
			$nb = strlen($s);
			if($nb>0 && $s[$nb-1]=="\n")
				$nb--;
		}
		$b = 0;
		if($border) {
			if($border==1){
				$border = 'LTRB';
				$b = 'LRT';
				$b2 = 'LR';
			} else {
				$b2 = '';
				if(strpos($border,'L')!==false)
					$b2 .= 'L';
				if(strpos($border,'R')!==false)
					$b2 .= 'R';
				$b = (strpos($border,'T')!==false) ? $b2.'T' : $b2;
			}
		}
		$sep = -1;
		$i = 0;
		$j = 0;
		$l = 0;
		$ns = 0;
		$nl = 1;
		while($i<$nb) {
			// Get next character
			if ($this->unifontSubset) {
				$c = mb_substr($s,$i,1,'UTF-8');
			} else {
				$c=$s[$i];
			}
			if($c=="\n") {
				// Explicit line break
				if($this->ws>0) {
					$this->ws = 0;
				}
				$i++;
				$sep = -1;
				$j = $i;
				$l = 0;
				$ns = 0;
				$nl++;
				if($border && $nl==2)
					$b = $b2;
				continue;
			}
			if($c==' ')	{
				$sep = $i;
				$ls = $l;
				$ns++;
			}
			if ($this->unifontSubset) { $l += $this->GetStringWidth($c); }
			else { $l += $cw[$c]*$this->FontSize/1000; }
			if($l>$wmax) {
				// Automatic line break
				if($sep==-1) {
					if($i==$j)
						$i++;
					if($this->ws>0) {
						$this->ws = 0;
					}
				} else {
					if($align=='J') {
						$this->ws = ($ns>1) ? ($wmax-$ls)/($ns-1) : 0;
					}
					$i = $sep+1;
				}
				$sep = -1;
				$j = $i;
				$l = 0;
				$ns = 0;
				$nl++;
				if($border && $nl==2)
					$b = $b2;
			}
			else
				$i++;
		}
		return $nl;
	}

}
