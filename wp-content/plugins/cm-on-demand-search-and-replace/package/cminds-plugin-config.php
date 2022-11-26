<?php
$cminds_plugin_config = array(
	'plugin-is-pro'						=> false,
	'plugin-version'					=> '1.2.8',
	'plugin-abbrev'						=> 'cmodsar',
	'plugin-short-slug'					=> 'search-and-replace',
	'plugin-parent-short-slug'			=> '',
	'plugin-file'						=> CMODSAR_PLUGIN_FILE,
    'plugin-campign'					=> '?utm_source=searchreplacefree&utm_campaign=freeupgrade',
    'plugin-affiliate'					=> '',
    'plugin-redirect-after-install'		=> admin_url( 'admin.php?page=cmodsar_settings' ),
    'plugin-show-guide'					=> TRUE,
	'plugin-upgrade-text'				=> 'Good Reasons to Upgrade to Pro',
    'plugin-upgrade-text-list'			=> array(
        array( 'title' => 'Introduction to the search and replace plugin', 'video_time' => '0:00' ),
        array( 'title' => 'Use in titles, comments and excerpts', 'video_time' => '0:58' ),
        array( 'title' => 'Time restricted search and replace', 'video_time' => '1:18' ),
        array( 'title' => 'Exclude specific pages', 'video_time' => '1:30' ),
        array( 'title' => 'Regex support', 'video_time' => 'More' ),
        array( 'title' => 'Import and export rules', 'video_time' => 'More' ),
        array( 'title' => 'ACF support', 'video_time' => 'More' ),
        array( 'title' => 'More support to Yoast and WooCommerce content', 'video_time' => 'More' ),
    ),
    'plugin-upgrade-video-height'		=> 240,
    'plugin-upgrade-videos'				=> array(
        array( 'title' => 'Search and Replace Premium Features', 'video_id' => '124893784' ),
    ),
    'plugin-guide-text'					=> '<div style="display:block">
        <ol>
         <li>This plugin allows you to setup the search & replace rules for the content of your site.</li>
        <li>You can set a <strong>textual string or HTML</strong> which should be found and the string/HTML that should be placed instead.</li>
        <li> You may also decide only to remove without replacing it (just leave the "To String" empty).</li>
        <li>This plugin and replacment tules <strong>does not change the content on the database</strong>. Instead it changes the content right before it is displayed.</li>
        <li><strong>Example:</strong>Create a rule, in the From String field type: "test" in the To String field: "passed"</li>
        <li>Create a new page, add some title (any), and write the "test" in the content</li>
        <li>Save the page and view it</li>
        <li>You should see the string "passed" in the content</li>
        <li>If there is still "test" displayed - it may mean that your theme is not using "the_content" filter.</li>
        </ol>
    </div>',
    'plugin-guide-video-height'          => 240,
    'plugin-guide-videos'            => array(
        array( 'title' => 'Installation tutorial', 'video_id' => '157541752' ),
    ),
	'plugin-dir-path'			 => plugin_dir_path( CMODSAR_PLUGIN_FILE ),
	'plugin-dir-url'			 => plugin_dir_url( CMODSAR_PLUGIN_FILE ),
	'plugin-basename'			 => plugin_basename( CMODSAR_PLUGIN_FILE ),
	'plugin-icon'				 => '',
	'plugin-name'				 => CMODSAR_NAME,
	'plugin-license-name'		 => CMODSAR_CANONICAL_NAME,
	'plugin-slug'				 => '',
	'plugin-menu-item'			 => CMODSAR_SETTINGS_OPTION,
	'plugin-textdomain'			 => CMODSAR_SLUG_NAME,
	'plugin-userguide-key'		 => '2244-cm-search-and-replace-cmsr-free-version-guide',
	'plugin-store-url'			 => 'https://www.cminds.com/wordpress-plugins-library/purchase-cm-on-demand-search-and-replace-plugin-for-wordpress?utm_source=searchreplacefree&utm_campaign=freeupgrade&upgrade=1',
	'plugin-support-url'		 => 'https://www.cminds.com/contact/',
	'plugin-review-url'			 => 'https://wordpress.org/support/view/plugin-reviews/cm-on-demand-search-and-replace',
	'plugin-changelog-url'		 => CMODSAR_RELEASE_NOTES,
	'plugin-licensing-aliases'	 => array( CMODSAR_LICENSE_NAME ),
	'plugin-compare-table'	 => '<div class="pricing-table" id="pricing-table"><h2 style="padding-left:10px;">Upgrade The On Demand Search and Replace Plugin:</h2>
                <ul>
                    <li class="heading" style="background-color:red;">Current Edition</li>
                    <li class="price">FREE<br /></li>
                     <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Define find and replace rules</li>
                    <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Supports posts and pages</li>
                     <hr>
                    Other CreativeMinds Offerings
                    <hr>
                 <a href="https://www.cminds.com/wordpress-plugins-library/seo-keyword-hound-wordpress?utm_source=searchreplacefree&utm_campaign=freeupgrade&upgrade=1" target="blank"><img src="' . plugin_dir_url( __FILE__ ). 'views/Hound2.png"  width="220"></a><br><br><br>
                <a href="https://www.cminds.com/store/cm-wordpress-plugins-yearly-membership?utm_source=searchreplacefree&utm_campaign=freeupgrade&upgrade=1" target="blank"><img src="' . plugin_dir_url( __FILE__ ). 'views/banner_yearly-membership_220px.png"  width="220"></a><br>
              </ul>
                <ul>
                   <li class="heading">Pro<a href="https://www.cminds.com/wordpress-plugins-library/purchase-cm-on-demand-search-and-replace-plugin-for-wordpress?utm_source=searchreplacefree&utm_campaign=freeupgrade&upgrade=1" style="float:right;font-size:11px;color:white;" target="_blank">More</a></li>
                    <li class="price">$29.00<br /> <span style="font-size:14px;">(For one Year / 1 Site)<br />Additional pricing options available <a href="https://www.cminds.com/wordpress-plugins-library/purchase-cm-on-demand-search-and-replace-plugin-for-wordpress?utm_source=searchreplacefree&utm_campaign=freeupgrade&upgrade=1" target="_blank"> >>> </a></span> <br /></li>
                    <li class="action"><a href="https://www.cminds.com/?edd_action=add_to_cart&download_id=33640&edd_options[price_id]=1&utm_source=searchreplacefree&utm_campaign=freeupgrade&upgrade=1" style="font-size:18px;" target="_blank">Upgrade Now</a></li>
                     <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>All Free Version Features <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="All free features are supported in the pro"></span></li>  
                <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>All WordPress Content Types<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Define search and replace rules for comments, posts, pages, titles, content and excerpts "></span></li>
                <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Limit to Specific Post Types<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support search and replace only on specific content types or custom posts"></span></li>
                <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Remove Content<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support removing content without replacing it"></span></li>
                <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Case Sensitive<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Support case sensitive replacements"></span></li>
                <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Rules Management<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Pause certain rules or delete them"></span></li>
                <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Frontend Widget<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Front-end control widget to turn search and replace off and on"></span></li>
                <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Import and Export Rules<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Import and export rules"></span></li>
                <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Search Rules<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Search within rules set - in case you have many rules between sites"></span></li>
                <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Drag and Drop<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Easily change order of rules using drag and drop interface"></span></li>
                <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Time restricted search and replace<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Supports applying the search and replace rules on specific dates."></span></li>
                <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Target specific post<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Target rules to specific post or page."></span></li>
                <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>Regex search and replace<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Supports regex search and replace statements."></span></li>
                <li style="text-align:left;"><span class="dashicons dashicons-yes"></span>External Plugins Support<span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:green" title="Special support for ACF, Yoast, Woocommerce and bbPress plugins fields"></span></li>
                     <li class="support" style="background-color:lightgreen; text-align:left; font-size:14px;"><span class="dashicons dashicons-yes"></span> One year of expert support <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="You receive 365 days of WordPress expert support. We will answer questions you have and also support any issue related to the plugin. We will also provide on-site support."></span><br />
                         <span class="dashicons dashicons-yes"></span> Unlimited product updates <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="During the license period, you can update the plugin as many times as needed and receive any version release and security update"></span><br />
                        <span class="dashicons dashicons-yes"></span> Plugin can be used forever <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="Once license expires, If you choose not to renew the plugin license, you can still continue to use it as long as you want."></span><br />
                        <span class="dashicons dashicons-yes"></span> Save 40% once renewing license <span class="dashicons dashicons-admin-comments cminds-package-show-tooltip" style="color:grey" title="Once license expires, If you choose to renew the plugin license you can do this anytime you choose. The renewal cost will be 35% off the product cost."></span></li>
             </ul>
            </div>',
);