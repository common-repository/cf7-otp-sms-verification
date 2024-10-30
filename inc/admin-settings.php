<?php
/**
 * Custom functions for creating admin menu settings for the plugin.
 *
 * @package Abl SMS OTP Verification
 */

add_action( 'admin_menu', 'ihs_otp_create_menu' );

if ( ! function_exists( 'ihs_otp_create_menu' ) ) {
	/**
	 * Creates Menu for Abl Plugin in the dashboard.
	 */
	function ihs_otp_create_menu() {

		// Create new top-level menu.
		add_menu_page( 'Abl OTP Plugin Settings', 'Abl OTP', 'administrator', __FILE__, 'ihs_otp_plugin_settings_page', 'dashicons-email' );

		// Call register settings function.
		add_action( 'admin_init', 'register_ihs_otp_plugin_settings' );
	}
}

if ( ! function_exists( 'register_ihs_otp_plugin_settings' ) ) {

	/**
	 * Register our settings.
	 */
	function register_ihs_otp_plugin_settings() {
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_auth_key' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_sender_id' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_country_code' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_form_selector' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_submit_btn-selector' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_mobile_input_required' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_mobile_input_name' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_msg_template' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_mob_meta_key' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_login_form_selector' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_mob_country_code' );
		register_setting( 'ihs-otp-plugin-settings-group', 'ihs_otp_reset_template' );
	}
}

if ( ! function_exists( 'ihs_get_checked_val' ) ) {

	/**
	 * Find the value of checked mobile input value and return an array.
	 *
	 * @return {array} $checked_array Array containing values yes or no.
	 */
	function ihs_get_checked_val() {
		$checked_array = array(
			'checked-yes' => '',
			'checked-no' => '',
		);
		$checkbox_val = esc_attr( get_option( 'ihs_otp_mobile_input_required' ) );
		if ( 'Yes' === $checkbox_val ) {
			$checked_array['checked-yes'] = 'checked';
		} else if ( 'No' === $checkbox_val ) {
			$checked_array['checked-no'] = 'checked';
		}
		return $checked_array;
	}
}

if ( ! function_exists( 'ihs_otp_plugin_settings_page' ) ) {

	/**
	 * Settings Page for Abl Plugin.
	 */
	function ihs_otp_plugin_settings_page() {
		?>
		<div class="wrap">
			<h1>Abl OTP SMS Verification</h1>

			<form method="post" action="options.php">
				<?php settings_fields( 'ihs-otp-plugin-settings-group' ); ?>
				<?php do_settings_sections( 'ihs-otp-plugin-settings-group' ); ?>
				<table class="form-table">
									
					<tr valign="top">
						<th scope="row">lOGIN Mobile Number<span class="ihs-otp-red">*</span></th>
						<td><label for=""><input type="number" name="ihs_otp_country_code" value="<?php echo esc_attr( get_option( 'ihs_otp_country_code' ) ); ?>" /></label></td>
					</tr>
					<tr valign="top">
						<th scope="row">PASSWORD<span class="ihs-otp-red">*</span></th>
						<td><label for=""><input type="text" name="ihs_otp_auth_key" value="<?php echo esc_attr( get_option( 'ihs_otp_auth_key' ) ); ?>" /></label></td>
					</tr>
					<tr valign="top">
						<th scope="row">Sender ID ( 6 digits )<span class="ihs-otp-red">*</span></th>
						<td><label for=""><input type="text" name="ihs_otp_sender_id" value="<?php echo esc_attr( get_option( 'ihs_otp_sender_id' ) ); ?>" /></label></td>
					</tr>
					
				</table>
				<h2>User Registration or Contact Form 7 Settings</h2>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Contact Form Selector<span class="ihs-otp-red">*</span>( prefix . for Class name and # for ID )</th>
						<td><label for=""><input type="text" name="ihs_otp_form_selector" value="<?php echo esc_attr( get_option( 'ihs_otp_form_selector' ) ); ?>" /></label></td>
					</tr>
					<tr valign="top">
						<th scope="row">Submit Btn Selector<span class="ihs-otp-red">*</span>( prefix . for Class name and # for ID )</th>
						<td><label for=""><input type="text" name="ihs_otp_submit_btn-selector" value="<?php echo esc_attr( get_option( 'ihs_otp_submit_btn-selector' ) ); ?>" /></label></td>
					</tr>
					<tr valign="top">
						<th scope="row">Create Mobile Input Filed ( Y/N ): <span class="ihs-otp-red">*</span></th>
						<td><label for="">
								<?php $checked_array = ihs_get_checked_val(); ?>
								<input type="radio" name="ihs_otp_mobile_input_required" class="ihs-otp-mob-input" value="Yes" <?php echo esc_attr( $checked_array['checked-yes'] ); ?>/>Yes
								<input type="radio" name="ihs_otp_mobile_input_required" class="ihs-otp-mob-input" value="No" <?php echo esc_attr( $checked_array['checked-no'] ); ?>/>No
							</label></td>
					</tr>
					<?php $hide = ( $checked_array['checked-yes'] ) ? 'ihs-otp-hide' : ''; ?>
					<tr valign="top" id="ihs_otp_mobile_input_name" class="<?php echo esc_html( $hide ); ?>">
						<th scope="row">PreExisting Mobile Input Field Name: <span class="ihs-otp-red">*</span></th>
						<td><label for=""><input type="text" name="ihs_otp_mobile_input_name" class="ihs_otp_mob_input_name" value="<?php echo esc_attr( get_option( 'ihs_otp_mobile_input_name' ) ); ?>" /></label></td>
					</tr>
					<tr valign="top">
						<th scope="row">OTP Template<span class="ihs-otp-red">*</span></th>
						<td><label for=""><textarea type="text" name="ihs_otp_msg_template" cols="60"  rows="3"   ><?php echo esc_attr( get_option( 'ihs_otp_msg_template' ) ); ?></textarea></label>
						
					<br/>	EX: Your One Time Password is {OTP}. This OTP is valid for today and please don't share this OTP with anyone for security 
						</td>
					</tr>
				</table>
				<h2>Send forgot Password SMS</h2>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Login Form Selector<span class="ihs-otp-red">*</span>( prefix . for Class name and # for ID )</th>
						<td><label for=""><input type="text" name="ihs_otp_login_form_selector" value="<?php echo esc_attr( get_option( 'ihs_otp_login_form_selector' ) ); ?>" /></label></td>
					</tr>
					<tr valign="top">
						<th scope="row">meta_key for mobile number<span class="ihs-otp-red">*</span>( provided mobile no. is being saved in wp_usermeta table)</th>
						<td><label for=""><input type="text" name="ihs_otp_mob_meta_key" value="<?php echo esc_attr( get_option( 'ihs_otp_mob_meta_key' ) ); ?>" /></label></td>
					</tr>
					<tr valign="top">
						<th scope="row">If mobile number is being saved with the country code. Please enter the country code<span class="ihs-otp-red">*</span>( e.g. if the mobile number is saved as +919960119780 then enter +91 )</th>
						<td><label for=""><input type="text" name="ihs_otp_mob_country_code" value="<?php echo esc_attr( get_option( 'ihs_otp_mob_country_code' ) ); ?>" /></label></td>
					</tr>
					<tr valign="top">
						<th scope="row">Msg Template<span class="ihs-otp-red">*</span></th>
						<td><label for=""><textarea type="text" name="ihs_otp_reset_template" cols="60" rows="3" placeholder="Your New Password is {OTP}. Please don't share this OTP with anyone for security"><?php echo esc_attr( get_option( 'ihs_otp_reset_template' ) ); ?></textarea></label></td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
