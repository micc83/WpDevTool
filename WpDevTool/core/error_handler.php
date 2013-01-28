<?php
/**
 * Define log file path
 *
 * @since 0.1.0
 */
define( 'WPDEVTOOL_LOG_FILE' , WP_CONTENT_DIR . '/debug.log' );

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
	
	if ( $error_display_level == 1 || $error_display_level == 3 )
		ini_set( 'display_errors', 1 );
	
	if ( $error_display_level == 2 || $error_display_level == 3 ){
		ini_set( 'log_errors', 1 );
		ini_set( 'error_log', WPDEVTOOL_LOG_FILE );
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
