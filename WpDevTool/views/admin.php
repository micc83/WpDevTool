<?php


class WDT_Admin_View {
	
	var $page,
		$options,
		$option_group;
	
	function __construct() {
		
		$this->options = array(
			array( 'wpdevtool_maintenance', 'intval', true ),
			array( 'wpdevtool_maintenance_message', 'wp_kses_post', false ),
			array( 'wpdevtool_debug_bar', 'intval', true ),
			array( 'wpdevtool_handle_errors', 'intval', true ),
			array( 'wpdevtool_redirect_emails', 'intval', true ),
			array( 'wpdevtool_redirect_email', 'wpdevtool_catch_all_email_eval', false ),
			array( 'wpdevtool_error_display_level', 'intval', true ),
			array( 'wpdevtool_only_admin_errors', 'intval', true ),
			array( 'wpdevtool_errors_backtrace', 'intval', true )
		);
		
		$this->option_group = strtolower( get_class( $this ) ) . '-settings';
		
		add_action( 'admin_menu', array( $this, 'create_page' ) );	
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		
	}
	
	/**
	 * WpDevTool Admin Page
	 *
	 * @since 0.0.1
	 */
	function create_page() {
		
		$icon = WPDEVTOOL_URI . 'img/develop_16.png';
		$this->page = add_menu_page( __( 'WpDevTool Options', 'wpdevtool' ) , 'WpDevTool', 'manage_options', 'wpdevtool_admin', array( $this, 'page' ), $icon );
		
		add_action( 'admin_print_styles-' . $this->page, array( $this, 'enqueue_styles' ) );
		add_action( 'admin_print_scripts-' . $this->page, array( $this, 'enqueue_scripts' ) );
		add_action( 'load-' . $this->page, array( $this, 'page_load' ) );
		
	}
	
	/**
	 * Page Styles
	 *
	 * @since 0.1.0
	 */
	function enqueue_styles() {
		
		wp_enqueue_style( 'WpDevToolStylesheet' );
		wp_enqueue_style( 'wp-pointer' );
		
	}
	
	/**
	 * Page Scripts
	 *
	 * @since 0.1.0
	 */
	function enqueue_scripts() {
	
		wp_enqueue_script( 'WpDevToolScript' );
		wp_enqueue_script( 'wp-pointer' );
		
	}
	
	/**
	 * On page load
	 *
	 * @since 0.1.0
	 */
	function page_load() {
	
		$errors = get_settings_errors( $this->option_group );
		
		if ( empty( $errors ) && isset( $_GET['settings-updated'] ) && isset( $_GET['page'] ) && $_GET['page'] == 'wpdevtool_admin'  )
			add_settings_error( $this->option_group, 'code', __( 'Well done!', 'wpdevtool' ), 'updated' );
		
	}
	
	/**
	 * Register page settings
	 *
	 * @since 0.0.1
	 */
	function register_settings( $var ) {
		
		// Reset delle opzioni
		if ( isset( $_GET['reset'] ) ){
			wpdevtool_reset_url();
			foreach ( $this->options as $option ) {
				if ( true === $option[2] )
					delete_option( $option[0] );
			}
			return;
		}
		
		foreach ( $this->options as $option )
			register_setting( $this->option_group, $option[0], $option[1] );
		
	}
	
	/**
	 * Catch All Email validation
	 *
	 * @since 0.0.3
	 * @params string Maintenance text
	 * @return string Text through wp_kes_post or old value on empty field 
	 */
	function wpdevtool_catch_all_email_eval( $email ) {
	
		if ( empty( $email ) || !is_email( $email ) ) {
			add_settings_error( $this->option_group, 'code', __( 'Something went wrong with the catch all email address', 'wpdevtool' ), 'error' );
			return get_option( 'wpdevtool_redirect_email' );
		}
		
		return $email;
		
	}
	
	
	/**
	 * Unregister page options
	 *
	 * @since 0.1.0
	 */
	function unregister_options() {
		
		foreach ( $this->options as $option )
			delete_option( $option[0] );
		
	}
	
	/**
	 * Set options on plugin install/update
	 *
	 * @since 0.1.0
	 */
	function set_default_options() {
		
		if ( !get_option( 'wpdevtool_maintenance_message' ) )
			update_option( 'wpdevtool_maintenance_message', sprintf( __( '%s is under maintenance at the moment. Contact us at %s', 'wpdevtool' ), '[name]', '[email]' ) );
		
		if ( !get_option( 'wpdevtool_redirect_email' ) ){
			$current_user = wp_get_current_user();
			update_option( 'wpdevtool_redirect_email', $current_user->user_email );
		}
		
	}
	
	/**
	 * Show error messages
	 *
	 * @since 0.0.1
	 */
	function admin_notices() {
	
		settings_errors( $this->option_group );
		
	}
	
	/**
	 * Display Page
	 *
	 * @uses do_settings_sections('wpdevtool_admin') to add custom fields to the panel
	 * @since 0.0.1
	 */
	function page() {
		
		if ( !current_user_can( 'manage_options' ) )
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		
		wdt_set_log_file_permission();
		?>
		
		<!-- Admin page -->
		<div class="wrap wpdevtool">
			
			<div class="icon32 icon-wpdevtool-32"><br></div>
			<h2><strong class="wpdevtool_logo">WpDevTool</strong> - <?php _e( 'WordPress Development Tool', 'wpdevtool' ); ?></h2>
			
			<!-- Container -->
			<div id="wpdevtool_container">
			
				<form method="post" action="options.php">
					<?php settings_fields( $this->option_group ); ?>
				
					<!-- Left column -->
					<div id="wpdevtool_left_column">
						<div class="postbox">
							<div class="handlediv"><br></div>
							<h3 class="hndle"><?php _e( 'WpDevTool Options', 'wpdevtool' ); ?></h3>
							<div class="inside">
							
								<table class="form-table">
								
									<!-- Enable Maintenance Mode -->
									<tr valign="top">
										<th scope="row">
											<label for="wpdevtool_maintenance"><?php _e( 'Enable maintenance mode', 'wpdevtool' ); ?></label>
											<p class="description"><?php _e( 'Return a HTTP RESPONSE 503 (Service Temporary Unavailable) landing page', 'wpdevtool' ); ?></p>
										</th>
										<td>
											<fieldset>
												<legend class="screen-reader-text">
													<label for="wpdevtool_maintenance"><?php _e( 'Enable maintenance mode', 'wpdevtool' ); ?></label>
												</legend>
												<input name="wpdevtool_maintenance" type="checkbox" id="wpdevtool_maintenance" value="1" <?php checked( '1', get_option('wpdevtool_maintenance') ); ?>  >
											</fieldset>
										</td>
									</tr>
									
									<!-- Enable Debug bar -->
									<tr valign="top">
										<th scope="row">
											<label for="wpdevtool_debug_bar"><?php _e( 'Enable Debug Bar', 'wpdevtool' ); ?></label>
											<p class="description"><?php _e( 'Show a simple debug bar on the bottom of every template page', 'wpdevtool' ); ?></p>
										</th>
										<td>
											<fieldset>
												<legend class="screen-reader-text">
													<label for="wpdevtool_debug_bar"><?php _e( 'Enable Debug Bar', 'wpdevtool' ); ?></label>
												</legend>
												<input name="wpdevtool_debug_bar" type="checkbox" id="wpdevtool_debug_bar" value="1" <?php checked( '1', get_option('wpdevtool_debug_bar') ); ?> >
											</fieldset>
										</td>
									</tr>
									
									<!-- Redirect All Emails -->
									<tr valign="top">
										<th scope="row">
											<label for="wpdevtool_redirect_emails"><?php _e( 'Redirect all emails', 'wpdevtool' ); ?></label>
											<p class="description"><?php _e( 'Redirect all WordPress emails to a single address', 'wpdevtool' ); ?></p>
										</th>
										<td>
											<fieldset>
												<legend class="screen-reader-text">
													<label for="wpdevtool_redirect_emails"><?php _e( 'Redirect all emails', 'wpdevtool' ); ?></label>
												</legend>
												<input name="wpdevtool_redirect_emails" type="checkbox" id="wpdevtool_redirect_emails" value="1" <?php checked( '1', get_option('wpdevtool_redirect_emails') ); ?>  >
											</fieldset>
										</td>
									</tr>
									
									<!-- Catch all email address -->
									<tr valign="top" <?php if ( !get_option('wpdevtool_redirect_emails') ) echo('style="display:none"'); ?>>
										<th scope="row">
											<label for="wpdevtool_redirect_email"><span class="required_field">*</span> <?php _e( 'Catch all Email', 'wpdevtool' ); ?></label>
											<p class="description"><?php _e( "Catch all the emails sent through wp_mail()", 'wpdevtool' ); ?></p>
										</th>
										<td>
											<fieldset>
												<legend class="screen-reader-text">
													<label for="wpdevtool_redirect_email"><?php _e( 'Catch all Email', 'wpdevtool' ); ?></label>
												</legend>
												<input name="wpdevtool_redirect_email" type="email" id="wpdevtool_redirect_email" value="<?php echo get_option('wpdevtool_redirect_email'); ?>" class="long-text code">
											</fieldset>
										</td>
									</tr>
									
									<!-- Check if WpDevTool handle errors -->
									<tr valign="top">
										<th scope="row">
											<label for="wpdevtool_handle_errors"><?php _e( 'Let WpDevTool handle errors', 'wpdevtool' ); ?> <?php if ( WP_DEBUG ): ?> ( <a href="#" id="wpdevtool_handle_errors-help">?</a> ) <?php endif; ?></label>
											<p class="description"><?php _e( 'Enable this option to let WpDevTool do all the job without having to edit wp-config file', 'wpdevtool' ); ?>.
											</p>
										</th>
										<td>
											<fieldset>
												<legend class="screen-reader-text">
													<label for="wpdevtool_handle_errors"><?php _e( 'Let WpDevTool handle errors', 'wpdevtool' ); ?></label>
												</legend>
												<input name="wpdevtool_handle_errors" type="checkbox" id="wpdevtool_handle_errors" value="1" <?php checked( '1', get_option( 'wpdevtool_handle_errors' ) ); ?> <?php if ( WP_DEBUG ) echo 'disabled'; ?> >
											</fieldset>
										</td>
									</tr>
									
								</table>
	
							</div>
						</div>
						
						<!-- Error Box -->
						<div id="wpdevtool_handle_errors-box" class="postbox" <?php if ( !get_option( 'wpdevtool_handle_errors' ) ) echo('style="display:none"') ?> >
							<div class="handlediv"><br></div>
							<h3 class="hndle"><?php _e( 'Errors Handling', 'wpdevtool' ); ?></h3>
							<div class="inside">
							
								<table class="form-table">
								
									<!-- WpDevTool errors level settings -->
									<tr valign="top">
										<th scope="row">
											<label for="wpdevtool_error_display_level"><?php _e( 'Error display level', 'wpdevtool' ); ?></label>
											<p class="description"><?php _e( 'Should errors be logged or just displayed?', 'wpdevtool' ); ?></p>
										</th>
										<td>
											<fieldset>
												<legend class="screen-reader-text">
													<label for="wpdevtool_error_display_level"><?php _e( 'Error display level', 'wpdevtool' ); ?></label>
												</legend>
												<?php $wdt_edl = get_option( 'wpdevtool_error_display_level' ); ?>
												<select name="wpdevtool_error_display_level" id="wpdevtool_error_display_level" >
													<option value="0" <?php selected( $wdt_edl, 0 ); ?> ><?php _e( 'Hide Errors', 'wpdevtool' ); ?></option>
													<option value="1" <?php selected( $wdt_edl, 1 ); ?>><?php _e( 'Display Errors', 'wpdevtool' ); ?></option>
													<option value="2" <?php selected( $wdt_edl, 2 ); ?>><?php _e( 'Log Errors', 'wpdevtool' ); ?></option>
													<option value="3" <?php selected( $wdt_edl, 3 ); ?>><?php _e( 'Log and Display Errors', 'wpdevtool' ); ?></option>
												</select>
											</fieldset>
										</td>
									</tr>
									
									<!-- Check if errors must be shown only to admin -->
									<tr valign="top">
										<th scope="row">
											<label for="wpdevtool_only_admin_errors"><?php _e( 'Show errors only to Administrators', 'wpdevtool' ); ?></label>
											<p class="description"><?php _e( 'If enabled errors will be shown only to Administrators', 'wpdevtool' ); ?></p>
										</th>
										<td>
											<fieldset>
												<legend class="screen-reader-text">
													<label for="wpdevtool_only_admin_errors"><?php _e( 'Show errors only to Administrators', 'wpdevtool' ); ?></label>
												</legend>
												<input name="wpdevtool_only_admin_errors" type="checkbox" id="wpdevtool_only_admin_errors" value="1" <?php checked( '1', get_option( 'wpdevtool_only_admin_errors' ) ); ?> >
											</fieldset>
										</td>
									</tr>
									
									<!-- Enable errors backtrace -->
									<tr valign="top">
										<th scope="row">
											<label for="wpdevtool_errors_backtrace"><?php _e( 'Backtrace errors', 'wpdevtool' ); ?></label>
											<p class="description"><?php _e( 'Enable errors backtracing to find out errors origin', 'wpdevtool' ); ?></p>
										</th>
										<td>
											<fieldset>
												<legend class="screen-reader-text">
													<label for="wpdevtool_errors_backtrace"><?php _e( 'Backtrace errors', 'wpdevtool' ); ?></label>
												</legend>
												<input name="wpdevtool_errors_backtrace" type="checkbox" id="wpdevtool_errors_backtrace" value="1" <?php checked( '1', get_option( 'wpdevtool_errors_backtrace' ) ); ?> >
											</fieldset>
										</td>
									</tr>
									
								</table>
								
							</div>
						</div>
						
						<!-- Maintenance Box -->
						<div class="postbox" <?php if ( !get_option( 'wpdevtool_maintenance' ) ) echo('style="display:none"') ?> >
							<div class="handlediv"><br></div>
							<h3 class="hndle"><?php _e( 'Maintenance Page Content', 'wpdevtool' ); ?></h3>
							<div class="inside">
							
								<?php wp_editor( get_option('wpdevtool_maintenance_message'), 'wpdevtool_maintenance_message', array(  'wpautop' => false, 'tinymce' => array(
								        'theme_advanced_buttons1' => 'bold, italic, strikethrough, forecolor, justifyleft, justifycenter, justifyright,link, unlink, bullist, numlist, pastetext, pasteword, removeformat, fullscreen',
								      ) ) ); ?>
								<p class="description"><strong><?php _e( "Shortcodes:", 'wpdevtool' ); ?></strong> <?php _e( "[email] Blog email, [name] Blog name", 'wpdevtool' ); ?></p>
							
							</div>
						</div>
						
					</div>
					
					<!-- Right column -->
					<div id="wpdevtool_right_column">
						
						<!-- Save text -->
						<div class="postbox">
							<div class="handlediv"><br></div>
							<h3 class="hndle"><?php _e( 'WpDevTool Options', 'wpdevtool' ); ?></h3>
							<div class="inside">
							
								<?php do_settings_sections('wpdevtool_admin'); ?>
								<?php submit_button( '', 'primary', 'submit', false ); ?> <a href="<?php echo add_query_arg( array( 'reset' => 'true' ) ) ?>" class="button button-secondary"><?php _e( 'Reset' ); ?></a>
							
							</div>
						</div>
						
						<?php include( WPDEVTOOL_ABS . 'inc/credits.php' ); ?>
					
					</div>
				
				</form>
			</div>
		</div>
		
		<script>
			jQuery(document).ready( function($) {
				
				$('#wpdevtool_handle_errors-help').click(function () {
					options = $.extend( {"content":"<h3><?php _e( 'Let WpDevTool handle errors', 'wpdevtool' ); ?></h3><p><?php _e( 'WP_DEBUG constant, in wp-config.php file, must be set to false to let WpDevTool manage errors.', 'wpdevtool' ); ?></p>","position":{"edge":"left","align":"center"}}, {} );
					$('#wpdevtool_handle_errors').pointer( options ).pointer("open");
					return false;
				});
				
			});
		</script>
		
		<?php
	}	
	
}
