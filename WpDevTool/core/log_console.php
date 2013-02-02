<?php
/**
 * Class WDT_Console
 *
 * Retrieve errors log and return em in formatted in html
 *
 * @since 0.1.0
 */
class WDT_Console {
	
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
		
		$this->log_file_path = apply_filters( 'wpdevtool_error_log_file', WPDEVTOOL_LOG_FILE );
		
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
	 * Parse file content
	 *
	 * Reverse content, count the numbers of row and limit result
	 *
	 * @since 0.1.0 
	 */
	private function parse_file_content() {
		
		if ( $this->has_error )
			return false;
		
		$this->log_array_content = explode ( "\n", $this->log_file_content );
		
		$this->errors_count = count( $this->log_array_content ) - 1;
		
		$limit = 200;

		$this->log_array_content = array_reverse( $this->log_array_content );
		
		if ( $this->errors_count > $limit ){
		
			$this->log_array_content = array_splice( $this->log_array_content, 0, $limit );
			$this->bail( sprintf( __( 'There are too many errors, only the last %d will be shown. For the full list <a href="%s">download the file</a>.', 'wpdevtool' ), $limit, add_query_arg( array( 'wpdevtool_download_log_file' => 'true', 'wdt_nonce' => wp_create_nonce( 'wpdevtool_dwn_log' ) ) ) ), 'updated' );
			
		}
		
		$this->log_file_content = implode( "\n", $this->log_array_content );
		
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
	 * Format the code to be shown in the console
	 *
	 * @since 0.1.0 
	 * @param string $code The code to be formatted
	 */
	private function format_code( $code ) {
		
		$format_rules = array(
			'/\[.*\]/' => "<span class='error-title'>\\0",
			'/PHP Fatal error:/i' => "<span class='fatal-error'>\\0</span></span>",
			'/PHP Warning:/i' => "<span class='warning-error'>\\0</span></span>",
			'/php Parse error:/i' => "<span class='parse-error'>\\0</span></span>",
			'/PHP Notice:/i' => "<span class='notice-error'>\\0</span></span>",
			'/PHP Catchable fatal error:/i' => "<span class='catchable-error'>\\0</span></span>",
			'/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'\".,<>?«»“”‘’]))/' => "<a class='url-error' href=\"\\0\" target=\"_blank\">\\0</a>"
		);
		
		$format_rules = apply_filters( 'wdt_console_format_rules', $format_rules );
		
		foreach ( $format_rules as $rule => $rewrite ) {
			$code = preg_replace( $rule, $rewrite, $code );
		}
		
		return $code;
		
	}
	
	/**
	 * Return the console
	 *
	 * @since 0.1.0 
	 */
	public function display () {
		
		$result = $this->format_code( $this->log_file_content );
		
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
	 * Manage class errors
	 *
	 * @since 	0.1.0 
	 * @param 	string 	$message	Error message
	 * @param 	string 	$error		Error type
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
	
	$log_file = apply_filters( 'wpdevtool_error_log_file', WPDEVTOOL_LOG_FILE );
	
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
