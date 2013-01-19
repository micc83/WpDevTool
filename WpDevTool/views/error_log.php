<?php
/**
 * WpDevTool Error Logs Console
 *
 * @since 0.0.1
 */
function wpdevtool_menu_error_log_console() {

	if ( !WP_DEBUG_LOG )
		return;

	$page = add_submenu_page( 'wpdevtool_admin', __( 'WpDevTool Error Log Console', 'wpdevtool' ), __( 'Error Console', 'wpdevtool' ), 'manage_options', 'wpdevtool_error_log_console', 'wpdevtool_error_log_console_page' );
	add_action( 'admin_print_styles-' . $page, 'wpdevtool_admin_styles' );

}
add_action( 'admin_menu', 'wpdevtool_menu_error_log_console' );

/**
 * WpDevTool Main Admin Page
 *
 * @uses apply_filters() Calls 'wpdevtool_error_console_colors' to apply a different log console style
 * @since 0.0.1
 */
function wpdevtool_error_log_console_page() {

	if ( !current_user_can( 'manage_options' ) )
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	
	$logfilepath = apply_filters( 'wpdevtool_error_log_file', WP_CONTENT_DIR . '/debug.log' );
	
	// Default console color scheme
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
	
	$error_count = 0;
	$result = '';
	if ( $log_file_content ){
		$error_count = $log_file_content['count'];
		$result = $log_file_content['result'];
	}
	
	?>

	<!-- Admin page -->
	<div class="wrap wpdevtool">
	
		<h2><strong style="color: #21759b;">WpDevTool</strong> - <?php _e( 'WordPress Development Tool', 'wpdevtool' ); ?></h2>
		
		<!-- Container -->
		<div id="wpdevtool_container">
		
			<!-- Left column -->
			<div id="wpdevtool_left_column">
				<div class="postbox">
					<div class="handlediv"><br></div>
					<h3 class="hndle"><?php _e( 'WpDevTool Error Log Console', 'wpdevtool' ); ?> - <?php echo $error_count; ?> <?php _e( 'errors', 'wpdevtool' )?></h3>
					<div class="inside">
						<div style="max-width: 100%;border: 1px solid #aaa;color: #<?php echo $my_color_scheme['text'] ?>;background: #<?php echo $my_color_scheme['background'] ?>;height: 600px;overflow: auto;padding: 10px;font-family: Courier, Helvetica;font-size: 13px;">
							<?php
							if ( !empty( $result ) ) {
								echo $result;
							} else {
								echo '<strong>'.__( 'It\'s your lucky day... Ain\'t no errors!', 'wpdevtool' ).'</strong>' ;
							}
							?>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Right column -->
			<div id="wpdevtool_right_column">
				<div class="postbox">
					<div class="handlediv">
						<br>
					</div>
					<h3 class="hndle"><?php _e( 'More Options', 'wpdevtool' ); ?></h3>
					<div class="inside">
						<a href="<?php echo add_query_arg( array( 'wpdevtool_delete_log_file' => 'true' ) ); ?>" class="button button-primary"><?php _e( 'Clear log file', 'wpdevtool' ) ?></a>
						<a href="<?php echo add_query_arg( array( 'wpdevtool_download_log_file' => 'true' ) ); ?>" class="button delete"><?php _e( 'Download log file', 'wpdevtool' ) ?></a>
					</div>
				</div>
				<?php include( WPDEVTOOL_ABS . 'inc/credits.php' ) ?>
			</div>
			
		</div>
	</div>
	
	<?php
}
