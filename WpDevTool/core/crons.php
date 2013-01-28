<?php
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
