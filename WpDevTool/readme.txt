=== Plugin Name ===
Contributors: micc83
Tags: debug, development
Requires at least: 3.0.1
Tested up to: 3.5
Stable tag: 0.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple tool to develop on WordPress platform...

== Description ==

WpDevTool implements many useful functions for WordPress Developer such as:

1. Maintenance mode: Return a HTTP RESPONSE 503 (Service Temporary Unavailable) Under Maintenance landing page
2. Debug bar: A simple bar which show number of query, timing and memory of current page
3. Log Console: A console to show WordPress Error Log ( WP_DEBUG_LOG must be set to TRUE )

== Installation ==

1. Upload 'plugin-name.php' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How to enable Silent Logging =

Add the following lines of code to your wp-config.php file to enable silent logging :
`
define('WP_DEBUG', true);
if (WP_DEBUG) {
	define('WP_DEBUG_LOG', true);
	define('WP_DEBUG_DISPLAY', false);
	@ini_set('display_errors',0);
}
`

== Screenshots ==

1. Error Console Page
2. Main WpDevTool Admin Page

== Changelog ==

= 0.0.1 =
First release

== Upgrade Notice ==

= 0.0.1 =
First release

== To Do ==

* Add default options to wpdevtool_activation
* Delete options on plugin uninstall
* Add time to log filename download
* Add link to Git Hub Issues
* Add to worpdress plugin repository

== To Be ==

* Add wp_cron page
* Add permalinks page
* Add some stat to main admin page
* Add db table check up
* Enable advanced error tracking

== Credits ==

Icon Credits goes to: [Everaldo](http://www.everaldo.com)
