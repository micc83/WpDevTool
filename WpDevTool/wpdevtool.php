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
	
	// Debug bar style
	wp_register_style( 'WpDevToolBarStylesheet', WPDEVTOOL_URI . 'styles/wpdevtool_bar.css' );
	
}
add_action( 'init', 'wpdevtool_register' );

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

	if ( !get_option('wpdevtool_maintenance') || current_user_can( 'manage_options' ) )
		return;
		
	$message = str_replace( array( '[name]', '[email]' ), array( get_bloginfo('name'), antispambot( get_bloginfo('admin_email') ) ), wp_kses_post( get_option('wpdevtool_maintenance_message') ) );
	
	wp_die( '<h1>' . get_bloginfo('name') . ' ' . __( 'is under maintenance', 'wpdevtool' ) . '</h1><p>' . $message . '</p>', get_bloginfo('name'). ' | ' . __( 'Maintenance Screen', 'wpdevtool' ) , array( 'response' => '503') );
	
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
 * 
 * @return array Log file html formatted content or false on error
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
			$redirect_url = add_query_arg( array( 'upandrunning'  => 'true' ) );
			?>
			<script type="text/javascript">
			<!--
			window.location= '<?php echo $redirect_url; ?>';
			//-->
			</script>
			<?php
		} else {
			echo '<div id="message" class="error"><p>' . __( 'Something went wrong. Your log file is missing...', 'wpdevtool' ) . '</p></div>';
		}
		
		return false;
	}

	return array ( 'result' => str_replace( '<br>', '', $log_file_content), 'count' => count( $log_array ) );

}

/**
 * WpDevTool Logs Processing
 *
 * Download and delete of log file through query args
 *
 * @since 0.0.1
 */
function wpdevtool_log_processing() {
	
	$log_file = apply_filters( 'wpdevtool_error_log_file', WP_CONTENT_DIR . '/debug.log' );
	
	if ( isset( $_GET['wpdevtool_download_log_file'] ) && is_super_admin() ) {
		header( 'Content-Type: text' );
		header( 'Content-Disposition: attachment;filename=logs_' . date('Ymd') . '.txt' );
		readfile( $log_file );
		exit;
	}
	
	if ( isset( $_GET['wpdevtool_delete_log_file'] ) && is_super_admin() ) {
		file_put_contents( $log_file, '' );
		wp_die( sprintf( __( 'Log file has been deleted. <a href="%s">Go back to WpDevTool</a>', 'wpdevtool' ), add_query_arg( array( 'wpdevtool_delete_log_file' => false ) ) ) );
	}
	
}
add_action( 'admin_init', 'wpdevtool_log_processing' );

/**
 * WpDevTool Debug Bar
 *
 * @since 0.0.1
 */
function wpdevtool_debug_bar_init() {
	
	if ( !get_option('wpdevtool_debug_bar') || !is_super_admin() )
		return;
	
	wp_enqueue_style( 'WpDevToolBarStylesheet' );
	
	add_action( 'wp_footer', 'wpdevtool_debug_bar' );
	
}
add_action( 'wp_enqueue_scripts', 'wpdevtool_debug_bar_init' ); 

function wpdevtool_debug_bar() {
	
	$num_query = (int) get_num_queries();
	$time = timer_stop( 0 );
	$memory = number_format_i18n( (int) memory_get_usage() / 1024 );
	$output = sprintf( __( '%d query in %s secondi, memoria %s Kb', 'wpdevtool' ), $num_query, $time, $memory );
	$output = apply_filters( 'wpdevtol_debug_bar_content', $output );
	
	$output_links = '<a href="' . admin_url('admin.php?page=wpdevtool_admin') . '">' . __( 'WpDevTool Options', 'wpdevurl' ) . '</a>';
	
	if ( WP_DEBUG_LOG )
		$output_links .= ' | <a href="' . admin_url('admin.php?page=wpdevtool_error_log_console') . '">' . __( 'WordPress Logs', 'wpdevurl' ) . '</a>';
	
	echo('<div id="wpdevtool_debug_bar">' . $output . '<div id="wpdevtool_debug_bar_more">' . $output_links . '</div></div>');
}

