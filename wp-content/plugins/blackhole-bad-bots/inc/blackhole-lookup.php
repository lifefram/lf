<?php // Blackhole for Bad Bots - Blackhole WHOIS Lookup 

if (!defined('ABSPATH')) exit;

function blackhole_whois($ip) {
	
	$msg = '';
	$extra = '';
	$buffer = '';
	$server = 'whois.arin.net';
	
	if (!$ip = gethostbyname($ip)) {
		
		$msg .= esc_html__('IP address required for WHOIS lookup. ', 'blackhole-bad-bots') . "\n\n";
		
	} else {
		
		if (!$sock = fsockopen($server, 43, $num, $error, 20)) {
			
			unset($sock);
			$msg .= esc_html__('Timed-out connecting to ', 'blackhole-bad-bots') . $server . esc_html__(' (port 43). ', 'blackhole-bad-bots') . "\n\n";
			
		} else {
			
			fputs($sock, "n " . $ip . "\n");
			while (!feof($sock)) $buffer .= fgets($sock, 10240); 
			fclose($sock);
			
		}
		
		if (stripos($buffer, 'ripe.net')) {
			
			$nextServer = 'whois.ripe.net';
			
		} elseif (stripos($buffer, 'nic.ad.jp')) {
			
			$nextServer = 'whois.nic.ad.jp';
			$extra = '/e';
			
		} elseif (stripos($buffer, 'registro.br')) {
			
			$nextServer = 'whois.registro.br';
			
		}
		
		if (isset($nextServer)) {
			
			$msg .= esc_html__('Deferred to specific whois server: ', 'blackhole-bad-bots') . $nextServer . esc_html__('...', 'blackhole-bad-bots') . "\n\n";
			
			if (!$sock = fsockopen($nextServer, 43, $num, $error, 10)) {
				
				unset($sock);
				$msg .= esc_html__('Timed-out connecting to ', 'blackhole-bad-bots') . $nextServer . esc_html__(' (port 43). ', 'blackhole-bad-bots') . "\n\n";
				
			} else {
				
				fputs($sock, $ip . $extra . "\n");
				while (!feof($sock)) $buffer .= fgets($sock, 10240);
				fclose($sock);
				
			}
		}
		
		$replacements = array("\n", "\n\n", "");
		$patterns = array("/\\n\\n\\n\\n/i", "/\\n\\n\\n/i", "/#(\s)?/i");
		$buffer = preg_replace($patterns, $replacements, $buffer);
		$buffer = htmlentities(trim($buffer), ENT_QUOTES, get_option('blog_charset', 'UTF-8'));
		
		$msg .= $buffer;
		
	}
	
	return $msg;
	
}