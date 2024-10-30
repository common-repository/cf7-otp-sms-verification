( function ( $ ) {
	"use strict";

	var resetOtp = {

		/**
		 * Init Function.
		 */
		init: function () {
			if ( ! reset_pass_obj.form_selector ) {
				return;
			}
			resetOtp.Select = reset_pass_obj.form_selector;
			resetOtp.countryCode = reset_pass_obj.country_code;
			resetOtp.Selector = 'form' + resetOtp.Select;
			resetOtp.addRequiredInputFields();
			resetOtp.bindEvents();
		},

		/**
		 * Add required Input Fields.
		 */
		addRequiredInputFields: function () {
			resetOtp.resetPassLink = '<a class="ihs-otp-password-reset-link btn" href="javascript:void(0)">Reset Password</a>';
			$( resetOtp.Selector ).append( resetOtp.resetPassLink );
		},

		/**
		 * Bind Events.
		 */
		bindEvents: function () {
			$( '.ihs-otp-password-reset-link' ).on( 'click', function () {
				if ( ! resetOtp.countryCode ) {
					resetOtp.countryCode = '+91';
				}

				var mobileInputEl = '<br><label id="ihs-otp-reset-pass-input"> Mobile Number (required)<br>\n' +
					'<span class="">' +
					'<span class="ihs-otp-prefix">+' + resetOtp.countryCode + ' - </span><input type="number" name="ihs-otp-reset-pass-input" value="" class="ihs-otp-reset-pass-input" aria-required="true" aria-invalid="false">' +
					'</span> ' +
					'</label>',
					sendPassBtn = '<div class="ihs-otp-send-pass-btn" id="ihs-otp-send-pass-btn">Send New Password</div>',
					content = mobileInputEl + sendPassBtn;
				$( resetOtp.Selector ).append( content );
				$( '.ihs-otp-password-reset-link' ).remove();
			} );
			$( resetOtp.Selector ).on( 'click', '.ihs-otp-send-pass-btn', function () {
				var mobileNumber = $( '.ihs-otp-reset-pass-input' ).val();
				if ( mobileNumber ) {
					if ( 10 <= mobileNumber.length ) {
						resetOtp.sendNewPassAjaxRequest( mobileNumber );
					} else {
						alerts.error(
							'Enter the correct Mobile Number','',{
								displayDuration: 3000
							});
					}
				} else {
					alerts.error(
						'Enter your Mobile Number','',{
							displayDuration: 3000
						});
				}
			} );
		},

		/**
		 * Send New Password Ajax Request.
		 *
		 * @param {int} mobileNumber
		 */
		sendNewPassAjaxRequest: function ( mobileNumber ) {
			var request = $.post(
				reset_pass_obj.ajax_url,   // this url till admin-ajax.php  is given by functions.php wp_localoze_script()
				{
					action: 'ihs_otp_reset_ajax_hook',
					security: reset_pass_obj.ajax_nonce,
					data: {
						mob: mobileNumber
					}
				}
			);

			request.done( function ( response ) {
				if ( response.data.otp_pin_sent_to_js ) {
					alerts.info(
						'New password sent to your mobile',
						{
							displayDuration: 0
						});
					$( '#ihs-otp-reset-pass-input' ).hide();
					$( '#ihs-otp-send-pass-btn' ).hide();
				}
			} );
		}
	},

	selector = 'form' + reset_pass_obj.form_selector;
	if ( $( selector ) ) {
		resetOtp.init();
	}

})( jQuery );
