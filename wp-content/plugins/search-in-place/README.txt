=== Search in Place ===
Contributors: codepeople
Donate link: https://searchinplace.dwbooster.com
Tags: search,search pages,search posts,ajax,posts,page,post,post search,page search,content,title,highlight,attachment,navigation,search custom post type,custom post,woocommerce,admin,image,images,taxonomy,all or any terms,colors
Requires at least: 3.0.5
Tested up to: 6.0
Stable tag: 1.0.103
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Search in Place improves blog search by displaying query results in real time. It displays the results dynamically as you enter the search criteria.

== Description ==

Search in Place features:

» Performs real time search as you enter search criteria
» Groups search results by post type (post, page)
» Allows to limit the number of elements in the dynamic search results
» Offers a different navigation option on the website
» Use a friendly AJAX technology for searching
» Suggests search terms based on the information typed by the user.

**Search in Place** improves blog search by displaying query results in real time. Search in place displays a list with results dynamically as you enter the search criteria.

**Search in Place** groups search results by their type, labeling them as post, page or the post type entered, and highlights the searched terms in search page and resulting pages. Search in Place advanced allows to search in metadata and author display name too.


**More about the Main Features:**

*  Performs **real time search** as you enter search criteria;
*  Use a friendly AJAX technology for searching;
*  Groups search results **by post type** (post, page);
*  Allows to **limit the number of elements** in the dynamic search results;
*  Allows to customize the **box colors**;
*  Offers a different navigation option on the website.

The plugin converts the website's search boxes into search in places. Furthermore, the plugin allows to insert additional search boxes using the shortcode:

	`[search-in-place-form]`

For searching in the current page only, insert the shortcode with the **in_current_page** attribute:

	`[search-in-place-form in_current_page="1"]`

For inserting the shortcode in the website's pages it is possible to use the specific integrations with the editors: Gutenberg, Elementor, or the widget for Page Builder by SiteOrigin. For the other editors, insert the shortcode.

Frequently, while performing a search in a blog with terms we think are present in the blog's pages and posts, after various search attempts and a slow/tedious process of page reloads, we feel frustration because we haven’t found the page/post being searched for. With Search in Place the search process is easier and seamless. The Search in Place makes the post search in real time, allowing the correction of the search criteria without reloading the website (The basic version of "Search in Place" makes the search in posts and pages).

The plugin transforms all search boxes in the website into search in place boxes, furthermore, the plugin includes a shortcode allowing to insert additional "search boxes", or "search in current page boxes".

**Premium Features:**

*   Allows **highlight** the criteria for searching in results;
*   Allows the searching in **metadata, taxonomies, and author display name** associated to the post, page or custom post type;
*   Allows to define additional **post_types** to be considered for searching;
*   Includes the integration with popular plugins like: **WooCommerce**, **WP e-Commerce**, **Jigoshop**, **Ready! Ecommerce Shopping Cart** and more;
*   Include labels in the search results page;

The plugin displays search results in a popup window by default. However, it is possible to show them in a div tag in the page content:

[youtube https://youtu.be/X9MOjSZO14M]

**Demo of Premium Version of Plugin**

[https://demos.dwbooster.com/search-in-place/wp-login.php](https://demos.dwbooster.com/search-in-place/wp-login.php "Click to access the Administration Area demo")

[https://demos.dwbooster.com/search-in-place/](https://demos.dwbooster.com/search-in-place/ "Click to access the Public Page")


The usual WordPress behavior is searching in the posts, but the post's search may not be sufficient if you are using plugins that includes custom post_types. The WordPress born as a blog manager, but this great platform has been evolved through its plugins until allow be used as a content management system, an eCommerce (WooCommerce, WP e-Commerce, etc.) or a social network, so the search feature require evolve too and allow searching by products, users and any custom post_types. Search in Place came to fill this empty in WordPress, Search in Place allow to search in custom post_types, taxonomies associated to the post_types, its metadata, or the authord display name.But Search in Place don't stop there, with Search in Place is possible to format the results, set labels to identify the search results, and highlight the terms in the resulting pages.

If you want more information about this plugin or another one don't doubt to visit my website:

[https://searchinplace.dwbooster.com](https://searchinplace.dwbooster.com "CodePeople WordPress Repository")

== Installation ==

**To install Search in Place, follow these steps:**

1.	Download and unzip the plugin
2.	Upload the entire search_in_place/ directory to the /wp-content/plugins/ directory
3.	Activate the plugin through the menu option "Plugins" in your Wordpress dashboard.

**Search in Place Setup**

**Search in Place** has several setup options to simplify searches and personalize results. Among the setup possibilities you will find:

**Enter the number of posts to display:** limits the number of elements in the dynamic search results. (Enter a whole number).

**Enter a minimum number of characters to start a search:** The dynamic search will be activated only when the entered search term equals or exceeds the number of characters specified in this setup field.

**Elements to display:** defines the elements that will be shown for each dynamic search result. The post title will always be visible, but the featured image, the author, the publication date, and the post summary can be visible or not depending on this setting option.

**Select the Date Format:** allows to select the format of publication date to be shown in the dynamic search results.

**Enter the number of characters for post summaries:**  if you choose to display post summaries in search results, you can use this option to limit the amount of characters  in these summaries.

**In Search Page/Identify the posts type in search result:** if this option is checked, each search result will be identified by type (post or page).

**Translations**

The Seach in Place use the English language by default, but includes the following language packages:

* Spanish
* French
* Portuguese
* Russian

== Frequently Asked Questions ==

= Q: Why to use Search in Place? =

A: Search in Place allows to find posts and pages without abandon the webpage (the premium version of plugin allows include custom post types).

= Q: Why the posts search doesn't display the thumbnails in the search results? =

A: The thumbnails are get from the featured images assigned to the post or page. Please verify your posts have assigned featured images.

= Q: How to modify the number of items in a post search? =

A: Modify the value of field "Enter the number of posts to display" in the settings page of Search in Place.

If you require more information, please visit our FAQ page in:

= Q: How to display the pages/posts that include all or any of terms typed in the search box? =

A: In the settings page of the plugin there is a new attribute for selecting if get the pages/posts with all terms entered through the search box (AND), or any of terms (OR)

= Q: How to find the products created by WooCommerce? =

A: The Pro version of the plugin allows searching in custom post types, and taxonomies.

In the settings page of the plugin there are two attributes: the "Post type", and "Taxonomy". You simply should press the "Add new type" button, and type the name of post_type, the process to add the taxonomies is similar, press the "Taxonomy" button, and type the taxonomy name.

For common plugins like: WooCommerce, WP e-Commerce, Jigoshop, and Ready! Ecommerce Shopping Cart, with only pressing the corresponding button, all post types and taxonomies used by these plugins are added to the "Search in Place".

= Q: Is possible to exclude some pages or posts from the search result? =

A: Go to the settings page of the plugin, and enter the IDs of pages or posts through the attribute: "Exclude posts/pages (Ids separated by comma)", separated by comma.

[https://searchinplace.dwbooster.com/faq#q5](https://searchinplace.dwbooster.com/faq#q5)

== Screenshots ==

1.	Dynamic Search result.
1.	Inserted a Search in Page box.
3.	Search page (Advanced version only)
4.	Search in Place's Setup page.

== Changelog ==

= 1.0.103 =
= 1.0.102 =

* Accepts additional accordion structure.

= 1.0.101 =

* Improves the plugin code and security.

= 1.0.100 =

* Highlights the term clicked on the current page search.

= 1.0.99 =

* Implements the integration with Wpbakery accordion.

= 1.0.98 =

* Modifies the plugin settings.

= 1.0.97 =

* Improves the plugin's code and its security.

= 1.0.96 =

* Modifies the Display Button option to fix a flashing effect.

= 1.0.95 =

* Modifies the uninstall module.
* Includes the troubleshooting area in the commercial distribution to prevent conflicts with WPML.

= 1.0.94 =

* Modifies functions deprecated by the latest Elementor update.

= 1.0.93 =

* Modifies the root URL.

= 1.0.92 =

* Allows translating the placeholder text.

= 1.0.91 =

* Modifies the Elementor widget.

= 1.0.90 =

* Fixes a minor issue jumping to the terms in the search on the current page.
* Optimizes the js and css public files.

= 1.0.89 =

* Implements the integration with Helpie FAQ accordion.

= 1.0.88 =

* Implements support for Appilo Theme accordions.

= 1.0.87 =

* Includes support for BeTheme accordions.

= 1.0.86 =

* Modifies the Elementor widget.

= 1.0.85 =

* Includes a new attribute in the search in page shortcode to disable the enter key.
* Improves the Gutenberg block, Elementor module, and SiteOrigin widget.

= 1.0.84 =

* Includes a new option in the plugin's settings to enter the More Results text.

= 1.0.83 =

* Allow to display the search results into a div tag in the page content.

= 1.0.82 =

* Encloses the search boxes between div tags with the search-in-place-box-container class name. This modification allows customizing the search box appearance easily.

= 1.0.81 =

* Search in page founds terms in multi-level Elementor accordions and opens them.

= 1.0.80 =

* Improves the integration with Gutenberg editor.

= 1.0.79 =

* Implements the searches inside popular accordions, like the Elementor block, Divi accordion, SiteOrigin accordion.

= 1.0.78 =

* Optimizes search queries by reducing execution time and memory consumption.

= 1.0.77 =

* Modifies the search on the current page to ignore the tildes.

= 1.0.76 =

* Fixes an issue with searching in the current page content.

= 1.0.75 =

* Includes a new attribute in the shortcode to exclude the terms that are not visible on the page.

= 1.0.74 =

* Modifies the parameters in the more results link.
* Fixes a minor conflict with WPML.

= 1.0.73 =

* Fixes an issue with the more results link.

= 1.0.72 =

* Fixes a warning message.

= 1.0.71 =

* Improves the accessibility.

= 1.0.70 =

* Applies the length of the summary, entered through the plugin's settings, to the search in page too.

= 1.0.69 =

* Fixes a conflict with Elementor.

= 1.0.68 =
= 1.0.67 =

* Displays found terms in the middle of viewport for the search in page.

= 1.0.66 =

* Modifies the search in page for accepting to search by the terms or the complete phrase (configured through the settings page of the plugin).

= 1.0.65 =

* Includes a new attribute in the search form shortcode, and editors modules, to display/hide search button in the "search in page" form.

= 1.0.64 =

* Allows to display the found terms into a pop-up for the search in the page process, similar to the pop-up in the website's search.

= 1.0.63 =

* Includes multiple filters to allow other developers can extend the plugin:

search-in-page-summary allows us to edit the results summaries. It receives two parameters: the summary and post id.

search-in-page-form allows us to edit the search form. It receives as parameter the search form's structure.

search-in-page-query allows us to edit the search query and receives as parameter the original query.

search-in-page-item allows us to edit the components of results items. It receives as parameter a standard object with the item attributes and the post id.

search-in-page-results allows us to modify the search results before sending them to the browser. It receives as parameters an array with the results components.

search-in-page-autocomplete allows us to edit the autocomplete texts. It receives as parameters an array with the texts' suggestions.

= 1.0.62 =

* Includes a new attribute in the settings of the plugin. This attribute allows us to enter the selectors where to search.  It affects only the search on the page.

= 1.0.61 =

* Modifies the post summary extraction to prioritize the text that includes the search terms, even if the post has the excerpt defined.

= 1.0.60 =

* Modifies the styles of the search in place results.

= 1.0.59 =

* Improves the plugin's behavior and appearance.
* Displays the close icon only if needed.

= 1.0.58 =

* Fixes the autohide in iOS (iPad and iPhone), furthermore, includes an icon to hide the search results.

= 1.0.57 =

* Modifies the settings page of the plugin.
* Accepts a new attribute in the search box shortcode to search for specific post types (Professional version)

= 1.0.56 =

* Fixes a notice.
* Accepts a new attribute in the form's shortcode for placeholder.
* Improves the plugin's registration process (Professional version)

= 1.0.55 =

* Assigns a class name to the ellipsis symbols to allow hiding them.

= 1.0.54 =

* Modifies the behavior of the "Apply to the search box inserted as shortcode only" feature extending it to server side too.

= 1.0.53 =

* Modifies the behavior of the search in page. Now, pressing enter into the search box move the user to the places where the terms were found.

= 1.0.52 =

* Fixes an issue encoding the ampersand symbols in some URLs.
* Includes the body selector in the list of terms for highlighting.

= 1.0.51 =

* Fixes an issue highlighting the terms in the Elementor pages.

= 1.0.50 =

* Fixes an issue in plugin's block for the Gutenberg editor.

= 1.0.49 =

* Modifies the settings page of the plugin increasing the relevance of the search in page feature.

= 1.0.48 =

* Includes a new section in the plugin's settings to edit the background colors of highlighted terms.

= 1.0.47 =

* Fixes an issue in the integration with the Gutenberg editor.

= 1.0.46 =

* Improves the behavior of the autocomplete boxes.

= 1.0.45 =

* Replaces the access to the new website of the plugin.

= 1.0.44 =

* Includes the search in current page feature. For searching in the content of current page only, insert the `[search-in-place-form]` shortcode with the new attribute:

	`[search-in-place-form in_current_page="1"]`

= 1.0.43 =

* Modifies the access to the demos.

= 1.0.42 =

* Allows to reset the plugin's settings, and delete its data from the database.
* Fixes the issue that was resetting the settings every time the plugin is installed.

= 1.0.41 =

* Improves the plugin's security.

= 1.0.40 =

* Improves the integration with WPML.

= 1.0.39 =

* Modifies some texts in the plugin's interface, and language files.
* Improves the integration between the commercial version of the plugin and translation plugins like WPML.

= 1.0.38 =

* Modifies the way that search results are processed to make them compatible with the WPGlobus plugin.

= 1.0.37 =

* Fixes some issues with the autocomplete feature.
* Allows to configure the plugin to apply the search in place behavior only to the search boxes inserted in the plugin's shortcode.

= 1.0.36 =

* Fixes a little issue with the Elementor integration.
* Forces to the browser to load the correct resources for the version of the plugin.

= 1.0.35 =

* Includes some validation rules to preserve the compatibility with outdated versions of WordPress.

= 1.0.34 =

* Improves the integration with the Gutenberg, and Elementor editors.
* Implements a new widget for the specific integration with Page Builder by SiteOrigin.

= 1.0.33 =

* Includes terms suggestions, based on the information typed by the users.

= 1.0.32 =

* Includes specific widgets to integrate the plugin with the Elementor editor.
* Modifies the blocks for the Gutenberg editor,  preparing the plugin for WordPress 5.1

= 1.0.31 =

* Modifies the language files and plugin header.

= 1.0.30 =

* Fixes an issue between the Promote Banner and the official distribution of WP5.0

= 1.0.29 =

* Fixes a conflict with BBpress.

= 1.0.28 =

* The options for controlling the apperance of results po-pups have been enabled in the settings page of the plugin for the free version.
* Now it is possible to use the right and middle mouse buttons for opening the results in new tabs.
* Auto-hides the search results pop-up if the number of characters in the input box is lesser than configured in the plugin's settings.

= 1.0.27 =

* Fixes a conflict with the latest update of the Gutenberg editor.

= 1.0.26 =

* Solves a conflict with the "Speed Booster Pack" plugin.

= 1.0.25 =

* Hides the promotion banner for the majority of roles and fixes a conflict between the promotion banner and the Gutenberg editor.
* Modifies the deactivation plugin process.

= 1.0.24 =

* Implements the integration with Gutenberg, the editor to be distributed with the next version of WordPress.

= 1.0.23 =

* Includes some minor changes in the default limit of search results, and number of characters in the summary.

= 1.0.22 =

* Modifies the way of scripts are executed to prevent the uncaught javascript errors in the website affect the search in place process.

= 1.0.21 =

* Modifies the events to transform the "Search" boxes in "Search in Place" boxes, to allow create them even at runtime allowing the integration with other plugins that generate these elements after the webpage be loaded.

= 1.0.20 =

* Allows the access to the plugin menu only to the website administrator.

= 1.0.19 =

* Includes the `[search-in-place-form]` shortcode to insert the search box in the pages' contents (furthermore to convert all search boxes into search in place components)

= 1.0.18 =

* Fixes an issue in the promote banner.

= 1.0.17 =

* Modifies the module for accessing the WordPress reviews section.

= 1.0.16 =

* Fixes an issue naming the language files.

= 1.0.15 =

* Improves the access to the plugin documentation.
* Modifies the language files and texts.

= 1.0.14 =

* Fixes a typo in the variable that defines plugin's entries in the WordPress menu.

= 1.0.13 =

* Fixes an issue generating json objects with texts in UTF-8 characters of 4 bytes.
* Improves the module that highlight the search terms in posts/pages' contents.

= 1.0.12 =

* Convert the Global variables in explicitly globals.
* Includes new menu options to make the help more accessible.

= 1.0.11 =

* The current update validates the length of characters used to get the posts/pages summaries in the search results.

= 1.0.10 =

* Fixes some warnings concerning to variables passed as reference.

= 1.0.9 =

* Displays the "More Results" link if there are more results available.

= 1.0.8 =

* Modifies some deprecated jQuery functions.

= 1.0.7 =

* Corrects an issue with the searches in WordPress websites protected with SSL.

= 1.0.6 =

* Modifies the settings page.

= 1.0.5 =

* Deletes the plugin settings in the uninstall process, and not in deactivation.

= 1.0.4 =

* Allows searching the pages/posts that include all words, or any of the words in the search box.

= 1.0.3 =

* This version resolves an issue with the "More Results" link in sub-pages and multi-site installations.
* The use of taxonomies as criteria for searching.
* The integration with very popular plugins.
* Customizing the search results box.
* Updated the administration interface.
* Shown in the search result list, the thumbnail version of featured images.
* Improves the items selection in the search result list.
* Includes the images captions between the elements for searching.
* Improves the compatibility with themes non standards.
* Removes the shortcodes from descriptions in the results list.
* The attachments have been removed from results.

= 1.0.2 =

* If the post has associated a Featured image, it will be shown on dynamic search dialog.

= 1.0.1 =

* First Release