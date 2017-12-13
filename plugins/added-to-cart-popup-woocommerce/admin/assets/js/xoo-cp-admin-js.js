jQuery(document).ready(function($){
	
	//Color Picker
	$(function(){
		$('.color-field').wpColorPicker();
	})

	//Tabs Change
	$('.xoo-tabs li').on('click',function(){
		var tab_class = $(this).attr('class').split(' ')[0];
		$('li').removeClass('active-tab');
		$('.settings-tab').removeClass('settings-tab-active');
		$(this).addClass('active-tab');
		var class_c = $('[tab-class='+tab_class+']').attr('class');
		$('[tab-class='+tab_class+']').attr('class',class_c+' settings-tab-active');
	})

	//Product details
	$('#xoo-cp-gl-pden').on('change',function(){
		if($(this).is(':checked')){
			$('#xoo-cp-gl-ibtne , #xoo-cp-gl-qtyen').parents('tr').show();
		}
		else{
			$('#xoo-cp-gl-ibtne , #xoo-cp-gl-qtyen').parents('tr').hide();
		}
	}).trigger('change');


})