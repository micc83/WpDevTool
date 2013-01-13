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

Long Description

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

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets 
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png` 
(or jpg, jpeg, gif).

== Changelog ==

= 0.0.1 =
First release

== To Do ==

* Add Debug Bar
* Check permission to delete log file

== To Be ==

* Add wp_cron page
* Add permalinks page
* Add some stat to main admin page
* Add db table check up

== Credits ==

Icon Credits goes to: [Everaldo](http://www.everaldo.com)