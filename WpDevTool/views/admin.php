<?php
/**
 * WpDevTool Admin Page
 *
 * @since 0.0.1
 */
 
add_action( 'admin_menu', 'my_plugin_menu' );
function my_plugin_menu() {
	add_menu_page( __( 'WpDevTool Options', 'wpdevtool' ) , 'WpDevTool', 'manage_options', 'wpdevtool_admin', 'wpdevtool_options' );
}

function wpdevtool_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>
	<style>
		#left_col {width: 48%;float: left;margin-right: 2%;display: block;}
		#right_col {width: 20%;float: left;}
		h3 {line-height: 30px;padding: 0 10px;}
		.postbox {margin: 10px 0;}
	</style>
	<div class="wrap">
		<div id="left_col" class="postbox">
			<div class="handlediv">
				<br>
			</div>
			<h3 class="hndle"><?php _e( 'WpDevTool Options', 'wpdevtool' ); ?></h3>
			<div class="inside">
				<form method="post" action="#">
					<table class="form-table">
						<tr valign="top">
							<th scope="row">
								<label for="maintenance"><?php _e( 'Enable maintenance mode', 'wpdevtool' ); ?></label>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<label for="debug_bar"><?php _e( 'Enable maintenance mode', 'wpdevtool' ); ?></label>
									</legend>
									<input name="debug_bar" type="checkbox" id="maintenance" value="1">
								</fieldset>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="debug_bar"><?php _e( 'Enable Debug Bar', 'wpdevtool' ); ?></label>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<label for="debug_bar"><?php _e( 'Enable Debug Bar', 'wpdevtool' ); ?></label>
									</legend>
									<input name="debug_bar" type="checkbox" id="debug_bar" value="1">
								</fieldset>
							</td>
						</tr>
					</table>
				</form>
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
		<br>
	</div>
	<?php
}