<?php // Blackhole for Bad Bots - Enqueue Resources

if (!defined('ABSPATH')) exit;

function blackhole_enqueue_resources_admin() {
	
	$screen = get_current_screen();
	
	if (!$screen) return;
	
	if (!property_exists($screen, 'id')) return;
	
	if (($screen->id === 'toplevel_page_blackhole_settings') || ($screen->id === 'blackhole_page_blackhole_badbots')) {
		
		// wp_enqueue_style ( $handle, $src, $deps, $ver, $media )
		// https://developer.wordpress.org/reference/functions/wp_enqueue_style/
		wp_enqueue_style('blackhole_admin', BBB_URL .'css/admin-styles.css', array(), BBB_VERSION);
		wp_enqueue_style('wp-jquery-ui-dialog');
		
		$js_deps = array('jquery', 'jquery-ui-core', 'jquery-ui-dialog');
		
		// wp_enqueue_script ( $handle, $src, $deps, $ver, $in_footer )
		// https://developer.wordpress.org/reference/functions/wp_enqueue_script/
		wp_enqueue_script('blackhole_admin', BBB_URL .'js/admin-scripts.js', $js_deps, BBB_VERSION);
		
	}
	
}

function blackhole_print_js_vars_admin() {
	
	$screen = get_current_screen();
	
	if (!$screen) return;
	
	if (!property_exists($screen, 'id')) return;
	
	if (($screen->id === 'toplevel_page_blackhole_settings') || ($screen->id === 'blackhole_page_blackhole_badbots')) :
	
	?>
	
	<script type="text/javascript">
		var 
		alert_reset_badbots_title   = '<?php _e('Confirm Reset', 'blackhole-bad-bots'); ?>',
		alert_reset_badbots_message = '<?php _e('Reset the list of bad bots?', 'blackhole-bad-bots'); ?>',
		alert_reset_badbots_true    = '<?php _e('Yes, make it so.', 'blackhole-bad-bots'); ?>',
		alert_reset_badbots_false   = '<?php _e('No, abort mission.', 'blackhole-bad-bots'); ?>',
		
		alert_reset_options_title   = '<?php _e('Confirm Reset', 'blackhole-bad-bots'); ?>',
		alert_reset_options_message = '<?php _e('Restore default options?', 'blackhole-bad-bots'); ?>',
		alert_reset_options_true    = '<?php _e('Yes, make it so.', 'blackhole-bad-bots'); ?>',
		alert_reset_options_false   = '<?php _e('No, abort mission.', 'blackhole-bad-bots'); ?>',
		
		alert_delete_bot_title      = '<?php _e('Confirm Delete', 'blackhole-bad-bots'); ?>',
		alert_delete_bot_message    = '<?php _e('Delete this bot?', 'blackhole-bad-bots'); ?>',
		alert_delete_bot_true       = '<?php _e('Yes, let him go.', 'blackhole-bad-bots'); ?>',
		alert_delete_bot_false      = '<?php _e('No, stay on the leader.', 'blackhole-bad-bots'); ?>';
	</script>
	
	<?php 
	
	endif;
	
}
