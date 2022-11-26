<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
try {
	 
	$ss360_options = array('ss360_api_token', 'ss360_account_created', 'ss360_sr_type', 'ss360_config_updated','ss360_config','ss360_siteId', 
		'ss360_is_indexed', 'ss360_is_configured', 'ss360_config_midifications', 'ss360_review_interaction', 'ss360_plugin_version', 'ss360_indexing_mode',
		'ss360_old_indexing_notice', 'ss360_active_plan', 'ss360_data_points', 'ss360_inactive_dp', 'ss360_renamed_dp', 'ss360_installation_id', 'ss360_config_modifications',
		'ss360_sync_on_save', 'ss360_sync_on_status','ss360_sync_on_future','ss360_sync_on_delete', 'ss360_callbacks', 'ss360_acf_def', 'ss360_woocommerce_categories', 'ss360_woocommerce_filters', 'ss360_selected_menus', 'ss360_page_limit',
		'ss360_inject_search');
	
	global $wpdb;
	
	if(!is_multisite()){
		foreach($ss360_options as $ss360_option){
			delete_option($ss360_option);
		}
		$ss360_plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'ss360_%filter_id' OR option_name LIKE 'ss360_%_index_offset' OR option_name LIKE 'ss360_%_to_index'" );
		foreach( $ss360_plugin_options as $ss360_option ) {
			delete_option( $ss360_option->option_name );
		}
	}else {
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
		$original_blog_id = get_current_blog_id();
		foreach ( $blog_ids as $blog_id ) 
		{
			switch_to_blog( $blog_id );
			foreach($ss360_options as $ss360_option){
				delete_option($ss360_option);
			}
			$ss360_plugin_options = $wpdb->get_results("SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'ss360_%filter_id' OR option_name LIKE 'ss360_%_index_offset' OR option_name LIKE 'ss360_%_to_index'" );
			foreach( $ss360_plugin_options as $ss360_option ) {
				delete_option( $ss360_option->option_name );
			}
		}
	
		switch_to_blog( $original_blog_id );
	}
} catch (Error $e) {
	// ccl
}
