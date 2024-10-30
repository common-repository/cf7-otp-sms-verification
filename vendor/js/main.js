( function ( $ ) {
	"use strict";

	var otp = {

		mobileInputElement: '',
		mobileInputSelector: '',
		submitBtnSelector: '',
		mobileOtpInputEl: '',
		sendOtpBtnEl: '',
		verifyOtpBtnEl: '',
		otpPinSent: '',
		mobileVerified: false,

		/**
		 * Init function.
		 */
		init: function () {
			if (  '' === otp_obj.form_selector ) {
				console.log( otp_obj.form_selector );
			    return;
			}
			otp.Select = otp_obj.form_selector;
			otp.submitBtnSelector = otp_obj.submit_btn_selector;
			otp.Selector = 'form' + otp.Select;
			otp.addRequiredInputFields();
			otp.bindEvents();
		},

		/**
		 * Binds Events.
		 */
		bindEvents: function () {
			if ( otp.submitBtnSelector ) {
				$( otp.submitBtnSelector ).on( 'click', function () {
					if ( ! otp.mobileVerified ) {
						event.preventDefault();
						alerts.error(
							'Please verify OTP first','',{
								displayDuration: 3000
							});
						return false;
					}
				} );
			} else {
				$( otp.Selector ).on( 'submit', function () {
					if ( ! otp.mobileVerified ) {
						event.preventDefault();
						alerts.error(
							'Please enter the required fields','',{
								displayDuration: 3000
							});
						return false;
					}
				} );
			}

			$( otp.Selector ).on( 'click', '#ihs-send-otp-btn', function () {
				var mobEl = $( otp.mobileInputSelector ),
					mobElVal = mobEl.val();
				if ( mobElVal ) {
					if ( 10 <= mobElVal.length ) {
						otp.sendOtpAjaxRequest( mobElVal );
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

			$( otp.Selector ).on( 'click', '#ihs-submit-otp-btn', function () {
				var otpInputEl = $( '#ihs-mobile-otp' ),
					otpInputElVal = otpInputEl.val();
					// otpInputElVal = otpInputElVal;
				if ( otpInputElVal ) {
					if ( otp.otpPinSent == otpInputElVal ) {
						otp.mobileVerified = true;
						alerts.success(
							'Thanks for the verification',
							{
								displayDuration: 3000,
								pos: 'top'
							});
						$( '.ihs-otp-required' ).fadeOut( 500 );
						otp.verifyOtpBtnEl.fadeOut( 500 );
					} else {
						alerts.error(
							'OTP entered is incorrect','',{
								displayDuration: 3000
							});
					}
					// otp.verifyOtpAjaxRequest( otpInputElVal );
				} else {
					alerts.error(
						'Please enter OTP','',{
							displayDuration: 3000
						});
				}
			} );
		},

		/**
		 * Create and append the required input fields.
		 */
		addRequiredInputFields: function () {
			var mobileInputName = 'ihs-mobile',
				createOtpFieldsWithMobInput = otp_obj.input_required,
				htmlEl;
			if ( 'Yes' === createOtpFieldsWithMobInput ) {
				createOtpFieldsWithMobInput = true;
			} else if ( 'No' === createOtpFieldsWithMobInput ) {
				createOtpFieldsWithMobInput = false;
			} else {
				createOtpFieldsWithMobInput = false;
			}
			otp.mobileInputName = mobileInputName;

			if ( ! createOtpFieldsWithMobInput ) {
				var mobileInputNm = otp_obj.mobile_input_name;
				if ( mobileInputNm ) {
					var mobInpSelector = otp.Selector + ' input[name="' + mobileInputNm + '"]';
					htmlEl = otp.createMobileInputAndOtherFields( mobileInputNm );
					$( htmlEl.allOtpHtml ).insertAfter( mobInpSelector );
					otp.mobileInputSelector = htmlEl.mobileInputNameSelector;
					otp.mobileInputElement = otp.setInputElVariables( htmlEl.mobileInputNameSelector );
					otp.setOtpInputElementVar();
				} else {
					htmlEl = otp.createMobileInputAndOtherFields( mobileInputName );
					$( htmlEl.allOtpHtml ).insertAfter( htmlEl.mobileInputNameSelector );
					otp.mobileInputSelector = htmlEl.mobileInputNameSelector;
					otp.mobileInputElement = otp.setInputElVariables( htmlEl.mobileInputNameSelector );
					otp.setOtpInputElementVar();
				}

			} else {
				var mobileInpName = 'ihs-mobile',
					mobileInputEl = '<label id="ihs-mobile-number"> Mobile Number (required)<br>\n' +
									'<span class="">' +
										'<input type="number" name="' + mobileInpName + '" value="" class="wpcf7-form-control" aria-required="true" aria-invalid="false">' +
									'</span> ' +
									'</label>',
					submitBtnSelector = otp.Selector + ' input[type="submit"]';
				htmlEl = otp.createMobileInputAndOtherFields( mobileInputName );
				mobileInputEl += htmlEl.allOtpHtml;
				otp.mobileInputSelector = '#ihs-mobile-number input';
				$( mobileInputEl ).insertBefore( submitBtnSelector );
				otp.setOtpInputElementVar();
				otp.mobileInputElement = otp.setInputElVariables( '#ihs-mobile-number' );
			}
		},

		setOtpInputElementVar: function () {
			otp.mobileOtpInputEl = otp.setInputElVariables( '#ihs-mobile-otp' );
			otp.mobileOtpHiddenInputEl = otp.setInputElVariables( '#ihs-otp-hidden' );
			otp.sendOtpBtnEl = otp.setInputElVariables( '#ihs-send-otp-btn' );
			otp.verifyOtpBtnEl = otp.setInputElVariables( '#ihs-submit-otp-btn' );
		},

		/**
		 * Sets the value of an element.
		 *
		 * @param elementSelector
		 * @return {*|HTMLElement} elementSelector Element Selector.
		 */
		setInputElVariables: function ( elementSelector ) {
			return $( elementSelector );
		},

		/**
		 * Creates markup for OTP input fields and submit button.
		 *
		 * @param mobileInputName
		 * @return {obj} htmlEl Contains markup for OTP input fields and submit button.
		 */
		createMobileInputAndOtherFields: function ( mobileInputName ) {
			var htmlEl = {},
				otpInputEl = '<br><label class="ihs-otp-required ihs-otp-hide"> OTP (required)<br>\n' +
				'<span class="wpcf7-form-control-wrap ihs-otp">' +
				'<input type="number" id="ihs-mobile-otp" name="ihs-otp" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false">' +
				'</span>' +
				'</label>',
				sendOtpBtn = '<div class="ihs-otp-btn" id="ihs-send-otp-btn">Send OTP</div>',
				submitOtpBtn = '<div class="ihs-otp-btn ihs-otp-hide" id="ihs-submit-otp-btn">Verify OTP</div>';
				htmlEl.allOtpHtml = otpInputEl + sendOtpBtn + submitOtpBtn;
				htmlEl.mobileInputNameSelector = otp.Selector + ' input[name="' + mobileInputName + '"]';
			return htmlEl;
		},

		/**
		 * OTP function
		 * @param {} mobileNumber
		 */
		sendOtpAjaxRequest: function ( mobileNumber ) {
			var request = $.post(
				otp_obj.ajax_url,   // this url till admin-ajax.php  is given by functions.php wp_localoze_script()
				{
					action: 'ihs_otp_ajax_hook',
					security: otp_obj.ajax_nonce,
					data: {
						mob: mobileNumber
					}
				}
			);

			request.done( function ( response ) {
				otp.otpPinSent = response.data.otp_pin_sent_to_js;
				if ( response.data.otp_pin_sent_to_js ) {
					alerts.info(
						'OTP sent to your mobile',
						{
							displayDuration: 0
						});

					// Hide the Send OTP button once OTP is sent and disable moble input field
					$( '#ihs-send-otp-btn' ).hide();
					$( otp.verifyOtpBtnEl ).removeClass( 'ihs-otp-hide' );
					$( '.ihs-otp-required' ).removeClass( 'ihs-otp-hide' );
					$( otp.mobileInputSelector ).attr( 'readonly', true );
					$( otp.mobileInputSelector ).css( 'opacity', '0.5' );

				}
			} );
		}
	};
		if( 'undefined' !== typeof otp_obj ){
		var selector = 'form' + otp_obj.form_selector;
		if ( $( selector ) ) {
			otp.init();
		}
	}

})( jQuery );
