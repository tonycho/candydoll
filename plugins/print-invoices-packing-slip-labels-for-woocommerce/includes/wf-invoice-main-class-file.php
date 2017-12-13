<?php

if (!defined('ABSPATH')) {
	exit;
}

if(!class_exists('WF_Invoice_Plugin_Main_Class'))
{
	class WF_Invoice_Plugin_Main_Class
	{
		 function __construct()
		 {
		 	//function to init values of the fields
		 	$this->wf_pklist_init_fields();
		 	
		 }
		 //function for initializing fields included from packaging type
		public function wf_pklist_init_fields()
		{
			$this->wf_package_type_options = array(
				'single_packing' => __('Single Package Per Order','wf-woocommerce-packing-list')
			);
			$this->create_package_documents  = array(
				'print_packing_list',
				'print_shipment_label',
				'print_delivery_note',
				'download_shipment_label',
				
			);
			$this->print_documents = array(
				'print_shipment_label' => 'Print Shipping Label',
				'print_invoice'        => 'Print Invoice',
				'print_packing_list'   => 'Print Packing List',
				'print_delivery_note'  => 'Print Delivery Note'
			);
			$this->download_documents = array(
				'download_shipment_label'     => 'Download Shipping Label',
				
			);
			$this->font_size_options = array(
				'medium'       => 'Medium',
			);
			$this->wf_pklist_font_list = $this->wf_pklist_get_fonts();
			$this->wf_package_type = 'single_packing';
			$this->weight_unit = get_option('woocommerce_weight_unit');
			$this->dimension_unit = get_option('woocommerce_dimension_unit');
			$this->wf_enable_contact_number = get_option('woocommerce_wf_packinglist_contact_number') !=''  ? get_option('woocommerce_wf_packinglist_contact_number') : 'Yes';
			$this->wf_generate_invoice_for = get_option('woocommerce_wf_generate_for_orderstatus') ? get_option('woocommerce_wf_generate_for_orderstatus') : array();
			$this->wf_pklist_font_name = 'arial';
			$this->wf_pklist_font_size = 'medium';				
			$this->wf_packinglist_plugin_path = $this->wf_packinglist_get_plugin_path();
			$this->invoice_labels = apply_filters('wf_pklist_modify_invoice_labels',$this->get_invoice_labels());
				
		}
	}
}

new WF_Invoice_Plugin_Main_Class()

?>