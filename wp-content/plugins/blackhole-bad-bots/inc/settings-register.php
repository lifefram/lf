<?php // Blackhole for Bad Bots - Register Settings

if (!defined('ABSPATH')) exit;

function blackhole_register_settings() {
	
	// register_setting( $option_group, $option_name, $sanitize_callback );
	register_setting('bbb_options', 'bbb_options', 'blackhole_validate_options');
	
	// add_settings_section( $id, $title, $callback, $page ); 
	add_settings_section('settings', esc_html__('Set the Controls..', 'blackhole-bad-bots'), 'blackhole_settings_section_options', 'bbb_options');
	
	// add_settings_field( $id, $title, $callback, $page, $section, $args );
	add_settings_field('robots_rules',    esc_html__('Robots Rules',     'blackhole-bad-bots'), 'blackhole_callback_robots',   'bbb_options', 'settings', array('id' => 'robots_rules',    'label' => esc_html__('Add these rules to your site&rsquo;s robots.txt file', 'blackhole-bad-bots')));
	add_settings_field('email_alerts',    esc_html__('Email Alerts',     'blackhole-bad-bots'), 'blackhole_callback_checkbox', 'bbb_options', 'settings', array('id' => 'email_alerts',    'label' => esc_html__('Enable email alerts', 'blackhole-bad-bots')));
	add_settings_field('email_address',   esc_html__('Email Address',    'blackhole-bad-bots'), 'blackhole_callback_text',     'bbb_options', 'settings', array('id' => 'email_address',   'label' => esc_html__('Email address for alerts', 'blackhole-bad-bots')));
	add_settings_field('email_from',      esc_html__('From Address',     'blackhole-bad-bots'), 'blackhole_callback_text',     'bbb_options', 'settings', array('id' => 'email_from',      'label' => esc_html__('Email address for &ldquo;From&rdquo; header', 'blackhole-bad-bots')));
	add_settings_field('message_display', esc_html__('Message Display',  'blackhole-bad-bots'), 'blackhole_callback_radio',    'bbb_options', 'settings', array('id' => 'message_display', 'label' => esc_html__('Message displayed to blocked bots', 'blackhole-bad-bots')));
	add_settings_field('message_custom',  esc_html__('Message Custom',   'blackhole-bad-bots'), 'blackhole_callback_textarea', 'bbb_options', 'settings', array('id' => 'message_custom',  'label' => esc_html__('Custom message', 'blackhole-bad-bots') .' <span class="bbb-light-text">'. esc_html__('(when Custom is selected in previous setting)', 'blackhole-bad-bots') .'</span>'));
	add_settings_field('bot_whitelist',   esc_html__('Whitelisted Bots', 'blackhole-bad-bots'), 'blackhole_callback_textarea', 'bbb_options', 'settings', array('id' => 'bot_whitelist',   'label' => esc_html__('User agents that never should be blocked', 'blackhole-bad-bots')  .' <span class="bbb-light-text">'. esc_html__('(separate with commas)', 'blackhole-bad-bots') .'</span>'));
	add_settings_field('ip_whitelist',    esc_html__('Whitelisted IPs',  'blackhole-bad-bots'), 'blackhole_callback_textarea', 'bbb_options', 'settings', array('id' => 'ip_whitelist',    'label' => esc_html__('IP addresses that never should be blocked', 'blackhole-bad-bots') .' <span class="bbb-light-text">'. esc_html__('(separate with commas)', 'blackhole-bad-bots') .'</span>'));
	add_settings_field('reset_options',   esc_html__('Reset Options',    'blackhole-bad-bots'), 'blackhole_callback_reset',    'bbb_options', 'settings', array('id' => 'reset_options',   'label' => esc_html__('Restore default plugin options', 'blackhole-bad-bots')));
	add_settings_field('rate_plugin',     esc_html__('Support Plugin',   'blackhole-bad-bots'), 'blackhole_callback_rate',     'bbb_options', 'settings', array('id' => 'rate_plugin',     'label' => esc_html__('Show support with a 5-star rating &raquo;', 'blackhole-bad-bots')));
	add_settings_field('pro_version',     esc_html__('Upgrade to Pro',   'blackhole-bad-bots'), 'blackhole_callback_pro',      'bbb_options', 'settings', array('id' => 'pro_version',     'label' => esc_html__('Get Blackhole Pro &raquo;', 'blackhole-bad-bots')));
	
}

function blackhole_validate_options($input) {
	
	$message_display = blackhole_message_display();
	$allowed_tags = wp_kses_allowed_html('post');
	
	if (!isset($input['email_alerts'])) $input['email_alerts'] = null;
	$input['email_alerts'] = ($input['email_alerts'] == 1 ? 1 : 0);
	
	if (isset($input['email_address'])) $input['email_address'] = wp_filter_nohtml_kses($input['email_address']);
	
	if (isset($input['email_from'])) $input['email_from'] = wp_filter_nohtml_kses($input['email_from']);
	
	if (!isset($input['message_display'])) $input['message_display'] = null;
	if (!array_key_exists($input['message_display'], $message_display)) $input['message_display'] = null;
	
	if (isset($input['message_custom'])) $input['message_custom'] = wp_kses(stripslashes_deep($input['message_custom']), $allowed_tags);
	
	if (isset($input['bot_whitelist']))  $input['bot_whitelist'] = wp_filter_nohtml_kses($input['bot_whitelist']);
	
	if (isset($input['ip_whitelist']))  $input['ip_whitelist'] = wp_filter_nohtml_kses($input['ip_whitelist']);
	
	return $input;
	
}

function blackhole_settings_section_options() {
	
	echo '<p>'. esc_html__('Thanks for using the free Blackhole plugin. May your site be free of bad bots. Visit the', 'blackhole-bad-bots');
	
	echo ' <strong>'. esc_html__('Help tab', 'blackhole-bad-bots') .'</strong> '. esc_html__('for complete documentation and tips.', 'blackhole-bad-bots') .'</p>';
	
	echo '<p><strong>'. esc_html__('Important', 'blackhole-bad-bots') .'</strong> '. esc_html__('note about', 'blackhole-bad-bots');
	
	echo ' <a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/blackhole-pro-cache-plugins/">'. esc_html__('cache plugins', 'blackhole-bad-bots') .'&nbsp;&raquo;</a></p>';
	
}

function blackhole_message_display() {
	
	$message_display = array(
		'default' => array(
			'value' => 'default',
			'label' => esc_html__('Default Message', 'blackhole-bad-bots') .' <span class="bbb-light-text">'. esc_html__('(displays some basic text and markup)', 'blackhole-bad-bots') .'</span>',
		),
		'custom' => array(
			'value' => 'custom',
			'label' => esc_html__('Custom Message', 'blackhole-bad-bots') .' <span class="bbb-light-text">'. esc_html__('(define your own message in the next setting)', 'blackhole-bad-bots') .'</span>',
		),
		'nothing' => array(
			'value' => 'nothing',
			'label' => esc_html__('Into the Void', 'blackhole-bad-bots') .' <span class="bbb-light-text">'. esc_html__('(displays an empty page with a black background)', 'blackhole-bad-bots') .'</span>',
		),
	);
	return $message_display;
	
}

function blackhole_callback_text($args) {
	
	global $bbb_options;
	
	$id = isset($args['id']) ? $args['id'] : '';
	$label = isset($args['label']) ? $args['label'] : '';
	$value = isset($bbb_options[$id]) ? sanitize_text_field($bbb_options[$id]) : '';
	
	echo '<input name="bbb_options['. $id .']" type="text" size="40" value="'. $value .'" />';
	echo '<label class="bbb-label" for="bbb_options['. $id .']">'. $label .'</label>';
	
}

function blackhole_callback_textarea($args) {
	
	global $bbb_options;
	
	$allowed_tags = wp_kses_allowed_html('post');
	
	$id = isset($args['id']) ? $args['id'] : '';
	$label = isset($args['label']) ? $args['label'] : '';
	$value = isset($bbb_options[$id]) ? wp_kses(stripslashes_deep($bbb_options[$id]), $allowed_tags) : '';
	
	echo '<textarea name="bbb_options['. $id .']" rows="3" cols="50">'. $value .'</textarea>';
	echo '<label class="bbb-label" for="bbb_options['. $id .']">'. $label .'</label>';
	
}

function blackhole_callback_checkbox($args) {
	
	global $bbb_options;
	
	$id = isset($args['id']) ? $args['id'] : '';
	$label = isset($args['label']) ? $args['label'] : '';
	$checked = isset($bbb_options[$id]) ? checked($bbb_options[$id], 1, false) : '';
	
	echo '<input name="bbb_options['. $id .']" type="checkbox" value="1" '. $checked .' /> ';
	echo '<label class="bbb-label inline-block" for="bbb_options['. $id .']">'. $label .'</label>';
	
}

function blackhole_callback_radio($args) {
	
	global $bbb_options;
	
	$options_array = array();
	if ($args['id'] === 'message_display') $options_array = blackhole_message_display();
	
	$id = isset($args['id']) ? $args['id'] : '';
	$label = isset($args['label']) ? $args['label'] : '';
	$value = isset($bbb_options[$id]) ? sanitize_text_field($bbb_options[$id]) : '';
	
	echo '<label class="bbb-label inline-block" for="bbb_options['. $id .']">'. $label .'</label>';
	echo '<ul>';
	
	foreach ($options_array as $option) {
		
		$checked = '';
		if ($value == $option['value']) $checked = ' checked="checked"';
		
		echo '<li><input type="radio" name="bbb_options['. $id .']" value="'. $option['value'] .'"'. $checked .' /> '. $option['label'] .'</li>';
		
	}
	echo '</ul>';
	
}

function blackhole_callback_select($args) {
	
	global $bbb_options;
	
	$options_array = array();
	if ($args['id'] === 'message_display') $options_array = blackhole_message_display(); // example, replace with actual id and function
	
	$id = isset($args['id']) ? $args['id'] : '';
	$label = isset($args['label']) ? $args['label'] : '';
	$value = isset($bbb_options[$id]) ? sanitize_text_field($bbb_options[$id]) : '';
	
	echo '<select name="bbb_options['. $id .']">';
	
	foreach ($options_array as $option) {
		echo '<option '. selected($option['value'], $value, false) .' value="'. $option['value'] .'">'. $option['label'] .'</option>';
	}
	echo '</select><label class="bbb-label inline-block" for="bbb_options['. $id .']">'. $label .'</label>';
	
}

function blackhole_callback_reset($args) {
	
	$nonce = wp_create_nonce('blackhole_reset_options');
	$href  = esc_url(add_query_arg(array('reset-options-verify' => $nonce), admin_url('options-general.php?page=blackhole_settings')));
	$label = isset($args['label']) ? $args['label'] : esc_html__('Restore default plugin options', 'blackhole-bad-bots');
	
	echo '<a class="bbb-reset-options" href="'. $href .'">'. $label .'</a>';
	
}

function blackhole_callback_robots() {
	
	$rules = blackhole_robots();
	
	if (empty($rules)) {
		
		$display = '<em class="bbb-warning">'. esc_html__('Please check WP General Settings.', 'blackhole-bad-bots') .'</em>';
		
	} else {
		
		$protocol = is_ssl() ? 'https://' : 'http://';
		
		$url = $protocol . blackhole_domain() . '/robots.txt';
		
		$title = __('View your site&rsquo;s robots.txt', 'blackhole-bad-bots');
		
		$text = __('robots.txt file', 'blackhole-bad-bots');
		
		$link = '<a target="_blank" rel="noopener noreferrer" href="'. esc_url($url) .'" title="'. esc_attr($title) .'">'. esc_html($text) .'</a>';
		
		$display  = '<div><em>'. esc_html__('Add the following rules to your site&rsquo;s ', 'blackhole-bad-bots') . $link .':</em></div>';
		
		$display .= '<pre>'. trim($rules) .'</pre>';
		
		$display .= '<div><em>'. esc_html__('If you are using WP-generated robots.txt, these rules are added automatically.', 'blackhole-bad-bots') .'</em></div>';
		
	}
	
	echo $display;
	
}

function blackhole_callback_rate($args) {
	
	$label = isset($args['label']) ? $args['label'] : esc_html__('Show support with a 5-star rating &raquo;', 'blackhole-bad-bots');
	$href  = 'https://wordpress.org/support/plugin/'. BBB_SLUG .'/reviews/?rate=5#new-post';
	$title = esc_attr__('Help keep Blackhole going strong! A huge THANK YOU for your support!', 'blackhole-bad-bots');
	
	echo '<a target="_blank" rel="noopener noreferrer" class="bbb-rate-plugin" href="'. $href .'" title="'. $title .'">'. $label .'</a>';
	
}

function blackhole_callback_pro($args) {
	
	$label = isset($args['label']) ? $args['label'] : esc_html__('Get Blackhole Pro &raquo;', 'blackhole-bad-bots');
	$href  = 'https://plugin-planet.com/blackhole-pro/';
	$alt   = esc_attr__('Blackhole Pro', 'blackhole-bad-bots');
	
	echo '<div class="blackhole-pro"><a target="_blank" rel="noopener noreferrer" href="'. $href .'" title="'. $label .'">';
	echo '<img src="'. BBB_URL .'/img/blackhole-pro.jpg" width="400" height="104" alt="'. $alt .'" /></a></div>';
	
}
