jQuery(document).ready(function(){
	wf_packinglist_load_packing_method_options();
	jQuery('#woocommerce_wf_packinglist_package_type').change(function(){
		wf_packinglist_load_packing_method_options();
	});
	
	jQuery('.woocommerce_wf_packinglist_boxes .insert').click( function() {
	var $tbody = jQuery('.woocommerce_wf_packinglist_boxes').find('tbody');
	var size = $tbody.find('tr').size();
	var dimension_unit = jQuery('#dimension_unit').val();
	var weight_unit = jQuery('#weight_unit').val();
	var code = '<tr class="new">\
			<td class="check-column" style="padding: 10px; vertical-align: middle;"><input type="checkbox" /></td>\
			<td style="text-align: center;"><input type="text" size="5" name="woocommerce_wf_packinglist_boxes[' + size + '][length]" />'+dimension_unit+'</td>\
			<td style="text-align: center;"><input type="text" size="5" name="woocommerce_wf_packinglist_boxes[' + size + '][width]" />'+dimension_unit+'</td>\
			<td style="text-align: center;"><input type="text" size="5" name="woocommerce_wf_packinglist_boxes[' + size + '][height]" />'+dimension_unit+'</td>\
			<td style="text-align: center;"><input type="text" size="5" name="woocommerce_wf_packinglist_boxes[' + size + '][box_weight]" />'+weight_unit+'</td>\
			<td style="text-align: center;"><input type="text" size="5" name="woocommerce_wf_packinglist_boxes[' + size + '][max_weight]" />'+weight_unit+'</td>\
			<td style="text-align: center;"><input type="checkbox" name="woocommerce_wf_packinglist_boxes[' + size + '][enabled]" /></td>\
		</tr>';
	$tbody.append( code );
	return false;
	} );

	jQuery('.woocommerce_wf_packinglist_boxes .remove').click(function() {
		var $tbody = jQuery('.woocommerce_wf_packinglist_boxes').find('tbody');
		$tbody.find('.check-column input:checked').each(function() {
			jQuery(this).closest('tr').hide().find('input').val('');
		});
		return false;
	});
});

function wf_packinglist_load_packing_method_options(){
	pack_method	= jQuery('#woocommerce_wf_packinglist_package_type').val();
	switch(pack_method){
		case 'per_item':
			jQuery('#woocommerce_wf_packinglist_box_packing').hide();
			jQuery('#woocommerce_wf_packinglist_weight_pacakge_type').closest('tr').hide();
			jQuery('#woocommerce_wf_packinglist_weight_pacakge_maxweight').closest('tr').hide();
			break;
		case 'box_packing':
			jQuery('#woocommerce_wf_packinglist_box_packing').show();
			jQuery('#woocommerce_wf_packinglist_weight_pacakge_type').closest('tr').hide();
			jQuery('#woocommerce_wf_packinglist_weight_pacakge_maxweight').closest('tr').hide();
			break;
		case 'weight_based_packing':
			jQuery('#woocommerce_wf_packinglist_box_packing').hide();
			jQuery('#woocommerce_wf_packinglist_weight_pacakge_type').closest('tr').show();
			jQuery('#woocommerce_wf_packinglist_weight_pacakge_maxweight').closest('tr').show();
			break;
		default :
			jQuery('#woocommerce_wf_packinglist_box_packing').hide();
			jQuery('#woocommerce_wf_packinglist_weight_pacakge_type').closest('tr').hide();
			jQuery('#woocommerce_wf_packinglist_weight_pacakge_maxweight').closest('tr').hide();
			break;
	}
}

jQuery(document).ready(function() {
	if (jQuery('[name=woocommerce_wf_invoice_as_ordernumber]').is(':checked')){
		jQuery('.invoice_hide').hide();
	}
	jQuery('[name=woocommerce_wf_invoice_as_ordernumber]').click(function(){
		if(this.checked) {
			jQuery('.invoice_hide').hide();
		}else jQuery('.invoice_hide').show();
	});
	jQuery('[name=woocommerce_wf_invoice_regenerate]').click(function(){
		if(this.checked) {
			jQuery('[name=woocommerce_wf_invoice_start_number]').prop("readonly", false);
		}else jQuery('[name=woocommerce_wf_invoice_start_number]').prop("readonly", true); 
	});
	jQuery('#order_status').change(function() {
		//  Select options for Invoice select Box 
		var multipleValues = jQuery('#order_status').val();
		var multipletext = []; 
		jQuery('#order_status :selected').each(function(i, selected){ 
		  multipletext[i] = jQuery(selected).text(); 
		});
		jQuery('#invoice_pdf').select2('val',''); 
		var select = jQuery('#invoice_pdf');
		jQuery('option', select).remove();	
		for (i = 0; i < multipleValues.length; i++) { 
			jQuery('<option>').val(multipleValues[i]).text(multipletext[i]).appendTo("select[id='invoice_pdf']");
		}
		//  Select options for Packinglist select Box 
		jQuery('#packinglist_pdf').select2('val',''); 
		var select = jQuery('#packinglist_pdf');
		jQuery('option', select).remove();	
		for (i = 0; i < multipletext.length; i++) { 
			jQuery('<option>').val(multipleValues[i]).text(multipletext[i]).appendTo("select[id='packinglist_pdf']");
		}
		//  Select options for Delivery Note select Box 
		jQuery('#deliverynote_pdf').select2('val',''); 
		var select = jQuery('#deliverynote_pdf');
		jQuery('option', select).remove();	
		for (i = 0; i < multipletext.length; i++) { 
			jQuery('<option>').val(multipleValues[i]).text(multipletext[i]).appendTo("select[id='deliverynote_pdf']");
		}
	});					
});
