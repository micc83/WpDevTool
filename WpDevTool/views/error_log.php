<?php
/**
 * WpDevTool Error Logs Console
 *
 * @since 0.0.1
 */
function wpdevtool_menu_error_log_console() {
	
	if ( !WP_DEBUG_LOG && (int) get_option( 'wpdevtool_error_display_level' ) < 2 )
		return;

	$page = add_submenu_page( 'wpdevtool_admin', __( 'WpDevTool Error Log Console', 'wpdevtool' ), __( 'Error Console', 'wpdevtool' ), 'manage_options', 'wpdevtool_error_log_console', 'wpdevtool_error_log_console_page' );
	add_action( 'admin_print_styles-' . $page, 'wpdevtool_error_log_console_styles' );

}
add_action( 'admin_menu', 'wpdevtool_menu_error_log_console' );

/**
 * WpDevTool Permalinks Page Styles
 *
 * @since 0.1.0
 */
function wpdevtool_error_log_console_styles() {
	
	wp_enqueue_style( 'WpDevToolStylesheet' );
	
}

/**
 * WpDevTool Main Admin Page
 *
 * @uses apply_filters() Calls 'wpdevtool_error_console_colors' to apply a different log console style
 * @since 0.0.1
 */
function wpdevtool_error_log_console_page() {

	if ( !current_user_can( 'manage_options' ) )
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	
	$console = new WDT_Console();
	
	$error_count = $console->get_errors_number();
	
	$del_log_url = add_query_arg( array( 'wpdevtool_delete_log_file' => 'true', 'wdt_nonce' => wp_create_nonce( 'wpdevtool_del_log' ) ) );
	$dwn_log_url = add_query_arg( array( 'wpdevtool_download_log_file' => 'true', 'wdt_nonce' => wp_create_nonce( 'wpdevtool_dwn_log' ) ) );
	
	wdt_set_log_file_permission();
	?>

	<!-- Admin page -->
	<div class="wrap wpdevtool">
		
		<div class="icon32 icon-wpdevtool-32"><br></div>
		<h2><strong style="color: #21759b;">WpDevTool</strong> - <?php _e( 'WordPress Development Tool', 'wpdevtool' ); ?></h2>
		
		<!-- Container -->
		<div id="wpdevtool_container">
		
			<!-- Left column -->
			<div id="wpdevtool_left_column">
				<div class="postbox">
					<div class="handlediv"><br></div>
					<h3 class="hndle"><?php _e( 'WpDevTool Error Log Console', 'wpdevtool' ); ?> - <?php echo $error_count; ?> <?php _e( 'errors', 'wpdevtool' )?></h3>
					<div class="inside">
						<?php echo $console->display(); ?>
					</div>
				</div>
			</div>
			
			<!-- Right column -->
			<div id="wpdevtool_right_column">
				<?php if ( $error_count > 0 ): ?>
				<div class="postbox">
					<div class="handlediv">
						<br>
					</div>
					<h3 class="hndle"><?php _e( 'More Options', 'wpdevtool' ); ?></h3>
					<div class="inside">
						<a href="<?php echo $del_log_url; ?>" class="button button-primary"><?php _e( 'Clear log file', 'wpdevtool' ); ?></a>
						<a href="<?php echo $dwn_log_url; ?>" class="button button-secondary"><?php _e( 'Download log file', 'wpdevtool' ); ?></a>
					</div>
				</div>
				<?php endif; ?>
				<?php include( WPDEVTOOL_ABS . 'inc/credits.php' ) ?>
			</div>
			
		</div>
	</div>
	
	<?php
}
