<?php 
/*
	Plugin Name: Blackhole for Bad Bots
	Plugin URI: https://perishablepress.com/blackhole-bad-bots/
	Description: Protects your site against bad bots by trapping them in a blackhole.
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
*/

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 
	2 of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	with this program. If not, visit: https://www.gnu.org/licenses/
	
	Copyright 2021 Monzilla Media. All rights reserved.
*/

if (!defined('ABSPATH')) die();

if (!class_exists('Blackhole_Bad_Bots')) {
	
	final class Blackhole_Bad_Bots {
		
		private static $instance;
		
		public static function instance() {
			if (!isset(self::$instance) && !(self::$instance instanceof Blackhole_Bad_Bots)) {
				
				self::$instance = new Blackhole_Bad_Bots;
				self::$instance->constants();
				self::$instance->includes();
				
				add_action('admin_init',          array(self::$instance, 'check_blackhole'));
				add_action('admin_init',          array(self::$instance, 'check_version'));
				add_action('plugins_loaded',      array(self::$instance, 'load_i18n'));
				add_filter('plugin_action_links', array(self::$instance, 'action_links'), 10, 2);
				add_filter('plugin_row_meta',     array(self::$instance, 'plugin_links'), 10, 2);
				add_filter('safe_style_css',      array(self::$instance, 'blackhole_allow_style'));
				
				add_action('admin_enqueue_scripts', 'blackhole_enqueue_resources_admin');
				add_action('admin_print_scripts',   'blackhole_print_js_vars_admin');
				add_action('admin_notices',         'blackhole_tools_admin_notice');
				add_action('admin_init',            'blackhole_register_settings');
				add_action('admin_init',            'blackhole_register_badbots');
				add_action('admin_init',            'blackhole_reset_options');
				add_action('admin_init',            'blackhole_reset_badbots');
				add_action('admin_init',            'blackhole_delete_bot');
				add_action('admin_menu',            'blackhole_menu_pages');
				
				add_action('login_footer', 'blackhole_trigger');
				add_action('wp_footer',    'blackhole_trigger');
				add_action('init',         'blackhole_scanner');
				
				add_filter('robots_txt', 'blackhole_robots_wordpress', 11, 2);
				
			}
			return self::$instance;
		}
		
		public static function options() {
			
			$blackhole_options = array(
				'email_alerts'    => true,
				'email_address'   => get_option('admin_email'),
				'email_from'      => get_option('admin_email'),
				'message_display' => 'default',
				'message_custom'  => '<h1>'. esc_html__('You have been banned from this site.', 'blackhole-bad-bots') .'</h1>',
				'bot_whitelist'   => self::default_uas(),
				'ip_whitelist'    => self::default_ips(),
			);
			
			return apply_filters('blackhole_options', $blackhole_options);
			
		}
		
		public static function badbots() {
			$blackhole_badbots = array(
				array(
					'ip_address'   => '173.203.204.123',
					'request_uri'  => 'https://example.com/',
					'query_string' => 'example=true',
					'user_agent'   => 'Cygnus X-1 (Space Invaders) User Agent (Atari 2600)',
					'referrer'     => 'https://domain.tld/',
					'protocol'     => 'HTTP/1.1',
					'method'       => 'GET',
					'date'         => '2024/07/04 @ 12:00:00 am',
				)
			);
			return apply_filters('blackhole_badbots', $blackhole_badbots);
		}
		
		private function constants() {
			if (!defined('BBB_REQUIRE')) define('BBB_REQUIRE', '4.1');
			if (!defined('BBB_VERSION')) define('BBB_VERSION', '3.2');
			if (!defined('BBB_NAME'))    define('BBB_NAME',    'Blackhole for Bad Bots');
			if (!defined('BBB_AUTHOR'))  define('BBB_AUTHOR',  'Jeff Starr');
			if (!defined('BBB_HOME'))    define('BBB_HOME',    'https://perishablepress.com/blackhole-bad-bots/');
			if (!defined('BBB_URL'))     define('BBB_URL',     plugin_dir_url(__FILE__));
			if (!defined('BBB_DIR'))     define('BBB_DIR',     plugin_dir_path(__FILE__));
			if (!defined('BBB_FILE'))    define('BBB_FILE',    plugin_basename(__FILE__));
			if (!defined('BBB_SLUG'))    define('BBB_SLUG',    basename(dirname(__FILE__)));
		}
		
		private function includes() {
			
			require_once BBB_DIR .'inc/blackhole-core.php';
			require_once BBB_DIR .'inc/blackhole-helpers.php';
			require_once BBB_DIR .'inc/blackhole-ip.php';
			require_once BBB_DIR .'inc/blackhole-response.php';
			
			if (is_admin()) {
				
				require_once BBB_DIR .'inc/contextual-help.php';
				require_once BBB_DIR .'inc/resources-enqueue.php';
				require_once BBB_DIR .'inc/settings-register.php';
				require_once BBB_DIR .'inc/settings-display.php';
				require_once BBB_DIR .'inc/settings-reset.php';
				require_once BBB_DIR .'inc/badbots-register.php';
				
			}
			
		}
		
		public static function default_ips() {
			
			$ip_server = isset($_SERVER['SERVER_ADDR']) ? sanitize_text_field($_SERVER['SERVER_ADDR']) : '';
			$ip_remote = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '';
			
			$ips = $ip_server .', '. $ip_remote;
			
			return apply_filters('blackhole_default_ips', $ips);
			
		}
		
		public static function default_uas() {
			
			$uas  = 'a6-indexer, adsbot-google, ahrefsbot, aolbuild, apis-google, baidu, bingbot, bingpreview, butterfly, cloudflare, chrome, duckduckgo, embedly, ';
			$uas .= 'facebookexternalhit, facebot, google page speed, googlebot, ia_archiver, linkedinbot, mediapartners-google, msnbot, netcraftsurvey, ';
			$uas .= 'outbrain, pinterest, quora, rogerbot, showyoubot, slackbot, slurp, sogou, teoma, tweetmemebot, twitterbot, ';
			$uas .= 'uptimerobot, urlresolver, vkshare, w3c_validator, wordpress, wp rocket, yandex';
			
			return apply_filters('blackhole_default_uas', $uas);
			
		}
		
		public function action_links($links, $file) {
			
			if ($file == BBB_FILE && current_user_can('manage_options')) {
				
				$settings = '<a href="'. admin_url('admin.php?page=blackhole_settings') .'">'. esc_html__('Settings', 'blackhole-bad-bots') .'</a>';
				
				array_unshift($links, $settings);
				
			}
			
			if ($file == BBB_FILE) {
				
				$pro_href   = 'https://plugin-planet.com/blackhole-pro/?plugin';
				$pro_title  = esc_attr__('Get Blackhole Pro!', 'blackhole-bad-bots');
				$pro_text   = esc_html__('Go&nbsp;Pro', 'blackhole-bad-bots');
				$pro_style  = 'font-weight:bold;';
				
				$pro = '<a target="_blank" rel="noopener noreferrer" href="'. $pro_href .'" title="'. $pro_title .'" style="'. $pro_style .'">'. $pro_text .'</a>';
				
				array_unshift($links, $pro);
				
			}
			
			return $links;
			
		}
		
		public function plugin_links($links, $file) {
			if ($file == plugin_basename(__FILE__)) {
				
				$home_href  = 'https://perishablepress.com/blackhole-bad-bots/';
				$home_title = esc_attr__('Plugin Homepage', 'blackhole-bad-bots');
				$home_text  = esc_html__('Homepage', 'blackhole-bad-bots');
				
				$links[] = '<a target="_blank" rel="noopener noreferrer" href="'. $home_href .'" title="'. $home_title .'">'. $home_text .'</a>';
				
				$rate_href  = 'https://wordpress.org/support/plugin/'. BBB_SLUG .'/reviews/?rate=5#new-post';
				$rate_title = esc_attr__('Click here to rate and review this plugin on WordPress.org', 'blackhole-bad-bots');
				$rate_text  = esc_html__('Rate this plugin&nbsp;&raquo;', 'blackhole-bad-bots');
				
				$links[] = '<a target="_blank" rel="noopener noreferrer" href="'. $rate_href .'" title="'. $rate_title .'">'. $rate_text .'</a>';
				
			}
			return $links;
		}
		
		public function check_blackhole() {
			if (class_exists('Blackhole_Pro')) {
				if (is_plugin_active(BBB_FILE)) {
					deactivate_plugins(BBB_FILE);
					
					$msg  = '<strong>'. esc_html__('Warning:', 'blackhole-bad-bots') .'</strong> '. esc_html__('Pro version of Blackhole currently active. Free and Pro versions cannot be activated at the same time. ', 'blackhole-bad-bots');
					$msg .= esc_html__('Please return to the', 'blackhole-bad-bots') .' <a href="'. admin_url('plugins.php') .'">'. esc_html__('WP Admin Area', 'blackhole-bad-bots') .'</a> '. esc_html__('and try again.', 'blackhole-bad-bots');
					
					wp_die($msg);
				}
			}
		}
		
		public function check_version() {
			$wp_version = get_bloginfo('version');
			if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
				if (version_compare($wp_version, BBB_REQUIRE, '<')) {
					if (is_plugin_active(BBB_FILE)) {
						deactivate_plugins(BBB_FILE);
						$msg  = '<strong>'. BBB_NAME .'</strong> '. esc_html__('requires WordPress ', 'blackhole-bad-bots') . BBB_REQUIRE . esc_html__(' or higher, and has been deactivated! ', 'blackhole-bad-bots');
						$msg .= esc_html__('Please return to the', 'blackhole-bad-bots') .' <a href="'. admin_url() .'">'. esc_html__('WP Admin Area', 'blackhole-bad-bots') .'</a> '. esc_html__('to upgrade WordPress and try again.', 'blackhole-bad-bots');
						wp_die($msg);
					}
				}
			}
		}
		
		public function blackhole_allow_style($styles) {
			
			$styles[] = 'display';
			
			return $styles;
			
		}

		public function load_i18n() {
			load_plugin_textdomain('blackhole-bad-bots', false, BBB_DIR .'languages/');
		}
		
		public function __clone() {
			_doing_it_wrong(__FUNCTION__, esc_html__('Cheatin&rsquo; huh?', 'blackhole-bad-bots'), BBB_VERSION);
		}
		
		public function __wakeup() {
			_doing_it_wrong(__FUNCTION__, esc_html__('Cheatin&rsquo; huh?', 'blackhole-bad-bots'), BBB_VERSION);
		}
		
	}
}

if (class_exists('Blackhole_Bad_Bots')) {
	
	$bbb_options = get_option('bbb_options', Blackhole_Bad_Bots::options());
	$bbb_badbots = get_option('bbb_badbots', Blackhole_Bad_Bots::badbots());
	
	$bbb_options = apply_filters('blackhole_get_options', $bbb_options);
	$bbb_badbots = apply_filters('blackhole_get_badbots', $bbb_badbots);
	
	if (!function_exists('blackhole_bad_bots')) {
		
		function blackhole_bad_bots() {
			
			return Blackhole_Bad_Bots::instance();
		}
	}
	
	blackhole_bad_bots();
	
}
