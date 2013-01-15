<?php
/**
 * WpDevTool Admin Page
 *
 * @since 0.0.1
 */
function wpdevtool_menu() {

	$icon = WPDEVTOOL_URI . 'img/develop.png';
	$page = add_menu_page( __( 'WpDevTool Options', 'wpdevtool' ) , 'WpDevTool', 'manage_options', 'wpdevtool_admin', 'wpdevtool_options', $icon );
	add_action( 'admin_print_styles-' . $page, 'wpdevtool_admin_styles' );
	
}
add_action( 'admin_menu', 'wpdevtool_menu' );

/**
 * Enqueue CSS Styles
 *
 * @since 0.0.1
 */
function wpdevtool_admin_styles() {
	wp_enqueue_style( 'WpDevToolStylesheet' );
}

/**
 * Register WpDevTool admin page data
 *
 * @since 0.0.1
 */
function register_wpdevtool_admin_settings() {

	register_setting( 'wpdevtool_admin-settings', 'wpdevtool_maintenance', 'intval' );
	register_setting( 'wpdevtool_admin-settings', 'wpdevtool_maintenance_message', 'wp_kses_post' );
	register_setting( 'wpdevtool_admin-settings', 'wpdevtool_debug_bar', 'intval' );
	
}
add_action( 'admin_init', 'register_wpdevtool_admin_settings' );

/**
 * Manage error messages
 *
 * @since 0.0.1
 */
function wpdevtool_admin_notices_action() {

	$errors = get_settings_errors( 'wpdevtool_admin-settings' );

	if ( empty( $errors ) && isset( $_GET['settings-updated'] ) && isset( $_GET['page'] ) && $_GET['page'] == 'wpdevtool_admin'  )
		add_settings_error( 'wpdevtool_admin-settings', 'code', __( 'Well done!', 'wpdevtool' ), 'updated' );

    settings_errors( 'wpdevtool_admin-settings' );

}
add_action( 'admin_notices', 'wpdevtool_admin_notices_action' );

/**
 * WpDevTool Main Admin Page
 *
 * @since 0.0.1
 */
function wpdevtool_options() {
	
	if ( !current_user_can( 'manage_options' ) )
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	
	?>
	
	<!-- Admin page -->
	<div class="wrap wpdevtool">
		<h2><strong style="color: #21759b;">WpDevTool</strong> - <?php _e( 'WordPress Development Tool', 'wpdevtool' ); ?></h2>
		<div class="postbox left_col">
			<div class="handlediv">
				<br>
			</div>
			<h3 class="hndle"><?php _e( 'WpDevTool Options', 'wpdevtool' ); ?></h3>
			<div class="inside">
				<form method="post" action="options.php">
					<?php settings_fields( 'wpdevtool_admin-settings' ); ?>
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for="wpdevtool_maintenance"><?php _e( 'Enable maintenance mode', 'wpdevtool' ); ?></label>
								<p class="description"><?php _e( 'Return a HTTP RESPONSE 503 (Service Temporary Unavailable) landing page', 'wpdevtool' ); ?></p>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<label for="wpdevtool_maintenance"><?php _e( 'Enable maintenance mode', 'wpdevtool' ); ?></label>
									</legend>
									<input name="wpdevtool_maintenance" type="checkbox" id="wpdevtool_maintenance" value="1" <?php checked( '1', get_option('wpdevtool_maintenance') ); ?>  >
								</fieldset>
							</td>
						</tr>
						<tr valign="top" <?php if ( !get_option('wpdevtool_maintenance') ) echo('style="display:none"'); ?>>
							<th scope="row">
								<label for="wpdevtool_maintenance_message"><?php _e( 'Maintenance message', 'wpdevtool' ); ?></label>
								<p class="description"><?php _e( "Shortcodes: <br>[email] Blog email <br>[name] Blog name", 'wpdevtool' ); ?></p>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<label for="wpdevtool_maintenance_message"><?php _e( 'Enable maintenance mode', 'wpdevtool' ); ?></label>
									</legend>
									<input name="wpdevtool_maintenance_message" type="text" id="wpdevtool_maintenance_message" value="<?php echo get_option('wpdevtool_maintenance_message'); ?>" class="regular-text code">
								</fieldset>
							</td>
						</tr>			
						<tr valign="top">
							<th scope="row">
								<label for="wpdevtool_debug_bar"><?php _e( 'Enable Debug Bar', 'wpdevtool' ); ?></label>
								<p class="description"><?php _e( 'Show a simple debug bar on the bottom of every template page', 'wpdevtool' ); ?></p>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<label for="wpdevtool_debug_bar"><?php _e( 'Enable Debug Bar', 'wpdevtool' ); ?></label>
									</legend>
									<input name="wpdevtool_debug_bar" type="checkbox" id="wpdevtool_debug_bar" value="1" <?php checked( '1', get_option('wpdevtool_debug_bar') ); ?> >
								</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="wp_debug"><?php _e( 'WP_DEBUG is active', 'wpdevtool' ); ?></label>
								<p class="description"><?php _e( 'Check wheter you have set WP_DEBUG to TRUE', 'wpdevtool' ); ?></p>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<label for="wp_debug"><?php _e( 'WP_DEBUG is active', 'wpdevtool' ); ?></label>
									</legend>
									<input name="wp_debug" type="checkbox" id="wp_debug" value="1" disabled <?php checked( '1', WP_DEBUG ); ?> >
								</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="silent_logging"><?php _e( 'Logging is enabled', 'wpdevtool' ); ?></label>
								<p class="description"><?php _e( 'To enable silent logging give a look to Contextual Help', 'wpdevtool' ); ?></p>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<label for="silent_logging"><?php _e( 'Logging is enabled', 'wpdevtool' ); ?></label>
									</legend>
									<input name="silent_logging" type="checkbox" id="silent_logging" value="1" disabled <?php checked( '1', WP_DEBUG_LOG ); ?> >
								</fieldset>
							</td>
						</tr>
					</table>
					<?php do_settings_sections('wpdevtool_admin'); ?>
					<?php submit_button(); ?>
				</form>
			</div>
		</div>
		<?php include( WPDEVTOOL_ABS . 'inc/credits.php' ) ?>
	</div>

	<?php
}
