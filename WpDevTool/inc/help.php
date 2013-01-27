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
				'id'		=> 	'wpdevtool_help_tab_1',
				'title'		=> 	__( 'Wp Error Handling', 'wpdevtool' ),
				'content'	=> 	'<p>' . __( "WpDevTool can handle errors for you with the only downside that is fired at plugins activation. If you need a more complete logging and still use WpDevTool Error Console you can manually edit your wp-config.php file. Set WP_DEBUG constant to TRUE in your wp-config.php file and add the following lines of code right after",'wpdevtool' )." :</p>
<pre>if ( WP_DEBUG ) {
	define( 'WP_DEBUG_LOG', TRUE );
	define( 'WP_DEBUG_DISPLAY', FALSE );
	@ini_set( 'display_errors', 0 );
}</pre>
<p>" . __( "<strong>Warning:</strong> Dont mess up with your wp-config file if you are not sure about what you're doing and remember to clear up the log file periodically so that it does not become too large.",'wpdevtool' ) ."</p>"
			) );
			$screen->add_help_tab( array(
				'id'		=> 	'wpdevtool_help_tab_2',
				'title'		=> 	__( 'Coding tips', 'wpdevtool' ),
				'content'	=> 	'<p>
				<ul style="-moz-column-width: 13em; -webkit-column-width: 13em; -moz-column-gap: 1em; -webkit-column-gap: 1em;">
					<li><a href="http://codex.wordpress.org/Function_Reference" target="_blank">Function Reference</a></li>
					<li><a href="http://codex.wordpress.org/Creating_Options_Pages" target="_blank">Options Pages</a></li>
					<li><a href="http://codex.wordpress.org/Settings_API" target="_blank">Setting API</a></li>
					<li><a href="http://codex.wordpress.org/Category:WP-Cron_Functions" target="_blank">WP-Cron Functions</a></li>
					<li><a href="http://codex.wordpress.org/Data_Validation" target="_blank">Data Validation</a></li>
					<li><a href="http://wordpress.org/extend/plugins/about/readme.txt" target="_blank">Plugin Readme.txt</a></li>
					<li><a href="http://wordpress.org/extend/plugins/about/svn/" target="_blank">Using subversion</a></li>
					<li><a href="http://codex.wordpress.org/Plugin_API" target="_blank">Plugin API</a></li>
					<li><a href="http://codex.wordpress.org/I18n_for_WordPress_Developers" target="_blank">Localization</a></li>
					<li><a href="http://codex.wordpress.org/Plugin_API/Action_Reference" target="_blank">Action Reference</a></li>
					<li><a href="http://codex.wordpress.org/Plugin_API/Filter_Reference" target="_blank">Filter Reference</a></li>
					<li><a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">Formatting Date and Time</a></li>
					<li><a href="http://codex.wordpress.org/Creating_Tables_with_Plugins" target="_blank">Creating Tables</a></li>
					<li><a href="http://codex.wordpress.org/Function_Reference/add_meta_box" target="_blank">Add Meta Box</a></li>
					<li><a href="http://codex.wordpress.org/Theme_Development target="_blank"">Theme Development</a></li>
					<li><a href="http://codex.wordpress.org/Theme_Unit_Test" target="_blank">Theme Unit Test</a></li>
					<li><a href="http://codex.wordpress.org/Class_Reference/WP_Query" target="_blank">WP_Query</a></li>
					<li><a href="http://codex.wordpress.org/Class_Reference/WP_Rewrite" target="_blank">WP_Rewrite</a></li>
				</ul></p>'
			) );
			break;
		default:
			return $contextual_help;
	}
		
}
add_filter('contextual_help', 'wpdevtool_help', 10, 3);