<?php
/**
 * WpDevTool Admin Page
 *
 * @since 0.0.1
 */
function wpdevtool_menu_maintenance() {
	
	if ( !get_option( 'maintenance' ) )
		return;
	
	$page = add_submenu_page( 'wpdevtool_admin', __( 'WpDevTool Maintenance Options', 'wpdevtool' ) , 'Maintenance Options', 'manage_options', 'wpdevtool_maintenance', 'wpdevtool_maintenance_page' );
	add_action( 'admin_print_styles-' . $page, 'wpdevtool_maintenance_styles' );
	
}
add_action( 'admin_menu', 'wpdevtool_menu_maintenance' );

/**
 * Register WpDevTool admin page data
 *
 * @since 0.0.1
 */
function register_wpdevtool_maintenance_settings() {

	register_setting( 'wpdevtool_maintenance-settings', 'maintenance_message', 'wp_kses_post' );
	
}
add_action( 'admin_init', 'register_wpdevtool_maintenance_settings' );

/**
 * Manage error messages
 *
 * @since 0.0.1
 */
function wpdevtool_maintenance_notices_action() {

	$errors = get_settings_errors( 'wpdevtool_maintenance-settings' );

	if ( empty( $errors ) && isset( $_GET['settings-updated'] ) && isset( $_GET['page'] ) && $_GET['page'] == 'wpdevtool_maintenance' )
		add_settings_error( 'wpdevtool_maintenance-settings', 'code', __( 'Well done!', 'wpdevtool' ), 'updated' );

    settings_errors( 'wpdevtool_maintenance-settings' );

}
add_action( 'admin_notices', 'wpdevtool_maintenance_notices_action' );

/**
 * Enqueue CSS Styles
 *
 * @since 0.0.1
 */
function wpdevtool_maintenance_styles() {
	wp_enqueue_style( 'WpDevToolStylesheet' );
}

/**
 * WpDevTool Main Admin Page
 *
 * @since 0.0.1
 */
function wpdevtool_maintenance_page() {

	if ( !current_user_can( 'manage_options' ) )
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	
	?>
	
	<!-- Admin page -->
	<div class="wrap wpdevtool">
		<h2><strong style="color: #21759b;">WpDevTool</strong> - WordPress Development Tool</h2>
		<div id="left_col" class="postbox">
			<div class="handlediv">
				<br>
			</div>
			<h3 class="hndle"><?php _e( 'WpDevTool Options', 'wpdevtool' ); ?></h3>
			<div class="inside">
				<form method="post" action="options.php">
					<?php settings_fields( 'wpdevtool_maintenance-settings' ); ?>
					<?php $options = get_option('options'); ?>
					<div id="maintenance_message_div">
						<?php wp_editor( get_option('maintenance_message'), 'maintenance_message', array( 'media_buttons' => false, 'teeny' => true, 'wpautop' => false )) ?>
					</div>
					<?php submit_button(); ?>
				</form>
			</div>
		</div>
		<?php include( WPDEVTOOL_ABS . 'inc/credits.php' ) ?>
	</div>

	<?php
}
