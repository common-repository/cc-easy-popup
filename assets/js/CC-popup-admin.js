"use strict";

/* ValidateForm */
var ValidateForm = function(){};
var vForm = new ValidateForm();

/**
 * [getRule description]
 * @param  {[type]} ruleName [description]
 * @return {[type]}          [description]
 */
ValidateForm.prototype.getRule = function(ruleName){
	var rules = {
		'required' : { 'rule': 'not-empty', 'message': '* required field' }
	};
	if (typeof(ruleName) != 'undefined' && ruleName != '' && ruleName in rules) {
		return rules[ruleName];
	}
	return {};
};

/**
 * [validate description]
 * @param  {[type]} field [description]
 * @return {[type]}       [description]
 */
ValidateForm.prototype.validate = function($field){
	if (typeof ($field) != 'undefined' && $field != ''){
		var validator = vForm.getRule($field.data('validation-rule'));
		switch(validator.rule){
			case 'not-empty':
				var value = jQuery.trim($field.val());
				if (value.length <= 0){
					if ($field.parent().find('span.validator-error').length <= 0) {
						$field.after('<span class="validator-error" style="color:red;display:block;">'+validator.message+'</span>');
					}
					$field.focus();
					return false;
				}
				else if ($field.parent().find('span.validator-error').length > 0){
					$field.parent().find('span.validator-error').remove();
				}
			break;
		}
	}
	else {
		var bool = true;
		jQuery('input[data-validation-rule], textarea[data-validation-rule], select[data-validation-rule]').each(function(index, element){
			if (vForm.validate(jQuery(element)) === false){
				bool = false;
			}
		});
		return bool;
	}
};


/* On page load */
jQuery(document).on('ready', function (){

	jQuery('#CC_newsletter_settings_form').on('submit', function(e){
		e.preventDefault();
		var formData = jQuery('#CC_newsletter_settings_form').serializeArray();
		formData.push({name:'action', value :'CPAS_ajax_update_settings'});
		if(vForm.validate()) {
			jQuery.ajax({
				type : 'POST',
				url: ajax_object.ajax_url,
				data: formData,
				dataType:"json",
				success : function(res){
					if (res.response) {
						location.reload(true);
					}
					else {
						alert("Problem occured! Kindly contact (Plugin Author)me.");
					}
				}
			});
		}
	});

	/* Trigger type and location on page ready */
	jQuery('#CC_popup_display_type').trigger('change');
	jQuery('#CC_popup_display_location').trigger('change');

});

jQuery(document).on('change', '#CC_popup_display_location', function(){
	var rowElement = jQuery(this).parent().parent();
	jQuery.ajax({
		type: 'POST',
		url: ajax_object.ajax_url,
		datatype: 'html',
		data: {action: 'CPAS_ajax_getAllPosts', 'value': jQuery(this).val()},
		success: function(res){
			res = jQuery.parseJSON(res);
			if (res.result) {
				rowElement.after(res.html);
			}
			else if(jQuery('.dyn_row').length > 0){
				jQuery('.dyn_row').remove();
			}
		}
	});
});

jQuery(document).on('change', '#CC_popup_display_type', function(){
	var rowElement = jQuery(this).parent().parent();
	jQuery.ajax({
		type: 'POST',
		url: ajax_object.ajax_url,
		datatype: 'html',
		data: {action: 'CPAS_ajax_getInputOptions', 'value': jQuery(this).val()},
		success: function(res){
			res = jQuery.parseJSON(res);
			if(jQuery('.dyn_row1').length > 0){
				jQuery('.dyn_row1').remove();
			}
			if (res.result) {
				rowElement.after(res.html);
			}
		}
	});
});

jQuery(document).on('mouseout mousedown keyup', '.wp-editor-container', function() {
	tinyMCE.triggerSave();
});

jQuery(document).ready(function(){
    
var CC_media_init = function(selector, button_selector)  {
    var clicked_button = false;
 
    jQuery(selector).each(function (i, input) {
        var button = jQuery(input).next(button_selector);
        button.click(function (event) {
            event.preventDefault();
            var selected_img;
            clicked_button = jQuery(this);
            console.log(wp)
 
            // check for media manager instance
            if(wp.media.frames.CC_frame) {
                wp.media.frames.CC_frame.open();
                return;
            }
            // configuration of the media manager new instance
            wp.media.frames.CC_frame = wp.media({
                title: 'Select image',
                multiple: false,
                library: {
                    type: 'image'
                },
                button: {
                    text: 'Use selected image'
                }
            });
 
            // Function used for the image selection and media manager closing
            var CC_media_set_image = function() {
                var selection = wp.media.frames.CC_frame.state().get('selection');
 
                // no selection
                if (!selection) {
                    return;
                }
 
                // iterate through selected elements
                selection.each(function(attachment) {
                    var url = attachment.attributes.url;
                    clicked_button.prev(selector).val(url);
                });
            };
 
            // closing event for media manger
            wp.media.frames.CC_frame.on('close', CC_media_set_image);
            // image selection event
            wp.media.frames.CC_frame.on('select', CC_media_set_image);
            // showing media manager
            wp.media.frames.CC_frame.open();
        });
   });
};

CC_media_init('.media-input', '.media-button');

});
