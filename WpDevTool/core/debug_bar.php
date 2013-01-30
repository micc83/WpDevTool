<?php
/**
 * WpDevTool Debug Bar Register Stylesheet
 *
 * @since 0.0.1
 */
function wpdevtool_debug_bar_register() {

	wp_register_style( 'WpDevToolBarStylesheet', WPDEVTOOL_URI . 'styles/wpdevtool_bar.css' );
	
}
add_action( 'init', 'wpdevtool_debug_bar_register' );

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
	
	if ( is_admin() ){
		add_action( 'admin_footer', 'wpdevtool_debug_bar' );
	} else {
		add_action( 'wp_footer', 'wpdevtool_debug_bar' );
	}
	
}
add_action( 'wp_enqueue_scripts', 'wpdevtool_debug_bar_init' ); 
add_action( 'admin_print_styles', 'wpdevtool_debug_bar_init' ); 

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
	
	if ( WP_DEBUG_LOG || (int) get_option( 'wpdevtool_error_display_level' ) > 1 )
		$output_links .= ' | <a href="' . admin_url('admin.php?page=wpdevtool_error_log_console') . '">' . __( 'WordPress Logs', 'wpdevtool' ) . '</a>';
	
	echo('<div id="wdt_debug_bar">' . $output . '<div id="wpdevtool_debug_bar_more">' . $output_links . '</div></div>');
	
}
