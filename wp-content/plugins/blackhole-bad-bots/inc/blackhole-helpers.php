<?php // Blackhole for Bad Bots - Helper functions

if (!defined('ABSPATH')) exit;

// add_filter('nonce_life', 'blackhole_nonce_life');
function blackhole_nonce_life($life) {
	
	return 60; // enabled only when testing
	
}

function blackhole_get_vars() {
	
	$ip_address = blackhole_get_ip();
	
	$remote_host = blackhole_get_host($ip_address);

	$request_uri  = isset($_SERVER['REQUEST_URI'])     ? sanitize_text_field($_SERVER['REQUEST_URI'])     : '';
	$query_string = isset($_SERVER['QUERY_STRING'])    ? sanitize_text_field($_SERVER['QUERY_STRING'])    : '';
	$user_agent   = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '';
	$referrer     = isset($_SERVER['HTTP_REFERER'])    ? sanitize_text_field($_SERVER['HTTP_REFERER'])    : '';
	$protocol     = isset($_SERVER['SERVER_PROTOCOL']) ? sanitize_text_field($_SERVER['SERVER_PROTOCOL']) : '';
	$method       = isset($_SERVER['REQUEST_METHOD'])  ? sanitize_text_field($_SERVER['REQUEST_METHOD'])  : '';
	
	$date = date('Y/m/d @ h:i:s a', current_time('timestamp'));
	
	$vars = array($ip_address, $request_uri, $remote_host, $query_string, $user_agent, $referrer, $protocol, $method, $date);
	
	return apply_filters('blackhole_vars', $vars);
	
}

function blackhole_get_host($ip_address) {
	
	$remote_host = '';
	
	if (filter_var($ip_address, FILTER_VALIDATE_IP) && apply_filters('blackhole_enable_host_check', true)) {
		
		$remote_host = isset($ip_address) ? sanitize_text_field(gethostbyaddr($ip_address)) : '';
		
		if (strstr($remote_host, ', ')) {
			
			$ips = explode(', ', $remote_host);
			
			$remote_host = $ips[0];
			
		}
		
	}
	
	return $remote_host;
	
}

function blackhole_get_deps() {
	
	$filename   = apply_filters('blackhole_template_filename', 'blackhole-template');
	
	$default    = BBB_DIR .'/inc/'. $filename .'.php';
	
	$custom     = get_stylesheet_directory() .'/'. $filename .'.php';
	
	$custom_dir = apply_filters('blackhole_custom_dir', 'blackhole');
	
	$custom_alt = WP_CONTENT_DIR .'/'. $custom_dir .'/'. $filename .'.php';
	
	require_once 'blackhole-lookup.php';
	
	if (file_exists($custom)) {
		
		require_once $custom;
		
	} elseif (file_exists($custom_alt)) {
		
		require_once $custom_alt;
		
	} else {
		
		require_once $default;
		
	}
	
}





function blackhole_abort($vars) {
	
	$ignore_loggedin = apply_filters('blackhole_ignore_loggedin', false);
	$ignore_backend  = apply_filters('blackhole_ignore_backend',  true);
	$ignore_login    = apply_filters('blackhole_ignore_login',    true);
	
	if (
		
		(wp_doing_ajax()) || 
		(blackhole_is_tty()) || 
		(defined('DOING_CRON') && DOING_CRON) || 
		($ignore_loggedin && is_user_logged_in()) || 
		($ignore_backend  && is_admin()) || 
		($ignore_login    && blackhole_is_login()) || 
		(blackhole_whitelist($vars))
		
	) return true;
	
	return false;
	
}

function blackhole_whitelist($vars) {
	
	global $bbb_options;
	
	list ($ip_address, $request_uri, $remote_host, $query_string, $user_agent, $referrer, $protocol, $method) = $vars;
	
	// bots
	
	$whitelist_bots = isset($bbb_options['bot_whitelist']) ? $bbb_options['bot_whitelist'] : '';
	$whitelist_bots = array_filter(array_map('trim', explode(',', $whitelist_bots)));
	
	if (!empty($whitelist_bots)) {
		
		foreach ($whitelist_bots as $bot) {
			
			if (stripos($user_agent, $bot) !== false) {
				
				return true;
				
			}
			
		}
		
	}
	
	// ips
	
	$whitelist_ips = isset($bbb_options['ip_whitelist']) ? $bbb_options['ip_whitelist'] : '';
	$whitelist_ips = array_filter(array_map('trim', explode(',', $whitelist_ips)));
	
	foreach ($whitelist_ips as $ip) {
		
		if (strpos($ip, '/') === false) {
			
			if (substr($ip_address, 0, strlen($ip)) === $ip) {
				
				return true;
				
			}
			
		} else {
			
			if (blackhole_ip_in_range($ip_address, $ip)) {
				
				return true;
				
			}
			
		}
		
	}
	
	return false;
	
}

function blackhole_send_email($whois, $vars) {
	
	global $bbb_options;
	
	if (isset($bbb_options['email_alerts']) && !$bbb_options['email_alerts']) return false;
	
	list ($ip_address, $request_uri, $remote_host, $query_string, $user_agent, $referrer, $protocol, $method, $date) = $vars;
	
	require_once 'blackhole-lookup.php';
	
	$whois = htmlspecialchars_decode($whois, ENT_QUOTES);
	
	$name  = apply_filters('blackhole_alert_name', get_option('blogname'));
	
	$domain = parse_url(get_home_url(), PHP_URL_HOST);
	
	$email = isset($bbb_options['email_address']) ? $bbb_options['email_address'] : get_option('admin_email');
	
	$from = (isset($bbb_options['email_from']) && !empty($bbb_options['email_from'])) ? $bbb_options['email_from'] : $email;

	$subject = apply_filters('blackhole_alert_subject', __('Bad Bot Alert at ', 'blackhole-bad-bots') . $name .' @ '. $domain);
	
	$intro  = __('Hello! This email alert is sent from your WordPress site, ', 'blackhole-bad-bots') . $name .' @ '. $domain .'. ';
	$intro .= __('There, you are using a plugin called Blackhole for Bad Bots. ', 'blackhole-bad-bots');
	$intro .= __('This email alert tells you that the plugin is working great, ', 'blackhole-bad-bots');
	$intro .= __('doing its job blocking bad bots. ', 'blackhole-bad-bots');
	$intro .= __('Below you will find details about the bad bot that was denied access. ', 'blackhole-bad-bots');
	$intro .= __('To disable these email alerts at any time, visit the plugin settings. ', 'blackhole-bad-bots');
	
	$message   = $intro . "\n\n";
	$message  .= $date . "\n\n";
	$message  .= __('Request URI: ',  'blackhole-bad-bots') . $request_uri    . "\n";
	$message  .= __('IP Address: ',   'blackhole-bad-bots') . $ip_address     . "\n";
	$message  .= __('Host Name: ',    'blackhole-bad-bots') . $remote_host    . "\n";
	$message  .= __('User Agent: ',   'blackhole-bad-bots') . $user_agent     . "\n\n";
	$message  .= __('Whois Lookup: ', 'blackhole-bad-bots') . "\n\n" . $whois . "\n\n";
	
	$message = apply_filters('blackhole_alert_message', $message, $vars);
	
	$headers  = 'X-Mailer: Blackhole for Bad Bots'. "\n";
	$headers .= 'From: '. $name .' <'. $from .'>'. "\n";
	$headers .= 'Content-Type: text/plain; charset='. get_option('blog_charset', 'UTF-8') . "\n";
	
	$headers = apply_filters('blackhole_alert_headers', $headers, $vars);
	
	$alert = wp_mail($email, $subject, $message, $headers);
	
	return $alert;
		
}

function blackhole_disable_cache() {
	
	if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE', true);
	
	if (
		isset($GLOBALS['wp_fastest_cache']) && 
		is_object($GLOBALS['wp_fastest_cache']) && 
		method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache') && 
		is_callable(array($GLOBALS['wp_fastest_cache'], 'deleteCache'))
	) {
		
		$GLOBALS['wp_fastest_cache']->deleteCache();
		
	}
	
	return DONOTCACHEPAGE;
	
}

function blackhole_clear_cache() {
	
	if (function_exists('w3tc_pgcache_flush')) w3tc_pgcache_flush();
	
	if (function_exists('wp_cache_clear_cache')) wp_cache_clear_cache();
	
	if (isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')) $GLOBALS['wp_fastest_cache']->deleteCache();
		
}

function blackhole_is_tty() {
	
	if (function_exists('posix_isatty')) {
		
		if (defined('STDOUT')) {
			
			if (posix_isatty(STDOUT)) return true;
			
		}
		
	}
	
	return false;
	
}

function blackhole_is_login() {
	
	return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
	
}