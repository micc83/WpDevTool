<?php
/**
 * Reset query urls after a GET request
 *
 * @since 0.1.0
 */
function wpdevtool_reset_url() {

	$uri = $_SERVER['REQUEST_URI'];
	
	if( strpos( $uri, '?' ) !== false ) {
		$new_uri = substr( $uri, 0, strpos( $uri, '?' ) );
		
		$safe_query_args = array(
			'page', 'paged', 'settings-updated'
		);
		
		foreach ( $safe_query_args as $query_arg ) {
			if ( isset( $_GET[$query_arg] ) )
				$new_uri = add_query_arg( array( $query_arg => $_GET[$query_arg] ), $new_uri );
		}

		header( 'location: ' . $new_uri );
	}
	
}

/**
 * Formatted version of var_dump
 *
 * @since 0.0.2
 * @uses apply_filters() Calls 'wpdevtool_dump_style' to edit debug bar css
 * @param $var Var to debug
 */
function wdt_dump( $var ) {
	
	$style = apply_filters( 'wpdevtool_dump_style', 'background:#111;background:rgba(0,0,0,0.6);border:3px solid #eee;outline:1px solid #fff;padding: 5px 10px;margin:10px;color:#fff;-moz-box-shadow: inset 0 0 3px #333, 0 0 4px rgba(0,0,0,0.4);-webkit-box-shadow: inset 0 0 3px #333, 0 0 4px rgba(0,0,0,0.4);box-shadow: inset 0 0 3px #333, 0 0 4px rgba(0,0,0,0.4);line-height:20px;z-index:10000;white-space:pre-wrap;overflow: auto;font-size:13px;' );
	
	$output = htmlentities( var_export( $var, true ) );
	
	echo( '<pre class="wpdevtool_var_dump" style="' . $style . '">' . $output . '</pre>' );
	
}

/**
 * Returns current plugin version.
 *
 * @return string Plugin version
 */
function wdt_plugin_get_version() {

	$plugin_data = get_file_data( WPDEVTOOL_FILE, array( 'Version' => 'Version' ), 'plugin' );
	return $plugin_data['Version'];
	
}

/**
 * When called check nonce on GET and POST
 *
 * @since 0.0.1
 * @param 	string 	$action 	Wp_nonce action
 * @return 	true or wp_die() on fail
 */
function wpdevtool_check_nonce( $action ) {

	if ( !isset( $_REQUEST[ 'wdt_nonce' ] ) || !wp_verify_nonce( $_REQUEST[ 'wdt_nonce' ], $action ) ) 
		wp_die( __( 'Cheatin&#8217; uh?' ) );
		
	return true;
	
}

/**
 * Check if file permission are the ones given
 *
 * @since 0.0.1
 * @param 	string 	$file 			File path
 * @param 	string 	$wanted_perms 	Permission to check
 * @return true or false on failure
 */
function wdt_check_file_permission( $file, $wanted_perms ){
	
	$perms = substr( decoct( fileperms( $file ) ), -4 );
	
	clearstatcache();
	
	if ( $perms == $wanted_perms )
		return true;
	
	return false;

}

/**
 * Check and set debug.log file permission
 *
 * @since 	0.1.0
 * @uses 	wdt_check_file_permission()
 */
function wdt_set_log_file_permission() {

	if ( !file_exists( WPDEVTOOL_LOG_FILE  ) || wdt_check_file_permission( WPDEVTOOL_LOG_FILE, '0600' ) )
		return;
	
	@chmod( WPDEVTOOL_LOG_FILE, 0600 );

	if ( wdt_check_file_permission( WPDEVTOOL_LOG_FILE, '0600' ) )
		return;
	
	echo( '<div id="message" class="error"><p>' . sprintf( __( 'WpDevTool couldn\'t edit %s file permission. Manually set permission to 0600 to avoid security issues.', 'wpdevtool' ), WPDEVTOOL_LOG_FILE ) . '</p></div>' );
	
}
