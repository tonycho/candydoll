jQuery(function($) {
var file_frame;
$(document).on('click', '.ed__load__image', function(e) {
	
    e.preventDefault();
 	var that = $(this);
	
    if (file_frame) file_frame.close();

    file_frame = wp.media.frames.file_frame = wp.media({
      title: $(this).data('uploader-title'),
      button: {
        text: $(this).data('uploader-button-text'),
      },
      multiple: true
    });

    file_frame.on('select', function() {
      var selection = file_frame.state().get('selection');

      selection.map(function(attachment, i) {
        attachment = attachment.toJSON();
		 that.parents('tr').find('input').val(attachment.url);
		 console.log(attachment.url);
	  });
    });

    file_frame.open();

  });
  
  
	$('.ed__settings__loops table').each(function(index, element) {
		
            var button_type = $(this).find('select.button_type').val();
			if(button_type == 1){
				$(this).find('input.ed_color_button_show').parents('tr').hide();
			}else if(button_type == 2){
				$(this).find('input.link_btn').parents('tr').hide();
			}else{
				$(this).find('input.common_hide,select.common_hide').parents('tr').hide();
			}
        });
		
		
 	if($('select.button_type').length){
		
			
		$(document).on('change', 'select.button_type', function(e) {
			
			if($(this).val()==1){
				$(this).parents('table').find('tr.common_hide').hide();
				$(this).parents('table').find('tr.ed_show_link_button').show();
			}else if($(this).val()==2){
				$(this).parents('table').find('tr.common_hide').hide();
				$(this).parents('table').find('tr.ed_show_color_button').show();
			}else if($(this).val()==3){
				$(this).parents('table').find('tr.common_hide').hide();
				$(this).parents('table').find('tr.ed_show_image_button').show();
			}else{
				$(this).parents('table').find('tr.common_hide').hide();
			}
		});
	}
	if($('.ed-color-field').length){
		$('.ed-color-field').wpColorPicker();
	}
	if($('.ed_woo_meta_uploader')){
		$(document).on('click', '.ed_woo_meta_uploader', function(e) {
			
			e.preventDefault();
			var that = $(this);
			
			if (file_frame) file_frame.close();
		
			file_frame = wp.media.frames.file_frame = wp.media({
			  title: $(this).data('uploader-title'),
			  button: {
				text: $(this).data('uploader-button-text'),
			  },
			  multiple: true
			});
		
			file_frame.on('select', function() {
			  var selection = file_frame.state().get('selection');
		
			  selection.map(function(attachment, i) {
				attachment = attachment.toJSON();
				that.parents('td.attribute_woo_var_style_img_row').find('input[type=hidden]').val(attachment.id);
				that.parents('td.attribute_woo_var_style_img_row').find('.ed_woo_img_wrp').html('<img class="image-preview" src="' + attachment.url + '">');
				console.log(attachment.url);
			  });
			});
		
			file_frame.open();
		
		  });
		  $(document).on('click', '.remove_ed_woo_meta_img', function(e) {
			e.preventDefault();
			
				$(this).parents('td.attribute_woo_var_style_img_row').find('input[type=hidden]').val(null);
				$(this).parents('td.attribute_woo_var_style_img_row').find('.ed_woo_img_wrp').html('');
			});
	}
	
	if($('select.ed_woo_load_attribute_configuration').length){
		
		$(document).on('change', 'select.ed_woo_load_attribute_configuration', function(e) {
			if($(this).val()==1){
				$(this).parents('div.ed_woo_otions_wrp').find('tr.ed_woo_var_color_attribute_configuration').show();
				$(this).parents('div.ed_woo_otions_wrp').find('tr.ed_woo_var_images_attribute_configuration').hide();
				$(this).parents('div.ed_woo_otions_wrp').find('tr.ed_woo_var_color_attribute_configuration').show();
				
			}else if($(this).val()==2){
				$(this).parents('div.ed_woo_otions_wrp').find('tr.ed_woo_var_color_attribute_configuration').hide();
				$(this).parents('div.ed_woo_otions_wrp').find('tr.ed_woo_var_images_attribute_configuration').show();
			}else{
				$(this).parents('div.ed_woo_otions_wrp').find('tr.ed_woo_var_images_attribute_configuration').hide();
				$(this).parents('div.ed_woo_otions_wrp').find('tr.ed_woo_var_color_attribute_configuration').hide();
			}
		});
	}
  	if($('.eds_attribute_heading').length){
		 $(document).on('click', '.eds_attribute_heading', function(e) {
				e.preventDefault();
				$(this).parents('li').find('.ed_woo_otions_wrp').slideToggle();
			});
	}
  
});

