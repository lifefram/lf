<?php // Blackhole for Bad Bots - Register Bad Bots

if (!defined('ABSPATH')) exit;

function blackhole_register_badbots() {
	
	// register_setting( $option_group, $option_name, $sanitize_callback );
	register_setting('bbb_badbots', 'bbb_badbots', 'blackhole_validate_badbots');
	
	// add_settings_section( $id, $title, $callback, $page ); 
	add_settings_section('botdata', esc_html__('Welcome to Blackhole..', 'blackhole-bad-bots'), 'blackhole_settings_section_badbots', 'bbb_badbots');
	
	add_settings_field('blocked_bots',  esc_html__('Blocked Bots',   'blackhole-bad-bots'), 'blackhole_callback_blocked_bots',  'bbb_badbots', 'botdata', array('id' => 'blocked_bots',  'label' => esc_html__('List of blocked bots', 'blackhole-bad-bots')));
	add_settings_field('reset_badbots', esc_html__('Reset Bad Bots', 'blackhole-bad-bots'), 'blackhole_callback_reset_badbots', 'bbb_badbots', 'botdata', array('id' => 'reset_badbots', 'label' => esc_html__('Reset the list of bad bots', 'blackhole-bad-bots')));
	add_settings_field('pro_version',   esc_html__('Upgrade to Pro', 'blackhole-bad-bots'), 'blackhole_callback_pro',           'bbb_badbots', 'botdata', array('id' => 'pro_version',   'label' => esc_html__('Get Blackhole Pro &raquo;', 'blackhole-bad-bots')));
	
}

function blackhole_validate_badbots($bots) {
	
	foreach ($bots as $key => $val) {
		
		$bots[$key]['ip_address']   = isset($bots[$key]['ip_address'])   ? sanitize_text_field($bots[$key]['ip_address'])   : null;
		$bots[$key]['request_uri']  = isset($bots[$key]['request_uri'])  ? sanitize_text_field($bots[$key]['request_uri'])  : null;
		$bots[$key]['remote_host']  = isset($bots[$key]['remote_host'])  ? sanitize_text_field($bots[$key]['remote_host'])  : null;
		$bots[$key]['query_string'] = isset($bots[$key]['query_string']) ? sanitize_text_field($bots[$key]['query_string']) : null;
		$bots[$key]['user_agent']   = isset($bots[$key]['user_agent'])   ? sanitize_text_field($bots[$key]['user_agent'])   : null;
		$bots[$key]['referrer']     = isset($bots[$key]['referrer'])     ? sanitize_text_field($bots[$key]['referrer'])     : null;
		$bots[$key]['protocol']     = isset($bots[$key]['protocol'])     ? sanitize_text_field($bots[$key]['protocol'])     : null;
		$bots[$key]['method']       = isset($bots[$key]['method'])       ? sanitize_text_field($bots[$key]['method'])       : null;
		$bots[$key]['date']         = isset($bots[$key]['date'])         ? sanitize_text_field($bots[$key]['date'])         : null;
		
	}
	
	return $bots;
}

function blackhole_settings_section_badbots() {
	
	echo '<p>'. esc_html__('Here is a complete log of all blocked bots. ', 'blackhole-bad-bots');
	echo esc_html__('To remove a bot from the list, click the', 'blackhole-bad-bots') .' <span class="pseudo-link">[x]</span>. ';
	echo esc_html__('Visit the Help tab for complete documentation.', 'blackhole-bad-bots') .'</p>';
	
}

function blackhole_callback_reset_badbots($args) {
	
	$nonce = wp_create_nonce('blackhole_reset_badbots');
	$href = esc_url(add_query_arg(array('reset-badbots-verify' => $nonce), admin_url('admin.php?page=blackhole_badbots')));
	
	echo '<a class="bbb-reset-badbots" href="'. $href .'">'. esc_html__('Reset Bad Bots Log', 'blackhole-bad-bots') .'</a>';
	
}

function blackhole_callback_blocked_bots() {
	global $bbb_badbots;
	
	$nonce = wp_create_nonce('blackhole_delete_bot');
	
	echo '<div class="bbb-blocked-bots">';
	echo '<ol>';
	
	foreach ($bbb_badbots as $key => $val) {
		
		$href = esc_url(add_query_arg(array('delete-bot-verify' => $nonce, 'bot-id' => $key), admin_url('admin.php?page=blackhole_badbots')));
		
		$ip_address   = isset($val['ip_address'])   ? $val['ip_address']   : '';
		$request_uri  = isset($val['request_uri'])  ? $val['request_uri']  : '';
		$remote_host  = isset($val['remote_host'])  ? $val['remote_host']  : '';
		$query_string = isset($val['query_string']) ? $val['query_string'] : '';
		$user_agent   = isset($val['user_agent'])   ? $val['user_agent']   : '';
		$referrer     = isset($val['referrer'])     ? $val['referrer']     : '';
		$protocol     = isset($val['protocol'])     ? $val['protocol']     : '';
		$method       = isset($val['method'])       ? $val['method']       : '';
		$date         = isset($val['date'])         ? $val['date']         : '';
		
		$data  = '<strong>'. $date .'</strong> - '. $ip_address .' - '. $protocol .' - <span class="bbb-user-agent">'. $user_agent . '</span>';
		
		$data = apply_filters('blackhole_log_data', $data, $key, $val);
		
		echo '<input name="bbb_badbots['. $key .'][ip_address]"   type="hidden" value="'. $ip_address .'" /> ';
		echo '<input name="bbb_badbots['. $key .'][request_uri]"  type="hidden" value="'. $request_uri .'" /> ';
		echo '<input name="bbb_badbots['. $key .'][remote_host]"  type="hidden" value="'. $remote_host .'" /> ';
		echo '<input name="bbb_badbots['. $key .'][query_string]" type="hidden" value="'. $query_string .'" /> ';
		echo '<input name="bbb_badbots['. $key .'][user_agent]"   type="hidden" value="'. $user_agent .'" /> ';
		echo '<input name="bbb_badbots['. $key .'][referrer]"     type="hidden" value="'. $referrer .'" /> ';
		echo '<input name="bbb_badbots['. $key .'][protocol]"     type="hidden" value="'. $protocol .'" /> ';
		echo '<input name="bbb_badbots['. $key .'][method]"       type="hidden" value="'. $method .'" /> ';
		echo '<input name="bbb_badbots['. $key .'][date]"         type="hidden" value="'. $date .'" /> ';
		
		$delete_link = '<a class="bbb-delete-bot" href="'. $href .'" title="'. esc_attr__('Delete this bot', 'blackhole-bad-bots') .'">[x]</a> ';
		
		$delete = ($ip_address === '173.203.204.123') ? '<span class="bbb-delete-bot-icon"></span>' : $delete_link;
		
		echo '<li>'. $delete . $data .'</li>';
		
	}
	
	echo '</ol>';
	echo '</div>';
	
}
