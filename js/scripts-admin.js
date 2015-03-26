/*
contact-form-7-anti-spambot plugin
wordpress.org/plugins/contact-form-7-anti-spambot/
*/
jQuery(function($) {
	$('#wpcf7asb_loglist .ditail').not(':first').hide();
	$('#wpcf7asb_loglist .dathead').click(function() {
	    if($(this).next('.ditail').is(':visible')) {
	        $(this).next('.ditail').slideUp(300);
	    } else {
	        $(this).next('.ditail').slideDown(300).siblings('.ditail').slideUp(300);
	    }
	});
});
