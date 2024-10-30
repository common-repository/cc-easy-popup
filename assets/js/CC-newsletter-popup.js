"use strict";

var Popup = function(){};
var Cookie= function(){};
var currentPopupId = null;

/**
 * Popup Close Method
 * @param  {popupId}
 * @return {null}
 */
Popup.prototype.close = function(popupId) {
	if (popupId != '' && popupId != null && jQuery('#'+popupId).hasClass('open')) {
		jQuery('#'+popupId).removeClass('open');
	}
	else {
		console.info('Current Popup could not be closed due to incorrent id : %s OR it may already opened!', popupId);
	}
};

/**
 * Popup Open Method
 * @param  {popupId, verifyCookie}
 * @return {null}
 */
Popup.prototype.open = function(popupId, verifyCookie) {
	if (typeof(verifyCookie) === 'string') {
		var cookie = Cookie.prototype.read('CC-newsletter-popup-shown');
		if (!!cookie) {
			console.info('Newsletter Popup has been viewed already');return false;
		}
		else {
			Cookie.prototype.write('CC-newsletter-popup-shown', true, 1);
		}
	}
	if (popupId != '' && popupId != null && !jQuery('#'+popupId).hasClass('open')) {
		jQuery('#'+popupId).addClass("open");
		currentPopupId = popupId;
	}
	else {
		console.warn('Current Popup could not be opened due to incorrent id : ', popupId);
	}
};

/**
 * [write description]
 * @param  {[type]} name     [description]
 * @param  {[type]} value    [description]
 * @param  {[type]} exp_days [description]
 * @return {[type]}          [description]
 */
Cookie.prototype.write = function(name, value, exp_days){
	var date = new Date();
	var days = exp_days || 1;
	date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
	window.document.cookie = name + "=" + value + "; expires=" + date.toGMTString() + "; path=/";
};

/**
 * [read description]
 * @param  {[type]} name [description]
 * @return {[type]}      [description]
 */
Cookie.prototype.read = function(name){
	var nameEQ = name + "=";
    var ca = window.document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
};

/**
 * [delete description]
 * @param  {[type]} name [description]
 * @return {[type]}      [description]
 */
Cookie.prototype.delete = function(name){
	Cookie.prototype.write(name, "", -1);
};

/** ---------- On Load ------------- **/
var CCPopup = new Popup();
var _cookie = new Cookie();

jQuery(document).on('ready', function (){

	jQuery('.CC-popup-trigger').on('click', function(event){
		event.preventDefault();
		var href = jQuery(this).attr('href');
		currentPopupId = href.replace("#","");
		CCPopup.open(currentPopupId);
	});
	jQuery('.CC-newsletter-popup .close').on('click', function(event){
		event.preventDefault();
		CCPopup.close(currentPopupId);
	});

	jQuery('.CC-newsletter-popup-overlay').on('click', function(event){
		event.preventDefault();
		if (jQuery(event.target).hasClass('open')) {
			CCPopup.close(currentPopupId);
		}
	});

	jQuery('body').on('keyup', function(event){
		if (event.keyCode == 27) {
			CCPopup.close(currentPopupId);
		}
	});

	var userAgent = window.navigator.userAgent;
	if (userAgent.indexOf('Firefox') > -1) {
		jQuery('.CC-newsletter-popup .email-section button').css('top','3px');
	}

	//CCPopup.open('CC_popup_id');

	// End of Onload
});