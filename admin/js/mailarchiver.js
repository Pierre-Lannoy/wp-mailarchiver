jQuery(document).ready( function($) {
	$('.mailarchiver-about-logo').css({opacity:1});
	$('#mailarchiver_listeners_options_auto').on('change', function() {
		if( 'auto' === this.value ) {
			$('#listeners-settings').addClass('hidden');
		} else {
			$('#listeners-settings').removeClass('hidden');
		}
	});
	$('#mailarchiver_listeners_options_auto').change();
} );
