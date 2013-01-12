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

	// Main admin style
	wp_register_style( 'WpDevToolStylesheet', WPDEVTOOL_URI . 'styles/style.css' );
	
}
add_action( 'admin_init', 'wpdevtool_register' );

/**
 * Load WpDevTool main admin page
 *
 * @since 0.0.1
 */
require_once( WPDEVTOOL_ABS . 'views/admin.php' );

/**
 * Load WpDevTool log error console
 *
 * @since 0.0.1
 */
require_once( WPDEVTOOL_ABS . 'views/error_log.php' );

/**
 * Load WpDevTool maintenance options
 *
 * @since 0.0.1
 */
require_once( WPDEVTOOL_ABS . 'views/maintenance.php' );

/**
 * Load WpDevTool Contextual Help
 *
 * @since 0.0.1
 */
require_once( WPDEVTOOL_ABS . 'inc/help.php' );

/**
 * Enable Under Construction
 *	
 * @since 0.0.1
 */
function wpdevtool_under_construction() {

	if ( get_option('maintenance') && !current_user_can( 'manage_options' ) )
		wp_die( '<h1>' . get_bloginfo('name') . '</h1>' . get_option('maintenance_message'), get_bloginfo('name'). ' | Manutenzione programmata', array( 'response' => '503') );
	
}
add_action( 'get_header','wpdevtool_under_construction' );

/**
 * WpDevTool Get Logs Function
 *
 * Retrieve formatted log and return it formatted in html
 *
 * @since 0.0.1
 *
 * @param string $logfilepath Path to the log file	
 */
function wpdevtool_get_logs( $logfilepath, $color_scheme ) {

	$log_file_content = @file_get_contents( $logfilepath );

	if ( $log_file_content !== false ) {

		extract( $color_scheme );

		$log_array = explode ( "\n", $log_file_content );
		$log_file_content = implode( "\n", array_reverse( $log_array ) );
		
		$log_file_content = preg_replace('/\[.*\]/',"<span style='line-height:30px;font-weight:700;display:block;'>\\0", $log_file_content);
		$log_file_content = preg_replace('/PHP Fatal error:/i',"<span style='color:#$fatal;'>\\0</span></span><br>", $log_file_content);
		$log_file_content = preg_replace('/PHP Warning:/i',"<span style='color:#F8CA00;'>\\0</span></span><br>", $log_file_content);
		$log_file_content = preg_replace('/php parse error:/i',"<span style='color:#FA6900;'>\\0</span></span><br>", $log_file_content);
		$log_file_content = preg_replace('/PHP Notice:/i',"<span style='color:#A7DBD8;'>\\0</span></span><br>", $log_file_content);
		$log_file_content = preg_replace('/PHP Catchable fatal error:/i',"<span style='color:#BD1550;'>\\0</span></span><br>", $log_file_content);
		$log_file_content = preg_replace(
		'/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'\".,<>?«»“”‘’]))/',
		"<a class='logurl' href=\"\\0\" target=\"_blank\">\\0</a>" , $log_file_content);
	
	} else {

		if ( !isset( $_GET['upandrunning'] ) ) {
			$file = @fopen( $logfilepath, "x" );
			$redirect_url = add_query_arg( array( 'page' => $_GET['page'], 'upandrunning'  => 'true' ), admin_url( 'admin.php' ) );
			?>
			<script type="text/javascript">
			<!--
			window.location= '<?php echo $redirect_url; ?>';
			//-->
			</script>
			<?php
		} else {
			echo '<div id="message" class="error"><p>' . __('Something went wrong. Your log file is missing...') . '</p></div>';
		}
	}

	return str_replace( '<br>', '', $log_file_content);

}

