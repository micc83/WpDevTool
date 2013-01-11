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
 */
define( 'WPDEVTOOL_ABS' , plugin_dir_path( __FILE__ ) );
define( 'WPDEVTOOL_URI' , plugin_dir_url( __FILE__ ) );

/**
 * Load plugin language file
 *
 * @since 0.0.1
 */
function wpdevtool_init() {
	load_plugin_textdomain( 'wpdevtool', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
}
add_action( 'plugins_loaded', 'wpdevtool_init' );

/**
 * Load plugin admin page
 *
 * @since 0.0.1
 */
require_once( WPDEVTOOL_ABS . 'views/admin.php'  );

function test_function() {
	echo __( 'Lets do this test', 'wpdevtool' );
}
add_action( 'wp_head', 'test_function' );

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