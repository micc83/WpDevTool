=== WpDevTool ===
Contributors: micc83
Donate link: http://codeb.it
Tags: debug, development, developer, maintenance, log, console, errors
Author URI: http://codeb.it
Plugin URI: https://github.com/micc83/WpDevTool
Requires at least: 3.0.1
Tested up to: 3.5
Stable tag: 0.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Development tool for WordPress to track bugs, manage crons, permalinks and much more.

== Description ==

WpDevTool implements many useful functions for WordPress Developers such as:

* **Maintenance mode**: Return a HTTP RESPONSE 503 (Service Temporary Unavailable) Under Maintenance landing page
* **Debug bar**: A simple bar which show number of query, timing and memory of current page
* **Enable error display and logging**: Now you can enable PHP errors diplay and logging without editing wp_config.php
* **Log console**: A console to show WordPress Error Log
* **Email redirect**: Redirect all WordPress emails to a single address
* **Wp-Cron manager**: Visualisation, search and deletion of Wp-Cron
* **Permalinks viewer**: Visualisation and search of Permalinks
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

WpDevTool can handle errors for you with the only downside that is fired at plugins activation. If you need a more complete logging and still use WpDevTool Error Console you can manually edit your wp-config.php file. Set WP_DEBUG constant to TRUE in your wp-config.php file and add the following lines of code right after :
`
if (WP_DEBUG) {
	define('WP_DEBUG_LOG', true);
	define('WP_DEBUG_DISPLAY', false);
	@ini_set('display_errors',0);
}
`

== Screenshots ==

1. Main WpDevTool Admin Screen
2. Error Console Screen
3. Permalinks Screen
4. Crons Screen

== Changelog ==

= 0.1.0 =
* Added a install/update hook for setting options
* Fixed a problem with color schemes
* Added Wp-Cron Manager
* Added Permalinks Viewer
* Added a quick links section in the contextual help
* Improved responsiveness
* Add wp_nonce check to deletion/download of cron and log file
* Check debug.log file permissions
* Enable debug bar in wp-admin
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
Fix a problem with crons visualization on same date

= 0.1.0 =
Major update and minor bug fixes

= 0.0.3 =
Fix some issues. Add redirect all email feature. Most important, fix the including of WpDevTool script on every admin page.

= 0.0.1 =
First release

== To Do ==

* Think about moving debug.log creation ( if missing ) to error_handler.php instead of console
* Add missing help voices and update documentation

== To Be ==

* Add update-count tip to WpDevTool menu button with errors count
* List of options
* List of transients
* Add some stat to main admin page
* Add db table visualisation
* Add backup/restore db
* Add Developer User type
* Add ajax behaviors
* Addons

== Credits ==

Icon Credits goes to: [miniMAC](http://www.minimamente.com)
