jQuery(document).ready(function($) {
	"use strict";

	// Handling uploading of the logo on shipment label printing settings form.
	// Adapted from Mike Jolley
	// http://mikejolley.com/2012/12/using-the-new-wordpress-3-5-media-uploader-in-plugins/
	var file_frame;

	$('#upload_image_button').click(function( event ){

		event.preventDefault();
		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: jQuery( this ).data( 'uploader_title' ),
			button: {
				text: jQuery( this ).data( 'uploader_button_text' ),
			},
			// Set to true to allow multiple files to be selected
			multiple: false
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			var attachment = file_frame.state().get('selection').first().toJSON();
			// Send the value of attachment.url back to shipment label printing settings form
			jQuery('#woocommerce_wf_packinglist_logo').val(attachment.url);
		});

		// Finally, open the modal
		file_frame.open();
	});
	
	$('#invoice_upload_image_button').click(function( event ){

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
			jQuery('#woocommerce_wf_packinglist_invoice_logo').val(attachment.url);
		});

		// Finally, open the modal
		file_frame.open();
	});
});
