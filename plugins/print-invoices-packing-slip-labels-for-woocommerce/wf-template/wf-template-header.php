<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php _e('Print Invoice, Packing Slip, Delivery Note and Shipping Label','wf-woocommerce-packing-list');?></title>
		<link href="<?php echo $this->wf_packinglist_template('uri','wf-4-6-template-header.php');?>css/wf-packinglist.css" rel="stylesheet" type="text/css" media="scrren,print" />
		<link href="<?php echo $this->wf_packinglist_template('uri','wf-4-6-template-header.php');?>css/wf-packinglist-print.css" rel="stylesheet" type="text/css" media="print" />
	</head>
	<body onload="window.print()">
	<?php
		$heading_size;
		$title_size;
		$content_size;
		switch($this->wf_pklist_font_size) {
			case 'small':
				$heading_size = 23;
				$title_size = 16;
				$content_size = 14;
				break;
			case 'large':
				$heading_size = 27;
				$title_size = 20;
				$content_size = 18;
				break;
			default:
				$heading_size = 25;
				$title_size = 18;
				$content_size = 16;
				break;
		}
	?>