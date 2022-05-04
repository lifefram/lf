<?php // Blackhole for Bad Bots - IP functionality

if (!defined('ABSPATH')) exit;

function blackhole_get_ip() {
	
	$ip = blackhole_evaluate_ip();
	
	if (preg_match('/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $ip, $ip_match)) {
		
		$ip = $ip_match[1];
		
	}
	
	return sanitize_text_field($ip);
	
}

function blackhole_evaluate_ip() {
	 
	$ip_keys = array('HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_X_REAL_IP', 'HTTP_X_COMING_FROM', 'HTTP_PROXY_CONNECTION', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_COMING_FROM', 'HTTP_VIA', 'REMOTE_ADDR');
	
	$ip_keys = apply_filters('blackhole_ip_keys', $ip_keys);
	
	foreach ($ip_keys as $key) {
		
		if (array_key_exists($key, $_SERVER) === true) {
			
			foreach (explode(',', $_SERVER[$key]) as $ip) {
				
				$ip = trim($ip);
				
				$ip = blackhole_normalize_ip($ip);
				
				if (blackhole_validate_ip($ip)) {
					
					return $ip;
					
				}
				
			}
			
		}
		
	}
	
	return esc_html__('Error: Invalid Address', 'blackhole-bad-bots');
	
}

function blackhole_normalize_ip($ip) {
	
	if (strpos($ip, ':') !== false && substr_count($ip, '.') == 3 && strpos($ip, '[') === false) {
		
		// IPv4 with port (e.g., 123.123.123:80)
		$ip = explode(':', $ip);
		$ip = $ip[0];
		
	} else {
		
		// IPv6 with port (e.g., [::1]:80)
		$ip = explode(']', $ip);
		$ip = ltrim($ip[0], '[');
		
	}
	
	return $ip;
	
}
	
function blackhole_validate_ip($ip) {
	
	$options  = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
	
	$options  = apply_filters('blackhole_ip_filter', $options);
	
	$filtered = filter_var($ip, FILTER_VALIDATE_IP, $options);
	
	 if (!$filtered || empty($filtered)) {
		
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			
			return $ip; // IPv4
			
		} elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			
			return $ip; // IPv6
			
		}
		
		if ($ip) {
			
			$log = apply_filters('blackhole_validate_ip_log', 'Invalid IP Address: '. $ip, $ip);
			
			if (!empty($log)) error_log($log);
			
		}
		
		return false;
		
	}
	
	return $filtered;
	
}

function blackhole_ip_in_range($ip, $range) {

	list($range, $netmask) = explode('/', $range, 2);
	
	$range_decimal = ip2long($range);
	
	$ip_decimal = ip2long($ip);
	
	$wildcard_decimal = pow(2, (32 - $netmask)) - 1;
	
	$netmask_decimal = ~ $wildcard_decimal;
	
	return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
	
}