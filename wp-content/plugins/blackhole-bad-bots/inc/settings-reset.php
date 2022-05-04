<?php // Blackhole for Bad Bots - Reset Settings

if (!defined('ABSPATH')) exit;

function blackhole_tools_admin_notice() {
	
	$screen = get_current_screen();
	
	if (($screen->id === 'toplevel_page_blackhole_settings') || ($screen->id === 'blackhole_page_blackhole_badbots')) {
		
		if (isset($_GET['reset-options'])) {
			
			if ($_GET['reset-options'] === 'true') : ?>
				
				<div class="notice notice-success is-dismissible"><p><strong><?php esc_html_e('Default options restored.', 'blackhole-bad-bots'); ?></strong></p></div>
				
			<?php else : ?>
				
				<div class="notice notice-info is-dismissible"><p><strong><?php esc_html_e('No changes made to options.', 'blackhole-bad-bots'); ?></strong></p></div>
				
			<?php endif;
			
		} elseif (isset($_GET['reset-badbots'])) {
			
			if ($_GET['reset-badbots'] === 'true') : ?>
				
				<div class="notice notice-success is-dismissible"><p><strong><?php esc_html_e('Bad bots reset successfully.', 'blackhole-bad-bots'); ?></strong></p></div>
				
			<?php else : ?>
				
				<div class="notice notice-info is-dismissible"><p><strong><?php esc_html_e('No changes made to bad bots.', 'blackhole-bad-bots'); ?></strong></p></div>
				
			<?php endif;
			
		} elseif (isset($_GET['delete-bot'])) {
			
			if ($_GET['delete-bot'] === 'true') : ?>
				
				<div class="notice notice-success is-dismissible"><p><strong><?php esc_html_e('Bot deleted.', 'blackhole-bad-bots'); ?></strong></p></div>
				
			<?php else : ?>
				
				<div class="notice notice-info is-dismissible"><p><strong><?php esc_html_e('No bots deleted.', 'blackhole-bad-bots'); ?></strong></p></div>
				
			<?php endif;
			
		}
		
	}
	
}

function blackhole_reset_options() {
	
	if (isset($_GET['reset-options-verify']) && wp_verify_nonce($_GET['reset-options-verify'], 'blackhole_reset_options')) {
		
		if (!current_user_can('manage_options')) exit;
		
		$options_default = Blackhole_Bad_Bots::options();
		$options_update = update_option('bbb_options', $options_default);
		
		$result = 'false';
		if ($options_update) $result = 'true';
		
		$location = admin_url('admin.php?page=blackhole_settings&reset-options='. $result);
		wp_redirect($location);
		exit;
		
	}
	
}

function blackhole_reset_badbots() { 
	
	if (isset($_GET['reset-badbots-verify']) && wp_verify_nonce($_GET['reset-badbots-verify'], 'blackhole_reset_badbots')) {
		
		if (!current_user_can('manage_options')) exit;
		
		$badbots_default = Blackhole_Bad_Bots::badbots();
		$badbots_update = update_option('bbb_badbots', $badbots_default);
		
		$result = 'false';
		
		if ($badbots_update) {
			
			blackhole_clear_cache();
			
			$result = 'true';
			
		}
		
		$location = admin_url('admin.php?page=blackhole_badbots&reset-badbots='. $result);
		wp_redirect($location);
		exit;
		
	}
	
}

function blackhole_delete_bot() { 
	
	global $bbb_badbots;
	
	if (isset($_GET['delete-bot-verify']) && wp_verify_nonce($_GET['delete-bot-verify'], 'blackhole_delete_bot')) {
		
		if (!current_user_can('manage_options')) exit;
		
		$bot_id = isset($_GET['bot-id']) ? $_GET['bot-id'] : '';
		if (empty($bot_id)) return false;
		
		unset($bbb_badbots[$bot_id]);
		
		$badbots_update = update_option('bbb_badbots', $bbb_badbots);
		
		$result = 'false';
		
		if ($badbots_update) {
			
			blackhole_clear_cache();
			
			$result = 'true';
			
		}
		
		$location = admin_url('admin.php?page=blackhole_badbots&delete-bot='. $result);
		wp_redirect($location);
		exit;
		
	}
	
}
