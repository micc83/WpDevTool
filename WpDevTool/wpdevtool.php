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
 * WpDevTool Enqueue Admin Javascript
 *
 * @since 0.0.2
 */
function wpdevtool_enqueue_admin_script() {
	
	if ( isset( $_GET['page'] ) && ( substr( $_GET['page'], 0, 9 ) === "wpdevtool" ) )
		wp_enqueue_script('WpDevToolScript');
	
}
add_action( 'admin_enqueue_scripts', 'wpdevtool_enqueue_admin_script' );

/**
 * Load WpDevTool main admin page
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
	 */
	function wpdevtool_paginate( $data, $perpage ) {
		
		$totalitems = count( $data ); //return the total number of affected rows
		$paged = !empty( $_GET["paged"] ) ? mysql_real_escape_string( $_GET["paged"] ) : '';
		if( empty( $paged ) || !is_numeric( $paged ) || $paged <= 0 ){ $paged=1; }
		$totalpages = ceil( $totalitems / $perpage );
		if( !empty( $paged ) && !empty( $perpage ) )
			$offset = ( $paged - 1 ) * $perpage;
		
		$this->set_pagination_args( array(
			"total_items"	=> 	$totalitems,
			"total_pages" 	=> 	$totalpages,
			"per_page" 		=> 	$perpage,
		) );
		
		return $offset;
		
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
	
	$maintenance_message = '<h1>' . get_bloginfo('name') . ' ' . __( 'is under maintenance', 'wpdevtool' ) . '</h1><p>' . $message . '</p>';
	
	wp_die( $maintenance_message, get_bloginfo('name') . ' | ' . __( 'Maintenance Screen', 'wpdevtool' ) , array( 'response' => '503') );
	
}
add_action( 'get_header','wpdevtool_under_construction' );

/**
 * WpDevTool Get Logs Function
 *
 * Retrieve formatted log and return it formatted in html
 *
 * @since 0.0.1
 * @param string $logfilepath Path to the log file
 * @param string $color_scheme The color scheme applied to console log
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

	return array ( 'result' => str_replace( '<br>', '', $log_file_content), 'count' => ( count( $log_array ) - 1 ) );

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
		header( 'Content-Disposition: attachment;filename=logs_' . date_i18n('Y-m-d_G-i-s') . '.txt' );
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
	$output = sprintf( __( '%d query in %s secondi, memoria %s Kb', 'wpdevtool' ), $num_query, $time, $memory );
	$output = apply_filters( 'wpdevtol_debug_bar_content', $output );
	
	$output_links = '<a href="' . admin_url('admin.php?page=wpdevtool_admin') . '">' . __( 'WpDevTool Options', 'wpdevurl' ) . '</a>';
	
	if ( WP_DEBUG_LOG )
		$output_links .= ' | <a href="' . admin_url('admin.php?page=wpdevtool_error_log_console') . '">' . __( 'WordPress Logs', 'wpdevurl' ) . '</a>';
	
	echo('<div id="wpdevtool_debug_bar">' . $output . '<div id="wpdevtool_debug_bar_more">' . $output_links . '</div></div>');
}

/**
 * Redirects all emails
 *
 * Redirect all emails sent through wp_mail to a custom address
 *
 * @since 0.0.3
 * @param 
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
		if ( isset( $_GET['wpdevtool_cron_args_to_delete'] ) ) {
			wp_clear_scheduled_hook( sanitize_title( $_GET['wpdevtool_cron_to_delete'] ), $_GET['wpdevtool_cron_args_to_delete'] );
		} else {
			wp_clear_scheduled_hook( sanitize_title( $_GET['wpdevtool_cron_to_delete'] ) );
		}
		wpdevtool_reset_url();
	}
	
}
add_action( 'admin_init', 'wpdevtool_delete_cron' );

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
	delete_option( 'wpdevtool_maintenance' );
	delete_option( 'wpdevtool_maintenance_message' );
	delete_option( 'wpdevtool_debug_bar' );
	delete_option( 'wpdevtool_redirect_emails' );
	delete_option( 'wpdevtool_redirect_email' );

}
register_uninstall_hook( __FILE__, 'wpdevtool_uninstall' );
