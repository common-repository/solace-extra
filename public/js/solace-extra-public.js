(function( $ ) {
	'use strict';

	// Clipboard Click
	$('#copy-clipboard').on('click', function(event) {
		// preventDefault
		event.preventDefault();

		// Datas
		var clear;
		var msgDuration = 1500;
		var msgSuccess = 'URL has been copied to the clipboard!';
		var currentUrl = window.location.href;

		// Copy to clipboard function
		function copyToClipboard(text) {
			var $temp = $("<input>");
			$("body").append($temp);
			$temp.val(text).select();
			document.execCommand("copy");
			$temp.remove();
		}

		// Render message
		function render(message){
			hide();
			if ( message === 'success' ) {
				$('.notif-clipboard').addClass('msg-success active').text(msgSuccess);
			}
		}

		// Timer
		function timer(){
			clearTimeout(clear);
			clear = setTimeout(function(){
				hide();
			}, msgDuration)
		}

		// Remove class
		function hide(){
			$('.notif-clipboard').removeClass('msg-success active');
		}

		// Copy URL to clipboard and show notification
		copyToClipboard(currentUrl);

		// Running
		render('success');
		$('.notif-clipboard').on('transitionend', timer);	

	});

})( jQuery );
