(function ( $ ) {
	"use strict";

	/**
	 * Toggles the mobile input field on the click on Create mobile input field.
	 */
	$( '.ihs-otp-mob-input' ).on( 'click', function () {
		if ( 'checked' === $( this ).attr( 'checked' ) ) {
			var inputVal = $( this ).val();
			if ( 'No' === inputVal ){
				$( '#ihs_otp_mobile_input_name' ).removeClass( 'ihs-otp-hide' );
			} else if ( 'Yes' === inputVal ) {
				$( '#ihs_otp_mobile_input_name' ).addClass( 'ihs-otp-hide' );
				$( '.ihs_otp_mob_input_name' ).val( '' );
			}
		}
	} );
})( jQuery );
