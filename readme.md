#WpDevTool - WordPress Developer Tool v0.1.0
A simple development tool for WordPress...

##Description
WpDevTool implements many useful functions for WordPress Developer such as:

* **Maintenance mode**: Return a HTTP RESPONSE 503 (Service Temporary Unavailable) Under Maintenance landing page
* **Debug bar**: A simple bar which show number of query, timing and memory of current page
* **Enable error display and logging**: Now you can enable PHP errors diplay and logging without editing wp_config.php
* **Log console**: A console to show WordPress Error Log ( WP_DEBUG_LOG must be set to TRUE )
* **Email redirect**: Redirect all WordPress emails to a single address
* **Wp-Cron manager**: Visualisation, search and deletion of Wp-Cron
* **Permalinks viewer**: Visualisation and search of Permalinks
* **wdt_dump()**: A formatted version of var_dump()

##Installation

1. Upload WpDevTool folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

##Frequently Asked Questions

###How to enable Silent Logging

Set WP_DEBUG to TRUE in your wp-config.php file and add the following lines of code right below to enable silent logging :
```php
if (WP_DEBUG) {
  define('WP_DEBUG_LOG', true);
  define('WP_DEBUG_DISPLAY', false);
  @ini_set('display_errors',0);
}
```

##Support or Contact
Having trouble with WpDevTool? Open an [issue](https://github.com/micc83/WpDevTool/issues) or contact me at micc83@gmail.com

##Credits
Icon Credits goes to: [miniMAC](http://http://www.minimamente.com)

##Screenshots
![Settings Window](https://raw.github.com/micc83/WpDevTool/assets/screenshot-1.jpg)
