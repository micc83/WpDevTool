<?php
/**
 * WpDevTool Contextual Help
 *
 * @since 0.0.1
 */
function wpdevtool_help( $contextual_help, $screen_id, $screen ) {

	if ( !isset( $_GET['page'] ) )
		return $contextual_help;

	$current_page = $_GET['page'];

	switch ( $current_page ) {
		case 'wpdevtool_admin':
			$screen->add_help_tab( array(
				'id'		=> 	'wpdevtool_help_tab_2',
				'title'		=> 	__('Enable silent logging'),
				'content'	=> 	'<p>' . __( "To enable silent logging set WP_DEBUG constant to TRUE in your wp-config.php file and add the following lines of code right after",'wpdevtool' )." :</p>
<pre>if ( WP_DEBUG ) {
	define( 'WP_DEBUG_LOG', TRUE );
	define( 'WP_DEBUG_DISPLAY', FALSE );
	@ini_set( 'display_errors', 0 );
}</pre>
<p>" . __( "<strong>Warning:</strong> Dont mess up with your wp-config file if you are not sure about what you're doing and remember to clear up the log file periodically so that it does not become too large.",'wpdevtool' ) ."</p>"
			) );
			break;
		default:
			return $contextual_help;
	}
		
}
add_filter('contextual_help', 'wpdevtool_help', 10, 3);