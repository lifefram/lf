=== Blackhole for Bad Bots ===

Plugin Name: Blackhole for Bad Bots
Plugin URI: https://perishablepress.com/blackhole-bad-bots/
Description: Protects your site against bad bots by trapping them in a virtual black hole.
Tags: anti-spam, bad bots, blackhole, honeypot, security, anti spam, antispam, ban, blacklist, block, bots, ip, robots, robots.txt, spam, spider, trap, whois
Author: Jeff Starr
Contributors: specialk
Author URI: https://plugin-planet.com/
Donate link: https://monzillamedia.com/donate.html
Requires at least: 4.1
Tested up to: 5.8
Stable tag: 3.2
Version: 3.2
Requires PHP: 5.6.20
Text Domain: blackhole-bad-bots
Domain Path: /languages
License: GPL v2 or later

Blackhole is a WordPress security plugin that detects and traps bad bots in a virtual black hole, where they are denied access to your entire site.



== Description ==

> Add your own virtual black hole trap for bad bots.

**Bye bye bad bots..**

Bad bots are the worst. They do all sorts of nasty stuff and waste server resources. The Blackhole plugin helps to stop bad bots and save precious resources for legit visitors.

**How does it work?**

First the plugin adds a hidden trigger link to the footer of your pages. You then add a line to your robots.txt file that forbids all bots from following the hidden link. Bots that then ignore or disobey your robots rules will crawl the link and fall into the trap. Once trapped, bad bots are denied further access to your WordPress site.

I call it the "one-strike" rule: bots have one chance to obey your site's robots.txt rule. Failure to comply results in immediate banishment. The best part is that the Blackhole only affects bad bots: human users never see the hidden link, and good bots obey the robots rules in the first place. Win-win! :)

_Using a caching plugin? Check out the [Installation notes](https://wordpress.org/plugins/blackhole-bad-bots/installation/) for important info._

**Features**

* Easy to set up
* Squeaky clean code
* Focused and modular
* Lightweight, fast and flexible
* Built with the WordPress API
* Works with other security plugins
* Easy to reset the list of bad bots
* Easy to delete any bot from the list
* Regularly updated and "future proof"
* Blackhole link includes "nofollow" attribute
* Plugin options configurable via settings screen
* Works silently behind the scenes to protect your site
* Whitelists all major search engines to never block
* Focused on flexibility, performance, and security
* Email alerts with WHOIS lookup for blocked bots
* Complete inline documentation via the Help tab
* Provides setting to whitelist any IP addresses
* Customize the message displayed to bad bots ;)
* One-click restore the plugin default options
* Does NOT use or require any .htaccess rules

Blackhole for Bad Bots protects your site against bad bots, spammers, scrapers, scanners, and other automated threats.

_Not using WordPress? Check out the [standalone PHP version of Blackhole](https://perishablepress.com/blackhole-bad-bots/)!_

_Check out [Blackhole Pro](https://plugin-planet.com/blackhole-pro/) and level up with advanced features!_



**Whitelist**

By default, this plugin does NOT block any of the major search engines (user agents):

* AOL.com
* Baidu
* Bingbot/MSN
* DuckDuckGo
* Googlebot
* Teoma
* Yahoo!
* Yandex

These search engines (and all of their myriad variations) are whitelisted via user agent. So are a bunch of other "useful" bots. They always are allowed full access to your site, even if they disobey your robots.txt rules. This list can be customized in the plugin settings. For a complete list of whitelisted bots, visit the Help tab in the plugin settings (under "Whitelist Settings").



**Privacy**

__User Data:__ This plugin automatically blocks bad bots. When bad bots fall into the trap, their IP address, user agent, and other request data are stored in the WP database. No other user data is collected by this plugin. At any time, the administrator may delete all saved data via the plugin settings. 

__Services:__ This plugin does not connect to any third-party locations or services.

__Cookies:__ This plugin does not set any cookies.

_Header Image Courtesy NASA/JPL-Caltech._



== Screenshots ==

1. Blackhole Settings Screen (showing default options)
2. Blackhole Bad Bots Screen (showing some example bots)



== Installation ==

**Installing Blackhole for Bad Bots**

1. Upload the Blackhole plugin to your blog and activate
2. Visit the Blackhole Settings and copy the Robots Rules
3. Add the Robots Rules to your site's robots.txt file (see note)*
4. Configure the Blackhole Settings as desired and done

__Note:__ For the robots.txt rules, there are two scenarios:

1. Your site has a physical robots.txt file that you can see on the server. In this case, you need to add the required rules manually.
2. OR, your site is using the dynamic/virtual WP-generated robots.txt file, and there is no physical robots.txt file on your server. In this case, the plugin adds the required rules automatically. You do not need to add anything manually.

For complete documentation, click the "Help" tab in the upper-right corner of the Blackhole settings screen. Help tab also available on the "Bad Bots" screen.

[More info on installing WP plugins](https://wordpress.org/support/article/managing-plugins/#installing-plugins)

[Verify Blackhole is working properly](https://plugin-planet.com/blackhole-pro-check-plugin-working/)

_Using a caching plugin? Check out the section below called "Caching Plugins" for important info._


**No robots.txt?**

For the robots.txt file, there are two possible scenarios:

1. You want to use your own physical robots.txt file that you can view and edit on the server. In this case, follow the steps below to create your site's robots.txt file.
2. OR, you want to use the dynamic/virtual WP-generated robots.txt file, such that there is no physical robots.txt file on your server. In this case, you don't need to do anything, because WordPress automatically generates a robots.txt file when requested. 

If you go with option #1, here are the steps to create a robots.txt file for your site:

1. Add a blank plain-text file to the root directory of your site
2. Name the text file `robots.txt` and upload to your server

Done. Now you can add the Blackhole rules provided on the plugin settings page. See the next section to learn more and validate your robots.txt file.

To view your robots.txt file, visit the following URL (replace example.com with your domain):

	https://example.com/robots.txt

__Tip:__ you can find a link to your site's robots.txt file on the plugin settings page.


**Robots Tools & Info**

* [Learn more about robots.txt](https://www.robotstxt.org/)
* [Validate your robots.txt file](https://lxrmarketplace.com/robots-txt-validator-tool.html)
* [Validate robots.txt in Google Webmaster Tools](https://www.google.com/webmasters/tools/robots-testing-tool)
* [Google Robots.txt Specifications](https://developers.google.com/search/reference/robots_txt)
* [How to Create a robots.txt file](https://support.google.com/webmasters/answer/6062596?hl=en)

Lots more great resources on the web to learn about and validate your robots.txt file. Read up, it's important for SEO.


**Caching Plugins**

Blackhole works with any type of caching plugin where "page caching" is not enabled.

There are many types of cache plugins. They provide all sorts of different caching mechanisms and features. All caching features work great with Blackhole except for “page caching”. With page caching, the required WP `init` hook may not be fired, which means that plugins like Blackhole are not able to log and ban requests dynamically. Fortunately, some of the most popular caching plugins provide settings that enable full compatibility with Blackhole. For a complete list, check out [this article](https://plugin-planet.com/blackhole-pro-cache-plugins/). Note: that article was written for [Blackhole Pro](https://plugin-planet.com/blackhole-pro/), but the compatibility list and general info apply also to Blackhole (free version).



**Testing**

To test that the Blackhole trap is working, view the source code of any web page on your site. Scroll down near the footer of the page until you locate a link that looks similar to the following:

	<a rel="nofollow" style="display:none;" href="https://example.com/?blackhole=1234567890" title="Do NOT follow this link or you will be banned from the site!">Name of Your Website</a>

Click the link (the `href` value) to view the Warning Message. After visiting the Warning Message, refresh the page to view the Access Denied message. And/or visit any other page on the front-end of your site to verify that you have been banned. But don't worry, you will never be banned from the WP Admin Area or the WP Login Page. So simply log in and remove your IP address from the Bad Bots list to restore front-end access. 

More information on [how to verify Blackhole is working &raquo;](https://plugin-planet.com/blackhole-pro-check-plugin-working/)



**Why no bots?**

If you're not seeing any bad bots getting blocked, there are several things to keep in mind:

* Make sure you've set up according to the docs above
* New(er) websites may not get a lot of bad bot traffic
* Sites with low traffic may not get a lot of bad bots
* Check if you are using any other bot-blocking plugins
* Not all websites (even popular ones) get tons of bots
* Blackhole will not work if you have page caching on site
* If in doubt, you can test if the plugin is working (see previous section above)

So keep those things in mind. In most cases it's just a matter of time before some bad bots fall into the black hole.



**Whitelisted Bots**

Blackhole for Bad Bots is rigorously tested to ensure that the top search engine bots are NEVER BLOCKED. Any bots reporting a User Agent that contains any of the following strings will always have access to your site, even if they disobey robots.txt.

	a6-indexer, adsbot-google, ahrefsbot, aolbuild, apis-google, baidu, bingbot, bingpreview, butterfly, cloudflare, chrome, duckduckgo, embedly, facebookexternalhit, facebot, google page speed, googlebot, ia_archiver, linkedinbot, mediapartners-google, msnbot, netcraftsurvey, outbrain, pinterest, quora, rogerbot, showyoubot, slackbot, slurp, sogou, teoma, tweetmemebot, twitterbot, uptimerobot, urlresolver, vkshare, w3c_validator, wordpress, wp rocket, yandex

Of course, this list is completely customizable via the plugin settings. Each added string is matched against the full user agent, so be careful. Learn more about [user agents of the top search engines](https://perishablepress.com/list-all-user-agents-top-search-engines/).

You can also whitelist bots by IP address. Visit the setting, "Whitelisted IPs", and enter the IP address (separate multiple IPs with commas). You can also whitelist entire ranges of IPs. In the same plugin setting, add something like this:

	123.456.

That will allow all bots reporting any IP that begins with `123.456.`. You can also whitelist IP addresses using CIDR notation. Check out the Help tab on the plugin settings page for details.



**Customizing**

Blackhole provides plenty of hooks for customizing and extending:

	blackhole_options
	blackhole_badbots
	blackhole_get_options
	blackhole_get_badbots
	blackhole_log_data
	blackhole_trigger
	blackhole_vars
	blackhole_log
	blackhole_ip_keys
	blackhole_alert_name
	blackhole_alert_subject
	blackhole_alert_message
	blackhole_alert_headers
	blackhole_needle
	blackhole_message_default
	blackhole_message_custom
	blackhole_message_nothing
	blackhole_ignore_loggedin
	blackhole_ignore_backend
	blackhole_ignore_login
	blackhole_block_status
	blackhole_block_protocol
	blackhole_block_connection
	blackhole_ip_filter
	blackhole_validate_ip_log
	blackhole_settings_contextual_help
	blackhole_badbots_contextual_help

If you need a hook added, [drop me a line](https://perishablepress.com/contact/), will be glad to hook it up ;)



**Custom Warning Template**

The Blackhole displays two types of messages:

* Warning Message - Displayed when bots follow the blackhole trigger
* Blocked Message - Displayed for all requests made by blocked bots

The Blocked Message may be customized via the plugin settings. The Warning Message may be customized by setting up a custom template:

1. Copy `blackhole-template.php` from the plugin's `/inc/` directory
2. Paste the file into your theme template, for example: `/wp-content/my-awesome-theme/blackhole-template.php`
3. Customize any of the markup between "BEGIN TEMPLATE" and "END TEMPLATE"
4. Upload to your server and done

If the custom template exists in your theme directory, the plugin automatically will use it to display the Warning Message. If the custom template does not exist in your theme directory, the plugin will fallback to the default warning message.

__Tip:__ Instead of including the custom template in your theme, you can include via `/wp-content/` directory, like: `/wp-content/blackhole/blackhole-template.php`

_[More options available in the Pro version &raquo;](https://plugin-planet.com/blackhole-pro/)_



**Uninstalling**

Blackhole for Bad Bots cleans up after itself. All plugin settings and the bad bot list will be removed from your database when the plugin is uninstalled via the Plugins screen. After uninstalling, don't forget to remove the blackhole rules from your `robots.txt` file. It's fine to leave them in place, it will not hurt anything, but they serve no purpose without the plugin installed.

More specifically, Blackhole adds only two things to the database: options and bot list. When the plugin is uninstalled/deleted via the Plugins screen, both of those items are removed automatically via the following lines in `uninstall.php`:

	delete_option('bbb_options');
	delete_option('bbb_badbots');

So after uninstalling the plugin and deleting the robots.txt rules, there will be no trace of Blackhole for Bad Bots on your site.



**Like the plugin?**

If you like Blackhole for Bad Bots, please take a moment to [give a 5-star rating](https://wordpress.org/support/plugin/blackhole-bad-bots/reviews/?rate=5#new-post). It helps to keep development and support going strong. Thank you!



== Upgrade Notice ==

To upgrade Blackhole for Bad Bots, remove the old version and replace with the new version. Or just click "Update" from the Plugins screen and let WordPress do it for you automatically.

Note: uninstalling the plugin from the WP Plugins screen results in the removal of all settings and data from the WP database. 



== Frequently Asked Questions ==


**Do you offer any other security plugins?**

Yes, three of them:

* [BBQ Firewall](https://wordpress.org/plugins/block-bad-queries/) for super-fast firewall security
* [Blackhole for Bad Bots](https://wordpress.org/plugins/blackhole-bad-bots/) to protect your site against bad bots
* [Banhammer](https://wordpress.org/plugins/banhammer/) to monitor and ban any user or IP address

Pro versions with more features available at [Plugin Planet](https://plugin-planet.com/).


**How is this plugin different than a firewall?**

Blackhole uses its own "smart bot technology" that only blocks bots if they have demonstrated bad behavior. Firewalls typically are "static" and block requests based on a predefined set of patterns. That means that firewalls sometimes block legitimate visitors. Blackhole never blocks regular visitors, and only it blocks bots that disobey your site's robots.txt rules. So the rate of false positives is close to zero.


**The trigger link is not appearing in the source code?**

In order for the plugin to add the trigger link to your pages, your theme must include the template tag, `wp_footer()`. This is a recommended tag for all WordPress themes, so your theme should include it. If not, you can either add it yourself or contact the theme developer and ask for help. Here is [more information about wp_footer()](https://codex.wordpress.org/Function_Reference/wp_footer). Once the footer tag is included, the plugin will be able to add the trigger link to your pages.


**Will this block good bots like Google and Bing?**

No. Never. All the major search engine bots are whitelisted and will never be blocked. Unless you remove them from the whitelist setting, which is not recommended.


**I think the plugin is blocking Chrome, Firefox, etc.?**

Impossible because the plugin never blocks by user agent. It only blocks by IP address. No other criteria are used to block anything.


**How to add bots to the Blackhole manually?**

Question: Is it possible to block some bots by just adding them to blocked list and deny them Access to my website?

Answer: Not possible with the free version, but the [Pro version](https://plugin-planet.com/blackhole-pro/) includes an easy way to add bots manually (via the Bad Bots Log).


**How do I add other bots to the whitelist?**

Visit the plugin settings and add to the list.


**How do I reset the list of blocked bots?**

Visit the plugin settings and click the button.


**How do I delete the example/default bot from the log?**

Not possible with the free version, but can do with the [Pro version](https://plugin-planet.com/blackhole-pro/).


**How can I disable the email alerts?**

Visit the plugin settings and click the button.


**Is there a standalone version of the Blackhole?**

Yes. Visit Perishable Press to download a [PHP-based version](https://perishablepress.com/blackhole-bad-bots/) that does not require WordPress.


**Is there a Pro version of Blackhole?**

Yes, the [Pro version](https://plugin-planet.com/blackhole-pro/) is available at Plugin Planet.


**Is Multisite supported?**

Not yet, but it's on the to-do list.


**Which IP address are added by default?**

Your server IP address and your local (home) IP address (or whichever IP you are using when the plugin is installed).


**Can I manually include the blackhole link?**

Yes, you can add the following code anywhere in your theme template:

`<?php if (function_exists('blackhole_trigger')) blackhole_trigger(); ?>`


**Should whitelisted bots contain exact names?**

Question: Should whitelisted bots contain exact names, or can I just use partial names?

Answer: You can use partial names or full names, depending on how specific you would like to be with blocking. If you look at the default whitelisted bot strings, you will see that they are just portions of the full user agent. So for example you can block all bots that include the string "whateverbot" by including that string in the whitelist setting. It makes it easier to block bots, but you have to be careful about false positives.


**What about WordPress automatic (hidden) robots.txt?**

By default, WordPress will automatically serve a hidden, "virtual" robots.txt file to anything that requests it. Once you add your own "real" robots.txt file, WordPress will stop generating the virtual one. So when it comes to WordPress and robots.txt, real trumps virtual. Blackhole Pro requires that you add some rules to an actual robots.txt file, but it does not create/add any robots rules or the robots.txt file for you. Check out the plugin's Help tab for more infos.


**Which WP caching plugins are compatible with Blackhole?**

Check out the section on "Caching Plugins" in the [plugin documentation](https://wordpress.org/plugins/blackhole-bad-bots/#installation)


**Does Blackhole clean up after itself?**

Yes! As explained in the "Uninstalling" section in the [plugin documentation](https://wordpress.org/plugins/blackhole-bad-bots/#installation), when Blackhole is uninstalled via the Plugins screen, it removes everything from the database. After uninstalling, don't forget to remove the blackhole rules from your `robots.txt` file. Then there will be zero trace of the plugin on your site.


**How to disable the hostname lookup?**

By default, the plugin uses PHP's `gethostbyaddr()` function to lookup the host name for blocked requests. This is fine on most servers but some may experience slight reduced performance. So for those who may need it, the following code snippet can be added to disable the host lookup:

	function blackhole_enable_host_check() { return false; }
	add_filter('blackhole_enable_host_check', 'blackhole_enable_host_check');


**How to disable the error log entries?**

By default the plugin adds an entry in the site error log for any invalid IP address. To disable this feature, add the following code snippet to your (child) theme's functions file, or add via simple custom plugin:

	function blackhole_validate_ip_log_custom($log, $ip) { return ''; }
	add_filter('blackhole_validate_ip_log', 'blackhole_validate_ip_log_custom', 10, 2);


**How to enable Blackhole protection on Login Page?**

By default, Blackhole never blocks anything on the WP Login Page. This is to prevent new users from accidentally getting locked out of their site. 

To change the default behavior, and add Blackhole protection to the Login Page, add the following code to theme or child theme's functions.php file (or add via simple custom plugin):

`function blackhole_ignore_login($ignore) { return false; }
add_filter('blackhole_ignore_login', 'blackhole_ignore_login');`

If you get locked out inadvertently, simply remove the code and the Login Page will be accessible once again.


**Got a question?**

Send any questions or feedback via my [contact form](https://perishablepress.com/contact/)



== Support development of this plugin ==

I develop and maintain this free plugin with love for the WordPress community. To show support, you can [make a donation](https://monzillamedia.com/donate.html) or purchase one of my books: 

* [The Tao of WordPress](https://wp-tao.com/)
* [Digging into WordPress](https://digwp.com/)
* [.htaccess made easy](https://htaccessbook.com/)
* [WordPress Themes In Depth](https://wp-tao.com/wordpress-themes-book/)

And/or purchase one of my premium WordPress plugins:

* [BBQ Pro](https://plugin-planet.com/bbq-pro/) - Super fast WordPress firewall
* [Blackhole Pro](https://plugin-planet.com/blackhole-pro/) - Automatically block bad bots
* [Banhammer Pro](https://plugin-planet.com/banhammer-pro/) - Monitor traffic and ban the bad guys
* [GA Google Analytics Pro](https://plugin-planet.com/ga-google-analytics-pro/) - Connect WordPress to Google Analytics
* [USP Pro](https://plugin-planet.com/usp-pro/) - Unlimited front-end forms

Links, tweets and likes also appreciated. Thanks! :)



== Changelog ==

If you like Blackhole for Bad Bots, please take a moment to [give a 5-star rating](https://wordpress.org/support/plugin/blackhole-bad-bots/reviews/?rate=5#new-post). It helps to keep development and support going strong. Thank you!

__Important!__ You need to update your robots.txt file. The [robots standards have changed](https://webmasters.googleblog.com/2019/07/rep-id.html), so you need to update your robots.txt file with the new Blackhole rules. Visit the plugin settings page to get the latest rules, and then add them to your robots.txt file. More information in the Installation docs and Help tab (located on the plugin settings page).


**3.2 (2021/07/19)**

* Adds `chrome` agent to whitelist
* Adds `google page speed` agent to whitelist
* Increases priority for adding robots rules
* Improves plugin readme/documentation
* Tests on WordPress 5.8

**3.1 (2021/02/11)**

* Improves performance of IP functionality
* Improves output of Blackhole trigger link
* Updates default translation template
* Tests on WordPress 5.7

**3.0 (2020/11/15)**

* Fixes bug with error log entries
* Updates plugin script to account for changes in jQuery UI
* Adds support for custom warning template in `/wp-content/`
* Adds filter hook `blackhole_template_filename`
* Adds filter hook `blackhole_custom_dir`
* Updates default translation template
* Updates and refines readme.txt
* Tests on PHP 7.4 and 8.0
* Tests on WordPress 5.6

**2.9.1 (2020/08/19)**

* Updates contextual Help tab information
* Updates default translation template
* Refines the plugin setting page
* Refines readme/documentation
* Tests on WordPress 5.5

**2.9 (2020/08/09)**

* Updates the Blackhole robots.txt rules (see above note)
* Adds rules dynamically to WP robots.txt (Thanks @blackcapdesign)
* Adds filter hooks `blackhole_robots_rules` and `blackhole_domain`
* Adds Blackhole trigger to Login Page (Thanks @wp-henne)
* Adds `wp rocket` to the default user-agent whitelist
* Removes `wprocketbot` from default user-agent whitelist
* Removes Securi and WP Rocket from default IP whitelist
* Appends version number to CSS and JS on settings page
* Bugfix: escapes special characters in whitelist setting
* Bugfix: now loads JavaScript vars only on settings page
* Updates contextual Help tab information
* Updates default translation template
* Refines the plugin setting page
* Refines readme/documentation
* Tests on WordPress 5.5

**2.8 (2020/03/19)**

* Improves markup/meta of blackhole warning page
* Adds note about cache plugins on settings page
* Fixes PHP warning with `gethostbyaddr()`
* Updates contextual help tab information
* Generates new default translation template
* Tests on WordPress 5.4

**2.7 (2019/11/09)**

* Adds Host Name to email alerts and warning page
* Adds filter `hook blackhole_enable_host_check`
* Adds `!defined('ABSPATH')` to new core files
* Adds "uptimerobot" to default whitelist
* Updates styles for plugin settings page
* Tests on WordPress 5.3

**2.6 (2019/08/21)**

* Optimizes plugin performance
* Optimizes internal code structure
* Removes all transients functionality
* Adds code to remove transients on uninstall
* Adds filter for error log output
* Improves error log handling
* Updates some links to https
* Generates new default translation template
* Tests on WordPress 5.3 (alpha)

**2.5 (2019/05/01)**

* Bumps [minimum PHP version](https://codex.wordpress.org/Template:Server_requirements) to 5.6.20
* Updates default translation template
* Tests on WordPress 5.2

**2.4 (2019/04/10)**

* Adds caching for get_vars data
* Adds `a6-indexer` and `ahrefsbot` to default user-agent whitelist
* Tests on WordPress 5.1 and 5.2 (alpha)

**2.3 (2019/03/11)**

* Adds domain name to email alerts
* Improves function `action_links()`
* Refines plugin settings screen UI
* Updates URL for constant `BBB_HOME`
* Generates new default translation template
* Tests on WordPress 5.1 and 5.2 (alpha)

**2.2 (2019/02/20)**

* Tests on WordPress 5.1

**2.1 (2018/11/16)**

* Adds link to robots.txt from settings page
* Adds homepage link to Plugins screen
* Updates default translation template
* Tests on WordPress 5.0

**2.0 (2018/08/21)**

* Adds intro blurb to email alerts
* Adds `rel="noopener noreferrer"` to all [blank-target links](https://perishablepress.com/wordpress-blank-target-vulnerability/)
* Updates GDPR blurb and donate link
* Regenerates default translation template
* Further tests on WP 4.9 and 5.0 (alpha)

**1.9.2 (2018/05/11)**

* Adds support for WP Fastest Cache
* Improves support for caching plugins
* Updates default set of whitelisted user agents
* Updates default set of whitelisted IP addresses
* Adds filter hook, `blackhole_verify_nonce`
* Adds function to enable `display` in textarea settings
* Changes example IP address to `173.203.204.123`
* Fixes obscure `call_user_func` error
* Generates new translation template
* Tests on WordPress 5.0 (alpha)

**1.8 (2017/10/19)**

* Adds to default whitelisted user agents: `apis-google`, `facebot`, `facebookexternalhit`, `pinterest`, `twitter`, `wordpress`
* Updates readme/documentation
* Tests on WordPress 4.9

**1.7.1 (2017/08/13)**

* Replaces `wp_doing_cron()` with direct check for `DOING_CRON`
* Tests on WordPress 4.9 (alpha)

**1.7 (2017/07/29)**

* Adds support for CIDR notation for whitelisted IPs
* Adds function to ignore command-line requests
* Adds logic to ignore WP-Cron requests
* Updates contextual help (Help tab)
* Tests on WordPress 4.9 (alpha)

**1.6 (2017/04/09)**

* Updates default IP keys
* Adds some missing translation strings
* Auto-adds server IP to whitelist settings
* Adds French translation (thanks to Bouzin)
* Adds filter for IP keys, `blackhole_ip_keys`
* Adds meta noindex, nofollow to blackhole page
* Replaces global `$wp_version` with `get_bloginfo('version')`
* Enhances default character set for whois lookup
* Regenerates default translation template
* Tests on WordPress version 4.8

**1.5 (2017/03/08)**

* Fixes some incorrect translation strings
* Adds some style to the Robots Rules setting
* Adds complete contextual help (via the Help tab)
* Adds info about the default bot to the Bad Bots screen
* Replaces delete link with blackhole icon for default bot
* Removes line breaks from plugin-activation warning messages
* Replaces `admin_url()` with `admin_url('plugins.php')` for activation warning
* Replaces `get_template_directory` with `get_stylesheet_directory`
* Updates plugin links on the WP Plugins screen
* Adds default option for `ip_whitelist` setting
* Adds option to set the address for the "From" email header
* Improves loading of PHP include files
* Changes link text for clearing list of bad bots
* Adds new action hook: `blackhole_settings_contextual_help`
* Adds new action hook: `blackhole_badbots_contextual_help`
* Refines the Settings and Bad Bots screens
* Improves logic for script/style enqueue
* Adds blurb about Pro version
* Generates new translation template
* Tests on WordPress version 4.8 (alpha)

**1.4 (2016/11/14)**

Important: if you are upgrading from a previous version and are using a Custom Warning Template, please move it to your theme directory. Otherwise it will be deleted when you upgrade the plugin. Check out the "Custom Warning Template" section under the [Installation documentation](https://wordpress.org/plugins/blackhole-bad-bots/installation/) for more information.

* Fixes missing settings notices via settings_errors
* Adds strong tags to admin notices
* Changes directory for custom warning template
* Removes the `/custom/` directory
* Removes Save Changes button from Bad Bot screen
* Updates some default settings page styles
* Changes stable tag from trunk to latest version
* Adds `&raquo;` to rate plugin link on Plugins screen
* Improves escaping for localization tags
* Updates URl for rate this plugin link
* Tests on WordPress version 4.7 (beta)

**1.3.1 (2016/08/11)**

* Fixes bug with IP-address whitelisting
* Fixes bug with user-agent whitelisting
* Makes a small change to settings label
* Generates new translation template

**1.3 (2016/08/08)**

* Updates [WP Admin Notices](https://digwp.com/2016/05/wordpress-admin-notices/)
* Replaces `_e()` with `esc_html_e()`
* Replaces `__()` with `esc_html__()`
* Removes unnecessary `settings_errors()`
* Renames text domain from "bbb" to "blackhole-bad-bots"
* Renames `/lang/` directory to `/languages/`
* Updates `load_i18n()`
* Refines `blackhole_callback_robots()`
* Fixes bug in `blackhole_whois()`
* Improves performance by eliminating redundant whois lookup
* Adds new hook to filter IP address validation: `blackhole_ip_filter`
* Improves IP address handling (adds three new IP-related functions)
* Improves readability of whois email notifications
* Adds "Whitelisted IPs" setting
* Adds a rate this plugin link
* Tests on WordPress 4.6

**1.2 (2016/03/28)**

* Updates URL for BBB_HOME
* Tweaks display of "Blocked Bots" in plugin settings
* Tests on WordPress 4.5 beta

**1.1 (2016/02/22)**

* Adds "baidu" to the search engine whitelist
* Adds credit to documentation for header image

**1.0 (2016/02/22)**

* Initial release
