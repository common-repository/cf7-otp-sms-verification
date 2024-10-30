<?php
/**
 * Custom functions for the Abl Plugin.
 * Contains definition of constants, file includes and enqueuing stylesheets and scripts.
 *
 * @package Abl SMS OTP verification
 */

/* Define Constants */
define( 'IHS_OTP_URI', plugins_url( 'abl-otp-sms-verification' ) );
define( 'IHS_OTP_PATH', plugin_dir_path( __FILE__ ) );
define( 'IHS_OTP_JS_URI', plugins_url( 'abl-otp-sms-verification' ) . '/vendor/js' );
define( 'IHS_OTP_CSS_URI', plugins_url( 'abl-otp-sms-verification' ) . '/css' );


if ( ! function_exists( 'ihs_otp_enqueue_scripts' ) ) {
	/**
	 * Enqueue Styles and Scripts.
	 */
	function ihs_otp_enqueue_scripts() {
		wp_enqueue_style( 'ihs_otp_styles', IHS_OTP_URI . '/style.css' );
		wp_enqueue_script( 'ihs_otp_alert_js', IHS_OTP_JS_URI . '/alert.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'ihs_otp_main_js', IHS_OTP_JS_URI . '/main.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'ihs_otp_reset_password_js', IHS_OTP_JS_URI . '/reset-password.js', array( 'jquery' ), '', true );
		wp_localize_script(
			'ihs_otp_main_js', 'otp_obj', array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'ihs_otp_nonce_action_name' ),
				'form_selector' => get_option( 'ihs_otp_form_selector' ),
				'submit_btn_selector' => get_option( 'ihs_otp_submit_btn-selector' ),
				'input_required' => get_option( 'ihs_otp_mobile_input_required' ),
				'mobile_input_name' => get_option( 'ihs_otp_mobile_input_name' ),
			)
		);
		wp_localize_script(
			'ihs_otp_reset_password_js', 'reset_pass_obj', array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'ihs_otp_nonce_reset_pass' ),
				'form_selector' => get_option( 'ihs_otp_login_form_selector' ),
				'country_code' => get_option( 'ihs_otp_country_code' ),
			)
		);
	}
}

add_action( 'wp_enqueue_scripts', 'ihs_otp_enqueue_scripts' );

if ( ! function_exists( 'ihs_otp_enqueue_admin_scripts' ) ) {
	/**
	 * Enqueue Styles and Scripts for admin.
	 *
	 * @param {string} $hook Hook.
	 */
	function ihs_otp_enqueue_admin_scripts( $hook ) {

		if ( 'toplevel_page_orion-sms-abl-otp-sms-verification/inc/admin-settings' != $hook ) {
			return;
		}
		wp_enqueue_style( 'ihs_otp_admin_styles', IHS_OTP_CSS_URI . '/admin.css' );
		wp_enqueue_script( 'ihs_otp_admin_script', IHS_OTP_JS_URI . '/admin.js', array( 'jquery' ), '', true );
	}
	add_action( 'admin_enqueue_scripts', 'ihs_otp_enqueue_admin_scripts' );
}


if ( ! function_exists( 'ihs_otp_ajax_handler' ) ) {
	/**
	 * Send OTP .
	 */
	function ihs_otp_ajax_handler() {
		if ( isset( $_POST['security'] ) ) {
			$nonce_val = esc_html( wp_unslash( $_POST['security'] ) );
		}

		if ( ! wp_verify_nonce( $nonce_val, 'ihs_otp_nonce_action_name' ) ) {
			wp_die();
		}
		$mobile_number = $_POST['data']['mob'];
		$mobile_number = ( isset( $mobile_number ) && is_numeric( $mobile_number ) ) ? wp_unslash( $mobile_number ) : '';
		$mobile_number = absint( $mobile_number );
		$message_template = get_option( 'ihs_otp_msg_template' );
		$otp_pin = ihs_generate_otp( $mobile_number, $message_template );

		wp_send_json_success(
			array(
				'otp_pin_sent_to_js' => $otp_pin,
				'data_recieved_from_js'    => $_POST,
			)
		);
	}

	add_action( 'wp_ajax_ihs_otp_ajax_hook', 'ihs_otp_ajax_handler' );
	add_action( 'wp_ajax_nopriv_ihs_otp_ajax_hook', 'ihs_otp_ajax_handler' );
}

if ( ! function_exists( 'ihs_generate_otp' ) ) {
	/**
	 * Generates random OTP, Calls function abl_send_otp to send otp and
	 * returns OTP if the message sent was successful.
	 *
	 * @param {int}    $mobile_number Mobile number.
	 * @param {string} $message_template Message template.
	 *
	 * @return {bool|string} $otp_pin Otp Pin.
	 */
	function ihs_generate_otp( $mobile_number, $message_template ) {
		$otp_pin = mt_rand( 100000, 500000 );
		$country_code = get_option( 'ihs_otp_country_code' );
		$country_code_length = strlen( $country_code );

		// Get the first two characters of the user input.
		 
		$response = abl_send_otp( $mobile_number, $country_code, $otp_pin, $message_template );
		return ( $response ) ? $otp_pin : '';
	}
}

if ( ! function_exists( 'abl_send_otp' ) ) {
	/**
	 * Send Otp.
	 *
	 * @param {int}    $mob_number Mobile number.
	 * @param {int}    $country_code Country Code.
	 * @param {string} $otp_pin Otp pin.
	 * @param {string} $message_template Message Template.
	 *
	 * @return {mixed} $response Response or Error.
	 */
	function abl_send_otp( $mob_number, $country_code, $otp_pin, $message_template ) {
		$auth_key = get_option( 'ihs_otp_auth_key' );
		$otp_length = strlen( $otp_pin );
		$message = str_replace( '{OTP}', $otp_pin, $message_template );
		$sender_id = get_option( 'ihs_otp_sender_id' );
		$country_code = str_replace( '+', '', $country_code );
	  $mob_number = $_POST['data']['mob'] ;
		$message = urlencode( $message );
		
   $url = "http://tsms.allbulksms.in/sendsms.aspx?mobile=$country_code&pass=$auth_key&senderid=$sender_id&to=$mob_number&msg=$message";
		
		 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, "http://tsms.allbulksms.in/sendsms.aspx?mobile=$country_code&pass=$auth_key&senderid=$sender_id&to=$mob_number&msg=$message");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close($ch); 
	 return true;
		 
	}
}

if ( ! function_exists( 'abl_otp_send_new_pass' ) ) {
	/**
	 * Send New Password.
	 */
	function abl_otp_send_new_pass() {
		if ( isset( $_POST['security'] ) ) {
			$nonce_val = sanitize_text_field( wp_unslash( $_POST['security'] ) );
		}

		if ( ! wp_verify_nonce( $nonce_val, 'ihs_otp_nonce_reset_pass' ) ) {
			wp_die();
		}
		$mobile_number = $_POST['data']['mob'];
		$mobile_number = ( isset( $mobile_number ) && is_numeric( $mobile_number ) ) ? wp_unslash( $mobile_number ) : '';
		$mobile_number = absint( $mobile_number );
		$meta_key = get_option( 'ihs_otp_mob_meta_key' );
		$meta_key = sanitize_text_field( $meta_key );
		$message_template = get_option( 'ihs_otp_reset_template' );
		$country_code_prefix = get_option( 'ihs_otp_mob_country_code' );
		$new_password = ihs_generate_otp( $mobile_number, $message_template );
		if ( $country_code_prefix && $new_password ) {
			$database_mob_number = $country_code_prefix . $mobile_number;
		}
		$args = array(
			'meta_key' => $meta_key,
			'meta_value' => $database_mob_number,
		);
		$user_obj = get_users( $args );
		$user_id = $user_obj[0]->data->ID;

		// If user exists update the new password for him.
		if ( $user_id ) {
			wp_set_password( $new_password, $user_id );
		}

		wp_send_json_success(
			array(
				'otp_pin_sent_to_js' => true,
				'data_recieved_from_js'    => $_POST,
			)
		);
	}

	add_action( 'wp_ajax_ihs_otp_reset_ajax_hook', 'abl_otp_send_new_pass' );
	add_action( 'wp_ajax_nopriv_ihs_otp_reset_ajax_hook', 'abl_otp_send_new_pass' );
}
