(function($){
	"use strict";

	// on document ready
	$(document).ready(function(){

		$('[data-confirm]').click(function(){
			
			var confirm_message = $(this).attr('data-confirm');
			var redirect_url = $(this).attr('href');

			tourmaster_confirm_box({
				sub: confirm_message,
				success: function(){
					window.location.href = redirect_url;
				}			
			});

			return false;
		});

	}); // document.ready

})(jQuery);