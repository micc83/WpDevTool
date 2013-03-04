<?php
/*
Plugin Name: WpDevTool
Plugin URI: https://github.com/micc83/WpDevTool
Description: A simple tool to develop on WordPress platform...
Version: 0.1.1
Author: Alessandro Benoit
Author URI: http://codeb.it
License: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/**
 * Define plugin folders and uri
 *
 * @since 0.0.1
 */
define( 'WPDEVTOOL_ABS' , plugin_dir_path( __FILE__ ) );
define( 'WPDEVTOOL_URI' , plugin_dir_url( __FILE__ ) );
define( 'WPDEVTOOL_FILE' , __FILE__ );

/**
 * Load WpDevTool API
 *
 * @since 0.1.0
 */
require_once( WPDEVTOOL_ABS . 'core/api.php' );

/**
 * Plugin activation checks
 *
 * On plugin activation check WordPress version is higher than 3.0
 * and PHP version higher than 5
 *
 * @since 0.0.1
 */
function wpdevtool_activation() {
	
	$error = '';

	if ( version_compare( get_bloginfo('version'), '3.0', '<') )
		$error = __( "This plugin requires WordPress 3.0 or greater.", 'wpdevtool' );
	
	if ( version_compare( PHP_VERSION, '5.0.0', '<' ) )
		$error = __( "This plugin requires PHP 5 or greater.", 'wpdevtool' );

	if ( $error ){
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( $error ); 
	}

}
register_activation_hook( __FILE__, 'wpdevtool_activation' );

/**
 * Load WpDevTool language file
 *
 * @since 0.0.1
 */
function wpdevtool_init() {

	load_plugin_textdomain( 'wpdevtool', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
	
}
add_action( 'plugins_loaded', 'wpdevtool_init' );

/**
 * Run on install and update hook
 *
 * @uses do_action() Calls 'wpdevtool_install_and_update' to set options
 * @since 0.0.2
 */
function wpdevtool_install_and_update() {
	
	if ( 	
		// if version is already set and new version is less or equal to current
		false !== get_option( 'wpdevtool_version' ) 
		&& version_compare( wdt_plugin_get_version(), get_option( 'wpdevtool_version' ), '<=' ) 
			
		)
		return; 

	update_option( 'wpdevtool_version', wdt_plugin_get_version() );
	do_action( 'wpdevtool_install_and_update' );
	
}
add_action( 'init', 'wpdevtool_install_and_update' );

/**
 * WpDevTool Uninstall Hook
 *
 * @since 0.0.2
 */
function wpdevtool_uninstall() {

	delete_option( 'wpdevtool_version' );
	do_action( 'wpdevtool_uninstall' );
	
}
register_uninstall_hook( __FILE__, 'wpdevtool_uninstall' );

/**
 * WpDevTool have to load first to debug other plugins
 *
 * @since 0.1.0
 */
function wpdevtool_load_first(){

	$path = str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ );
	
	if ( $plugins = get_option( 'active_plugins' ) ) {
		if ( $key = array_search( $path, $plugins ) ) {
			array_splice( $plugins, $key, 1 );
			array_unshift( $plugins, $path );
			update_option( 'active_plugins', $plugins );
		}
	}
}
add_action( 'activated_plugin', 'wpdevtool_load_first' );

/**
 * WpDevTool Register Base Stylesheet and Javascript
 *
 * @since 0.0.1
 */
function wpdevtool_register() {

	wp_register_style( 'WpDevToolStylesheet', WPDEVTOOL_URI . 'styles/style.css' );
	wp_register_script( 'WpDevToolScript', WPDEVTOOL_URI . 'js/script.js', array('jquery'), false, true );

}
add_action( 'init', 'wpdevtool_register' );

/**
 * Load WpDevTool Core
 *
 * @since 0.1.0
 */
require_once( WPDEVTOOL_ABS . 'core/core.php' );			// Error handler

/**
 * First action to fire
 *
 * @since 0.1.0
 */
do_action( 'wpdevtool_init' );

/**
 * Load WpDevTool Views
 *
 * @since 0.0.1
 */
require_once( WPDEVTOOL_ABS . 'views/admin.php' );					// Main admin page 
require_once( WPDEVTOOL_ABS . 'views/error_log.php' );				// Error console
require_once( WPDEVTOOL_ABS . 'views/crons.php' );					// Cron admin page
require_once( WPDEVTOOL_ABS . 'views/permalinks.php' );				// Permalinks admin page

/**
 * Load WpDevTool Includes
 *
 * @since 0.0.1
 */
require_once( WPDEVTOOL_ABS . 'inc/help.php' );

/**
 * Last action
 *
 * @since 0.1.0
 */
do_action( 'wpdevtool_load' );
