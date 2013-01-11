<?php
/**
 * WpDevTool Error Logs Console
 *
 * @since 0.0.1
 */
function wpdevtool_menu_error_log_console() {
	
	if ( !WP_DEBUG_LOG )
		return;

	$page = add_submenu_page( 'wpdevtool_admin', __( 'WpDevTool Error Log Console', 'wpdevtool' ), 'Error Log', 'manage_options', 'wpdevtool_error_log_console', 'wpdevtool_error_log_console_page' );
	add_action( 'admin_print_styles-' . $page, 'wpdevtool_menu_error_log_console_styles' );

}
add_action( 'admin_menu', 'wpdevtool_menu_error_log_console' );

/**
 * Enqueue CSS Styles
 *
 * @since 0.0.1
 */
function wpdevtool_menu_error_log_console_styles() {
	wp_enqueue_style( 'WpDevToolStylesheet' );
}

/**
 * WpDevTool Main Admin Page
 *
 * @since 0.0.1
 */
function wpdevtool_error_log_console_page() {

	if ( !current_user_can( 'manage_options' ) )
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	
	$logfilepath = WP_CONTENT_DIR . '/debug.log';

	$my_color_scheme = array(
		'background'	=>	'111',
		'text'			=>	'fff',
		'code'			=>	array(
			'fatal'		=>	'f00',
			'warning'	=>	'F8CA00',
			'parse'		=>	'FA6900',
			'notice'	=>	'A7DBD8',
			'catchable'	=>	'BD1550'
		)
	);

	$my_color_scheme = array_merge( $my_color_scheme, apply_filters( 'wpdevtool_error_console_colors', $my_color_scheme ) );

	$log_file_content = wpdevtool_get_logs( $logfilepath, $my_color_scheme['code'] );

	?>

	<!-- Admin page -->
	<div class="wrap">
		<h2><strong style="color: #21759b;">WpDevTool</strong> - WordPress Development Tools</h2>
		<div id="left_col" class="postbox">
			<div class="handlediv">
				<br>
			</div>
			<h3 class="hndle"><?php _e( 'WpDevTool Error Log Console', 'wpdevtool' ); ?> | <?php echo ( isset( $log_array ) )? count( $log_array ) : '0'; ?> <?php _e( 'errors', 'wpdevtool' )?></h3>
			<div class="inside">
				<div style="max-width: 100%;border: 1px solid #aaa;color: #<?php echo $my_color_scheme['text'] ?>;background: #<?php echo $my_color_scheme['background'] ?>;height: 600px;overflow: auto;padding: 10px;font-family: Courier, Helvetica;font-size: 13px;">
					<?php
					if ( !empty( $log_file_content ) ) {
						echo $log_file_content;
					} else {
						echo '<strong>'.__( 'It\'s your lucky day... Ain\'t no errors!', 'wpdevtool' ).'</strong>' ;
					}
					?>
				</div>
			</div>
		</div>
		<div id="right_col" class="postbox">
			<div class="handlediv">
				<br>
			</div>
			<h3 class="hndle"><?php _e( 'Credits', 'wpdevtool' ); ?></h3>
			<div class="inside">
				<p><?php _e( 'Proudly presented by', 'wpdevtool' ); ?> :<br>
				<a href="">Alessandro Benoit</a><br>
				<a href=""><strong>Comodo Lab Web Agency</strong></a></p>
			</div>
		</div>
	</div>

	<?php
}

/**
 * WpDevTool Get Logs Function
 *
 * Retrieve formatted logs and try t
 *
 * @since 0.0.1
 *
 * @param string $logfilepath Path to the log file	
 */
function wpdevtool_get_logs( $logfilepath, $user_color_scheme = array() ) {

	$log_file_content = @file_get_contents( $logfilepath );

	if ( $log_file_content !== false ) {

		$color_scheme = array(
			'fatal'		=>	'000',
			'warning'	=>	'000',
			'parse'		=>	'000',
			'notice'	=>	'000',
			'catchable'	=>	'000'
		);

		$color_scheme = array_merge( $color_scheme, $user_color_scheme );
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
