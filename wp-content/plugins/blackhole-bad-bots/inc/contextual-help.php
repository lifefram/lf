<?php // Blackhole for Bad Bots - Contextual Help

if (!defined('ABSPATH')) exit;



function blackhole_get_help_sidebar() {
	
	return '<p><strong>'. esc_html__('More Information', 'blackhole-bad-bots') .'</strong></p>'.
		
		'<p>'. 
			esc_html__('Visit the', 'blackhole-bad-bots') .' <a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/plugins/blackhole-bad-bots/installation/">'. esc_html__('Blackhole Docs', 'blackhole-bad-bots') .'</a> '. esc_html__('at WordPress.org.', 'blackhole-bad-bots') .
		'</p>'.
		
		'<p><strong>'. esc_html__('Support Blackhole Pro!', 'blackhole-bad-bots') .'</strong></p>'.
		
		'<ul>'.
		'<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/blackhole-pro/">'. esc_html__('Get Blackhole Pro&nbsp;&raquo;', 'blackhole-bad-bots') .'</a></li>'.
		'<li><a target="_blank" rel="noopener noreferrer" href="https://monzillamedia.com/donate.html">'. esc_html__('Make a donation&nbsp;&raquo;', 'blackhole-bad-bots') .'</a></li>'.
		'<li><a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/support/plugin/blackhole-bad-bots/reviews/?rate=5#new-post">'. esc_html__('Give 5&#10025; Rating&nbsp;&raquo;', 'blackhole-bad-bots') .'</a></li>'.
		'</ul>'.
		
		'<div id="fb-root"></div>
		<script>(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; 
			js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3"; fjs.parentNode.insertBefore(js, fjs); }(document, "script", "facebook-jssdk"));</script>
		<div class="share-button fb-share-button" data-href="https://wordpress.org/plugins/blackhole-bad-bots/" data-layout="button"></div>'.
		
		'<div class="share-button twitter-share-button"><a href="https://twitter.com/perishable" class="twitter-follow-button" data-show-count="false" data-dnt="true"></a></div>'.
		
		'<div class="share-button twitter-share-button">
			<a href="https://twitter.com/share" class="twitter-share-button" data-url="https://wordpress.org/plugins/blackhole-bad-bots/" data-text="Blackhole for Bad Bots!" 
			data-via="perishable" data-count="none" data-hashtags="wordpress,security,plugin" data-dnt="true">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?"http":"https";
			if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document, "script", "twitter-wjs");</script>
		</div><br />';
		
}



function blackhole_get_help_pro_info() {
	
	return array(
		
		'id' => 'blackhole-go-pro',
		'title' => esc_attr__('Pro Version', 'blackhole-bad-bots'),
		'content' => 
			'<p><strong>'. esc_html__('Blackhole Pro', 'blackhole-bad-bots') .'</strong></p>'.
			
			'<p>'. 
				esc_html__('Like Blackhole for Bad Bots? Show support and upgrade to the Pro version. ', 'blackhole-bad-bots') . 
				esc_html__('You get all the features of the free version, plus advanced settings to give you full control over virtually everything. ', 'blackhole-bad-bots') . 
				esc_html__('Pro version features include:', 'blackhole-bad-bots') .
			'</p>'.
			
			'<ul>'.
				'<li>Disable Blackhole for logged in users</li>'.
				'<li>Customize your own email alerts with shortcodes</li>'.
				'<li>Choose a custom HTTP Status Code for blocked bots</li>'.
				'<li>Set the number of hits before a bot is banned</li>'.
				'<li>Exclude Blackhole trigger link on specific posts/pages</li>'.
				'<li>Customize the trigger link with any text-markup</li>'.
				'<li>Optionally redirect all blocked and/or whitelisted bots</li>'.
			'</ul>'.
			
			'<p><strong>'. esc_html__('Bad Bots Log', 'blackhole-bad-bots') .'</strong></p>'.
			
			'<p>'. 
				esc_html__('To make the Pro version extra awesome, Blackhole Pro includes a robust, full-featured Bad Bots Log, where you can view and manage all of your blocked bots. ', 'blackhole-bad-bots') . 
				esc_html__('The Bad Bots Log is extra deluxe with the following features:', 'blackhole-bad-bots') . 
			'</p>'. 
			
			'<ul>'.
				'<li>Geo/IP location lookups for every blocked bot</li>'.
				'<li>Field-refined search, custom sorting, and paged results</li>'.
				'<li>One-click forward/reverse and DNS lookups for each bot</li>'.
				'<li>Add bots manually, directly via the Bad Bots screen</li>'.
				'<li>Delete any bot or multiple bots with a click</li>'. 
			'</ul>'.
			
			'<p><strong><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/blackhole-pro/">'. esc_html__('Learn more and get Blackhole Pro at Plugin Planet&nbsp;&raquo;', 'blackhole-bad-bots') .'</a></strong></p>'
		
	);
	
}



function blackhole_settings_contextual_help() {
	
	$screen = get_current_screen();
	
	if ($screen->id != 'toplevel_page_blackhole_settings') return;
	
	$screen->set_help_sidebar(blackhole_get_help_sidebar());
	
	$screen->add_help_tab(
		
		array(
			
			'id' => 'blackhole-intro',
			'title' => esc_attr__('Introduction', 'blackhole-bad-bots'),
			'content' => 
				'<p><strong>'. esc_html__('Introduction', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. 
					esc_html__('Blackhole for Bad Bots is a WordPress security plugin that detects and traps bad bots in a', 'blackhole-bad-bots') . 
					' <a target="_blank" rel="noopener noreferrer" href="https://perishablepress.com/blackhole-bad-bots/" title="Protect Your Site with a Blackhole for Bad Bots">'. 
					esc_html__('virtual blackhole', 'blackhole-bad-bots') .'</a>, '.  
					esc_html__('where they are denied access to your entire site. ', 'blackhole-bad-bots') . 
					esc_html__('You can customize Blackhole via the', 'blackhole-bad-bots') .' <a href="'. esc_url(admin_url('admin.php?page=blackhole_settings')) .'">'. esc_html__('Blackhole Settings', 'blackhole-bad-bots') .'</a>, '. 
					esc_html__('and you can manage blocked bots via the', 'blackhole-bad-bots') .' <a href="'. esc_url(admin_url('admin.php?page=blackhole_badbots')) .'">'. esc_html__('Bad Bots Log', 'blackhole-bad-bots') .'</a>.'.
				'</p>'.
				
				'<p><strong>'. esc_html__('Useful Resources', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<ul>'.
					'<li><a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/plugins/blackhole-bad-bots/">'. esc_html__('View the plugin documentation', 'blackhole-bad-bots') .'</a></li>'.
					'<li><a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/support/plugin/blackhole-bad-bots">'. esc_html__('Report an issue in the Support Forum', 'blackhole-bad-bots') .'</a></li>'. 
					'<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/blackhole-pro/#contact">'. esc_html__('Contact the plugin developer for help', 'blackhole-bad-bots') .'</a></li>'.
					'<li><a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/blackhole-pro/">'. esc_html__('Check out Blackhole Pro', 'blackhole-bad-bots') .'</a></li>'.
				'</ul>'.
				
				'<p><strong>'. esc_html__('About the Developer', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. 
					esc_html__('Blackhole Pro is developed by', 'blackhole-bad-bots') .' <a target="_blank" rel="noopener noreferrer" href="https://twitter.com/perishable">'. esc_html__('Jeff Starr', 'blackhole-bad-bots') .'</a>, '.
					esc_html__('10-year WordPress veteran and', 'blackhole-bad-bots') .' <a target="_blank" rel="noopener noreferrer" href="https://books.perishablepress.com/">'. esc_html__('book author', 'blackhole-bad-bots') .'</a>.'.
				'</p>'
		)
	);
	
	$screen->add_help_tab(
		
		array(
			
			'id' => 'blackhole-overview',
			'title' => esc_attr__('Overview', 'blackhole-bad-bots'),
			'content' => 
				'<p><strong>'. esc_html__('Bye bye bad bots..', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. 
					esc_html__('Bad bots are the worst. They do all sorts of nasty stuff and waste server resources. ', 'blackhole-bad-bots') . 
					esc_html__('Blackhole for Bad Bots helps to stop bad bots, spammers, scrapers, and other automated threats. ', 'blackhole-bad-bots') . 
					esc_html__('This increases security and saves precious server resources for your legit visitors.', 'blackhole-bad-bots') . 
				'</p>'.
				
				'<p><strong>'. esc_html__('How does it work?', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. 
					esc_html__('First the plugin adds a hidden trigger link to the footer of your pages. ', 'blackhole-bad-bots') . 
					esc_html__('You then add a line to your robots.txt file that forbids all bots from following the hidden link. ', 'blackhole-bad-bots') . 
					esc_html__('Bots that then ignore or disobey your robots rules will crawl the link and fall into the trap. ', 'blackhole-bad-bots') . 
					esc_html__('Once trapped, bad bots are denied further access to your entire site. ', 'blackhole-bad-bots') . 
				'</p>'.
				'<p>'. 
					esc_html__('I call it the &ldquo;one-strike&rdquo; rule: bots have one chance to obey your site&rsquo;s robots.txt rules. ', 'blackhole-bad-bots') . 
					esc_html__('Failure to comply results in immediate banishment.', 'blackhole-bad-bots') . 
				'</p>'. 
				
				'<p><strong>'. esc_html__('What about human visitors?', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. 
					esc_html__('Wait.. what about human visitors? What if they visit the blackhole link? Will they get banned from the site? ', 'blackhole-bad-bots') . 
					esc_html__('Nope. The blackhole link is completely hidden from normal visitors. The only way to discover the link is to snoop around in the page markup. ', 'blackhole-bad-bots') . 
					esc_html__('And even if someone does happen to find the link, it clearly warns against following it. So there is virtually zero chance of false positives. ', 'blackhole-bad-bots') . 
					esc_html__('Worst-case scenario if someone ignores the warning and follows the link? Visit the Bad Bots screen and remove them from the list.', 'blackhole-bad-bots') . 
				'</p>'.
				
				'<p><strong>'. esc_html__('Important note about cache plugins', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. 
					esc_html__('Blackhole works with any type of cache plugin where "page caching" is not enabled. ', 'blackhole-bad-bots') . 
				'</p>'.
				
				'<p>'.
					esc_html__('There are many types of cache plugins. They provide all sorts of different caching mechanisms and features. All caching features work great with Blackhole except for "page caching". ', 'blackhole-bad-bots') . 
					esc_html__('With page caching, the required WP init hook may not be fired, which means that plugins like Blackhole are not able to log and ban requests dynamically. ', 'blackhole-bad-bots') . 
					esc_html__('Fortunately, some of the most popular cache plugins provide settings that enable full compatibility with Blackhole. For a complete list, check out', 'blackhole-bad-bots') . 
					' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/blackhole-pro-cache-plugins/">this post</a> '.
					esc_html__('at Plugin Planet.', 'blackhole-bad-bots') . 
				'</p>'. 
				
				'<p><strong>'. esc_html__('How to use Blackhole Pro', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. esc_html__('Here is an overview of how to use the plugin:', 'blackhole-bad-bots') .'</p>'.
				
				'<ol>'.
					'<li>'. esc_html__('Visit the &ldquo;Robots Rules&rdquo; setting and add the provided rules to your robots.txt file', 'blackhole-bad-bots') .'</li>'.
					'<li>'. esc_html__('Customize any settings as desired', 'blackhole-bad-bots') .'</li>'.
					'<li>'. esc_html__('Visit the Bad Bots screen to view and manage all blocked bots', 'blackhole-bad-bots') .'</li>'.
				'</ol>'.
				
				'<p><strong>'. esc_html__('A quick tour..', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. esc_html__('Here are the various plugin screens available under the Blackhole Pro menu:', 'blackhole-bad-bots') .'</p>'.
				
				'<ul>'.
					'<li>'. esc_html__('Settings &mdash; Provides all plugin settings', 'blackhole-bad-bots') .'</li>'.
					'<li>'. esc_html__('Bad Bots &mdash; Complete log of all blocked bots', 'blackhole-bad-bots') .'</li>'.
				'</ul>'.
				
				'<p>'. 
					esc_html__('For further information, check out the ', 'blackhole-bad-bots') . 
					' <a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/plugins/blackhole-bad-bots/installation/">'. esc_html__('plugin documentation', 'blackhole-bad-bots') .'</a>.'. 
				'</p>'.
				
				'<p><strong>'. esc_html__('Verify that Blackhole is working', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. esc_html__('Here is a tutorial that explains how to', 'blackhole-bad-bots') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/blackhole-pro-check-plugin-working/">'. esc_html__('verify that Blackhole is working properly', 'blackhole-bad-bots') .'</a>.</p>'
			
		)
	);
	
	$screen->add_help_tab(
		
		array(
			
			'id' => 'blackhole-robots',
			'title' => esc_attr__('Robots Rules', 'blackhole-bad-bots'),
			'content' => 
				'<p><strong>'. esc_html__('Robots Rules', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. 
					esc_html__('If your site is using the dynamic WP-generated virtual robots.txt file, then the required robots rules are added for you automatically. ', 'blackhole-bad-bots') .
					esc_html__('You do not need to add anything or make any changes to your robots.txt file in this case.', 'blackhole-bad-bots') .
				'</p>'.
				
				'<p>'. 
					esc_html__('*OR* if your site is using a physical robots.txt on the server, then you need to add the robots rules manually. ', 'blackhole-bad-bots') .
					esc_html__('You can learn more about all of this in the', 'blackhole-bad-bots') .' <a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/plugins/blackhole-bad-bots/#installation">'. esc_html__('plugin documentation', 'blackhole-bad-bots') .'</a>.'.
				'</p>'.
				
				'<p><strong>'. esc_html__('For physical robots.txt files..', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. 
					esc_html__('In order for Blackhole to work properly, add the provided rules to your site&rsquo;s robots.txt file. ', 'blackhole-bad-bots') . 
					esc_html__('Simply copy and paste the rules at the end of your robots.txt file, and then upload to your server. ', 'blackhole-bad-bots') .
					esc_html__('For more complex/advanced robots.txt configurations, consult your web developer.', 'blackhole-bad-bots') .  
				'</p>'.
				
				'<p><strong>'. esc_html__('Important:', 'blackhole-bad-bots') .'</strong> '. esc_html__('proper robots.txt syntax is critical for good SEO. So make sure to validate your robots.txt rules after making any changes.', 'blackhole-bad-bots') .'</p>'. 
				
				'<p><strong>'. esc_html__('More Information', 'blackhole-bad-bots') .'</strong></p>'. 
				
				'<p>'. 
					esc_html__('Why are the robots.txt rules necessary? Because you only want to trap &ldquo;bad&rdquo; bots, not good bots. ', 'blackhole-bad-bots') . 
					esc_html__('The robots.txt rules explicitly instruct all bots to NOT crawl the blackhole link. ', 'blackhole-bad-bots') . 
					esc_html__('So any bots that follow the link are disobeying robots.txt and will be banned from further site access.', 'blackhole-bad-bots') .
				'</p>'.
				
				'<p><strong>'. esc_html__('Useful Resources', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<ul>'. 
					'<li><a target="_blank" rel="noopener noreferrer" href="https://www.robotstxt.org/">'. esc_html__('Learn more about robots.txt', 'blackhole-bad-bots') .'</a></li>'. 
					'<li><a target="_blank" rel="noopener noreferrer" href="http://searchenginepromotionhelp.com/m/robots-text-tester/robots-checker.php">'. esc_html__('Check your robots.txt rules', 'blackhole-bad-bots') .'</a></li>'. 
					'<li><a target="_blank" rel="noopener noreferrer" href="https://developers.google.com/search/reference/robots_txt">'. esc_html__('Google&rsquo;s robots.txt specifications', 'blackhole-bad-bots') .'</a></li>'. 
					'<li><a target="_blank" rel="noopener noreferrer" href="https://support.google.com/webmasters/answer/6062596?hl=en">'. esc_html__('How to create a robots.txt file', 'blackhole-bad-bots') .'</a></li>'. 
				'</ul>'.
				
				'<p><strong>'. esc_html__('Tip:', 'blackhole-bad-bots') .'</strong> '. esc_html__('Google also provides a robust robots.txt checker inside of your Google account (Google accounts are free). Worth checking out.', 'blackhole-bad-bots') .'</p>'
			
		)
	);
	
	$screen->add_help_tab(
		
		array(
			
			'id' => 'blackhole-email-alerts',
			'title' => esc_attr__('Email Alerts', 'blackhole-bad-bots'),
			'content' => 
				'<p><strong>'. esc_html__('Email Alerts', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. esc_html__('Enable this setting if you want to receive an email alert each time a bot visits the blackhole link (aka, &ldquo;trigger&rdquo;). ', 'blackhole-bad-bots') . '</p>'.
				
				'<p><em>'. esc_html__('Default: Enabled', 'blackhole-bad-bots') .'</em></p>'. 
				
				'<p><strong>'. esc_html__('Email Address', 'blackhole-bad-bots') .'</strong></p>'. 
				
				'<p>'. esc_html__('The email address to which email alerts should be sent. ', 'blackhole-bad-bots') . '</p>'.
				
				'<p><em>'. esc_html__('Default: Admin email', 'blackhole-bad-bots') .'</em></p>'. 
				
				'<p><strong>'. esc_html__('Email From', 'blackhole-bad-bots') .'</strong></p>'. 
				
				'<p>'. 
					esc_html__('The address to be used in the &ldquo;From&rdquo; header for email alerts. ', 'blackhole-bad-bots') . 
					esc_html__('If your email address is a domain-based address, then this setting should be the same as the previous Email setting. ', 'blackhole-bad-bots') . 
					esc_html__('Otherwise, if you are using a 3rd-party email service, this setting should be a local, domain-based address. ', 'blackhole-bad-bots') . 
					esc_html__('If you find that the email alerts are getting sent to the spam bin, this setting may help.', 'blackhole-bad-bots') . 
				'</p>'.
				
				'<p><em>'. esc_html__('Default: Admin email', 'blackhole-bad-bots') .'</em></p>'
			
		)
	);
	
	$screen->add_help_tab(
		
		array(
			
			'id' => 'blackhole-frontend-display',
			'title' => esc_attr__('Frontend Display', 'blackhole-bad-bots'),
			'content' => 
				
				'<p><strong>'. esc_html__('Message Display', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. 
					esc_html__('This setting determines the type of message displayed to all blocked bots. Here are the options:', 'blackhole-bad-bots') . 
				'</p>'.
				
				'<ul>'.
					'<li>'. esc_html__('Default message', 'blackhole-bad-bots') .' &ndash; '. esc_html__('Displays some basic text and markup', 'blackhole-bad-bots') .'</li>'.
					'<li>'. esc_html__('Custom message', 'blackhole-bad-bots') .' &ndash; '. esc_html__('Define your own message in the next setting', 'blackhole-bad-bots') .'</li>'.
					'<li>'. esc_html__('Into the Void', 'blackhole-bad-bots') .' &ndash; '. esc_html__('Displays an empty page with a black background', 'blackhole-bad-bots') .'</li>'.
				'</ul>'.
				
				'<p><em>'. esc_html__('Default: Default message', 'blackhole-bad-bots') .'</em></p>'. 
				
				'<p><strong>'. esc_html__('Message Custom', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. esc_html__('Defines a custom message to display to blocked bots. You can use any text/markup. Note: for this to work, &ldquo;Custom Message&rdquo; must be selected in the previous setting.', 'blackhole-bad-bots') .'</p>'.
				
				'<p><em>'. esc_html__('Default:', 'blackhole-bad-bots') .'<code>&lt;h1>You have been banned from this site.&lt;/h1></code></em></p>'. 
				
				'<p><strong>'. esc_html__('Warning Message', 'blackhole-bad-bots') .'</strong></p>'. 
				
				'<p>'. 
					esc_html__('About the warning message displayed to bad bots when they visit the Blackhole trigger link. ', 'blackhole-bad-bots') . 
					esc_html__('The default warning message displays some basic info and a whois lookup on a red background. ', 'blackhole-bad-bots') . 
					esc_html__('To customize this, follow these steps:', 'blackhole-bad-bots') . 
				'</p>'.
				
				'<ol>'. 
					'<li>'. esc_html__('Copy', 'blackhole-bad-bots') .' <code>blackhole-template.php</code> '. esc_html__('from the plugin&rsquo;s', 'blackhole-bad-bots') .' <code>/inc/</code> '. esc_html__('directory', 'blackhole-bad-bots') .'</li>'. 
					'<li>'. esc_html__('Paste into your theme, for example:', 'blackhole-bad-bots') .' <code>/wp-content/my-theme/blackhole-template.php</code></li>'. 
					'<li>'. esc_html__('Customize any of the markup between &ldquo;BEGIN TEMPLATE&rdquo; and &ldquo;END TEMPLATE&rdquo;', 'blackhole-bad-bots') .'</li>'. 
					'<li>'. esc_html__('Upload to the server and done', 'blackhole-bad-bots') .'</li>'. 
				'</ol>'. 
				
				'<p>
					<strong>'. esc_html__('Tip:', 'blackhole-bad-bots') .'</strong> '. esc_html__('Instead of including the custom template in your theme, you can include via wp-content directory, like:', 'blackhole-bad-bots') .
					' <code>/wp-content/blackhole/blackhole-template.php</code>'.
				'</p>'. 
				
				'<p>'. esc_html__('Note that the template provides the following variables:', 'blackhole-bad-bots') .' <code>$ip</code>, <code>$host</code>, <code>$date</code>, <code>$whois</code>, <code>$vars</code></p>'
			
		)
	);
	
	$screen->add_help_tab(
		
		array(
			
			'id' => 'blackhole-whitelist',
			'title' => esc_attr__('Whitelist Settings', 'blackhole-bad-bots'),
			'content' => 
				'<p><strong>'. esc_html__('Whitelisted Bots', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. 
					esc_html__('Blackhole blocks bad bots via their reported IP address. This setting enables you to whitelist bots based on their reported user agent. ', 'blackhole-bad-bots') . 
					esc_html__('That way you will never block important things like Google, et al. When adding user agents to the list, keep the names short, simple, and as unique as possible. Also do not include any special characters. Separate multiple strings with commas.', 'blackhole-bad-bots') . 
				'</p>'.
				
				'<p><strong>'. esc_html__('Important:', 'blackhole-bad-bots') .'</strong> '. esc_html__('Commas are used to separate the user-agent strings. Do NOT include them anywhere else.', 'blackhole-bad-bots') .'</p>'.
				
				'<p>'. esc_html__('Learn more about', 'blackhole-bad-bots') .' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/blackhole-pro-whitelist-bots/">'. esc_html__('whitelisting bots', 'blackhole-bad-bots') .'</a>.</p>'.
				
				'<p><em>'. esc_html__('Default:', 'blackhole-bad-bots') .' a6-indexer, adsbot-google, ahrefsbot, aolbuild, apis-google, baidu, bingbot, bingpreview, butterfly, cloudflare, chrome, duckduckgo, embedly, facebookexternalhit, facebot, google page speed, googlebot, ia_archiver, linkedinbot, mediapartners-google, msnbot, netcraftsurvey, outbrain, pinterest, quora, rogerbot, showyoubot, slackbot, slurp, sogou, teoma, tweetmemebot, twitterbot, uptimerobot, urlresolver, vkshare, w3c_validator, wordpress, wp rocket, yandex</em></p>'. 
				
				'<p><strong>'. esc_html__('Whitelisted IPs', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. 
					esc_html__('Here you can whitelist bots based on their IP address. ', 'blackhole-bad-bots') . 
					esc_html__('Any IPs entered here will be matched against the reported IP address via regular expression. ', 'blackhole-bad-bots') . 
					esc_html__('So you can do any of the following:', 'blackhole-bad-bots') . 
				'</p>'.
				
				'<ul>'. 
					'<li>'. esc_html__('Block an individual IP address, like', 'blackhole-bad-bots') .' <code>173.203.204.22</code></li>'. 
					'<li>'. esc_html__('Block a range of sequential IP addresses, like', 'blackhole-bad-bots') .' <code>173.203.</code></li>'. 
					'<li>'. esc_html__('Block a range of IP addresses in CIDR notation, like', 'blackhole-bad-bots') .' <code>173.203.204.22/24</code></li>'. 
				'</ul>'. 
				
				'<p>'. 
					esc_html__('Separate multiple IP/strings with commas. ', 'blackhole-bad-bots') . 
					esc_html__('Note that the plugin automatically adds your server IP address, if it is available. ', 'blackhole-bad-bots') . 
					esc_html__('If you are using anything like caching, load-balancing, or reverse proxy, make sure to add their respective IPs to the whitelist.', 'blackhole-bad-bots') . 
				'</p>'.
				
				'<p><strong>'. esc_html__('Important:', 'blackhole-bad-bots') .'</strong> '. esc_html__('Commas are used to separate the IP addresses. Do NOT include them anywhere else.', 'blackhole-bad-bots') .'</p>'.
				
				'<p><em>'. esc_html__('Default: Your server IP address, your local IP address', 'blackhole-bad-bots') .'</em></p>'.
				
				'<p><strong>'. esc_html__('Notes', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. 
					esc_html__('Just FYI, any items specified in the whitelist settings will never be blocked. I.e., the whitelist settings always take precedence and override all other options.', 'blackhole-bad-bots') . 
				'</p>'
			
		)
	);
	
	$screen->add_help_tab(
		
		array(
			
			'id' => 'blackhole-reset-options',
			'title' => esc_attr__('Reset Options', 'blackhole-bad-bots'),
			'content' => 
				'<p><strong>'. esc_html__('Reset Options', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. esc_html__('This setting enables you to restore the default plugin settings. Does not affect the Bad Bots Log.', 'blackhole-bad-bots') .'</p>'.
				
				'<p><em>'. esc_html__('Default: none (it&rsquo;s just a link)', 'blackhole-bad-bots') .'</em></p>'
			
		)
	);
	
	$screen->add_help_tab(
		
		blackhole_get_help_pro_info()
		
	);
	
	do_action('blackhole_settings_contextual_help', $screen);
	
}
add_action('load-toplevel_page_blackhole_settings', 'blackhole_settings_contextual_help');



function blackhole_badbots_contextual_help() {
	
	$screen = get_current_screen();
	
	if ($screen->id != 'blackhole_page_blackhole_badbots') return;
	
	$screen->set_help_sidebar(blackhole_get_help_sidebar());
	
	$screen->add_help_tab(
		
		array(
			
			'id' => 'blackhole-badbots',
			'title' => esc_attr__('Bad Bots Log', 'blackhole-bad-bots'),
			'content' => 
				'<p><strong>'. esc_html__('Welcome to Blackhole..', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. esc_html__('On this screen you will find a complete log of all blocked bots. Everything should be self-explanatory, but there are a few tricks worth knowing:', 'blackhole-bad-bots') .'</p>'. 
				
				'<ul>'. 
					'<li>'. esc_html__('To delete any bot(s), click its [x] button', 'blackhole-bad-bots') .'</li>'. 
					'<li>'. esc_html__('The plugin adds a default bot as an example (cannot be deleted)', 'blackhole-bad-bots') .'</li>'. 
					'<li>'. esc_html__('To clear the log and restore the default bot, click the link, &ldquo;Reset Bad Bots&rdquo;', 'blackhole-bad-bots') .'</li>'. 
				'</ul>'. 
				
				'<p>'. 
					esc_html__('Note that the Pro version provides much more information for each bot, along with robust bot-management features like refined search and Geo/IP lookups. ', 'blackhole-bad-bots') . 
					'<a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/blackhole-pro/">'. esc_html__('Check out Blackhole Pro&nbsp;&raquo;', 'blackhole-bad-bots') .'</a>'. 
				'</p>'
			
		)
	);
	
	$screen->add_help_tab(
		
		array(
			
			'id' => 'blackhole-reset-badbots',
			'title' => esc_attr__('Reset Bad Bots', 'blackhole-bad-bots'),
			'content' => 
				'<p><strong>'. esc_html__('Reset Bad Bots', 'blackhole-bad-bots') .'</strong></p>'.
				
				'<p>'. esc_html__('This setting enables you to clear the Bad Bots Log and restore the default bot. Does not affect plugin settings.', 'blackhole-bad-bots') .'</p>'.
				
				'<p><em>'. esc_html__('Default: none (it&rsquo;s just a link)', 'blackhole-bad-bots') .'</em></p>'
			
		)
	);
	
	$screen->add_help_tab(
		
		blackhole_get_help_pro_info()
		
	);
	
	do_action('blackhole_badbots_contextual_help', $screen);
	
}
add_action('load-blackhole_page_blackhole_badbots', 'blackhole_badbots_contextual_help');


