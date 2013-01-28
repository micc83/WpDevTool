<?php

/**
 * Load WpDevTool Core Files
 *
 * @since 0.1.0
 */
require_once( WPDEVTOOL_ABS . 'core/error_handler.php' );			// Error handler
require_once( WPDEVTOOL_ABS . 'core/log_console.php' );				// Log Console class
require_once( WPDEVTOOL_ABS . 'core/debug_bar.php' );				// Display debug bar
require_once( WPDEVTOOL_ABS . 'core/class-wdt_table.php' );			// Default table class extension

/**
 * Enable Under Construction
 *	
 * @since 0.0.1
 */
function wpdevtool_under_construction() {

	if ( !get_option('wpdevtool_maintenance') || current_user_can( 'manage_options' ) )
		return;
		
	$message = str_replace( array( '[name]', '[email]' ), array( get_bloginfo('name'), antispambot( get_bloginfo('admin_email') ) ), wp_kses_post( get_option('wpdevtool_maintenance_message') ) );
	
	$styles = '';
	
	$maintenance_message = '<h1>' . get_bloginfo('name') . ' ' . __( 'is under maintenance', 'wpdevtool' ) . '</h1><p>' . $message . '</p>';
	
	wp_die( $maintenance_message, get_bloginfo('name') . ' | ' . __( 'Maintenance Screen', 'wpdevtool' ) , array( 'response' => '503') );
	
}
add_action( 'get_header','wpdevtool_under_construction' );

/**
 * Redirect all emails sent through wp_mail to a custom address
 *
 * @since 	0.0.3
 * @param 	string 	$email	The catch all email
 */
function wpdevtool_redirect_wp_mail( $email ) {
	
	if ( !get_option( 'wpdevtool_redirect_emails' ) )
		return $email;
		
	$email['to'] = get_option( 'wpdevtool_redirect_email' );
	
	return $email;
}
add_filter( 'wp_mail', 'wpdevtool_redirect_wp_mail' );

/**
 * Delete crons
 *
 * @since 0.1.0
 */
function wpdevtool_delete_cron() {
	
	if ( isset( $_GET['wpdevtool_cron_to_delete'] ) && is_super_admin() ) {
		wpdevtool_check_nonce( 'wdt_cron_delete' );
		if ( isset( $_GET['wpdevtool_cron_args_to_delete'] ) ) {
			wp_clear_scheduled_hook( sanitize_title( $_GET['wpdevtool_cron_to_delete'] ), $_GET['wpdevtool_cron_args_to_delete'] );
		} else {
			wp_clear_scheduled_hook( sanitize_title( $_GET['wpdevtool_cron_to_delete'] ) );
		}
		wpdevtool_reset_url();
	}
	
}
add_action( 'admin_init', 'wpdevtool_delete_cron' );
