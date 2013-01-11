<?php
/**
 * WpDevTool Admin Page
 *
 * @since 0.0.1
 */
function wpdevtool_menu() {

	$icon = WPDEVTOOL_URI . 'img/develop.png';
	add_menu_page( __( 'WpDevTool Options', 'wpdevtool' ) , 'WpDevTool', 'manage_options', 'wpdevtool_admin', 'wpdevtool_options', $icon );
	
}
add_action( 'admin_menu', 'wpdevtool_menu' );

/**
 * Register WpDevTool admin page data
 *
 * @since 0.0.1
 */
function register_wpdevtool_admin_settings() {

	register_setting( 'wpdevtool_admin-settings', 'maintenance', 'intval' );
	register_setting( 'wpdevtool_admin-settings', 'debug_bar', 'intval' );
	
}
add_action( 'admin_init', 'register_wpdevtool_admin_settings' );

/**
 * Manage error messages
 *
 * @since 0.0.1
 */
function wpdevtool_admin_notices_action() {

	$errors = get_settings_errors( 'wpdevtool_admin-settings' );

	if ( empty( $errors ) && isset( $_GET['settings-updated'] ) )
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
	<!-- Styles -->
	<style>
		#left_col {width: 48%;float: left;margin-right: 10px;display: block;}
		#right_col {width: 260px;float: left;}
		h3 {line-height: 30px;padding: 0 10px;}
		.postbox {margin: 10px 0;}
	</style>

	<!-- Admin page -->
	<div class="wrap">
		<h2><strong style="color: #21759b;">WpDevTool</strong> - WordPress Development Tools</h2>
		<div id="left_col" class="postbox">
			<div class="handlediv">
				<br>
			</div>
			<h3 class="hndle"><?php _e( 'WpDevTool Options', 'wpdevtool' ); ?></h3>
			<div class="inside">
				<form method="post" action="options.php">
					<?php settings_fields( 'wpdevtool_admin-settings' ); ?>
					<?php $options = get_option('options'); ?>
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for="maintenance"><?php _e( 'Enable maintenance mode', 'wpdevtool' ); ?></label>
								<p class="description"><?php _e( 'Return a HTTP RESPONSE 503 (Service Temporary Unavailable) landing page', 'wpdevtool' ); ?></p>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<label for="debug_bar"><?php _e( 'Enable maintenance mode', 'wpdevtool' ); ?></label>
									</legend>
									<input name="maintenance" type="checkbox" id="maintenance" value="1" <?php checked( '1', get_option('maintenance') ); ?>  >
								</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="debug_bar"><?php _e( 'Enable Debug Bar', 'wpdevtool' ); ?></label>
								<p class="description"><?php _e( 'Show a simple debug bar on the bottom of every template page', 'wpdevtool' ); ?></p>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<label for="debug_bar"><?php _e( 'Enable Debug Bar', 'wpdevtool' ); ?></label>
									</legend>
									<input name="debug_bar" type="checkbox" id="debug_bar" value="1" <?php checked( '1', get_option('debug_bar') ); ?> >
								</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="silent_logging"><?php _e( 'Is silent logging enabled?', 'wpdevtool' ); ?></label>
								<p class="description"><?php _e( 'Give the ability to collect and show PHP errors logs.', 'wpdevtool' ); ?></p>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<label for="silent_logging"><?php _e( 'Enable Silent Logging', 'wpdevtool' ); ?></label>
									</legend>
									<input name="silent_logging" type="checkbox" id="silent_logging" value="1" disabled <?php checked( '1', get_option(WP_DEBUG_LOG) ); ?> >
								</fieldset>
							</td>
						</tr>
					</table>
					<?php submit_button(); ?>
				</form>
			</div>
		</div>
		<div id="right_col" class="postbox">
			<div class="handlediv">
				<br>
			</div>
			<h3 class="hndle"><?php _e( 'Credits', 'wpdevtool' ); ?></h3>
			<div class="inside">
				<p><?php _e( 'Proudly presented by', 'wpdevtool' ); ?> :<br>
				<a href="">Alessandro Benoit</a><br>
				<a href=""><strong>Comodo Lab Web Agency</strong></a></p>
			</div>
		</div>
		<br>
	</div>
	<?php
}
