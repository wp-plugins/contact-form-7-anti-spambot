/*
contact-form-7-anti-spambot plugin
http://wordpress.org/plugins/contact-form-7-anti-spambot/
*/
(function($) {
	if( (document.getElementById("wpcf7asb-input")!=null)
		||
		(wpcf7asb_debug==true)
		){
		var wpcf7asb_getkeytm;
			
		$('#wpcf7asb-input').closest('form').submit(function(event){
			if(wpcf7asb_getkeytm > parseInt( new Date() /1000 ) - 10 ){
				return true;
			}
			
			$.ajax({
		        type: 'POST',
		        url: wpcf7asb_ajaxurl,
		        cache: false,
	            dataType: 'text',
				data: {
		            'action' : 'wpcf7asb_currentkey',
		        },
		        success: function( response ){
		    		wpcf7asb_getkeytm = parseInt( new Date() /1000 );
					$('#wpcf7asb-input').val(response);		
					
					setTimeout(function(){
						$('#wpcf7asb-input').closest('form').find('input[type=submit]').click();
					},100);

		        },
		        error: function(){
					alert("Message sending not comleted.[Server Error]");

				}
		    });
			return false;

		});

		$(document).ready(function(){
			wpcf7asb_getkeytm = 0;
			if( typeof(wpcf7asb_debug) == "undefined"){
				jQuery("#wpcf7asb-input-block").css("display", "none");
			}

	    });
	}

})(jQuery);

