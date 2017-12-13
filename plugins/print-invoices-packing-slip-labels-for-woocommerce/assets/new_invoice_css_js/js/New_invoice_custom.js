$(document).ready(function(){

	$('input[type="checkbox"]').click(function(){
		var inputValue = $(this).attr("value");
		if($(this).is(":checked")) {
			if(inputValue == 'company_logo')
			{
				var text_width_value = $( "#company_logo_or_text" ).val();
				if(text_width_value !='logo')
				{
					$('#company_text_image').show();
				}
				else
				{
					$('#company_logo_image').show();
				}
			}
			if(inputValue == 'invoice_number')
			{
				$('#wf_invoice_name').show();
				$('#wf_packing_slip_name').show();
				$('#wf_delivery_note_name').show();
			}
			if(inputValue == 'invoice_date')
			{
				$('#wf_invoice_date').show();
				$('#wf_packing_slip_date').show();
				$('#wf_delivery_note_date').show();
			}
			if(inputValue == 'order_date')
			{
				$('#wf_order_date').show();
			}
			if(inputValue == 'from_address')
			{
				$('#wf_from_address_filed').show();
			}
			if(inputValue == 'billing_address')
			{
				$('#wf_billing_address_filed').show();
			}
			if(inputValue == 'shipping_address')
			{
				$('#wf_shipping_address_filed').show();
			}
			if(inputValue == 'email')
			{
				$('#wf_font_size_for_email').show();
			}
			if(inputValue == 'tel')
			{
				$('#wf_font_size_for_tel').show();
			}
			if(inputValue == 'vat')
			{
				$('#wf_font_size_for_vat').show();
			}

			if(inputValue == 'ssn')
			{
				$('#wf_font_size_for_ssn').show();
			}
			if(inputValue == 'tp')
			{
				$('#wf_font_size_for_tp').show();
			}
			if(inputValue == 'tn')
			{
				$('#wf_font_size_for_tn').show();
			}
			if(inputValue == 'product')
			{
				$('#wf_product_table_main_tag').show();
			}
		}else{
			if(inputValue == 'company_logo')
			{
				$('#company_logo_image').hide();
				$('#company_text_image').hide();
			}
			if(inputValue == 'invoice_number')
			{
				$('#wf_invoice_name').hide();
				$('#wf_packing_slip_name').hide();
				$('#wf_delivery_note_name').hide();
			}
			if(inputValue == 'invoice_date')
			{
				$('#wf_invoice_date').hide();
				$('#wf_packing_slip_date').hide();
				$('#wf_delivery_note_date').hide();

			}
			if(inputValue == 'order_date')
			{
				$('#wf_order_date').hide();

			}
			if(inputValue == 'from_address')
			{
				$('#wf_from_address_filed').hide();
			}
			if(inputValue == 'billing_address')
			{
				$('#wf_billing_address_filed').hide();
			}
			if(inputValue == 'shipping_address')
			{
				$('#wf_shipping_address_filed').hide();
			}
			if(inputValue == 'email')
			{
				$('#wf_font_size_for_email').hide();
			}
			if(inputValue == 'tel')
			{
				$('#wf_font_size_for_tel').hide();
			}
			if(inputValue == 'vat')
			{
				$('#wf_font_size_for_vat').hide();
			}
			if(inputValue == 'tp')
			{
				$('#wf_font_size_for_tp').hide();
			}
			if(inputValue == 'tn')
			{
				$('#wf_font_size_for_tn').hide();
			}
			if(inputValue == 'ssn')
			{
				$('#wf_font_size_for_ssn').hide();
			}
			if(inputValue == 'product')
			{
				$('#wf_product_table_main_tag').hide();
			}
		}       
		
	});


	//font size change invoice number
	jQuery("#collapseTwo1").on('keyup','#logo_extra_details_font',function(e){
		var text_width_value = $( "#logo_extra_details_font" ).val();
		if( $.isNumeric(text_width_value) )
		{
			jQuery('#wf_extra_data_below_logo').css('font-size',text_width_value+'px');
		}
	});
	jQuery("#collapseTwo1").on('click','#invoice_upload_image_button_cus',function( event ){

		event.preventDefault();
		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: jQuery( this ).data( 'invoice_uploader_title' ),
			button: {
				text: jQuery( this ).data( 'invoice_uploader_button_text' ),
			},
			// Set to true to allow multiple files to be selected
			multiple: false
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			var attachment = file_frame.state().get('selection').first().toJSON();
			// Send the value of attachment.url back to shipment label printing settings form
			jQuery('#woocommerce_wf_packinglist_invoice_logo_cus').val(attachment.url);
		});

		// Finally, open the modal
		file_frame.open();
	});

	//Invoice text change
	jQuery("#collapseTwo1").on('keyup','#logo_extra_details',function(e){
		var text_width_value = $( "#logo_extra_details" ).val();
		jQuery('#wf_extra_data_below_logo').text(text_width_value);
	});

	//height change for logo
	jQuery("#collapseTwo1").on('keyup','#logoheight',function(e){
		var text_height_value = $( "#logoheight" ).val();
		if( $.isNumeric(text_height_value) )
		{
			jQuery('#company_logo_image').prop("height",text_height_value);
		}
	});
	//width change for logo
	jQuery("#collapseTwo1").on('keyup','#logowidth',function(e){
		var text_width_value = $( "#logowidth" ).val();
		if( $.isNumeric(text_width_value) )
		{
			jQuery('#company_logo_image').prop("width",text_width_value);
		}
	});


	//width change for logo
	jQuery("#collapseTwo1").on('change','#company_logo_or_text',function(e){
		var text_width_value = $( "#company_logo_or_text" ).val();
		if( text_width_value != 'logo' )
		{
			jQuery('#company_logo_image').hide();
			jQuery('#company_text_image').show();


		}
		else{
			jQuery('#company_logo_image').show();
			jQuery('#company_text_image').hide();
			//jQuery('#company_logo_image').prop("src","https://www.xadapter.com/wp-content/uploads/2016/06/xadapter-logo-small.png");
		}
	}); 


	jQuery("#collapseThree1").on('change','#wf_invoice_number_font_weight',function(e){
		var text_width_value = $( "#wf_invoice_number_font_weight" ).val();

		jQuery('#wf_invoice_label').css('font-weight',text_width_value);
	});
	
	//font size change invoice number
	jQuery("#collapseThree1").on('keyup','#wf_invoice_font',function(e){
		var text_width_value = $( "#wf_invoice_font" ).val();
		if( $.isNumeric(text_width_value) )
		{
			jQuery('#wf_invoice_name').css('font-size',text_width_value+'px');
		}
	});

	//Invoice text change
	jQuery("#collapseThree1").on('keyup','#wf_invoice_number_text',function(e){
		var text_width_value = $( "#wf_invoice_number_text" ).val();
		jQuery('#wf_invoice_label').text(text_width_value);
	});
	


	//height change body color
	jQuery("#collapseThree1").on('change','#wf_invoice_number_color_code',function(e){
		var text_color_value = $( "#wf_invoice_number_color_code" ).val();
		
		jQuery('#wf_invoice_name').css("color", '#' + text_color_value , 'important');
		$("#wf_invoice_number_color_code_default").attr("checked", false);
	});


	//width change for logo
	jQuery("#collapseFour1").on('keyup','#wf_invoice_date_format',function(e){
		var text_width_value = $( "#wf_invoice_date_format" ).val();
		jQuery.ajax({
			type: 'post',
			url: ajaxurl,
			data: {
				action: 'wf_get_date_format_live',
				date_format: text_width_value
			},
			success: function (data) {

				jQuery('#wf_invoice_main_date').text(data);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log(textStatus, errorThrown);
			}
		});

		

	});	

//height change body color
jQuery("#collapseFour1").on('change','#wf_date_format_selection',function(e){
	var text_color_value = $( "#wf_date_format_selection" ).val();
	if(text_color_value != '0')
	{
		jQuery('#wf_invoice_date_format').val(text_color_value);

		var text_width_value = $( "#wf_invoice_date_format" ).val();
		jQuery.ajax({
			type: 'post',
			url: ajaxurl,
			data: {
				action: 'wf_get_date_format_live',
				date_format: text_width_value
			},
			success: function (data) {

				jQuery('#wf_invoice_main_date').text(data);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log(textStatus, errorThrown);
			}
		});
		
	}
});



//font size change invoice number
jQuery("#collapseFour1").on('keyup','#wf_invoice_date_font',function(e){
	var text_width_value = $( "#wf_invoice_date_font" ).val();
	if( $.isNumeric(text_width_value) )
	{
		jQuery('#wf_invoice_date').css('font-size',text_width_value+'px');
	}
});


//Invoice text change
jQuery("#collapseFour1").on('keyup','#wf_invoice_date_text',function(e){
	var text_width_value = $( "#wf_invoice_date_text" ).val();
	jQuery('#wf_invoice_date_label').text(text_width_value);
});

jQuery("#collapseFour1").on('change','#wf_invoice_date_font_weight',function(e){
	var text_width_value = $( "#wf_invoice_date_font_weight" ).val();

	jQuery('#wf_invoice_date_label').css('font-weight',text_width_value);
});

	//height change body color
	jQuery("#collapseFour1").on('change','#wf_invoice_date_color',function(e){
		var text_color_value = $( "#wf_invoice_date_color" ).val();
		
		jQuery('#wf_invoice_date').css("color", '#' + text_color_value , 'important');
		$("#wf_invoice_date_color_code_default").attr("checked", false);
	});

	//width change for logo
	jQuery("#collapse41").on('keyup','#wf_order_date_format',function(e){
		var text_width_value = $( "#wf_order_date_format" ).val();
		jQuery.ajax({
			type: 'post',
			url: ajaxurl,
			data: {
				action: 'wf_get_date_format_live',
				date_format: text_width_value
			},
			success: function (data) {

				jQuery('#wf_order_main_date').text(data);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log(textStatus, errorThrown);
			}
		});

	});	

//height change body color
jQuery("#collapse41").on('change','#wf_order_date_format_selection',function(e){
	var text_color_value = $( "#wf_order_date_format_selection" ).val();
	if(text_color_value != '0')
	{
		jQuery('#wf_order_date_format').val(text_color_value);

		var text_width_value = $( "#wf_order_date_format" ).val();
		jQuery.ajax({
			type: 'post',
			url: ajaxurl,
			data: {
				action: 'wf_get_date_format_live',
				date_format: text_width_value
			},
			success: function (data) {

				jQuery('#wf_order_main_date').text(data);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				console.log(textStatus, errorThrown);
			}
		});
		
	}
});



//font size change invoice number
jQuery("#collapse41").on('keyup','#wf_order_date_font',function(e){
	var text_width_value = $( "#wf_order_date_font" ).val();
	if( $.isNumeric(text_width_value) )
	{
		jQuery('#wf_order_date').css('font-size',text_width_value+'px');
	}
});


//Invoice text change
jQuery("#collapse41").on('keyup','#wf_order_date_text',function(e){
	var text_width_value = $( "#wf_order_date_text" ).val();
	jQuery('#wf_order_date_label').text(text_width_value);
});

jQuery("#collapse41").on('change','#wf_order_date_font_weight',function(e){
	var text_width_value = $( "#wf_order_date_font_weight" ).val();

	jQuery('#wf_order_date_label').css('font-weight',text_width_value);
});

	//height change body color
	jQuery("#collapse41").on('change','#wf_order_date_color',function(e){
		var text_color_value = $( "#wf_order_date_color" ).val();
		
		jQuery('#wf_order_date').css("color", '#' + text_color_value , 'important');
		$("#wf_order_date_color_code_default").attr("checked", false);
	});

	jQuery("#collapseFive1").on('keyup','#wf_from_address_title',function(e){
		var text_width_value = $( "#wf_from_address_title" ).val();
		jQuery('#wf_from_address_title_main').text(text_width_value);
	});




	jQuery("#collapseFive1").on('change','#wf_from_address_text_align',function(e){
		var text_width_value = $( "#wf_from_address_text_align" ).val();
		jQuery('#wf_from_address_filed').css('text-align',text_width_value);
	});

	//height change body color
	jQuery("#collapseFive1").on('change','#wf_from_address_color_code',function(e){
		var text_color_value = $( "#wf_from_address_color_code" ).val();
		
		jQuery('#wf_from_address_filed').css("color", '#' + text_color_value , 'important');
		$("#wf_from_address_color_code_default").attr("checked", false);
	});

//billing address

jQuery("#collapsesix1").on('keyup','#wf_billing_address_title',function(e){
	var text_width_value = $( "#wf_billing_address_title" ).val();
	jQuery('#wf_billing_address_title_main').text(text_width_value);
});




jQuery("#collapsesix1").on('change','#wf_billing_address_text_align',function(e){
	var text_width_value = $( "#wf_billing_address_text_align" ).val();
	jQuery('#wf_billing_address_filed').css('text-align',text_width_value);
});

	//height change body color
	jQuery("#collapsesix1").on('change','#wf_billing_address_color_code',function(e){
		var text_color_value = $( "#wf_billing_address_color_code" ).val();
		
		jQuery('#wf_billing_address_filed').css("color", '#' + text_color_value , 'important');
		$("#wf_billing_address_color_code_default").attr("checked", false);
	});

	//shipping address

	jQuery("#collapse71").on('keyup','#wf_shipping_address_title',function(e){
		var text_width_value = $( "#wf_shipping_address_title" ).val();
		jQuery('#wf_shipping_address_title_main').text(text_width_value);
	});


	jQuery("#collapse71").on('change','#wf_shipping_address_text_align',function(e){
		var text_width_value = $( "#wf_shipping_address_text_align" ).val();
		jQuery('#wf_shipping_address_filed').css('text-align',text_width_value);
	});

	//height change body color
	jQuery("#collapse71").on('change','#wf_shipping_address_color_code',function(e){
		var text_color_value = $( "#wf_shipping_address_color_code" ).val();
		
		jQuery('#wf_shipping_address_filed').css("color", '#' + text_color_value , 'important');
		$("#wf_shipping_address_color_code_default").attr("checked", false);
	});

// Email

jQuery("#collapse81").on('keyup','#email_font',function(e){
	var text_width_value = $( "#email_font" ).val();
	if( $.isNumeric(text_width_value) )
	{
		jQuery('#wf_font_size_for_email').css('font-size',text_width_value+'px');
	}
});


jQuery("#collapse81").on('keyup','#email_text',function(e){
	var text_width_value = $( "#email_text" ).val();
	jQuery('#wf_email_text_main').text(text_width_value);
});

jQuery("#collapse81").on('change','#wf_email_text_align',function(e){
	var text_width_value = $( "#wf_email_text_align" ).val();
	jQuery('#wf_font_size_for_email').css('text-align',text_width_value);
});

jQuery("#collapse81").on('change','#wf_email_color_code',function(e){
	var text_color_value = $( "#wf_email_color_code" ).val();
	
	jQuery('#wf_font_size_for_email').css("color", '#' + text_color_value , 'important');
	$("#wf_email_color_code_default").attr("checked", false);
});

//tel


jQuery("#collapse91").on('keyup','#tel_font',function(e){
	var text_width_value = $( "#tel_font" ).val();
	if( $.isNumeric(text_width_value) )
	{
		jQuery('#wf_font_size_for_tel').css('font-size',text_width_value+'px');
	}
});


jQuery("#collapse91").on('keyup','#tel_text',function(e){
	var text_width_value = $( "#tel_text" ).val();
	jQuery('#wf_tel_text_main').text(text_width_value);
});

jQuery("#collapse91").on('change','#wf_tel_text_align',function(e){
	var text_width_value = $( "#wf_tel_text_align" ).val();
	jQuery('#wf_font_size_for_tel').css('text-align',text_width_value);
});

jQuery("#collapse91").on('change','#wf_tel_color_code',function(e){
	var text_color_value = $( "#wf_tel_color_code" ).val();
	
	jQuery('#wf_font_size_for_tel').css("color", '#' + text_color_value , 'important');
	$("#wf_tel_color_code_default").attr("checked", false);
});

//VAT

jQuery("#collapse101").on('keyup','#vat_font',function(e){
	var text_width_value = $( "#vat_font" ).val();
	if( $.isNumeric(text_width_value) )
	{
		jQuery('#wf_font_size_for_vat').css('font-size',text_width_value+'px');
	}
});


jQuery("#collapse101").on('keyup','#vat_text',function(e){
	var text_width_value = $( "#vat_text" ).val();
	jQuery('#wf_vat_text_main').text(text_width_value);
});

jQuery("#collapse101").on('change','#wf_vat_text_align',function(e){
	var text_width_value = $( "#wf_vat_text_align" ).val();
	jQuery('#wf_font_size_for_vat').css('text-align',text_width_value);
});

jQuery("#collapse101").on('change','#wf_vat_color_code',function(e){
	var text_color_value = $( "#wf_vat_color_code" ).val();
	
	jQuery('#wf_font_size_for_vat').css("color", '#' + text_color_value , 'important');
	$("#wf_vat_color_code_default").attr("checked", false);
});


	//SSN

	jQuery("#collapse111").on('keyup','#ssn_font',function(e){
		var text_width_value = $( "#ssn_font" ).val();
		if( $.isNumeric(text_width_value) )
		{
			jQuery('#wf_font_size_for_ssn').css('font-size',text_width_value+'px');
		}
	});


	jQuery("#collapse111").on('keyup','#ssn_text',function(e){
		var text_width_value = $( "#ssn_text" ).val();
		jQuery('#wf_ssn_text_main').text(text_width_value);
	});

	jQuery("#collapse111").on('change','#wf_ssn_text_align',function(e){
		var text_width_value = $( "#wf_ssn_text_align" ).val();
		jQuery('#wf_font_size_for_ssn').css('text-align',text_width_value);
	});

	jQuery("#collapse111").on('change','#wf_ssn_color_code',function(e){
		var text_color_value = $( "#wf_ssn_color_code" ).val();
		
		jQuery('#wf_font_size_for_ssn').css("color", '#' + text_color_value , 'important');
		$("#wf_ssn_color_code_default").attr("checked", false);
	});

//Tracking Provider

jQuery("#collapse121").on('keyup','#tp_font',function(e){
	var text_width_value = $( "#tp_font" ).val();
	if( $.isNumeric(text_width_value) )
	{
		jQuery('#wf_font_size_for_tp').css('font-size',text_width_value+'px');
	}
});


jQuery("#collapse121").on('keyup','#tp_text',function(e){
	var text_width_value = $( "#tp_text" ).val();
	jQuery('#wf_tp_text_main').text(text_width_value);
});

jQuery("#collapse121").on('change','#wf_tp_text_align',function(e){
	var text_width_value = $( "#wf_tp_text_align" ).val();
	jQuery('#wf_font_size_for_tp').css('text-align',text_width_value);
});

jQuery("#collapse121").on('change','#wf_tp_color_code',function(e){
	var text_color_value = $( "#wf_tp_color_code" ).val();
	
	jQuery('#wf_font_size_for_tp').css("color", '#' + text_color_value , 'important');
	$("#wf_tp_color_code_default").attr("checked", false);
});

	//Tracking Number


	jQuery("#collapse131").on('keyup','#tn_font',function(e){
		var text_width_value = $( "#tn_font" ).val();
		if( $.isNumeric(text_width_value) )
		{
			jQuery('#wf_font_size_for_tn').css('font-size',text_width_value+'px');
		}
	});


	jQuery("#collapse131").on('keyup','#tn_text',function(e){
		var text_width_value = $( "#tn_text" ).val();
		jQuery('#wf_tn_text_main').text(text_width_value);
	});

	jQuery("#collapse131").on('change','#wf_tn_text_align',function(e){
		var text_width_value = $( "#wf_tn_text_align" ).val();
		jQuery('#wf_font_size_for_tn').css('text-align',text_width_value);
	});

	jQuery("#collapse131").on('change','#wf_tn_color_code',function(e){
		var text_color_value = $( "#wf_tn_color_code" ).val();
		
		jQuery('#wf_font_size_for_tn').css("color", '#' + text_color_value , 'important');
		$("#wf_tn_color_code_default").attr("checked", false);
	});

//Produt table

jQuery("#collapse141").on('change','#wf_head_back_code',function(e){
	var text_color_value = $( "#wf_head_back_code" ).val();
	
	jQuery('#wf_product_head').css("background", '#' + text_color_value , 'important');
	$("#wf_head_back_color_code_default").attr("checked", false);
});
jQuery("#collapse141").on('change','#wf_head_front_code',function(e){
	var text_color_value = $( "#wf_head_front_code" ).val();
	
	jQuery('#wf_product_head').css("color", '#' + text_color_value , 'important');
	$("#wf_head_front_color_code_default").attr("checked", false);
});

jQuery("#collapse141").on('change','#wf_body_front_code',function(e){
	var text_color_value = $( "#wf_body_front_code" ).val();
	
	jQuery('#wf_product_body').css("color", '#' + text_color_value , 'important');
	$("#wf_body_front_color_code_default").attr("checked", false);
});


jQuery("#collapse141").on('change','#wf_get_text_align_head',function(e){
	var text_width_value = $( "#wf_get_text_align_head" ).val();
	jQuery('#wf_head_tr_align_purpose').css('text-align',text_width_value);
});

jQuery("#collapse141").on('change','#wf_get_text_align_body',function(e){
	var text_width_value = $( "#wf_get_text_align_body" ).val();
	jQuery('#wf_product_body').css('text-align',text_width_value);
});

jQuery("#collapse141").on('keyup','#sku_text',function(e){
	var text_width_value = $( "#sku_text" ).val();
	jQuery('#th_sku').text(text_width_value);
});
jQuery("#collapse141").on('keyup','#img_text',function(e){
	var text_width_value = $( "#img_text" ).val();
	jQuery('#th_img').text(text_width_value);
});
jQuery("#collapse141").on('keyup','#product_text',function(e){
	var text_width_value = $( "#product_text" ).val();
	jQuery('#th_product').text(text_width_value);
});
jQuery("#collapse141").on('keyup','#qty_text',function(e){
	var text_width_value = $( "#qty_text" ).val();
	jQuery('#th_qty').text(text_width_value);
});
jQuery("#collapse141").on('keyup','#total_text',function(e){
	var text_width_value = $( "#total_text" ).val();
	jQuery('#th_total').text(text_width_value);
});
jQuery("#collapse141").on('keyup','#tw_text',function(e){
	var text_width_value = $( "#tw_text" ).val();
	jQuery('#th_total_weight').text(text_width_value);
});

jQuery("#collapse151").on('keyup','#wf_subtotal_text',function(e){
	var text_width_value = $( "#wf_subtotal_text" ).val();
	jQuery('#wf_id_for_st').text(text_width_value);
});
jQuery("#collapse151").on('keyup','#wf_shipping_text',function(e){
	var text_width_value = $( "#wf_shipping_text" ).val();
	jQuery('#wf_id_for_shipping').text(text_width_value);
});
jQuery("#collapse151").on('keyup','#wf_cd_text',function(e){
	var text_width_value = $( "#wf_cd_text" ).val();
	jQuery('#wf_id_for_cd').text(text_width_value);
});
jQuery("#collapse151").on('keyup','#wf_od_text',function(e){
	var text_width_value = $( "#wf_od_text" ).val();
	jQuery('#wf_id_for_od').text(text_width_value);
});
jQuery("#collapse151").on('keyup','#wf_tt_text',function(e){
	var text_width_value = $( "#wf_tt_text" ).val();
	jQuery('#wf_id_for_tt').text(text_width_value);
});
jQuery("#collapse151").on('keyup','#wf_total_text',function(e){
	var text_width_value = $( "#wf_total_text" ).val();
	jQuery('#wf_id_for_total').text(text_width_value);
});
jQuery("#collapse151").on('keyup','#wf_paym_text',function(e){
	var text_width_value = $( "#wf_paym_text" ).val();
	jQuery('#wf_id_for_paym').text(text_width_value);
});

});

	//Preview for Print
	function PrintElem(elem,WF_INVOICE_MAIN_ROOT_PATH,action)
	{
		var mywindow = window.open('', 'PRINT', 'height=scrren.height,width=screen.width');
		mywindow.document.write('<html><head><title>' + document.title  + '</title>');
		mywindow.document.write('<link href="'+WF_INVOICE_MAIN_ROOT_PATH.trim()+'assets/new_invoice_css_js/dist/css/bootstrap.min.css" rel="stylesheet">');
		mywindow.document.write('<link href="'+WF_INVOICE_MAIN_ROOT_PATH.trim()+'assets/new_invoice_css_js/font-awesome/css/font-awesome.min.cs" rel="stylesheet">');
		mywindow.document.write('<link href="'+WF_INVOICE_MAIN_ROOT_PATH.trim()+'assets/new_invoice_css_js/css/custom.min.css" rel="stylesheet">');
		mywindow.document.write('</head><body><div class="container body"><div class="main_container"><div class="" ><div class="x_content">');
		mywindow.document.write(document.getElementById(elem).innerHTML);
		mywindow.document.write('</div></div></div></div></body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        if(action == 'print')
        {
        	mywindow.print();
        }

        return true;

    }