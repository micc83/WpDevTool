=== Plugin Name ===
Contributors: micc83
Donate link: http://codeb.it
Tags: debug, development, developer, maintenance, log, console, errors
Author URI: http://codeb.it
Plugin URI: https://github.com/micc83/WpDevTool
Requires at least: 3.0.1
Tested up to: 3.5
Stable tag: 0.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple tool to develop on WordPress platform...

== Description ==

WpDevTool implements many useful functions for WordPress Developers such as:

* **Maintenance mode**: Return a HTTP RESPONSE 503 (Service Temporary Unavailable) Under Maintenance landing page
* **Debug bar**: A simple bar which show number of query, timing and memory of current page
* **Log Console**: A console to show WordPress Error Log ( WP_DEBUG_LOG must be set to TRUE )
* **Email Redirect**: Redirect all WordPress emails to a single address
* **Wp-Cron Manager**: Visualisation, search and deletion of Wp-Cron
* **Permalinks Viewer**: Visualisation and search of Permalinks
* **wdt_dump()**: A formatted version of var_dump()

= WpDevTool on GitHub =
https://github.com/micc83/WpDevTool

= Support or Contact =
Having trouble with WpDevTool? Open an [issue](https://github.com/micc83/WpDevTool/issues) or contact me at micc83@gmail.com

== Installation ==

1. Upload WpDevTool folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How to enable Silent Logging =

To enable silent logging set WP_DEBUG constant to TRUE in your wp-config.php file and add the following lines of code right after:
`
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

= 0.1.0 =
* Added a install/update hook for setting options
* Fixed a problem with color schemes
* Added Wp-Cron Manager
* Added Permalinks Viewer
* Added a quick links section in the contextual help
* Fixed maintenance text validation
* Improved responsiveness
* Fixed many minor issues

= 0.0.4 =
* Fix bug "updating plugin" cause maintenance message and email missing error

= 0.0.3 =
* Fix console showing 1 error with no errors bug
* Fix debug bar background on style.css for internet explorer enhancement
* Redirect all WordPress Mail feature
* Fix Check on which admin page to include script.js bug

= 0.0.2 =
* Add default options to wpdevtool_activation
* Log file name now includes time
* Delete options on plugin uninstall
* Added a formatted version of var_dump() function
* Add twitter, plugin home and general fix credits widget
* Completed italian translation

= 0.0.1 =
* First release

== Upgrade Notice ==

= 0.1.0 =
Major update and minor bug fixes

= 0.0.3 =
Fix some issues. Add redirect all email feature. Most important, fix the including of WpDevTool script on every admin page.

= 0.0.1 =
First release

== To Be ==

* Add some stat to main admin page
* Add db table visualisation
* Add backup/restore db
* Enable advanced error tracking
* Add Developer User type
* Add hidden test posts

== Credits ==

Icon Credits goes to: [Everaldo](http://www.everaldo.com)
