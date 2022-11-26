=== Site Search 360 ==

Contributors: dsky
Donate link: 
Tags: site search, wordpress search, search, better search, custom search, autocompletion, search suggest, autocomplete, suggest, typeahead, relevance search
Requires at least: 4.0.0
Tested up to: 5.8.2
Stable tag: 1.1.28
Requires PHP: 5.2.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Precise and fast search, autocompletion, and search suggestions for your WordPress page.

== Description ==

Site Search 360 replaces your standard WordPress search by a fast and precise on-site search on all your posts and pages. Site Search 360 is highly customizable and gives you [detailed insights](https://control.sitesearch360.com/) into search behavior.

Site Search 360 is responsive and mobile ready so your search will work no matter what screen your visitors are on.

[youtube https://www.youtube.com/watch?v=yZoYy-JBIh8]

## Features

* Fast indexing and swift search and suggestions
* Result set clusters: Group search results of the same type together, e.g. all article matches and all matches on review pages.
* Drop-in replacement: in most cases you do not need to change a single configuration to enable the search instantly.
* Fast typeahead autocomplete search suggestions based on titles, tags, and author names.
* Custom taxonomy indexing: custom taxonomy terms can be indexed and even shown in the search results.
* Search results **automatically update** when you save, delete, or change search content.
* Easily customizable by CSS and the [dashboard](https://control.sitesearch360.com/).
* Site Search 360 [Search designer](https://www.sitesearch360.com/search-designer) compatible.

== Installation ==

1. Install the plugin.
2. Activate the plugin.
3. On the Configuration page, you log in to your existing Site Search 360 account or simply create a new one. You will get an email with credentials.
4. Optionally, configure the search in your WordPress backend. Indexing settings and analytics can be seen at your  [dashboard](https://control.sitesearch360.com/).

See our [WordPress Integration Guide](https://docs.sitesearch360.com/wordpress-integration) for more details.

## Frequently Asked Questions ##

### My theme doesn't have a search box, how do I add one? ###

You can add a search box to your site by using one of the following options:

* In your WordPress administration go to **Appearance > Widgets** and add **Site Search 360 Search Form** widget to your widget area.
* Use shortcodes to add search box to your posts or pages, you can add a search box by using the `[ss360-searchbox]` shortcode, a search button by using the `[ss360-searchbutton]Search[/ss360-searchbutton]` shortcode or a full search form by using the `[ss360-form][ss360-searchbox][ss360-searchbutton]Search[/ss360-searchbutton][/form]` shortcode (**recommended**).
* Add a search box by modifying your theme files. See our [Wordpress Integration Guide](https://docs.sitesearch360.com/wordpress-integration) for more details. **Please Note:** Only modify the theme files if you know what you're doing and if the theme license allows modification.

### How can I index ACFs (Advanced Custom Fields)? ###

To index custom fields with Site Search 360 go to your WordPress administration and under **Site Search 360 > Indexing** you can select which custom fields should be indexed.

* Each **text field** can be used as the search result title, search result snippet, or added to the content to improve the search results.

* **Image field** can be used as the search result preview image.

If you need more flexibility when indexing custom fields, you can select the *Crawler indexing* option and customize the indexing behavior in the [Site Search 360 Control panel](https://control.sitesearch360.com).  

== Screenshots ==

1. An example of the grouped search suggest.
2. An example of search results.
3. The main plugin dashboard.
4. An example of a configuration page.
5. Analytics dashboard.

== Getting started ==

To get started, see the [Installation instructions](https://wordpress.org/plugins/site-search-360/#installation) or check our full **[WordPress Integration Guide](https://docs.sitesearch360.com/wordpress-integration)**.

== Help ==
Need help? Just post your question in the [support forum](https://wordpress.org/support/plugin/site-search-360) or [chat with us](https://gitter.im/site-search-360/Lobby) right away.


== Changelog ==
= 1.1.28 =
* Clean-up.

= 1.1.27 =
* Catch uninstall errors.

= 1.1.26 =
* Improved support for new control panel.

= 1.1.25 =
* Support SSO for the new control panel.

= 1.1.24 =
* Automatic re-indexing rate checking.

= 1.1.23 =
* Major fix.

= 1.1.22 =
* Minor fix.

= 1.1.21 =
* Add option to disable the js plugin.

= 1.1.20 =
* Add placeholder attribute to shortcodes and widgets.

= 1.1.19 =
* Don't prefill email when signing up.

= 1.1.18 =
* WP 5.5 compatibility.

= 1.1.17 =
* Include/exclude content groups attribute for shortcodes.

= 1.1.16 =
* Indexing fix.

= 1.1.15 =
* Add preRender callback setting.

= 1.1.14 =
* Version bump.

= 1.1.13 =
* Minor indexing bug fix.

= 1.1.12 =
* Database indexing bug fix.

= 1.1.11 =
* Option to add a search box to menu.

= 1.1.10 =
* WooCommerce filter indexing.

= 1.1.9 =
* Cleanup.

= 1.1.8 =
* Optional WooCommerce category indexing.

= 1.1.7 =
* Minor fix

= 1.1.6 =
* Shortcode stripping

= 1.1.5 =
* Fix Search Designer link.

= 1.1.4 =
* Fix indexing author name (nickname was being indexed instead of public name).
* Add navigationClick callback.

= 1.1.3 =
* Catch json encoding issues when indexing.
* Add composer.json file.

= 1.1.2 =
* Setting description fix.
* Redirect to login page after logout.

= 1.1.1 =
* Update JavaScript plugin to v13.
* Add new configuration options (accent color, all results tab settings).
* Update defaults to match the JavaScript plugin defaults.
* Apply styling to search widgets and forms added using shortcodes.
* Improved database indexing.
* Bug fixes.

= 1.0.45 =
* Bug fix.

= 1.0.44 =
* Move Log out button.

= 1.0.43 =
* Bug fix.

= 1.0.42 =
* Query ACFs by post id.

= 1.0.41 =
* ACF indexing fixes.

= 1.0.40 =
* Minor indexing page change.

= 1.0.39 =
* Partial ACF support with Database mode (text, wysiwyg and image types).

= 1.0.38 =
* Bug fix.

= 1.0.37 =
* Bug fix.

= 1.0.36 =
* Add support for custom callbacks (init, preSuggest, preSearch, postSearch).
* Add custom search widget.

= 1.0.35 =
* Style improvements on configuration pages.

= 1.0.34 =
* Improve integration with the Search Designer.
* Don't index unnecessary WooCommerce filters.
* Perform settings clean-up after Indexing Mode is switched.
* Minor tweaks.

= 1.0.33 =
* Show account name on the dashboard.
* Add shortcodes for integrating search components.
* Minor improvements and fixes.

= 1.0.32 =
* Index custom taxonomies.
* Improve search with database indexing.
* Detect ACF plugin usage.

= 1.0.31 =
* Add button to stop database re-indexing.

= 1.0.30 =
* Add grid title configuration.

= 1.0.29 =
* Fix bug preventing menu saving.

= 1.0.28 =
* Create content group configurations when indexing with DB Mode.
* Improve labeling of uncategorized results.

= 1.0.27 =
* Empty the index after indexing type switch.
* Update image extraction.
* Logging out.
* Bug fix.

= 1.0.26 =
* Update reset password link.

= 1.0.25 =
* Bug fix.

= 1.0.24 =
* Bug fix.

= 1.0.23 =
* Add index synchronization settings.
* Add forgot password link.
* Add new options to configuration editor.

= 1.0.22 =
* Bug fix.

= 1.0.21 =
* Bug fix.

= 1.0.20 =
* Minor indexing change after login.
* Improve DB indexing mode.

= 1.0.19 =
* Bug fix.

= 1.0.18 =
* Check whether post should be indexed.

= 1.0.17 =
* Bug fix.

= 1.0.16 =
* Remove auto-reindex on login.

= 1.0.15 =
* Add crawler indexing switch.
* Improve WP indexing.

= 1.0.14 =
* Connect login page to the new login API.

= 1.0.13 =
* Add new configuration settings (masonry layout, image x title positioning, infinite scroll,...).
* Minor bug fixes.

= 1.0.12 =
* Ensure settings can be saved in the Site Search 360 control panel.
* Prevent creating duplicate filters.
* Add default 'Other' Content Group name.

= 1.0.11 =
* Bug fix.

= 1.0.10 =
* Bug fix.

= 1.0.9 =
* Warning bug fix.

= 1.0.8 =
* Indexing bug fix.

= 1.0.7 =
* Bug fix.

= 1.0.6 =
* Add custom post type indexing.
* Bug fix.

= 1.0.5 =
* Adjust some texts.

= 1.0.4 =
* Minor bug fix.

= 1.0.3 =
* Better WooCommerce support.

= 1.0.2 =
* Remove .mjs script file to avoid caching issues from other WP plugins.

= 1.0.1 =
* Minor bug fix.

= 1.0.0 =
* Full redesign.
* Update JavaScript plugin to v12 and load from Site Search 360 CDN.
* Add filter configuration.
* Add search designer connection.
* Change default indexing logic.
* Improve configuration editor.
* Split-up configuration pages.
* Add basic search statistics.
* Improved multisite support.
* Many small improvements and bug fixes.

= 0.7.1 =
* Admin panel checkboxes fix.

= 0.7.01 =
* Update JavaScript plugin to v11.48.

= 0.7.0 =
* Update SiteSearch360 JavaScript plugin to v11 - tabbed navigation, enhanced tracking, grid layout,... ([full changelog](https://docs.sitesearch360.com/en/release-notes)).
* Add editor mode.
* Add option to render search results using the theme template.

= 0.6.93 =
* Update to v10.56.

= 0.6.92 =
* Add Search Result URL configuration.

= 0.6.9 =
* Add group results configuration option and update SiteSearch JavaScript version to v10.49.

= 0.6.8 =
* Update SiteSearch360 JavaScript version to v10.24 - minor fixes and adjustments.

= 0.6.7 =
* Update SiteSearch360 JavaScript version to v10 - accessibility improvements, mobile suggestions, layover styling and layover search box.

= 0.6.6 =
* Update SiteSearch360 JavaScript version.

= 0.6.5 =
* Site id configuration.

= 0.6.1 =
* Fix page indexing/removing on save/trash.

= 0.6.0 =
* Additional configuration options.
* Update to latest version of Site Search 360 JavaScript plugin.

= 0.5.0 =
* Initial release.