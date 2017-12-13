<?php
include_once('pdf-templates/tfpdf.php');
class PDF4x6 extends tFPDF
{
	//function to addpage
	public $xfactor=0;
	public $yfactor=0;
	public $fontfactor=1;
	public $to_title_size;
	public $to_content_size;
	public $from_title_size;
	public $from_content_size;
	public $phone_content_size;
	public $tracking_content_size;
	function init($par, $font, $font_size)
	{
		$this->AddPage();
		$font_name;
		$font_name = 'arial.ttf';
		$this->AddFont('DejaVu','',$font_name,true);
		$this->SetFont('DejaVu','',8*$this->xfactor);
		$this->xfactor=$par+0.18;
		if($this->xfactor>1) {
			$this->yfactor=2.5;
			$this->fontfactor=2;
		} else {
			$this->yfactor=2.5;
			$this->fontfactor=1.5;
		}
		$this->font_size($font_size);
	}

	//function to add company name
	function addCompanyname($companyname)
	{
		$this->SetFontSize($this->to_content_size*$this->fontfactor);
		$this->Cell(50*$this->xfactor,20,__($companyname,'wf-woocommerce-packing-list'),0,0,'R');
		$this->Ln(4);
	}

	//function to add shipping to address
	function addShippingToAddress($addr, $order_additional_information)
	{
		$i=28*$this->yfactor;
		$x=20*$this->xfactor;
		$this->setXY($x,25*$this->yfactor);
		$this->SetFontSize($this->to_title_size*$this->fontfactor);
		$this->Cell(65*$this->xfactor,5*$this->yfactor,__('To','wf-woocommerce-packing-list'),0,0,'L');
		$this->SetFontSize($this->to_content_size*$this->fontfactor);
		$this->setXY($x,$i);
		$this->Cell(65*$this->xfactor,5*$this->yfactor,__($addr['first_name'].' '.$addr['last_name'],'wf-woocommerce-packing-list'),0,0,'L');
		$this->Ln(5);
		$this->setyval($x);
		$this->Cell(65*$this->xfactor,5*$this->yfactor,__($addr['company'],'wf-woocommerce-packing-list'),0,0,'L');
		$this->Ln(5);
		$this->setyval($x);
		$this->Cell(65*$this->xfactor,5*$this->yfactor,__($addr['address_1'],'wf-woocommerce-packing-list'),0,0,'L');
		if($addr['address_2']!='')
		{
			$this->Ln(5);
			$this->setyval($x);
			$this->Cell(65*$this->xfactor,5*$this->yfactor,__($addr['address_2'],'wf-woocommerce-packing-list'),0,0,'L');
		}
		$this->Ln(5);
		$this->setyval($x);
		$this->Cell(65*$this->xfactor,5*$this->yfactor,__($addr['city'].' - '.$addr['postcode'],'wf-woocommerce-packing-list'),0,0,'L');
		$this->Ln(5);
		$this->setyval($x);
		$this->Cell(65*$this->xfactor,5*$this->yfactor,__($addr['state'].', '.$addr['country'],'wf-woocommerce-packing-list'),0,0,'L');
		if(key_exists('phone',$addr)) {
			$this->Ln(5);
			$this->setXY($x,($this->getY()+6));
			$this->SetFontSize($this->phone_content_size*$this->fontfactor);
			$this->Cell(65*$this->xfactor,5*$this->yfactor,__('Ph no:'.$addr['phone'],'wf-woocommerce-packing-list'),0,0,'L');
		}
	}

	//function to set XY
	function setyval($x)
	{
		$this->setXY($x,($this->getY()+3));
	}

	//function to add from address
	function addShippingFromAddress($faddress, $orderdata, $order_additional_information)
	{
		$x=12;
		$this->setXY($x,($this->getY()+(8*$this->yfactor)));
		$i=$this->getY()+(2*$this->yfactor);
		$this->SetFontSize($this->from_title_size*$this->fontfactor);
		$this->Cell(35*$this->xfactor,5*$this->yfactor,__('FROM','wf-woocommerce-packing-list'),0,0,'L');
		$this->SetFontSize($this->from_content_size*$this->fontfactor);
		if(!empty($orderdata)) {
			$this->Cell(22*$this->xfactor,5*$this->yfactor,__('Order Number ','wf-woocommerce-packing-list'),0,0,'L');
			$this->Cell(1*$this->xfactor,5*$this->yfactor,__(': ','wf-woocommerce-packing-list'),0,0,'R');
			$this->Cell(10*$this->xfactor,5*$this->yfactor,__($orderdata['order_id'],'wf-woocommerce-packing-list'),0,0,'L');
			$this->setXY($x,$i);
			$this->Cell(35*$this->xfactor,5*$this->yfactor,__($faddress['sender_name'],'wf-woocommerce-packing-list'),0,0,'L');
			$this->Cell(22*$this->xfactor,5*$this->yfactor,__('Weight ','wf-woocommerce-packing-list'),0,0,'L');
			$this->Cell(1*$this->xfactor,5*$this->yfactor,__(': ','wf-woocommerce-packing-list'),0,0,'R');
			$this->Cell(10*$this->xfactor,5*$this->yfactor,__($orderdata['weight'],'wf-woocommerce-packing-list'),0,0,'L');
		}
		$this->Ln(1);
		$this->setyval($x);
		$this->Cell(35*$this->xfactor,5*$this->yfactor,__($faddress['sender_address_line1'],'wf-woocommerce-packing-list'),0,0,'L');
		if (key_exists('ship_date', $order_additional_information)) {
			$this->Cell(22*$this->xfactor,5*$this->yfactor,__('Ship Date ','wf-woocommerce-packing-list'),0,0,'L');
			$this->Cell(1*$this->xfactor,5*$this->yfactor,__(': ','wf-woocommerce-packing-list'),0,0,'R');
			$this->Cell(10*$this->xfactor,5*$this->yfactor,__($order_additional_information['ship_date'],'wf-woocommerce-packing-list'),0,0,'L');
		}
		if($faddress['sender_address_line2']!='')
		{
			$this->Ln(1);
			$this->setyval($x);
			$this->Cell(65*$this->xfactor,5*$this->yfactor,__($faddress['sender_address_line2'],'wf-woocommerce-packing-list'),0,0,'L');
		}
		$this->Ln(1);
		$this->setyval($x);
		$this->Cell(65*$this->xfactor,5*$this->yfactor,__($faddress['sender_city'],'wf-woocommerce-packing-list'),0,0,'L');
		$this->Ln(1);
		$this->setyval($x);
		$this->Cell(65*$this->xfactor,5*$this->yfactor,__($faddress['sender_country'].' - '. $faddress['sender_postalcode'],'wf-woocommerce-packing-list'),0,0,'L');
		$this->setyval($x);
	}
	
	//function to set font size
	function font_size($font_size)
	{
		switch($font_size) {
			case 'large':
				$this->to_title_size = 15;
				$this->to_content_size = 11;
				$this->from_title_size = 10;
				$this->from_content_size = 6;
				$this->phone_content_size = 9;
				$this->tracking_content_size = 7;
				break;
			case 'small':
				$this->to_title_size = 13;
				$this->to_content_size = 9;
				$this->from_title_size = 8;
				$this->from_content_size = 4;
				$this->phone_content_size = 7;
				$this->tracking_content_size = 5;
				break;
			default:
				$this->to_title_size = 14;
				$this->to_content_size = 10;
				$this->from_title_size = 9;
				$this->from_content_size = 5;
				$this->phone_content_size = 8;
				$this->tracking_content_size = 6;
				break;
		}
	}
	
}
