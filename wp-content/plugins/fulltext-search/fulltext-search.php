<?php

/*
Plugin Name: WP Fast Total Search
Description: Speeds up and significantly extends the functionality of the built-in search by using the TF-IDF index
Version: 1.51.178
Tested up to: 6.0
Author: Epsiloncool
Author URI: https://e-wm.org
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: fulltext-search
Domain Path: /languages/
*/

/**
 *   Copyright 2013-2022 Epsiloncool
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 ******************************************************************************
 *  I am thank you for the help by buying PRO version of this plugin 
 *  at https://fulltextsearch.org/ 
 *  It will keep me working further on this useful product.
 ******************************************************************************
 * 
 *  @copyright 2013-2022
 *  @license GPLv3
 *  @version 1.51.178
 *  @package Wordpress Fulltext Search
 *  @author Epsiloncool <info@e-wm.org>
 */

/**
 * ACE code editor
 * 
 * BSD License
 * Source: https://github.com/ajaxorg/ace
 * 
 */

define('WPFTS_VERSION', '1.51.178');

require_once dirname(__FILE__).'/includes/wpfts_core.php';
require_once dirname(__FILE__).'/includes/wpfts_output.php';

add_action('cron_schedules', function($schedules)
{
	$schedules['wpfts_each_minute'] = array(
		'interval' => 60,	// 60 seconds
		'display' => __( 'Once a minute' )
	);

	return $schedules;
});

global $wpfts_core;

$wpfts_core = new WPFTS_Core();
$wpfts_core->root_dir = dirname(__FILE__);
$wpfts_core->Init();

register_activation_hook(__FILE__, array(&$wpfts_core, 'activate_plugin'));
register_deactivation_hook(__FILE__, array(&$wpfts_core, 'deactivate_plugin'));

add_action( 'wpmu_new_blog', function ($blog_id, $user_id, $domain, $path, $site_id, $meta)
{
    global $wpdb, $wpfts_core;
 
	if (!function_exists('is_plugin_active_for_network')) {
		require_once(ABSPATH.'/wp-admin/includes/plugin.php');
	}	

    if (is_plugin_active_for_network(plugin_basename(__FILE__))) {
        $old_blog = $wpdb->blogid;
        switch_to_blog($blog_id);
        $wpfts_core->_activate_plugin();
        switch_to_blog($old_blog);
    }
}, 10, 6);

add_action('wpfts_indexer_event', function()
{
	global $wpfts_core;

	if (!$wpfts_core) {
		return;
	}

	if ($wpfts_core->_dev_debug) {
		$wpfts_core->_flare->SendFire(array(
			'pt' => 'inx_cron',
			'stats' => array(
				
			),
		));	
	}

	// Force indexer job to run
	$wpfts_core->IndexerStart();
});

add_action('wp_enqueue_scripts', function () 
{
	global $wpfts_core;
	
	if (($wpfts_core) && (intval($wpfts_core->get_option('is_smart_excerpts')) != 0)) {
		//wp_enqueue_style( 'wpfts_front_styles', $wpfts_core->root_url.'/style/wpfts_front_styles.css', array(), WPFTS_VERSION);
		echo '<style type="text/css">'.$wpfts_core->ReadSEStylesMinimized().'</style>';
	}

	$version = (defined('WP_DEBUG') && WP_DEBUG) ? time() : WPFTS_VERSION;

	wp_enqueue_style('wpfts_jquery-ui-styles', $wpfts_core->root_url.'/style/wpfts_autocomplete.css', array(), $version);
	wp_enqueue_script('wpfts_frontend', plugins_url('js/wpfts_frontend.js', __FILE__), array('jquery', 'jquery-ui-autocomplete'), $version);
});

add_action('init', function () 
{
	global $wpfts_core;
	
	if ((is_object($wpfts_core)) && (is_callable(array($wpfts_core, 'set_hooks')))) {
	
		$wpfts_core->set_hooks();

		add_action('wp_ajax_nopriv_wpfts_autocomplete', 'wpfts_autocomplete_proc');
		add_action('wp_ajax_wpfts_autocomplete', 'wpfts_autocomplete_proc');

		add_action('wp_ajax_nopriv_wpfts_force_index', array($wpfts_core, 'ajax_force_index'));
		add_action('wp_ajax_wpfts_force_index', array($wpfts_core, 'ajax_force_index'));

		/**
		 * Small info row for Post Submit Meta Box
		 */
		add_action('post_submitbox_misc_actions', function($post)
		{
			global $wpfts_core;

			?>
			<div class="misc-pub-section curtime misc-pub-curtime wpfts_submitbox_block">
				<span class="dashicons-before dashicons-search wpfts_submitbox_icon">
					<?php echo __('Index', 'fulltext-search').':'; ?>
				<?php 
				
				// Get current post status
				if ($post && is_object($post) && isset($post->ID)) {
					$post_id = $post->ID;

					$st = $wpfts_core->GetPostIndexStatus(array($post_id));

					echo '<span class="wpfts_post_status" data-postid="'.$post_id.'">'.$st['p'.$post_id]['status_text'].'</span>';
				}

				/*
				<a href="#edit_timestamp" class="edit-timestamp hide-if-no-js" role="button">
					<span aria-hidden="true"><?php echo __( 'More', 'fulltext-search' ); ?></span>
					<span class="screen-reader-text"><?php echo __( 'More actions', 'fulltext-search' ); ?></span>
				</a>
				<fieldset id="timestampdiv" class="hide-if-js">
					<legend class="screen-reader-text"><?php _e( 'Date and time' ); ?></legend>
					
				</fieldset>
				*/ ?>
				</span>
			</div>
			<?php
		});

		if (is_admin()) {
			add_action('admin_menu', 'wpfts_admin_menu');
			add_filter('plugin_row_meta', 'wpfts_plugin_links', 10, 2);
		
			load_plugin_textdomain( 'fulltext-search', false, basename(dirname(__FILE__)).'/languages/');
		
			add_action('admin_enqueue_scripts', 'wpfts_enqueues');

			add_action('wp_ajax_wpftsi_ping', array($wpfts_core, 'ajax_ping'));
			add_action('wp_ajax_wpftsi_submit_testpost', array($wpfts_core, 'ajax_submit_testpost'));
			add_action('wp_ajax_wpftsi_submit_testsearch', array($wpfts_core, 'ajax_submit_testsearch'));
			add_action('wp_ajax_wpftsi_submit_rebuild', array($wpfts_core, 'ajax_submit_rebuild'));
			add_action('wp_ajax_wpftsi_set_pause', array($wpfts_core, 'ajax_set_pause'));
			add_action('wp_ajax_wpftsi_hide_notification', array($wpfts_core, 'ajax_hide_notification'));
			add_action('wp_ajax_wpftsi_se_style_preview', array($wpfts_core, 'ajax_se_style_preview'));
			add_action('wp_ajax_wpftsi_se_style_reset', array($wpfts_core, 'ajax_se_style_reset'));
			add_action('wp_ajax_wpftsi_smartform', array($wpfts_core, 'ajax_smartform'));
		}
		
		do_action('wpfts_init_addons');
	}
});

function wpfts_custom_js()
{
	global $wpfts_core, $wpfts_gstatus;
	
	$wpfts_gstatus = $wpfts_core->get_status();
	$mid = $wpfts_core->get_option('flare_mid');
	
	$is_settings = $wpfts_core->is_wpfts_settings_page ? 1 : 0;

	$lang_texts = array(
		'save_changes' => __('Save Changes', 'fulltext-search'),
		'changes_not_set' => __('Changes was not saved - an error occured!', 'fulltext-search'),
		'link_follows' => __("This link follows to\n\n%s", 'fulltext-search'),
		'reset_styles' => __('This action will reset your custom CSS styles, are you sure?', 'fulltext-search'),
	);

	?><script type="text/javascript">
		var wpfts_pid = "<?php echo $wpfts_core->getPid(); ?>";
		var wpfts_pingtimeout = <?php echo intval($wpfts_core->get_option('ping_period')) * 1000; ?>;
		var wpfts_root_url = "<?php echo $wpfts_core->root_url; ?>";
		var switch_caution_txt = <?php echo json_encode(__("The conversion process will take some time,\nduring which you should stay on this page of the browser.\n\nIf the progress value does not change for more than 2 minutes,\nrefresh the page manually.", 'fulltext-search')); ?>;
		var wpfts_is_settings_screen = <?php echo $is_settings; ?>;
		document.wpfts_settings_main_page = '<?php echo 'admin.php?page=wpfts-options'; ?>';
		document.wpfts_lang_texts = <?php echo json_encode($lang_texts); ?>;
		document.wpfts_mid = "<?php echo addslashes($mid); ?>";
		document.wpfts_last_ts = <?php echo isset($wpfts_gstatus['ts']) ? intval($wpfts_gstatus['ts']) : 0; ?>;
	</script><?php

	$version = (defined('WP_DEBUG') && WP_DEBUG) ? time() : WPFTS_VERSION;

	//$current_tab = isset($_GET['page']) ? $_GET['page'] : 'wpfts-options';
	//if ($current_tab == 'wpfts-options-search-relevance') {
		?>
		<script type="text/javascript">
		var wpfts_se_styles_editor = null;
		jQuery(document).ready(function()
		{
			if (jQuery('#wpfts_se_styles_editor').length > 0) {
				wpfts_se_styles_editor = ace.edit("wpfts_se_styles_editor");
				wpfts_se_styles_editor.setTheme("ace/theme/chrome");
				wpfts_se_styles_editor.session.setMode("ace/mode/css");

				if (wpfts_se_styles_editor && (jQuery('#wpfts_se_styles_editor_hidden').length > 0)) {
					wpfts_se_styles_editor.session.on('change', function()
					{
						jQuery('#wpfts_se_styles_editor_hidden').val(wpfts_se_styles_editor.session.getValue());
						jQuery('#wpfts_se_styles_editor_hidden').trigger('change');
					});
				}
			}

		});
		</script>
		<?php
	//}
}
add_action('admin_head', 'wpfts_custom_js');

function wpfts_frontend_js()
{
	?><script type="text/javascript">
		document.wpfts_ajaxurl = "<?php echo htmlspecialchars(admin_url('admin-ajax.php')); ?>";
	</script><?php
}
add_action('wp_head', 'wpfts_frontend_js');

add_action('widgets_init', function()
{
	require_once dirname(__FILE__).'/includes/widgets/wpfts_widget.class.php';
	register_widget('WPFTS_Custom_Widget');
});

function wpfts_autocomplete_proc()
{
	$widget_code = isset($_POST['wpfts_wdgt']) ? $_POST['wpfts_wdgt'] : '';
	$sq = isset($_POST['sq']) ? $_POST['sq'] : '';

	$res = array();

	//if ($widget_code == 'fecfj093r') {
		// Make a request
		$params = array(
			's' => $sq,
			'wpfts_is_force' => 1,
		);
		if (strlen($widget_code) > 0) {
			$params['wpfts_wdgt'] = $widget_code;
		} else {
			// Set default parameters like WP does (for Main Query)
			$params['post_status'] = 'publish';
		}
		$loop = new WP_Query($params);
		
		global $wpfts_core;
	
		if ($wpfts_core) {
			$wpfts_core->ForceSmartExcerpts($sq);		
		}		
		
		while ($loop->have_posts()) {
			$loop->the_post();
			
			$res[] = array(
				'label' => get_the_title(),
				'link' => get_permalink(),
			);
		}
		wp_reset_query();
	//}

	echo json_encode($res);
	exit();
}

add_action('plugins_loaded', 'wpfts_load_plugin_textdomain');
function wpfts_load_plugin_textdomain() {
	load_plugin_textdomain( 'fulltext-search', false, dirname(plugin_basename(__FILE__)).'/languages/');
}

function wpfts_plugin_links($links, $file)
{
	if (basename($file) == basename(__FILE__)) {
		//$links[] = '<a href="admin.php?page=wpfts-options">'.__('Settings', 'fulltext-search').'</a>';
	}
	return $links;
}

add_filter('plugin_action_links_'.plugin_basename(__FILE__), function($links)
{
	array_unshift($links, '<a href="'.admin_url('admin.php?page=wpfts-options').'">'.__('Settings', 'fulltext-search').'</a>');
	return $links;
});

function wpfts_admin_menu()
{
	global $wpfts_core;

	$is_pro_installed = $wpfts_core->get_option('is_pro_installed');

	//$position = ( ++$GLOBALS['_wp_last_object_menu'] );
	$position = ( ++$GLOBALS['_wp_last_utility_menu'] );
	
	$parent_menu = add_menu_page(__('WP FullText Search', 'fulltext-search'), __('Full Text Search', 'fulltext-search'), 'manage_options', 'wpfts-options', 'wpfts_option_page', 'dashicons-search', $position);

	$menu_items = array(
		'wpfts-options' => array(__('Main Configuration', 'fulltext-search')),
		'wpfts-options-indexing-engine' => array(__('Indexing Engine Settings', 'fulltext-search'), __('Indexing Engine', 'fulltext-search')),
		'wpfts-options-search-relevance' => array(__('Search & Output', 'fulltext-search')),
		'wpfts-options-sandbox-area' => array(__('Sandbox Area', 'fulltext-search')),
		'wpfts-options-analytics' => array(__('Analytics', 'fulltext-search')),
		'wpfts-options-support' => array(__('Support & Docs', 'fulltext-search')),
	);

	if ($is_pro_installed) {
		$menu_items['wpfts-options-licensing'] = array(__('Licensing', 'fulltext-search'));
	}

	$menu_items = apply_filters('wpfts_admin_menu_items', $menu_items);

	foreach ($menu_items as $k => $d) {
		add_submenu_page('wpfts-options', $d[0], (isset($d[1]) && $d[1]) ? $d[1] : $d[0], 'manage_options', $k, (isset($d[2]) && function_exists($d[2])) ? $d[2] : 'wpfts_option_page');
	}
	
	add_filter('plugin_action_links', 'wpfts_settings_link', 10, 2);

	do_action('wpfts_admin_menu');
}

function wpfts_enqueues($hook_suffix)
{
	global $wpfts_core;

	$version = (defined('WP_DEBUG') && WP_DEBUG) ? time() : WPFTS_VERSION;

	$wpfts_core->set_is_settings_page();

	wp_enqueue_style('wpfts_style', plugins_url('style/wpfts_main.css', __FILE__), array(), $version);
	wp_enqueue_script('wpfts_script', plugins_url('js/wpfts_script.js', __FILE__), array(), $version);
	
	if ($wpfts_core->is_wpfts_settings_page) {

		wp_enqueue_style('wpfts_style_bs', plugins_url('style/bs_wpfts.css', __FILE__), array(), $version);
		wp_enqueue_style('wpfts_style_fa', plugins_url('style/fontawesome-all.css', __FILE__), array(), $version);
		wp_enqueue_script('wpfts_script_bs', plugins_url('js/bootstrap.min.js', __FILE__), array(), $version);
	
		wp_enqueue_script('wpfts_ace_script', plugins_url('classes/ace/ace.js', __FILE__), array(), $version);

		// Remove welcome_message
		$wpfts_core->set_option('is_welcome_message', '');

		wp_enqueue_style('wp-pointer');

		wp_enqueue_script('postbox');
		wp_enqueue_script('wp-pointer');
	}
	
	do_action('wpfts_admin_scripts');
}

function wpfts_settings_link($links, $file)
{
	$this_plugin = dirname(plugin_basename(dirname(__FILE__))) . '/fulltext-search.php';
	if ($file == $this_plugin) {
		$links[] = '<a href="admin.php?page=wpfts-options">' . __('Settings', 'fulltext-search' ) . '</a>';
	}
	return $links;
}

function wpfts_option_page()
{
	global $wpfts_core;

	if (!current_user_can('manage_options')) {
		wp_die(__('Sorry, but you do not have permissions to change settings.', 'fulltext-search'));
	}

	/* Make sure post was from this page */
	if (isset($_POST) && (count($_POST) > 0)) {
		check_admin_referer('wpfts-options');
	}

	$wpfts_core->set_option('is_welcome_message', '');
	
	require dirname(__FILE__).'/includes/templates/admin_page.php';
}

function WPFTS_Get_Widget_List() 
{
	global $wpfts_core;

	if ($wpfts_core && is_object($wpfts_core) && (is_a($wpfts_core, 'WPFTS_Core'))) {
		return $wpfts_core->GetWidgetPresets();
	}

	return array();
}

/**
 * Called when any post/page/etc updated or created
 * 
 * We need to reindex the post by this action
 */
function wpfts_save_post_action($post_id)
{
	wpfts_post_reindex($post_id);
}
add_action('save_post', 'wpfts_save_post_action', 99);

/**
 * Called when any post/page/etc was deleted
 * 
 * We need to delete the post from the index by this action
 */
function wpfts_deleted_post_action($post_id)
{
	wpfts_post_reindex($post_id);
}
add_action('after_delete_post', 'wpfts_deleted_post_action', 99);

function wpfts_post_reindex($post_id, $is_force_remove = false, $is_raw_cache_remove = false)
{
	global $wpfts_core;
	
	if ($is_raw_cache_remove) {
		$wpfts_core->removeRawCache($post_id);
	}

	// First, let's force sync
	$wpfts_core->MakePostsSync(true);

	// Make some magic to force this post indexed first
	$q = 'update `wpftsi_index` set force_rebuild = 2 where tid = "'.addslashes($post_id).'" and tsrc = "wp_posts"';
	$wpfts_core->db->query($q);

	// Force status recalculation
	$wpfts_core->set_option('status_next_ts', 0);

	// Break current loop to start over
	$wpfts_core->set_option('is_break_loop', 1);

	// Ensure loop is starting
	$wpfts_core->CallIndexerStartNoBlocking();

	/*
	$res = $wpfts_core->reindex_post($post_id, $is_force_remove);
	
	if (!$res) {
		trigger_error('Error reindex post ID='.$post_id.': '.$wpfts_core->index_error, E_USER_NOTICE);
		return false;
	}
	*/
	
	return true;
}

function wpfts_set_pause($is_on = true, $is_start_indexer = false)
{
	global $wpfts_core;

	if ($wpfts_core) {
		return $wpfts_core->SetPause($is_on, $is_start_indexer);
	}

	return false;
}

/** Smart Excerpts filters */
add_filter('the_title', function($out)
{
	global $wpfts_core;

	if ($wpfts_core && is_a($wpfts_core, 'WPFTS_Core')) {
		$is_smart_excerpts = intval($wpfts_core->get_option('is_smart_excerpts'));
		if ($is_smart_excerpts != 0) {
		
			$loop_or_block = in_the_loop();
			if (!$loop_or_block) {
				if (class_exists('WP_Block_Supports')) {
					if (isset(WP_Block_Supports::$block_to_render['blockName'])) {
						$bn = WP_Block_Supports::$block_to_render['blockName'];
						if (preg_match('~\/post\-title$~', $bn)) {
							$loop_or_block = true;
						}
					}
				}
			}
			
			if ((is_search() && !is_admin() && $loop_or_block) || ($wpfts_core->forced_se_query !== false)) {
				$post_id = get_the_ID();
				$ri = new WPFTS_Result_Item($post_id);
				return $ri->TitleText($out);
			}
		}
	}

	return $out;
});

add_filter('attachment_link', function($link, $post_id)
{
	global $wpfts_core;

	if ($wpfts_core && is_a($wpfts_core, 'WPFTS_Core')) {
		$is_smart_excerpts = intval($wpfts_core->get_option('is_smart_excerpts'));
		if ($is_smart_excerpts != 0) {
		
			$loop_or_block = in_the_loop();
			if (!$loop_or_block) {
				if (class_exists('WP_Block_Supports')) {
					if (isset(WP_Block_Supports::$block_to_render['blockName'])) {
						$bn = WP_Block_Supports::$block_to_render['blockName'];
						if (preg_match('~\/post\-title$~', $bn)) {
							$loop_or_block = true;
						}
					}
				}
			}			
		
			if ((is_search() && !is_admin() && $loop_or_block) || ($wpfts_core->forced_se_query !== false)) {
				$ri = new WPFTS_Result_Item($post_id);
				return $ri->TitleLink($link);
			}
		}
	}

	return $link;
}, 10, 2);

add_filter('page_link', function($link, $post_id, $leavename)
{
	global $wpfts_core;

	if ($wpfts_core && is_a($wpfts_core, 'WPFTS_Core')) {
		$is_smart_excerpts = intval($wpfts_core->get_option('is_smart_excerpts'));
		if ($is_smart_excerpts != 0) {
		
			$loop_or_block = in_the_loop();
			if (!$loop_or_block) {
				if (class_exists('WP_Block_Supports')) {
					if (isset(WP_Block_Supports::$block_to_render['blockName'])) {
						$bn = WP_Block_Supports::$block_to_render['blockName'];
						if (preg_match('~\/post\-title$~', $bn)) {
							$loop_or_block = true;
						}
					}
				}
			}			
		
			if ((is_search() && !is_admin() && $loop_or_block) || ($wpfts_core->forced_se_query !== false)) {
				$ri = new WPFTS_Result_Item($post_id);
				return $ri->TitleLink($link);
			}
		}
	}

	return $link;
}, 10, 3);

add_filter('post_type_link', function($link, $post, $leavename)
{
	global $wpfts_core;

	if ($wpfts_core && is_a($wpfts_core, 'WPFTS_Core')) {
		$is_smart_excerpts = intval($wpfts_core->get_option('is_smart_excerpts'));
		if ($is_smart_excerpts != 0) {
		
			$loop_or_block = in_the_loop();
			if (!$loop_or_block) {
				if (class_exists('WP_Block_Supports')) {
					if (isset(WP_Block_Supports::$block_to_render['blockName'])) {
						$bn = WP_Block_Supports::$block_to_render['blockName'];
						if (preg_match('~\/post\-title$~', $bn)) {
							$loop_or_block = true;
						}
					}
				}
			}
					
			if ((is_search() && !is_admin() && $loop_or_block) || ($wpfts_core->forced_se_query !== false)) {
				$ri = new WPFTS_Result_Item($post->ID);
				return $ri->TitleLink($link);
			}
		}
	}

	return $link;
}, 10, 3);

add_filter('post_link', function($link, $post, $leavename)
{
	global $wpfts_core;

	if ($wpfts_core && is_a($wpfts_core, 'WPFTS_Core')) {
		$is_smart_excerpts = intval($wpfts_core->get_option('is_smart_excerpts'));
		if ($is_smart_excerpts != 0) {
		
			$loop_or_block = in_the_loop();
			if (!$loop_or_block) {
				if (class_exists('WP_Block_Supports')) {
					if (isset(WP_Block_Supports::$block_to_render['blockName'])) {
						$bn = WP_Block_Supports::$block_to_render['blockName'];
						if (preg_match('~\/post\-title$~', $bn)) {
							$loop_or_block = true;
						}
					}
				}
			}
					
			if ((is_search() && !is_admin() && $loop_or_block) || ($wpfts_core->forced_se_query !== false)) {
				$ri = new WPFTS_Result_Item($post->ID);
				return $ri->TitleLink($link);
			}
		}
	}

	return $link;
}, 10, 3);

add_filter('get_the_excerpt', function($out)
{
	global $wpfts_core;

	if ($wpfts_core && is_a($wpfts_core, 'WPFTS_Core')) {

		$is_smart_excerpts = intval($wpfts_core->get_option('is_smart_excerpts'));
		if ($is_smart_excerpts != 0) {

			$post_id = get_the_ID();
			
			$loop_or_block = in_the_loop();
			if (!$loop_or_block) {
				if (class_exists('WP_Block_Supports')) {
					if (isset(WP_Block_Supports::$block_to_render['blockName'])) {
						$bn = WP_Block_Supports::$block_to_render['blockName'];
						if (preg_match('~\/post\-excerpt$~', $bn)) {
							$loop_or_block = true;
						}
					}
				}
			}

			if (is_search() && !is_admin() && $loop_or_block) {

				$ri = new WPFTS_Result_Item($post_id);
				$query = get_search_query(false);
				$out = '<div class="wpfts-result-item">'.$ri->Excerpt($query).'</div>';
				return $out;
			} elseif ($wpfts_core->forced_se_query !== false) {
				$ri = new WPFTS_Result_Item($post_id);
				$query = $wpfts_core->forced_se_query;
				$out = '<div class="wpfts-result-item">'.$ri->Excerpt($query).'</div>';
				return $out;
			} else {
				// Leave excerpt unchanged
			}
		}
	}

	return $out;
});
