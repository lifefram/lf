<?php // Blackhole for Bad Bots - Responses & Messages

if (!defined('ABSPATH')) exit;

function blackhole_display_message() {
	
	global $bbb_options;
	
	$message_display = isset($bbb_options['message_display']) ? $bbb_options['message_display'] : 'default';
	
	if     ($message_display === 'custom')  $message = blackhole_message_custom();
	elseif ($message_display === 'nothing') $message = blackhole_message_nothing();
	else                                    $message = blackhole_message_default();
	
	$block_status     = apply_filters('blackhole_block_status',     '403');
	$block_protocol   = apply_filters('blackhole_block_protocol',   'HTTP/1.1');
	$block_connection = apply_filters('blackhole_block_connection', 'Connection: Close');
	
	header($block_protocol .' '. $block_status);
	header($block_connection);
	exit($message);
	
}

function blackhole_message_default() {
	
	$message  = '<meta name="robots" content="noindex,nofollow,noarchive,nosnippet,noodp,noydir">';
	$message .= '<h1>'. esc_html__('You have been banned from this site.', 'blackhole-bad-bots') .'</h1>';
	$message .= '<p>'. esc_html__('If you think there has been a mistake, please contact the administrator via proxy server.', 'blackhole-bad-bots') .'</p>';
	
	return apply_filters('blackhole_message_default', $message);
	
}

function blackhole_message_custom(){
	
	global $bbb_options;
	
	$message = isset($bbb_options['message_custom']) ? $bbb_options['message_custom'] : blackhole_default_message();
	
	return apply_filters('blackhole_message_custom', $message);
		
}

function blackhole_message_nothing() {
	
	$message = '<style type="text/css">html, body { background-color: black; }</style>';
	
	return apply_filters('blackhole_message_nothing', $message);
	
}