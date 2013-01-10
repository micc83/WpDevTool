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
 * Load plugin language file
 *
 * @since 0.0.1
 */

function wpdevtool_init() {
	load_plugin_textdomain( 'wpdevtool', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
}
add_action('plugins_loaded', 'wpdevtool_init');

/**
 * Load plugin admin page
 *
 * @since 0.0.1
 */

require_once( plugin_dir_path( __FILE__ ) . '/views/admin.php'  );

function test_function() {
	echo __( 'Lets do this test', 'wpdevtool' );
}
add_action( 'wp_head', 'test_function' );