<?php
/*
Plugin Name: WpDevTool
Plugin URI: https://github.com/micc83/WpDevTool
Description: A simple tool to develop on WordPress platform...
Version: 0.1.0
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
 * Set errors display level
 *
 * @since 0.1.0
 */
function wpdevtool_set_error_display_level() {
	
	$error_display_level = get_option( 'wpdevtool_error_display_level' );
	
	if ( !get_option( 'wpdevtool_handle_errors' ) || !$error_display_level )
		return;
	
	if ( WP_DEBUG ){
		update_option( 'wpdevtool_handle_errors', false );
		return;
	}
	
	error_reporting( E_ALL );
	
	if ( defined( 'E_DEPRECATED' ) )
		error_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT );

	$log_file = WP_CONTENT_DIR . '/debug.log';
	
	if ( $error_display_level == 1 || $error_display_level == 3 )
		ini_set( 'display_errors', 1 );
	
	if ( $error_display_level == 2 || $error_display_level == 3 ){
		ini_set( 'log_errors', 1 );
		ini_set( 'error_log', $log_file );
	}
	
	set_error_handler( 'wpdevtool_error_handler' );
	
}
add_action( 'wpdevtool_init', 'wpdevtool_set_error_display_level' );

/**
 * WpDevTool Error Handler
 *
 * @since 	0.1.0
 * @param 	int 	$errno 		Error number
 * @param 	string 	$errstr 	Error string
 * @param 	string 	$errfile 	File generating the error
 * @param 	int 	$errline 	Line of the error
 */
function wpdevtool_error_handler( $errno, $errstr, $errfile, $errline ) {

	if( !( error_reporting() & $errno ) )
		return;
	
	$trace = debug_backtrace();
	array_shift( $trace );
	$items = array();
	foreach( $trace as $id => $item ){
		$items[] = $id . '. ' . ( isset( $item['file'] ) ? $item['file'] : '<unknown file>' ) . ' ' . ( isset( $item['line'] ) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()';
	}
	
	if( ini_get( 'log_errors' ) ){
	
		$error = wpdevtool_error_type( $errno ) . ': ' . $errstr . ' in ' . $errfile . ' on line ' . $errline;
		if ( get_option( 'wpdevtool_errors_backtrace' ) )
			$error .= ': ' . join( " | ", $items );
		error_log( $error );
		
	}
	
	if ( ( is_super_admin() || !get_option( 'wpdevtool_only_admin_errors' ) ) && ini_get( 'display_errors' ) ){
		
		$error = wpdevtool_error_type( $errno ) .  ': '  . $errstr . ' in ' . $errfile . ' on line ' . $errline . '</strong>';
		if ( get_option( 'wpdevtool_errors_backtrace' ) )
			$error = '<strong>'. $error .':</strong><br> ' . join( " <br>", $items );
		
		$style = '<div style="background:#111;background:rgba(0,0,0,0.6);border:3px solid #eee;outline:1px solid #fff;padding: 5px 10px;margin:10px;color:#fff;-moz-box-shadow: inset 0 0 3px #333, 0 0 4px rgba(0,0,0,0.4);-webkit-box-shadow: inset 0 0 3px #333, 0 0 4px rgba(0,0,0,0.4);box-shadow: inset 0 0 3px #333, 0 0 4px rgba(0,0,0,0.4);line-height:20px;z-index:10000;white-space:pre-wrap;overflow: auto;font-size:13px;">%s</div>';
		
		echo sprintf( $style, $error );
	
	}

	flush();
		
}

/**
 * Set a first action
 *
 * @since 0.1.0
 */
do_action( 'wpdevtool_init' );

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
 * WpDevTool Register Stylesheet and Javascript
 *
 * @since 0.0.1
 */
function wpdevtool_register() {

	// Admin style e script
	wp_register_style( 'WpDevToolStylesheet', WPDEVTOOL_URI . 'styles/style.css' );
	wp_register_script( 'WpDevToolScript', WPDEVTOOL_URI . 'js/script.js', array('jquery'), false, true );
	
	// Debug bar style
	wp_register_style( 'WpDevToolBarStylesheet', WPDEVTOOL_URI . 'styles/wpdevtool_bar.css' );
	
}
add_action( 'init', 'wpdevtool_register' );

/**
 * WpDevTool Admin Javascript
 *
 * @since 0.0.2
 */
function wpdevtool_admin_script() {
	
	wp_enqueue_script('WpDevToolScript');
	
}

/**
 * Enqueue CSS Styles
 *
 * @since 0.0.1
 */
function wpdevtool_admin_styles() {

	wp_enqueue_style( 'WpDevToolStylesheet' );
	
}

/**
 * Include WP_List_Table Class to be used inside admin pages
 * 
 * @since 0.1.0
 */
if( !class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

/**
 * Add some useful methods to WP_List_Table
 * 
 * @since 0.1.0
 */
class Wpdevtool_Table extends WP_List_Table {
	
	/**
	 * Table pagination
	 * 
	 * @since 0.1.0
	 * @param array $data Array data to count
	 * @param int $perpage Number of rows per page
	 * @return int offset
	 */
	function wpdevtool_paginate( $data, $perpage ) {
		
		$totalitems = count( $data );
		
		$paged = !empty( $_GET["paged"] ) ? mysql_real_escape_string( $_GET["paged"] ) : '';
		
		if ( empty( $paged ) || !is_numeric( $paged ) || $paged <= 0 ) 
			$paged = 1;
			
		$totalpages = ceil( $totalitems / $perpage );
		
		$this->set_pagination_args( array(
			"total_items"	=> 	$totalitems,
			"total_pages" 	=> 	$totalpages,
			"per_page" 		=> 	$perpage,
		) );
		
		if ( !empty( $paged ) && !empty( $perpage ) )
			return ( $paged - 1 ) * $perpage; // offset
		
		return 0;
		
	}
	 
	 /**
	  * Prepare the items to be displayed
	  * 
	  * @since 0.1.0
	  */
	 function prepare_items() {
	 	global $_column_headers;
	 
	 	$data = $this->wpdevtool_get_data(); 
	 	
	 	// Search in a given field inside the array
	 	if ( isset( $_POST['wpdevtool_search'] ) && !empty( $_POST['wpdevtool_search'] ) && isset( $_POST['wpdevtool_search_field'] ) && !empty( $data ) && isset( $data[0][ $_POST['wpdevtool_search_field'] ] ) ){
	 		$search_result = array();
	 		foreach ( $data as $value ) {
	 			if ( false !== strrpos( $value[ $_POST['wpdevtool_search_field'] ], $_POST['wpdevtool_search'] ) )
	 				$search_result[] = $value;
	 		}
	 		$data = $search_result;
	 	}
	 		
	 	$perpage = 20;
		$offset = $this-> wpdevtool_paginate( $data, $perpage );
 		
 		$this->_column_headers[0] = $this->get_columns();
 		$this->_column_headers[1] = array();
 		$this->_column_headers[2] = array();
		
 		$this->items = array_slice( $data, $offset, $perpage );
	 }
	 
}

/**
 * Load WpDevTool Main Admin View
 *
 * @since 0.0.1
 */
require_once( WPDEVTOOL_ABS . 'views/admin.php' );

/**
 * Load WpDevTool Error Console View
 *
 * @since 0.0.1
 */
require_once( WPDEVTOOL_ABS . 'views/error_log.php' );

/**
 * Load WpDevTool Cron View
 *
 * @since 0.0.1
 */
require_once( WPDEVTOOL_ABS . 'views/crons.php' );

/**
 * Load WpDevTool Permalinks View
 *
 * @since 0.0.1
 */
require_once( WPDEVTOOL_ABS . 'views/permalinks.php' );

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
	
	$styles = '';
	
	$maintenance_message = '<h1>' . get_bloginfo('name') . ' ' . __( 'is under maintenance', 'wpdevtool' ) . '</h1><p>' . $message . '</p>';
	
	wp_die( $maintenance_message, get_bloginfo('name') . ' | ' . __( 'Maintenance Screen', 'wpdevtool' ) , array( 'response' => '503') );
	
}
add_action( 'get_header','wpdevtool_under_construction' );

/**
 * Class WpDevTool_Error_Console
 *
 * Retrieve errors log and return em in formatted in html
 *
 * @since 0.1.0
 */
class WpDevTool_Error_Console {
	
	/**
	 * Path to the log file
	 *
	 * Can be overwritten with the wpdevtool_error_log_file filter
	 *
	 * @since 0.1.0 
	 */
	private $log_file_path;
	
	/**
	 * Log file content
	 *
	 * @since 0.1.0 
	 */
	private $log_file_content;
	
	/**
	 * Log file content in array
	 *
	 * @since 0.1.0 
	 */
	private $log_array_content = array();
	
	/**
	 * Errors count
	 *
	 * @since 0.1.0 
	 */
	private $errors_count;
	
	/**
	 * Shit!
	 *
	 * @since 0.1.0 
	 */
	private $has_error = false;
	
	/**
	 * Construct
	 *
	 * Read the log file and make it ready to be displayed in the console
	 *
	 * @since 0.1.0 
	 */
	public function __construct() {
		
		$this->log_file_path = apply_filters( 'wpdevtool_error_log_file', WP_CONTENT_DIR . '/debug.log' );
		
		$this->log_file_content = @file_get_contents( $this->log_file_path );
		
		if ( $this->log_file_content === false )
			$this->create_debug_file();
		
		$this->parse_file_content();
		
	}
	
	/**
	 * Try to create the debug file
	 *
	 * If the file is not there try to create it
	 *
	 * @since 0.1.0 
	 */
	private function create_debug_file() {
		
		// If the file is not there let's create it and reload the page
		if ( !isset( $_GET['upandrunning'] ) ) {
		
			$file = @fopen( $this->log_file_path, "x" );
			$redirect_url = add_query_arg( array( 'upandrunning'  => 'true' ) );
			?>
			<script type="text/javascript">
			<!--
			window.location= '<?php echo $redirect_url; ?>';
			//-->
			</script>
			<?php
			
		}
		
		$this->bail( __( 'Something went wrong. Your log file is missing...', 'wpdevtool' ) );
		
	}
	
	/**
	 * Reverse the content and count the numbers of row
	 *
	 * @since 0.1.0 
	 */
	private function parse_file_content() {
		
		if ( $this->has_error )
			return false;
		
		$this->log_array_content = explode ( "\n", $this->log_file_content );
		
		$this->errors_count = count( $this->log_array_content ) - 1;
		
		$limit = 200;
		
		if ( $this->errors_count > $limit ){
		
			$this->log_array_content = array_splice( $this->log_array_content, 0, $limit );
			$this->bail( sprintf( __( 'There are too many errors, only the last %d will be shown. For the full list <a href="%s">download the file</a>.' ), $limit, add_query_arg( array( 'wpdevtool_download_log_file' => 'true', 'wdt_nonce' => wp_create_nonce( 'wpdevtool_dwn_log' ) ) ) ), 'updated' );
			
		}
		
		$this->log_file_content = implode( "\n", array_reverse( $this->log_array_content ) );
		
	}
	
	/**
	 * Return the error count
	 *
	 * @since 0.1.0 
	 */
	public function get_errors_number() {
		
		if ( $this->has_error )
			return 0;
		return $this->errors_count;
		
	}
	
	/**
	 * Return the console
	 *
	 * @since 0.1.0 
	 */
	public function display () {
		
		$result = $this->log_file_content;
		
		$result = preg_replace( '/\[.*\]/', "<span class='error-title'>\\0", $result );
		$result = preg_replace( '/PHP Fatal error:/i', "<span class='fatal-error'>\\0</span></span>", $result );
		$result = preg_replace( '/PHP Warning:/i', "<span class='warning-error'>\\0</span></span>", $result );
		$result = preg_replace( '/php parse error:/i', "<span class='parse-error'>\\0</span></span>", $result );
		$result = preg_replace( '/PHP Notice:/i', "<span class='notice-error'>\\0</span></span>", $result );
		$result = preg_replace( '/PHP Catchable fatal error:/i', "<span class='catchable-error'>\\0</span></span>", $result );
		$result = preg_replace(
		'/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'\".,<>?«»“”‘’]))/',
		"<a class='url-error' href=\"\\0\" target=\"_blank\">\\0</a>", $result );
		
		if ( !empty( $result ) ){
			$result = str_replace ( ' | ' , '<br>', $result );
		} elseif ( !$this->has_error ) {
			$result = '<strong>'.__( 'It\'s your lucky day... Ain\'t no errors!', 'wpdevtool' ).'</strong>';
		} else {
			$result = '<strong>:(</strong>';
		}
		
		$output = '<div id="wdt_console">'. $result .'</div>';
		
		return $output;
		
	}
	
	/**
	 * Manage errors
	 *
	 * @since 0.1.0 
	 */
	private function bail( $message, $error = 'error' ) {
		
		if ( $error == 'error' )
			$this->has_error = true;
		echo( '<div id="message" class="' . $error . '"><p>' . $message . '</p></div>' );
		
	}
	
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
		wpdevtool_check_nonce( 'wpdevtool_dwn_log' );
		header( 'Content-Type: text' );
		header( 'Content-Disposition: attachment;filename=logs_' . date_i18n('Y-m-d_G-i-s') . '.txt' );
		readfile( $log_file );
		exit;
		
	}
	
	if ( isset( $_GET['wpdevtool_delete_log_file'] ) && is_super_admin() ) {
		wpdevtool_check_nonce( 'wpdevtool_del_log' );
		file_put_contents( $log_file, '' );
		wpdevtool_reset_url();
	}
	
}
add_action( 'admin_init', 'wpdevtool_log_processing' );

/**
 * WpDevTool Debug Bar Styles
 *
 * @uses add_action() to display debug bar
 * @since 0.0.1
 */
function wpdevtool_debug_bar_init() {
	
	if ( !get_option('wpdevtool_debug_bar') || !is_super_admin() )
		return;
	
	wp_enqueue_style( 'WpDevToolBarStylesheet' );
	
	add_action( 'wp_footer', 'wpdevtool_debug_bar' );
	
}
add_action( 'wp_enqueue_scripts', 'wpdevtool_debug_bar_init' ); 

/**
 * WpDevTool Debug Bar
 *
 * @uses apply_filters() Calls 'wpdevtol_debug_bar_content' to edit the content of debug bar
 * @since 0.0.1
 */
function wpdevtool_debug_bar() {
	
	$num_query = (int) get_num_queries();
	$time = timer_stop( 0 );
	$memory = number_format_i18n( (int) memory_get_usage() / 1024 );
	$output = sprintf( __( '%d query in %s seconds, memory %s Kb', 'wpdevtool' ), $num_query, $time, $memory );
	$output = apply_filters( 'wpdevtol_debug_bar_content', $output );
	
	$output_links = '<a href="' . admin_url('admin.php?page=wpdevtool_admin') . '">' . __( 'WpDevTool Options', 'wpdevtool' ) . '</a>';
	
	if ( WP_DEBUG_LOG )
		$output_links .= ' | <a href="' . admin_url('admin.php?page=wpdevtool_error_log_console') . '">' . __( 'WordPress Logs', 'wpdevtool' ) . '</a>';
	
	echo('<div id="wpdevtool_debug_bar">' . $output . '<div id="wpdevtool_debug_bar_more">' . $output_links . '</div></div>');
}

/**
 * Redirects all emails
 *
 * Redirect all emails sent through wp_mail to a custom address
 *
 * @since 0.0.3
 * @param string $mail The catch all email
 */
function wpdevtool_redirect_wp_mail( $email ) {
	
	if ( !get_option( 'wpdevtool_redirect_emails' ) )
		return $email;
		
	$email['to'] = get_option( 'wpdevtool_redirect_email' );
	
	return $email;
}
add_filter( 'wp_mail', 'wpdevtool_redirect_wp_mail' );

/**
 * Delete cron
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

/**
 * Reset query urls after a GET request
 *
 * @since 0.1.0
 */
function wpdevtool_reset_url() {
	$uri = $_SERVER['REQUEST_URI'];
	if( strpos( $uri, '?' ) !== false ) {
		$new_url = substr( $uri, 0, strpos( $uri, '?' ) );
		if ( isset( $_GET['page'] ) )
			$new_url = add_query_arg( array( 'page' => $_GET['page'] ), $new_url );
		if ( isset( $_GET['paged'] ) )
			$new_url = add_query_arg( array( 'paged' => $_GET['paged'] ), $new_url );
		header( 'location: ' . $new_url );
	}
}

/**
 * Formatted version of var_dump
 *
 * @uses apply_filters() Calls 'wpdevtool_dump_style' to edit debug bar css
 * @since 0.0.2
 */
function wdt_dump( $var ) {
	
	$style = apply_filters( 'wpdevtool_dump_style', 'background:#111;background:rgba(0,0,0,0.6);border:3px solid #eee;outline:1px solid #fff;padding: 5px 10px;margin:10px;color:#fff;-moz-box-shadow: inset 0 0 3px #333, 0 0 4px rgba(0,0,0,0.4);-webkit-box-shadow: inset 0 0 3px #333, 0 0 4px rgba(0,0,0,0.4);box-shadow: inset 0 0 3px #333, 0 0 4px rgba(0,0,0,0.4);line-height:20px;z-index:10000;white-space:pre-wrap;overflow: auto;font-size:13px;' );
	
	echo('<pre class="wpdevtool_var_dump" style="' . $style . '">');
	var_dump( $var );
	echo('</pre>');
}

/**
 * Returns current plugin version.
 *
 * @return string Plugin version
 */
function plugin_get_version() {

	$plugin_data = get_plugin_data( __FILE__ );
	return $plugin_data['Version'];
	
}

/**
 * When called check nonce on GET and POST
 *
 * @since 0.0.1
 * @param 	string 	$action 	Wp_nonce action
 * @return true or wp_die() on fail
 */
function wpdevtool_check_nonce( $action ) {

	if ( !isset( $_REQUEST[ 'wdt_nonce' ] ) || !wp_verify_nonce( $_REQUEST[ 'wdt_nonce' ], $action ) ) 
		wp_die( __( 'Cheatin&#8217; uh?' ) );
		
	return true;
	
}

/**
 * Run on install and update hook
 *
 * @uses do_action() Calls 'wpdevtool_install_and_update' to set options
 * @since 0.0.2
 */
function wpdevtool_install_and_update() {

	if ( false === get_option( 'wpdevtool_version' ) ) {
		update_option( 'wpdevtool_version', plugin_get_version() );
	} elseif ( version_compare( plugin_get_version(), get_option( 'wpdevtool_version' ), '<=' ) ) {
		return;
	} else {
		update_option( 'wpdevtool_version', plugin_get_version() );
	}
	
	do_action( 'wpdevtool_install_and_update' );
	
}
add_action( 'admin_init', 'wpdevtool_install_and_update' );

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
 * @since 0.0.2
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
 * Get PHP error type from Error Number
 *
 * @param int $errno Id of the php error type
 * @return string Php error type
 */
function wpdevtool_error_type( $errno ){
	switch( $errno ){

	    case E_PARSE: // 4 //
	        return 'PHP Parse error';
	        
	    case E_USER_NOTICE: // 1024 //
	    case E_NOTICE: // 8 //
	        return 'PHP Notice';
	  
	    case E_WARNING: // 2 //
	    case E_COMPILE_WARNING: // 128 //
	    case E_CORE_WARNING: // 32 //
	    case E_USER_WARNING: // 512 //
	        return 'PHP Warning';
	        
	    case E_STRICT: // 2048 //
	        return 'PHP Strict';

		case E_CORE_ERROR: // 64 //
		case E_USER_ERROR: // 256 //
	    case E_ERROR: // 1 //
	    case E_CORE_ERROR: // 16 //
	    case E_RECOVERABLE_ERROR: // 4096 //
	        return 'PHP Fatal error';
	        
	    case E_DEPRECATED: // 8192 //
	    case E_USER_DEPRECATED: // 16384 //
	        return 'PHP Deprecated';
	        
	    }
	return $errno;
}

/**
 * Last action
 *
 * @since 0.1.0
 */
do_action( 'wpdevtool_load' );
