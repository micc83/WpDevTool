<?php
/*
Plugin Name: WpDevTool
Plugin URI: 
Description: A simple tool to develop on WordPress platform...
Version: 0.0.1
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

/**
 * Plugin activation
 *
 * On plugin activation check WordPress version is higher than 3.0
 * and PHP versione higher than 5
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
 * WpDevTool Register Stylesheet and Javascript
 *
 * @since 0.0.1
 */
function wpdevtool_register() {
	wp_register_style( 'WpDevToolStylesheet', WPDEVTOOL_URI . 'styles/style.css' );
}
add_action( 'admin_init', 'wpdevtool_register' );

/**
 * Load WpDevTool main admin page
 *
 * @since 0.0.1
 */
require_once( WPDEVTOOL_ABS . 'views/admin.php'  );

/**
 * Load WpDevTool log error console
 *
 * @since 0.0.1
 */
require_once( WPDEVTOOL_ABS . 'views/error_log.php'  );

/**
 * WpDevTool Contextual Help
 *
 * @since 0.0.1
 */
function wpdevtool_help( $contextual_help, $screen_id, $screen ) {

	if ( !isset( $_GET['page'] ) )
		return $contextual_help;

	$current_page = $_GET['page'];

	switch ( $current_page ) {
		case 'wpdevtool_admin':
			$screen->add_help_tab( array(
				'id'		=> 	'wpdevtool_help_tab_1',
				'title'		=> 	__('My Help Tab'),
				'content'	=> 	'<p>' . __( 'Descriptive content that will show in My Help Tab-body goes here.' ) . '</p>',
			) );
			$screen->add_help_tab( array(
				'id'		=> 	'wpdevtool_help_tab_2',
				'title'		=> 	__('Enable silent logging'),
				'content'	=> 	'<p>' . __( "Add the following lines of code to your wp-config.php file to enable silent logging",'wpdevtool' )." :</p>
<pre>define('WP_DEBUG', true);
if (WP_DEBUG) {
	define('WP_DEBUG_LOG', true);
	define('WP_DEBUG_DISPLAY', false);
	@ini_set('display_errors',0);
}</pre>"
			) );
			break;
		default:
			return $contextual_help;
	}
		
}
add_filter('contextual_help', 'wpdevtool_help', 10, 3);

/**
 * Enable Under Construction
 *	
 * @since 0.0.1
 */
function wpdevtool_under_construction() {
	if ( get_option('maintenance') && !current_user_can( 'manage_options' ) )
		wp_die( '<h1>' . get_bloginfo('name') . '</h1><h2>'.get_bloginfo('name').' è in manutenzione</h2><p>Per qualsiasi necessità potete contattarci all\'indirizzo email '. get_bloginfo('admin_email'), get_bloginfo('name'). ' | Manutenzione programmata', array( 'response' => '503') );
}
add_action( 'get_header','wpdevtool_under_construction' );

