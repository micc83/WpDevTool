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

	?>

	<!-- Admin page -->
	<div class="wrap wpdevtool">
		<h2><strong style="color: #21759b;">WpDevTool</strong> - WordPress Development Tool</h2>
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
		<?php include( WPDEVTOOL_ABS . 'inc/credits.php' ) ?>
	</div>

	<?php
}
