<?php

/**  
 * Copyright 2013-2022 Epsiloncool
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
 *  @package Wordpress Fulltext Search
 *  @author Epsiloncool <info@e-wm.org>
 */

require_once dirname(__FILE__).'/wpfts_index.php';
require_once dirname(__FILE__).'/wpfts_jx.php';
require_once dirname(__FILE__).'/wpfts_htmltools.php';
require_once dirname(__FILE__).'/wpfts_output.php';
require_once dirname(__FILE__).'/wpfts_result_item.php';
require_once dirname(__FILE__).'/wpfts_shortcodes.php';
require_once dirname(__FILE__).'/wpfts_semaphore.php';
require_once dirname(__FILE__).'/wpfts_flare.php';
require_once dirname(__FILE__).'/wpfts_db.php';
require_once dirname(__FILE__).'/wpfts_querylog.php';
require_once dirname(__FILE__).'/updater/updater.php';

class WPFTS_Core
{
	protected $_index = null;
	
	protected $_pid = false;
	
	public $_wpfts_domain = 'https://fulltextsearch.org';
	public $_documentation_link = '/documentation';
	public $_forum_link = '/forum/';
	public $_flare_url = 'https://fulltextsearch.org/fire';
	
	public $root_url;
	public $root_dir;

	public $db = null;	// WPDB wrapper (soft mode)
	
	public $index_error = '';
	
	public $forced_se_query = false;

	public $defer_indexing = false;

	public $widget_presets = array();

	public $tablist = array('vectors', 'words', 'index', 'docs', 'rawcache');
	
	public $is_wpfts_settings_page = false;

	public $se_css_cache_timeout = 600;	// 10 min
	public $se_css_tr_name = 'wpfts_se_css_transient';
	
	public $_flare = false;

	public $_dev_debug = false;

	public $is_mariadb = false;
	public $mysql_version = '';

	public function __construct()
	{
		$this->_index = new WPFTS_Index();
		$this->_flare = new WPFTS_Flare($this->_flare_url);
		$this->db = new WPFTS_DB();
		$this->_querylog = new WPFTS_QueryLog();
		
		if (is_admin()) {
			add_action('admin_notices', array($this, 'admin_notices'));
		}

		$this->root_url = dirname(plugins_url('', __FILE__));
	}
	
	public function Init()
	{
		// Reinstall cron hooks
		$this->installCronIndexerTask();
		
		// Install/init flare service
		$mid = $this->get_option('flare_mid');
		if (strlen($mid) < 1) {
			$mid = WPFTS_Flare::MakeUniqueMediumID();
			$this->set_option('flare_mid', $mid);
		}
		$this->_flare->mid = $mid;
		
		// Get basic MySQL info
		$q = 'select version() v';
		$res = $this->db->get_results($q, ARRAY_A);

		$is_mariadb = false;
		$mysql_version = '';
		if (isset($res[0]['v'])) {
			$version = $res[0]['v'];

			$zz = array();
			if (preg_match('~(\d+\.\d+\.\d+)~', $version, $zz)) {
				$mysql_version = $zz[1];
			}
			if (preg_match('~(maria)~i', $version, $zz)) {
				$is_mariadb = true;
			}
		}

		$this->is_mariadb = $is_mariadb;
		$this->mysql_version = $mysql_version;

		if ($this->is_mariadb && (intval($this->get_option('is_fixmariadb')) != 0)) {
			if (version_compare($this->mysql_version, '10.3.0', '>')) {
				// Apply the patch
				$q = "set optimizer_switch='split_materialized=off'";
				$this->db->query($q);
			}
		}

		$upds = $this->GetUpdates();

		if ($upds['is_new']) {
			// New installation
			$this->set_option('index_ready', 0);	// Disable index
			//$this->get_option('is_welcome_message', json_encode($upds['texts']));	// Welcome message
		} else {
			if ($upds['is_rebuild']) {
				// DB is outdated
				// Require DB update
				$this->set_option('index_ready', 0);	// Disable index
				$this->set_option('is_db_outdated', 1);	// Set outdated flag
			} else {
				$this->set_option('is_db_outdated', 0);	// Ok with DB version
			}

			if ($upds['callback'] && (count($upds['callback']) > 0)) {
				// Let's execute callbacks
				$is_success = true;
				foreach ($upds['callback'] as $cb) {
					if (is_callable($cb)) {
						try {
							$is_success = call_user_func($cb);
						} catch (Exception $e) {
							$is_success = false;
						}
						if (!$is_success) {
							break;
						}
					}
				}
				if (!$is_success) {
					// Switch off the index and propose user to rebuild the index (worst case)
					$this->set_option('index_ready', 0);	// Disable index
					$this->set_option('is_db_outdated', 1);	// Set outdated flag
				} else {
					// All is fine
				}
				// We make code changes only once
				$this->set_option('current_cb_version', WPFTS_VERSION);
			}
		}

		$this->FeatureDetector();
	}
	
	public function GetIndex()
	{
		return $this->_index;
	}

	public function set_is_settings_page()
	{
		// If we are on the WPFTS Settings Pages?
		$screen = get_current_screen();
		$hook_suffix = !is_null($screen) ? $screen->id : '';

		$is_wpfts_settings_page = false;
		if (preg_match('~^full\-text\-search_page_wpfts\-options\-~', $hook_suffix)) {
			$is_wpfts_settings_page = true;
		} else {
			if ($hook_suffix == 'toplevel_page_wpfts-options') {
				$is_wpfts_settings_page = true;
			}
		}
		$this->is_wpfts_settings_page = $is_wpfts_settings_page;
	}

	public function admin_notices()
	{
		$is_pro_installed = $this->get_option('is_pro_installed');

		if ($is_pro_installed) {
			$this->LicenseExpirationWarning();
		}

		$is_all_great = true;

		if ((strlen($this->get_option('is_welcome_message')) > 0) && (!$this->is_wpfts_settings_page)) {
			// Welcome message
			$text = json_decode($this->get_option('is_welcome_message'), true);
			if (isset($text[0])) {
				$s = $text[0];
				$this->output_admin_notice($s, 'notice notice-success is-dismissible wpfts-notice', 'welcome_message');

				$is_all_great = false;
			}
		}

		// Let's check if we have something to say!
		$text = json_decode($this->get_option('change_log'), true);

		if (is_array($text) && (count($text) > 0)) {
			$s = __('<b>WP Fulltext Search</b> new changes:', 'fulltext-search').'<br><br>';
			$a = array();
			foreach ($text as $k => $d) {
				$a[] = sprintf(__('In the version <b>%s</b>:', 'fulltext-search'), $k).'<br>'.$d;
			}
			$s .= implode('<br>', $a);
			$this->output_admin_notice($s, 'notice notice-success is-dismissible wpfts-notice', 'change_log');

			$is_all_great = false;
		}

		if (intval($this->get_option('is_db_outdated'))) {

			$is_all_great = false;

			if ($this->is_wpfts_settings_page) {
				// DB update required message (for internal pages)
				$s = __('<b style="color: red;">The plugin\'s database requires update.</b><br>It is necessary to rebuild the index to ensure the correct operation of the search engine. This may take some time.<br><br>Click <a href="#" class="btn_notify_start_indexing">here</a> to rebuild the search index now.', 'fulltext-search');

				$this->output_admin_notice($s, 'notice notice-warning wpfts-notice', 'db_update');
			} else {
				// DB update required message (for ext pages)
				$s = __('<b style="color: red;">Attention!</b> <b>WP Fulltext Search</b> plugin requires your attention.<br><br>Please <a href="admin.php?page=wpfts-options">click HERE</a> to go to WPFTS Settings page.', 'fulltext-search');

				$this->output_admin_notice($s, 'notice notice-warning wpfts-notice', 'db_update');
			}
		}
	
		if ($is_all_great) {
			if ($is_pro_installed) {		
				// This is a time to show license/network message
				$s = $this->get_option('wpftslic_message');
				if (strlen($s) > 0) {
					$pp_type = ($this->get_option('wpftslic_message_type') == 1) ? 'notice-error' : 'notice-warning';
					$this->output_admin_notice($s, 'notice '.$pp_type.' is-dismissible wpfts-notice', 'wpftslic_message', true);
					$is_all_great = false;
				}
			}
		}

		if ($is_all_great) {
			// We can show other messages
			$s = $this->get_option('detector_message');
			if (strlen($s) > 0) {
				// Detector message
				$this->output_admin_notice($s, 'notice notice-warning is-dismissible wpfts-notice', 'detector_message');
			}

			$s = $this->get_option('detector2_message');
			if (strlen($s) > 0) {
				// Detector2 message
				$this->output_admin_notice($s, 'notice notice-warning is-dismissible wpfts-notice', 'detector2_message');
			}
		}
	}

	public function GetUpdates()
	{
		$current_db_version = $this->get_option('current_db_version');
		$current_code_version = $this->get_option('current_cb_version');
		$actual_version = WPFTS_VERSION;

		$is_pro = (version_compare($actual_version, '2.0.0', '>=')) ? 1 : 0;

		$is_pro_installed = $this->get_option('is_pro_installed');

		if ($is_pro_installed) {
			if ($this->is_wpfts_settings_page) {
				// Simple Initial Wizard
				return array(
					'is_new' => true,
					'is_rebuild' => true,
					'text' => array(__('<h2>Initial Configuration Wizard</h2> First of all, thank you for your support by purchasing the copy of the WPFTS Pro plugin. Thus you are supporting the plugin development and the whole Open Source code idea.', 'fulltext-search')),
				);
			} else {
				// Show Welcome message
				return array(
					'is_new' => true,
					'is_rebuild' => true,
					'text' => array(__('<b style="color: red;">Congratulations!</b> <b>WP FullText Search Pro plugin</b> has just been installed!<br><br>To complete the installation, please follow some steps on the <a href="admin.php?page=wpfts-options">WPFTS Settings Page</a>', 'fulltext-search')),
				);
			}
		}

		if (strlen($current_db_version) > 0) {
			// Check if we have an actual version of the database
			$changes = array(
				'is_new' => false,
				'is_rebuild' => false,
				'db_changes' => array(),
				'text' => array(),
				'callback' => array(),
			);

			// We started to track code changes from the 1.37.101 and 2.40.151
			if (strlen($current_code_version) < 1) {
				$current_code_version = $is_pro ? '2.40.151' : '1.37.101';
			}

			// Get all changes from the 'version' folder
			$list_files = array();

			$path_version = dirname(__FILE__).'/version/';
			$old_dir = getcwd();
			chdir($path_version);
			foreach (glob('*.php') as $file) {
				$zz = array();
				if (preg_match('~^(\d+_\d+_\d+)\.php$~', $file, $zz)) {
					// If this version good for us?
					$v = str_replace('_', '.', $zz[1]);
					$f_db = version_compare($v, $current_db_version, '>') ? 1 : 0;
					$f_code = version_compare($v, $current_code_version, '>') ? 1 : 0;
					if (($f_db || $f_code) && version_compare($v, $actual_version, '<=')) {
						// Okay, good
						$list_files[$v] = array($file, $f_db, $f_code);
					}
				}
			}
			chdir($old_dir);

			if (count($list_files) > 0) {
				// Let's reorder versions from lower to higher
				$versions = array_keys($list_files);
				usort($versions, function($v1, $v2)
				{
					if (version_compare($v1, $v2, '<')) {
						return -1;
					} else {
						return 1;
					}
				});

				foreach ($versions as $k_version) {

					$data = false;
					try {
						$data = include($path_version.$list_files[$k_version][0]);
					} catch (Exception $e) {
						$data = false;
					}

					if (is_array($data)) {
						// Ok, now iterate till the actual version, inclusive
						if ($list_files[$k_version][1]) {
							// DB changes
							if (isset($data['is_rebuild']) && ($data['is_rebuild'])) {
								$changes['is_rebuild'] = true;
							}
							if (isset($data['db_changes']) && (count($data['db_changes']) > 0)) {
								$changes['db_changes'] = array_merge($changes['db_changes'], $data['db_changes']);
							}	
							if (isset($data['text']) && (strlen($data['text']) > 0)) {
								$changes['text'][$k_version] = $data['text'];
							}
						}
						if ($list_files[$k_version][2]) {
							if (isset($data['callback']) && is_callable($data['callback'])) {
								$changes['callback'][$k_version] = $data['callback'];
							}
						}
					}

				}

			}

			return $changes;
		} else {
			// No updates (just create a new database and reindex)
			return array(
				'is_new' => true,
				'is_rebuild' => true,
				'text' => array(__('<b style="color: red;">Congratulations!</b> <b>WP FullText Search plugin</b> has just been installed and successfully activated!<br><br>To complete the installation, we need to create the Search Index of your existing WP posts data. To start this process, simply go to the <a href="admin.php?page=wpfts-options">WPFTS Settings Page</a>', 'fulltext-search')),
			);
		}
	}

	public function output_admin_notice($text, $type = 'error', $n_id = '', $is_raw = false)
	{
		if ($is_raw) {
			?>
			<div class="<?php echo $type; ?>" data-notificationid="<?php echo $n_id; ?>"><?php echo $text; ?></div>
			<?php
		} else {
			?>
		    <div class="<?php echo $type; ?>" data-notificationid="<?php echo $n_id; ?>"><p><?php echo $text; ?></p></div>
			<?php
		}
	}

	public function network_actdeact($pfunction, $networkwide) 
	{
		global $wpdb;
	 
		if (function_exists('is_multisite') && is_multisite()) {
			// Multisite activation
			if ($networkwide) {
				$old_blog = $wpdb->blogid;
				$blogids = $wpdb->get_col('SELECT blog_id FROM '.$wpdb->blogs);
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					call_user_func($pfunction, $networkwide);
				}
				switch_to_blog($old_blog);
				return;
			}   
		}
		// One site activation
		call_user_func($pfunction, $networkwide);
	}
	 
	public function activate_plugin($networkwide) 
	{
		$this->network_actdeact(array(&$this, '_activate_plugin'), $networkwide);
	}
	 
	public function deactivate_plugin($networkwide) 
	{
		$this->network_actdeact(array(&$this, '_deactivate_plugin'), $networkwide);
	}

	public function _activate_plugin($networkwide = false)
	{
		if (!function_exists('register_post_status')) {
			deactivate_plugins(basename(dirname( __FILE__ )).'/'.basename (__FILE__));
			wp_die( __( "This plugin requires WordPress 3.0 or newer. Please update your WordPress installation to activate this plugin.", 'fulltext-search' ));
		}

		if ((isset($_GET['action'])) && ($_GET['action'] == 'error_scrape')) {
			// Showing error
			echo __('Error: ', 'fulltext-search').$this->get_option('activation_error');
			//$this->set_option('activation_error', '');
			
		} else {

			// Check db
			$this->_index->clearLog();

			$upds = $this->GetUpdates();

			if ($upds['is_new']) {
				// New installation
				$this->set_option('index_ready', 0);	// Disable index
				$this->set_option('is_welcome_message', json_encode($upds['text']));	// Welcome message
			} else {
				if ($upds['is_rebuild']) {
					// DB is outdated
					// Require DB update
					$this->set_option('index_ready', 0);	// Disable index
					$this->set_option('is_db_outdated', 1);	// Set outdated flag
				} else {
					$this->set_option('is_db_outdated', 0);	// Ok with DB version
				}

				if (isset($upds['text']) && (is_array($upds['text'])) && (count($upds['text']) > 0)) {
					$this->set_option('change_log', json_encode($upds['text']));
				}
			}

			// Reinstall scheduler
			$this->removeCronIndexerTask();
			$this->installCronIndexerTask();
		}
	}
	
	public function removeCronIndexerTask()
	{
		wp_clear_scheduled_hook('wpfts_indexer_event');
	}

	public function installCronIndexerTask()
	{
		if (!wp_next_scheduled('wpfts_indexer_event')) {
			wp_schedule_event( time(), 'wpfts_each_minute', 'wpfts_indexer_event');
		}
	}

	public function _deactivate_plugin($networkwide = false) 
	{
		$this->removeCronIndexerTask();

		// Break indexer loop
		$this->set_option('is_break_loop', 1);
	}
	
	public function getPid()
	{
		if (!$this->_pid) {
			$this->_pid = sha1(time().uniqid());
		}
		
		return $this->_pid;
	}
	
	public function get_post_types()
	{
		$post_types = get_post_types('', 'objects');

		$z = array();
		foreach ($post_types as $k => $d) {
			$z[$k] = isset($d->labels->singular_name) ? $d->labels->singular_name : $k;
		}

		return $z;
	}
	
	public function get_cluster_types()
	{
		return $this->_index->getClusters();
	}

	protected function default_options() 
	{
		$default_options = array(
			'enabled' => 1,
			'autoreindex' => 1,
			'index_ready' => 0,
			'deflogic' => 1, // OR
			'minlen' => 3,
			'maxrepeat' => 80, // 80%
			'stopwords' => '',
			'epostype' => '',
			'cluster_weights' => serialize(array(
				'post_title' => 0.8,
				'post_content' => 0.5,
			)),
			'testpostid' => '',
			'testquery' => '',
			'tq_disable' => 0,
			'tq_nocache' => 1,
			'tq_post_status' => 'any',
			'tq_post_type' => 'any',
			'tq_perpage' => 25,
			'rebuild_time' => 0,
			'process_time' => '0|',
			'ping_period' => 30,
			'est_time' => '00:00:00',
			'internal_search_terms' => 1,
			'include_attachments' => 1,
			'content_open_shortcodes' => 1,
			'content_is_remove_nodes' => 1,
			'deeper_search' => 0,
			'display_attachments' => 1,
			'is_welcome_message' => '',
			'current_db_version' => '',
			'current_cb_version' => '',
			'is_db_outdated' => 0,
			'mainsearch_orderby' => 'relevance',
			'mainsearch_order' => 'DESC',
			'is_smart_excerpts' => 1,
			'is_smart_excerpt_text' => 1,
			'is_show_score' => 1,
			'is_not_found_words' => 1,
			'optimal_length' => 300,
			'custom_se_css' => '_get_css_file_',
			'q_id' => 1000,
			'qlog_settings' => '',
			'preset_selector' => serialize(array(
				'wpmainsearch_frontend' => 'frontend_default',
			)),
			'presetdata_frontend_default' => serialize(array(
				'ident' => 'frontend_default',
				'name' => 'Default Frontend Search',
				'is_wpfts_search_enabled' => 1,
			)),
			
			'flare_mid' => '',
			'last_sync_ts' => 0,	// The timestamp of the last sync
			'is_break_loop' => 0,
			'is_pause' => 0,
			'ts_series' => '',

			'is_wpadmin' => 0,
			'is_fixmariadb' => 1,
			'is_optimizer' => 0,
			'status_next_ts' => 0,
			'status_cache' => '',
			'last_indexerstart_ts' => 0,

			'activation_error' => '',
			'subscription_key' => '',
			'is_pro_installed' => 0,
			'limit_mimetypes' => '',

			'change_log' => '',
			'content_strip_tags' => 1,
			'detector_message' => '',
			'detector_message_expdt' => '',
			'detector2_message' => '',
			'detector2_message_expdt' => '',
			'wpftslic_message' => '',
			'wpftslic_message_type' => 0,
			'wpftslic_message_key' => '',
			'wpftslic_message_expdt' => '',			
		);

		return apply_filters('wpfts_default_options', $default_options);
	}
	
	public function get_option($optname, $is_force_reread = false)
	{
		$defaults = $this->default_options();

		$wp_optname = 'wpfts_'.$optname;

		if ($is_force_reread && isset($GLOBALS['wp_object_cache']) && is_object($GLOBALS['wp_object_cache'])) {
			$GLOBALS['wp_object_cache']->delete($wp_optname , 'options');
		}

		$v = get_option($wp_optname, isset($defaults[$optname]) ? $defaults[$optname] : false);

		if (substr($optname, 0, 11) == 'presetdata_') {
			$v = ($v && (strlen($v) > 0)) ? @unserialize($v) : array();
		} else {
			switch ($optname) {
				case 'epostype':
					$v = (strlen($v) > 0) ? @unserialize($v) : array();
					break;
				case 'cluster_weights':
					$v = (strlen($v) > 0) ? @unserialize($v) : array();
					// We have to have post_title and post_content
					if (!isset($v['post_title'])) {
						$v['post_title'] = 0.8;
					}
					if (!isset($v['post_content'])) {
						$v['post_content'] = 0.5;
					}
					break;
				case 'custom_se_css':
					if ($v == '_get_css_file_') {
						// Read from file
						$v = $this->ReadSEStyles();
					}
					break;
				case 'preset_selector':
					$v = (strlen($v) > 0) ? @unserialize($v) : array();
					break;
			}	
		}

		return apply_filters('wpfts_get_option', $v, $optname, $is_force_reread);
	}

	public function set_option($optname, $value)
	{
		$defaults = $this->default_options();
		
		if (isset($defaults[$optname])) {
			// Allowed option
			$v = $value;
			switch ($optname) {
				case 'epostype':
				case 'cluster_weights':
					$v = serialize($value);
					break;
			}
			
			$option_name = 'wpfts_'.$optname;
			if (get_option($option_name, false) !== false) {
				update_option($option_name, $v);
			} else {
				add_option($option_name, $v, '', 'no');
			}			
			return true;
		} else {
			// Not allowed option
			return false;
		}
	}
	
	public function FeatureDetector()
	{
		global $wpdb;

		if (version_compare(WPFTS_VERSION, '2.0.0', '<')) {

			$pro_supported_mimes = array(
				'text/rtf' => 'RTF',
				'application/rtf' => 'RTF',
				'application/pdf' => 'Portable Document (PDF)',
				'application/msword' => 'Microsoft Word (DOC)',
				'application/vnd.ms-excel' => 'Microsoft Excel (XLS)',
				'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Microsoft Word (DOCX)',
				'application/vnd.ms-excel.sheet.macroEnabled.12' => 'XLSM',
				'application/vnd.ms-excel.sheet.binary.macroEnabled.12' => 'XLSB',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.template' => 'XLTX',
				'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'PowerPoint (PPTX)',
				'application/vnd.openxmlformats-officedocument.presentationml.template' => 'POTX',
				'application/vnd.oasis.opendocument.text' => 'Open Document (ODT)',
				'application/vnd.oasis.opendocument.presentation' => 'Open Document Presentation (ODP)',
				'application/vnd.oasis.opendocument.spreadsheet' => 'Open Document Spreadsheet (ODS)',
				'application/vnd.oasis.opendocument.graphics' => 'Open Document Graphics (ODG)',
				'application/epub+zip' => 'EPUB',
				'application/vnd.oasis.opendocument.text-template' => 'Open Document Template (OTT)',
				'application/vnd.oasis.opendocument.graphics-template' => 'Open Document Template (OTG)',
				'application/vnd.oasis.opendocument.presentation-template' => 'Open Document Template (OTP)',
				'application/vnd.oasis.opendocument.spreadsheet-template' => 'Open Document Template (OTS)',
			);
			$mimes_q = array();
			foreach ($pro_supported_mimes as $k => $d) {
				$mimes_q[] = '"'.$k.'"';
			}

			$expdt = $this->get_option('detector_message_expdt');

			if (strlen($expdt) > 0) {
				if (strtotime($expdt) < current_time('timestamp')) {
					// Ok, let's check now
					$q = 'select 
							post_mime_type, 
							count(*) n 
						from `'.$wpdb->posts.'` 
						where 
							(post_type = "attachment") and 
							post_mime_type in ('.implode(',', $mimes_q).')
						group by post_mime_type 
						order by n desc';
					$res2 = $this->db->get_results($q, ARRAY_A);

					if ((count($res2) > 0) && ($res2[0]['n'] >= 10)) {
						// Found something
						$notify_text = '<p>'.sprintf(__('<b>Important Notice:</b> WP Fulltext Search plugin has detected <b>%s</b> files of the type <b>%s</b>', 'fulltext-search'), $res2[0]['n'], $pro_supported_mimes[$res2[0]['post_mime_type']]);
						if (count($res2) > 1) {
							$notify_text .= ' '.__('and other supported files', 'fulltext-search');
						}
						$notify_text .= '.</p>';
						$notify_text .= '<p>'.sprintf(__('<a href="%s" target="_blank">Click here</a> to learn how to make them searchable by their <b>text content</b> and improve your website usability.', 'fulltext-search'), 'https://fulltextsearch.org').'</p>';
	
						$notify_text .= '<p>&nbsp;</p>';
						$notify_text .= '<p><a href="https://fulltextsearch.org" target="_blank" class="button">'.__('Learn More', 'fulltext-search').'</a> <span style="margin-left: 20px;text-decoration: underline;color: #888;cursor: pointer;" class="dismiss-link">'.__("Don't Show Again", 'fulltext-search').'</span></p>';

						$this->set_option('detector_message', $notify_text);
					
						// Recheck in a week if not dismissed
						$this->set_option('detector_message_expdt', date('Y-m-d H:i:s', current_time('timestamp') + 3600 * 24 * 7));
					
					} else {
						// Nothing were found, let's delay for a week
						$this->set_option('detector_message_expdt', date('Y-m-d H:i:s', current_time('timestamp') + 3600 * 24 * 7));
					}
			
				} else {
					// No need to check now
				}
			} else {
				// Never processed yet
				$this->set_option('detector_message_expdt', date('Y-m-d H:i:s', current_time('timestamp') + 15 * 60));
			}
		}
	
		// Detect x64
		$expdt = $this->get_option('detector2_message_expdt');

		if (strlen($expdt) > 0) {
			if (strtotime($expdt) < current_time('timestamp')) {
				// Ok, let's check now
				if (PHP_INT_SIZE == 8) {
					// Okay, x64 is supported!
				} else {
					// x86 does not supported. Let's warn about this
					$notify_text = '<p>'.__('<b>Important Notice:</b> WP Fulltext Search plugin is optimized for x64 platforms, however your website is currently using 32-bit version of PHP.', 'fulltext-search').'</p>';

					$notify_text .= '<p>'.__('As a result, we are forced to use x64 software emulation, which decreases performance and increases search time.', 'fulltext-search').'</p>';

					$notify_text .= '<p>'.__('Please contact your hosting provider to migrate your website to a modern <b>x64</b> server. This will speed up the search by 20-30%, and possibly even more.', 'fulltext-search').'</p>';

					$notify_text .= '<p>&nbsp;</p>';
					$notify_text .= '<p><span style="text-decoration: underline;color: #888;cursor: pointer;" class="dismiss-link">'.__("Don't Show Again", 'fulltext-search').'</span></p>';

					$this->set_option('detector2_message', $notify_text);
				
					// Recheck in a week if not dismissed
					$this->set_option('detector2_message_expdt', date('Y-m-d H:i:s', current_time('timestamp') + 3600 * 24 * 7));

				}
			} else {
				// No need to check now
			}
		} else {
			// Never processed yet
			$this->set_option('detector2_message_expdt', date('Y-m-d H:i:s', current_time('timestamp') + 5 * 60));
		}
	}

	public function LicenseExpirationWarning()
	{
		// Check if license is about to expire or already expired
		$lic_check = WPFTS_Updater::GetLicenseMessage();
	}

	public function ReadSEStyles()
	{
		$style_fn = dirname(__FILE__).'/../style/wpfts_front_styles.css';
		if (is_file($style_fn) && file_exists($style_fn)) {
			return file_get_contents($style_fn);
		} else {
			return '/'.'* '.__('Unable to find default stylesheet file', 'fulltext-search').' *'.'/';
		}
	}

	public function MinimizeSEStyle($buffer)
	{
		// Remove HTML (just in case!)
		$buffer = strip_tags($buffer);
		// Remove comments
		$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
		// Remove space after colons
		$buffer = str_replace(': ', ':', $buffer);
		// Remove whitespace
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);

		return $buffer;
	}

	public function ReadSEStylesMinimized($force_reset = false)
	{
		$tr_name = $this->se_css_tr_name;
		$css = get_transient($tr_name);
		if (($css === false) || $force_reset) {
			// Ok, let's create new one
			$css = $this->MinimizeSEStyle($this->get_option('custom_se_css'));
			set_transient($tr_name, $css, $this->se_css_cache_timeout);
		}
		return $css;
	}

	public function checkAndSyncWPPosts()
	{
		return $this->_index->checkAndSyncWPPosts($this->get_option('rebuild_time'));
	}
	
	public function get_status($is_reread_options = false) 
	{	
		$st = $this->_index->get_status();
		$st['is_pause'] = $this->get_option('is_pause', $is_reread_options);
		$st['enabled'] = intval($this->get_option('enabled', $is_reread_options));
		$st['index_ready'] = intval($this->get_option('index_ready', $is_reread_options));
		$st['autoreindex'] = intval($this->get_option('autoreindex', $is_reread_options));

		$st['est_time'] = '--:--:--';
		$ts_series = trim($this->get_option('ts_series', $is_reread_options));
		if (strlen($ts_series) > 2) {
			$aa = json_decode($ts_series, true);
			if (is_array($aa) && (count($aa) == 3)) {
				// Calculate est_time
				$t_avg = ($aa[1] - $aa[0]) / $aa[2];

				if (isset($st['n_pending'])) {
					$est_seconds = intval($t_avg * intval($st['n_pending']));

					$est_h = intval($est_seconds / 3600);
					$est_m = intval(($est_seconds - $est_h * 3600) / 60);
					$est_s = ($est_seconds - $est_h * 3600) % 60;
					$st['est_time'] = sprintf('%02d:%02d:%02d', $est_h, $est_m, $est_s);
				}
			}
		}

		$time = time();
		$st['ts'] = $time;
		
		$last_indexerstart_ts = intval($this->get_option('last_indexerstart_ts', $is_reread_options));
		$st['is_inx_outdated'] = ($last_indexerstart_ts + 1.5 * 60 < $time) ? 1 : 0;
		$st['is_inx_notrun'] = ($last_indexerstart_ts + 5 * 60 < $time) ? 1 : 0;
		
		return $st;
	}
	
	public function rebuild_index($time = false)
	{
		if (!$time) {
			$time = time();
		}
		
		$this->set_option('rebuild_time', $time);
		
		return $this->checkAndSyncWPPosts();
	}
	
	public function split_to_words($str)
	{
		return $this->_index->split_to_words($str);
	}

	public function sql_select($search, &$wpq)
	{
		return $this->_index->sql_select($search, $wpq);
	}
	
	/**
	 * Insert, Update or Delete index record for specified post
	 * 
	 * @param int $post_id Post ID
	 * @return boolean Success or not
	 */
	/*
	public function reindex_post($post_id, $is_force_remove = false, $is_raw_cache_remove = false) 
	{
		$post = get_post($post_id);
		if ($post && (!$is_force_remove)) {
			// Insert or update index record

			if (!$this->defer_indexing) {
				$chunks2 = $this->getPostChunks($post_id, $is_raw_cache_remove);
			}
			
			$modt = $post->post_modified;
			$time = time();
			$build_time = $this->get_option('rebuild_time');
			$insert_id = $this->_index->updateIndexRecordForPost($post_id, $modt, $build_time, $time, $this->defer_indexing ? 1 : 0);
			
			$res = true;
			if (!$this->defer_indexing) {
				$this->_index->clearLog();
				$res = $this->_index->reindex($insert_id, $chunks2, true);
				$this->index_error = (!$res) ? 'Indexing error: ' . $this->_index->getLog() : '';
			}
			
			return $res;
		} else {
			// Check if index record exists and delete it
			$this->_index->removeIndexRecordForPost($post_id);
			$this->removeRawCache($post_id);
			return true;
		}
	}
	*/

	public function GetDBPrefix()
	{
		return $this->_index->dbprefix();
	}

	public function removeRawCache($post_id)
	{
		$idx = $this->_index->dbprefix();

		$q = 'delete from `'.$idx.'rawcache` where (`object_id` = "'.addslashes($post_id).'") and (`object_type` = "wp_post")';
		$this->db->query($q);
	}

	public function getCachedAttachmentContent($post_id, $is_reset_cache = false) 
	{
		$post = get_post($post_id);
		$chunks = array(
			'post_title' => $post->post_title,
			'post_content' => $post->post_content,
		);

		return apply_filters('wpfts_get_attachment_content', $chunks, $post, $is_reset_cache);
	}

	public function contentStripTags($s)
	{
		return strip_tags($s);
	}

	public function GetShortcodesContent($post_id)
	{
		global $post;

		// We can get a fatal error inside the_content() call...
		$r = '';
		$error = '';

		if (function_exists('interface_exists') && interface_exists('Throwable')) {
			// PHP 7+
			$post = get_post($post_id);
			setup_postdata($post);
			try {
				ob_start();
				the_content();
				$r = ob_get_clean();
			} catch (Throwable $e) {
				// Thrown the error!
				$error = $e->getMessage();
			}
			wp_reset_postdata();

		} else {
			// PHP 5+ or lower
			$post = get_post($post_id);
			setup_postdata($post);
			try {
				ob_start();
				the_content();
				$r = ob_get_clean();
			} catch (Exception $e) {
				// Thrown the error!
				$error = $e->getMessage();
			}
			wp_reset_postdata();
		}
	
		return array(
			'content' => $r, 
			'error' => $error,
		);
	}

	public function getPostChunks($post_id, $is_refresh_raw_cache = false)
	{
		$post = get_post($post_id);
		$chunks = array();
		$tt = $post->post_title;
		if (strlen($tt) > 0) {
			$chunks['post_title'] = $tt;
		}
		$tt2 = $post->post_content;

		$content_error = '';

		// A smart startup
		do_action('wpfts_index_post_start', $this, $post, $is_refresh_raw_cache);

		if (strlen($tt2) > 0) {
			if ($this->get_option('content_open_shortcodes') != 0) {
				$zz = array();
				//if (preg_match('~\[[0-9a-z_\-]+.*]~U', $tt2, $zz)) {
				if (preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $tt2, $zz )) {	// Regexp from WP core
					
					$tt2_all = $this->GetShortcodesContent($post->ID);

					$tt2 = str_replace('<', ' <', $tt2_all['content']);

					$content_error = $tt2_all['error'];
				}
			}
		
			if ($this->get_option('content_is_remove_nodes')) {
				//$n_list = trim((string)$this->get_option('content_remove_nodes_list'));
				//$a = explode(',', $n_list);
				// @todo
				// Remove <script> with content
				$tt2 = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $tt2);
				// Remove <style> with content
				$tt2 = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', "", $tt2);
			}

			if ($this->get_option('content_strip_tags') != 0) {
				$tt2 = $this->contentStripTags($tt2);
				$tt2 = html_entity_decode(str_replace('&nbsp;', ' ', $tt2));
			}
		}
		$chunks['post_content'] = $tt2;	

		// Add error chunk!
		// @todo
		// $content_error ....

		$tt3 = $post->post_excerpt;
		if (strlen($tt3) > 0) {
			$chunks['post_excerpt'] = $tt3;
		}

		$chunks = apply_filters('wpfts_index_post', $chunks, $post, $is_refresh_raw_cache);
		
		// A smart finalization
		$chunks = apply_filters('wpfts_index_post_finish', $chunks, $post, $this);
		
		return $chunks;
	}

	public function ajax_ping()
	{
		$t0 = microtime(true);
		
		$jx = new WPFTS_jxResponse();
		
		if (($data = $jx->getData()) !== false) {
			
			$time = time();
			
			$status = $this->get_status();
			$jx->variable('status', $status);
			
			$box_post_ids = isset($data['box_post_ids']) ? $data['box_post_ids'] : array();
			$postdata = $this->GetPostIndexStatus($box_post_ids);
			$jx->variable('postdata', $postdata);

			// Force indexer (this is for situation when the server has wrong DNS records and 
			// the native WP cron can not run succesfully)
			/*
			$last_indexerstart_ts = intval($this->get_option('last_indexerstart_ts'));
			if ($last_indexerstart_ts + 1.5 * 60 < $time) {
				//$this->CallIndexerStartNoBlocking();
				$this->IndexerStart();
			}
			*/
			
			$jx->variable('code', 0);
		}
		
		echo $jx->getJSON();
		wp_die();
	}

	public function ajax_submit_testpost()
	{
		$jx = new WPFTS_jxResponse();
		
		if (($data = $jx->getData()) !== false) {
			if (wp_verify_nonce($data['wpfts_options-nonce_indextester'], 'wpfts_options_indextester')) {
				
				//$jx->console($data);
				
				$postid = trim($data['wpfts_testpostid']);

				$e = array();
				
				if (strlen($postid) < 1) {
					$e[] = array('testpostid', __('Please specify post ID', 'fulltext-search'));
				} else {
					if (!is_numeric($postid)) {
						$e[] = array('testpostid', __('Please specify a number', 'fulltext-search'));
					}
				}
				
				if (count($e) > 0) {
					$z = array();
					foreach ($e as $dd) {
						$z[] = '* ' . $dd[1];
					}
					$txt = __('There are errors', 'fulltext-search') . ":\n\n" . implode("\n", $z);

					$jx->alert($txt);
				} else {
					
					$post_id = intval($postid);

					$o_title = sprintf(__('Results of Pre-indexing Filter Tester for Post ID = %s', 'fulltext-search'), $post_id);
					
					// Looking for post ID
					$p = get_post($post_id);
					
					if ($p) {

						$index = $this->getPostChunks($post_id);
						
						if (is_array($index)) {
							
							ob_start();
							?>
							<table class="table table-condensed table-sm table-hover table-striped">
							<thead class="thead-dark">
							<tr>
								<th><?php echo __('Cluster', 'fulltext-search'); ?></th>
								<th><?php echo __('Text Content', 'fulltext-search'); ?></th>
							</tr>
							</thead>
							<?php
							foreach ($index as $k => $d) {
								?>
							<tr>
								<td><b><?php echo htmlspecialchars($k); ?></b></td>
								<td><?php echo htmlspecialchars($d); ?></td>
							</tr>
								<?php
							}
							?>
							</table>
							<?php
							$o_result = ob_get_clean();
							
						} else {
							// Wrong filter result
							$o_result = '<p>'.sprintf(__('Filter result is not array. Please read <a href="%s" target="_blank">documentation</a> to fix this error.', 'fulltext-search'), $this->_wpfts_domain.$this->_documentation_link).'</p>';
						}
						
					} else {
						// Post not found
						$o_result = '<p>'.__('The post with specified ID is not found.', 'fulltext-search').'</p>';
					}
					
					$this->set_option('testpostid', $postid);
					
					$output = '<hr>';
					$output .= '<h5>'.htmlspecialchars($o_title).'</h5>';
					$output .= $o_result;
					
					$jx->variable('code', 0);
					$jx->variable('text', $output);
				}
				
			} else {
				$jx->alert(__('The form is outdated. Please refresh the page and try again.', 'fulltext-search'));
			}
		}
		echo $jx->getJSON();
		wp_die();
	}
	
	public function ajax_submit_testsearch()
	{
		$jx = new WPFTS_jxResponse();
		
		if (($data = $jx->getData()) !== false) {
			if (wp_verify_nonce($data['wpfts_options-nonce_searchtester'], 'wpfts_options_searchtester')) {
				
				//$jx->console($data);
				
				$query = trim($data['wpfts_testquery']);
				$tq_disable = $data['wpfts_tq_disable'];
				$tq_nocache = $data['wpfts_tq_nocache'];
				$tq_post_type = $data['wpfts_tq_post_type'];
				$tq_post_status = $data['wpfts_tq_post_status'];

				$current_page = max(1, isset($data['wpfts_tq_current_page']) ? intval($data['wpfts_tq_current_page']) : 0);
				$n_perpage = isset($data['wpfts_tq_n_perpage']) ? intval($data['wpfts_tq_n_perpage']) : 25;
				
				$e = array();
				
				if (strlen($query) < 1) {
					$e[] = array('testquery', __('Please specify search query', 'fulltext-search'));
				}
				
				if (count($e) > 0) {
					$z = array();
					foreach ($e as $dd) {
						$z[] = '* ' . $dd[1];
					}
					$txt = __('There are errors', 'fulltext-search') . ":\n\n" . implode("\n", $z);

					$jx->alert($txt);
				} else {
					
					$o_title = sprintf(__('Results of search for query = "%s"', 'fulltext-search'), $query);
					
					$t0 = microtime(true);
					
					$wpq = new WP_Query(array(
						//'fields' => 'ids',
						'fields' => '*',
						's' => $query,
						'post_status' => 'any',
						//'nopaging' => true,
						'wpfts_disable' => $tq_disable ? 1 : 0,
						'wpfts_nocache' => $tq_nocache ? 1 : 0,
						'posts_per_page' => $n_perpage,
						'paged' => $current_page,
						'post_status' => $tq_post_status,
						'post_type' => $tq_post_type,
						'wpfts_is_force' => 1,
					));
					
					$t1 = microtime(true) - $t0;
					
					if (isset($GLOBALS['posts_clauses'])) {
						$jx->console($GLOBALS['posts_clauses']);
					}
					
					//$num = $wpq->have_posts() ? count($wpq->posts) : 0;
					$num = $wpq->found_posts;
					
					$o_result = '<p><i>'.sprintf(__('Time spent: <b>%.3f</b> sec', 'fulltext-search'), $t1).'</i><br>';
					$o_result .= '</p>';
					
					global $post;
					
					$a = array();
					$n = ($current_page - 1) * $n_perpage + 1;
					while ( $wpq->have_posts() ) {
					
						$wpq->the_post();
						
						$relev = isset($post->relev) ? $post->relev : 0;
						$post = get_post($post->ID);
						setup_postdata($post);
						
						$tn = '';
						$post_tn = get_post_thumbnail_id($post->ID);
						if ($post_tn) {
							$large_image_url = wp_get_attachment_image_src($post_tn, 'thumbnail');
							if ( ! empty( $large_image_url[0] ) ) {
								$tn = '<img src="'.esc_url($large_image_url[0]).'" alt="" class="wpfts_table_img">';
							}
						}
						
						ob_start();
						the_excerpt();
						$exc = ob_get_clean();
						
						$a[] = array(
							'n' => $n ++,
							'ID' => $post->ID,
							'post_type' => $post->post_type,
							'post_title' => $post->post_title,
							'post_status' => $post->post_status,
							'tn' => $tn,
							'exc' => $exc,
							'relevance' => sprintf('%.2f', $relev * 100).'%',
						);
					}
					wp_reset_postdata();
					
					if (count($a) > 0) {
						
						$o_result .= '<div class="sandbox_paginator">'.$this->sandboxPaginator($current_page, $num, $n_perpage).'</div>';
						
						ob_start();
						?>
						<table class="table table-sm table-condensed table-striped table-hover">
						<thead class="thead-dark">
						<tr>
							<th style="width: 10%;"><?php echo __('#', 'fulltext-search'); ?></th>
							<th style="width: 10%;"><?php echo __('ID', 'fulltext-search'); ?></th>
							<th style="width: 10%;"><?php echo __('Type', 'fulltext-search'); ?></th>
							<th style="width: 10%;"><?php echo __('Status', 'fulltext-search'); ?></th>
							<th style="width: 50%;"><?php echo __('Title, Thumbnail, Excerpt', 'fulltext-search'); ?></th>
							<th style="width: 10%;"><?php echo __('Relevance', 'fulltext-search'); ?></th>
						</tr>
						</thead>
						<?php
						$o_result .= ob_get_clean();
							
						foreach ($a as $d) {
							
							$content = '<div class="wpfts_tq_content"><div class="cont1">'.$d['tn'].'</div><div class="cont2"><b>'.htmlspecialchars($d['post_title']).'</b><br>'.$d['exc'].'</div></div>';
							
							$o_result .= '<tr>';
							$o_result .= '<td>'.$d['n'].'</td>';
							$o_result .= '<td><a href="/?p='.$d['ID'].'">'.$d['ID'].'</a></td>';
							$o_result .= '<td>'.$d['post_type'].'</td>';
							$o_result .= '<td>'.$d['post_status'].'</td>';
							$o_result .= '<td>'.$content.'</td>';
							$o_result .= '<td>'.$d['relevance'].'</td>';
							$o_result .= '</tr>';
						}
							
						ob_start();
						?>
						</table>
						<?php
						$o_result .= ob_get_clean();
						
						$o_result .= '<div class="sandbox_paginator">'.$this->sandboxPaginator($current_page, $num, $n_perpage).'</div>';
					} else {
						$o_result .= '<p><i>'.sprintf(__('Found: <b>%d</b> posts', 'fulltext-search'), $num).'</i></p>';
					}
					
					$this->set_option('testquery', $query);
					$this->set_option('tq_disable', $tq_disable);
					$this->set_option('tq_post_type', $tq_post_type);
					$this->set_option('tq_post_status', $tq_post_status);
					$this->set_option('tq_perpage', $n_perpage);
					
					$output = '<hr>';
					$output .= '<h4>'.htmlspecialchars($o_title).'</h4>';
					$output .= $o_result;
					
					$jx->variable('code', 0);
					$jx->variable('text', $output);
				}
				
			} else {
				$jx->alert(__('The form is outdated. Please refresh the page and try again.', 'fulltext-search'));
			}
		}
		echo $jx->getJSON();
		wp_die();
	}
	
	public function ajax_submit_rebuild()
	{
		$jx = new WPFTS_jxResponse();
		
		if (($data = $jx->getData()) !== false) {
			//if (wp_verify_nonce($data['wpfts_options-nonce'], 'wpfts_options')) {
				
				$this->set_option('index_ready', 0);
				$this->set_option('is_break_loop', 1);

				$this->_index->create_db_tables();
				$this->rebuild_index(time());

				$this->SetPause(false, true);

				// Force status recalculation
				$this->set_option('status_next_ts', 0);
				$this->set_option('last_indexerstart_ts', 0);

				$jx->reload();
				
			//} else {
			//	$jx->alert(__('The form is outdated. Please refresh the page and try again.', 'fulltext-search'));
			//}
		}
		echo $jx->getJSON();
		wp_die();
	}
	
	public function SetPause($is_pause = true, $is_start_indexer = false)
	{
		if ($is_pause) {
			// Set pause mode
			$this->set_option('is_pause', 1);
			$this->set_option('is_break_loop', 1);

		} else {
			// Remove pause mode
			$this->set_option('is_pause', 0);
			$this->set_option('ts_series', '');	// Reset est. time
			$this->set_option('last_sync_ts', 0);
		}

		$this->set_option('status_next_ts', 0);

		if ((!$is_pause) && $is_start_indexer) {
			// Start loop
			$this->CallIndexerStartNoBlocking();
		}

		return true;
	}

	public function ajax_set_pause()
	{
		$jx = new WPFTS_jxResponse();
		
		if (($data = $jx->getData()) !== false) {
			//if (wp_verify_nonce($data['wpfts_options-nonce'], 'wpfts_options')) {
				
				$is_pause = isset($data['is_pause']) ? intval($data['is_pause']) : 0;

				$this->SetPause($is_pause, true);

				$status = $this->get_status();

				$jx->variable('status', $status);
				$jx->variable('code', 0);

			//} else {
			//	$jx->alert(__('The form is outdated. Please refresh the page and try again.', 'fulltext-search'));
			//}
		}
		echo $jx->getJSON();
		wp_die();
	}

	public function ajax_se_style_preview()
	{
		$jx = new WPFTS_jxResponse();
		
		if (($data = $jx->getData()) !== false) {

			$css = isset($data['wpfts_se_styles']) ? $data['wpfts_se_styles'] : '';
			// Minimize
			$c_css = $this->MinimizeSEStyle($css);

			$jx->variable('code', 0);
			$jx->variable('c_css', $c_css);
		}
		echo $jx->getJSON();
		wp_die();
	}

	public function ajax_se_style_reset()
	{
		$jx = new WPFTS_jxResponse();
		
		if (($data = $jx->getData()) !== false) {
			$def_styles = $this->ReadSEStyles();
			$c_css = $this->MinimizeSEStyle($def_styles);

			$jx->variable('code', 0);
			$jx->variable('c_css', $c_css);
			$jx->variable('css_data', $def_styles);
		}
		echo $jx->getJSON();
		wp_die();
	}

	public function sandboxPaginator($current_page, $total_items, $n_perpage) 
	{	
		$maxpage = ceil($total_items / $n_perpage);
		
		$a_pages = array();
		for ($i = 1; $i <= $maxpage; $i ++) {
			$a_pages[$i] = (($i - 1) * $n_perpage + 1).' - '.min($total_items, $i * $n_perpage);
		}
		
		$pager = '<div class="wpfts_tq_pager col-6 text-left">';
		$pager .= '<span class="wpfts_tq_prevpage btn btn-secondary btn-sm"'.(($current_page > 1) ? '' : ' disabled="disabled"').' type="button"><i class="fa fa-angle-double-left"></i></span>';
		$pager .= sprintf(__('Shown <span>%1s</span> from <b>%2s</b>', 'fulltext-search'), WPFTS_HtmlTools::makeSelect($a_pages, $current_page, array('class' => 'wpfts_tq_current_page')), $total_items);
		$pager .= '<span class="wpfts_tq_nextpage btn btn-secondary btn-sm"'.(($current_page < $maxpage) ? '' : ' disabled="disabled"').' type="button"><i class="fa fa-angle-double-right"></i></span>';
		$pager .= '</div>';
		
		$a_nn = array(
			10 => 10,
			25 => 25,
			50 => 50,
			100 => 100,
			250 => 250,
			500 => 500,
		);
		$sel_perpage = '<div class="wpfts_tq_perpage col-6 text-right">'.WPFTS_HtmlTools::makeSelect($a_nn, $n_perpage, array('class' => 'wpfts_tq_n_perpage')).'&nbsp;'.__('posts per page', 'fulltext-search').'</div>';

		return '<div class="row mb-2 mt-2">'.$pager.$sel_perpage.'</div>';
	}

	public function set_hooks()
	{
		add_action('pre_get_posts', array($this, 'index_pre_get_posts'), 10);			// 1753
		add_filter('posts_search', array($this, 'index_sql_select'), 10, 2);			// 2100
		add_filter('posts_search_orderby', array($this, 'index_sql_orderby'), 10, 2);	// 2355
		add_filter('posts_where', array($this, 'index_sql_where'), 10, 2);				// 2537	// for mime-types only
		add_filter('posts_join', array($this, 'index_sql_joins'), 10, 2);				// 2547
		add_filter('posts_distinct', array($this, 'index_posts_distinct'), 10, 2);		// 2714	// set sql_no_cache
		add_filter('post_limits', array($this, 'index_post_limits'), 10, 2);			// 2724
		add_filter('posts_fields', array($this, 'index_posts_fields'), 10, 2);			// 2734
		add_filter('posts_clauses', array($this, 'index_posts_clauses'), 10, 2);		// 2747	// information only
		// sql request merge point														// 2894
		add_filter('posts_pre_query', array($this, 'index_posts_pre_query'), 10, 2);	// 2925	// get relev for split_mode
		add_filter('split_the_query', array($this, 'index_split_the_query'), 10, 2);	// 2985
		add_filter('the_posts', array($this, 'index_the_posts'), 10, 2);		// 3169 (almost at the end - good for cleanup)
	}

	function index_posts_clauses($clauses, $wpq)
	{
		if ((!isset($GLOBALS['posts_clauses'])) || (!is_array($GLOBALS['posts_clauses']))) {
			$GLOBALS['posts_clauses'] = array();
		}
		$GLOBALS['posts_clauses'][] = $clauses;
		
		return $clauses;
	}
	
	function index_posts_fields($fields, $wpq)
	{
		$is_disabled = intval($wpq->get('wpfts_disable', 0));
		if (!$is_disabled) {
			return $this->_index->sql_posts_fields($fields, $wpq);
		}
		return $fields;
	}
	
	function index_post_limits($limits, $wpq) 
	{
		$is_disabled = intval($wpq->get('wpfts_disable', 0));
		if (!$is_disabled) {
			return $this->_index->sql_post_limits($limits, $wpq);
		}
		return $limits;
	}
	
	function index_split_the_query($split_the_query, $wpq) 
	{
		$is_disabled = intval($wpq->get('wpfts_disable', 0));
		if (!$is_disabled) {
			return $this->_index->sql_split_the_query($split_the_query, $wpq);
		}
		return $split_the_query;
	}

	function index_sql_joins($join, $wpq)
	{
		$is_disabled = intval($wpq->get('wpfts_disable', 0));
		if (!$is_disabled) {
			$t_rm = array();
			return $this->_index->sql_joins($join, $wpq, $t_rm);
		}
		return $join;
	}
	
	function index_posts_pre_query($posts, $wpq) 
	{
		$is_disabled = intval($wpq->get('wpfts_disable', 0));
		if (!$is_disabled) {
			return $this->_index->sql_posts_pre_query($posts, $wpq);
		}

		return $posts;
	}	

	function index_sql_select($search, $wpq)
	{
		$is_disabled = intval($wpq->get('wpfts_disable', 0));
		if (!$is_disabled) {
			return $this->_index->sql_select($search, $wpq);
		}
		return $search;
	}
	
	function index_sql_orderby($orderby, $wpq)
	{
		$is_disabled = intval($wpq->get('wpfts_disable', 0));
		if (!$is_disabled) {
			return $this->_index->sql_orderby($orderby, $wpq);
		}
		return $orderby;
	}
	
	function index_pre_get_posts(&$wpq)
	{
		// Set default values for WP_Query() parameters
		$is_wpfts_disabled = intval($wpq->get('wpfts_disable', intval($this->get_option('enabled')) ? 0 : 1));
		$wpq->set('wpfts_disable', $is_wpfts_disabled);

		// Step 1: Populate wp_query parameters (in case "tweak main search mode" is ON)

		$is_force = $wpq->get('wpfts_is_force', 0);
		$is_forcetweaks = $wpq->get('wpfts_is_forcetweaks', 0);	// @todo is_forcetweaks mode will be implemented a bit later

		$is_wpadmin = $this->get_option('is_wpadmin');

		// Enable attachments in WP search (if enabled)
		if (!$is_wpfts_disabled) {

			// Check if we need to tweak main query
			if (($wpq->is_main_query() && !is_admin()) || ($is_force)) {

				// Set "global values" (can be changed later by wpfts_widget presets)

				// word_logic
				$word_logic = mb_strtolower(
					$is_forcetweaks ? 
						intval($this->get_option('deflogic') ? 'or' : 'and') :
						($wpq->get('word_logic', intval($this->get_option('deflogic')) ? 'or' : 'and'))
				);
				$wpq->set('word_logic', $word_logic == 'and' ? 'and' : 'or');
		
				// display attachments
				$display_attachments = $is_forcetweaks ? 
						intval($this->get_option('display_attachments')) :
						intval($wpq->get('display_attachments', intval($this->get_option('display_attachments'))));
				$wpq->set('display_attachments', $display_attachments);	// We can remove this line later!
		
				// Order by
				$t = $wpq->get('orderby');
				if ( ((!is_array($t)) && (strlen(trim($t)) < 1)) || $is_forcetweaks) {
					// Go tweak!
					$orderby = $this->get_option('mainsearch_orderby');
					$wpq->set('orderby', $orderby);
				}

				// Order
				$t2 = $wpq->get('order');
				if (((!is_array($t2)) && (strlen(trim($t2)) < 1)) || $is_forcetweaks) {
					// Go tweak!
					$order = $this->get_option('mainsearch_order');
					$wpq->set('order', $order);
				}

				// Mime_types
				$t3 = $is_forcetweaks ? 
							trim($this->get_option('limit_mimetypes')) : 
							$wpq->get('limit_mimetypes', trim($this->get_option('limit_mimetypes')));
				if (strlen($t3) > 0) {
					// WPFTS is limiting mime-types
					//$mtp = $wpq->get('post_mime_type');
					$wpq->set('limit_mimetypes', $t3);
				} else {
					// WPFTS does not limit mimetypes
					if ($is_forcetweaks) {
						// And forced this
						$wpq->set('post_mime_type', '');	// Allow all mimetypes
					}
				}

				// Cluster weights
				$cw = $wpq->get('cluster_weights', false);
				if ($cw === false) {
					$cw = $this->get_option('cluster_weights');
					$wpq->set('cluster_weights', $cw);
				}

				// ------
				// Apply widget configuration
				$wgt = $wpq->get('wpfts_wdgt', isset($_GET['wpfts_wdgt']) ? $_GET['wpfts_wdgt'] : '');
				$ws = $this->GetWidgetPresets();
				if ((strlen($wgt) > 0) && (isset($ws[$wgt]))) {
					
					// We going to check results_url here @todo
					$wdata = $ws[$wgt];
					$wdata['id'] = $wgt;

					// Now we assume it's a widget-powered search
					$wpq->set('pagename', '');
					$wpq->set('name', '');
					//$wpq->set('do_not_redirect', 1);
					unset($wpq->queried_object);
					unset($wpq->queried_object_id);
					//$wpq->set( 'posts_per_page', 10 );	// @todo
					$wpq->is_search = true; // We making WP think it is Search page 
            		$wpq->is_page = false; // disable unnecessary WP condition
            		$wpq->is_singular = false; // disable unnecessary WP condition

					do_action_ref_array('wpfts_pre_get_posts', array(&$wpq, $wdata));
				} else {
					do_action_ref_array('wpfts_pre_set_file_search', array(&$wpq, $display_attachments));
				}
			}
		}

		// Step 2: Justify parameters based on wp_query parameters
		$is_wpfts_disabled = $wpq->get('wpfts_disable', 0);
		if (!$is_wpfts_disabled) {
			if (($wpq->is_main_query() && (!is_admin() || $is_wpadmin)) || ($is_force)) {
				$cw = $wpq->get('cluster_weights', false);

				$cluster_weights = apply_filters('wpfts_cluster_weights', $cw);
				if (!is_array($cluster_weights)) {
					$cluster_weights = array();
				}

				// In case limit_mimetypes is set, let's clear the post_mime_type
				// It's important for "where" algoritm (to prevent wp_query to fill '$where' by wrong mimetypes)
				$limit_mimetypes = trim($wpq->get('limit_mimetypes', ''));
				if (strlen($limit_mimetypes) > 0) {
					// We will use WPFTS's mimetypes
					$wpq->set('wpfts_temp_mimes', $limit_mimetypes);
				} else {
					// Save WP mimetypes
					$wp_mimes = $wpq->get('post_mime_type');
					$wpq->set('wpfts_temp_mimes', $wp_mimes);
				}
				$wpq->set('post_mime_type', '');	// Anyway clear WP mimetypes
	
				$this->_index->sql_pre_posts($wpq, $cluster_weights);
			}
		}
	}
	
	function wpfts_wp_post_mime_type_where($post_mime_types, $table_alias = '')
	{
		$where = '';
		$wildcards = array('', '%', '%/%');
		if (is_string($post_mime_types)) {
			$post_mime_types = array_map('trim', explode(',', $post_mime_types));
		}
	
		$wheres = array();
		foreach ((array) $post_mime_types as $mime_type) {
			$mime_type = preg_replace('/\s/', '', $mime_type);
			$slashpos = strpos($mime_type, '/');
			if ( false !== $slashpos ) {
				$mime_group = preg_replace('/[^-*.a-zA-Z0-9]/', '', substr($mime_type, 0, $slashpos));
				$mime_subgroup = preg_replace('/[^-*.+a-zA-Z0-9]/', '', substr($mime_type, $slashpos + 1));
				if ( empty($mime_subgroup) )
					$mime_subgroup = '*';
				else
					$mime_subgroup = str_replace('/', '', $mime_subgroup);
				$mime_pattern = "$mime_group/$mime_subgroup";
			} else {
				if ($mime_type == '#usual_posts#') {
					$mime_pattern = '';
				} else {
					$mime_pattern = preg_replace('/[^-*.a-zA-Z0-9]/', '', $mime_type);
					if ( false === strpos($mime_pattern, '*') )
						$mime_pattern .= '/*';
				}
			}
	
			$mime_pattern = preg_replace('/\*+/', '%', $mime_pattern);
	
			if ( in_array( $mime_type, $wildcards ) )
				return '';
	
			if ( false !== strpos($mime_pattern, '%') )
				$wheres[] = empty($table_alias) ? "post_mime_type LIKE '$mime_pattern'" : "$table_alias.post_mime_type LIKE '$mime_pattern'";
			else
				$wheres[] = empty($table_alias) ? "post_mime_type = '$mime_pattern'" : "$table_alias.post_mime_type = '$mime_pattern'";
		}
		if ( !empty($wheres) )
			$where = ' AND (' . join(' OR ', $wheres) . ') ';
		return $where;
	}

	function index_sql_where($where, $wpq)
	{
		$is_disabled = intval($wpq->get('wpfts_disable', 0));
		if (!$is_disabled) {

			$t_post_mime_types = array();

			$z = trim($wpq->get('wpfts_temp_mimes', ''));
			if (is_string($z)) {
				if (strlen($z) > 0) {
					$a3 = explode(',', $z);
					foreach ($a3 as $dd) {
						$t_post_mime_types[] = trim($dd);	
					}
				}
			} elseif (is_array($z)) {
				if (count($z) > 0) {
					foreach ($z as $dd) {
						$t_post_mime_types[] = trim($dd);
					}
				}
			}
			if (count($t_post_mime_types) > 0) {
				$t_post_mime_types[] = '#usual_posts#';
			}

			global $wpdb;

			$tt = $this->wpfts_wp_post_mime_type_where($t_post_mime_types, $wpdb->posts);

			$where .= $tt;
		}
		return $where;
	}

	function index_the_posts($posts, $wpq)
	{
		$is_disabled = intval($wpq->get('wpfts_disable', 0));
		if (!$is_disabled) {
			return $this->_index->sql_the_posts($posts, $wpq);
		}
		return $posts;
	}
	
	function index_posts_distinct($distinct, $wpq)
	{
		$is_disabled = intval($wpq->get('wpfts_disable', 0));
		if (!$is_disabled) {
			return $this->_index->sql_posts_distinct($distinct, $wpq);
		}
		return $distinct;
	}

	function ajax_hide_notification()
	{
		$jx = new WPFTS_jxResponse();
		
		if (($data = $jx->getData()) !== false) {
			$notification_id = isset($data['notification_id']) ? $data['notification_id'] : '';

			switch ($notification_id) {
				case 'change_log':
					$this->set_option('change_log', '');
					break;
				case 'welcome_message':
					$this->set_option('is_welcome_message', '');
					break;
				case 'detector_message':
					$this->set_option('detector_message', '');
					$this->set_option('detector_message_expdt', date('Y-m-d H:i:s', current_time('timestamp') + 3600 * 24 * 90));
					break;
				case 'detector2_message':
					$this->set_option('detector2_message', '');
					$this->set_option('detector2_message_expdt', date('Y-m-d H:i:s', current_time('timestamp') + 3600 * 24 * 365));
					break;
				case 'wpftslic_message':
					$this->set_option('wpftslic_message', '');
					$this->set_option('wpftslic_message_expdt', date('Y-m-d H:i:s', current_time('timestamp') + 3600 * 24 * 1));
					break;
			}
		}
		echo $jx->getJSON();
		wp_die();
	}

	public function ForceSmartExcerpts($query = false)
	{
		$this->forced_se_query = mb_strlen((string)$query) > 0 ? (string)$query : false;
	}

	public function GetUsedMimetypes()
	{
		global $wpdb;

		// Get used mimetypes
		$used_mt = get_transient('wpfts_used_mt');
		if ($used_mt === false) {
			$q = 'select post_mime_type, count(*) n from `'.$wpdb->posts.'` group by `post_mime_type`';
			$r2 = $this->db->get_results($q, ARRAY_A);
			$used_mt = array();
			foreach ($r2 as $d) {
				if (strlen($d['post_mime_type']) > 0) {
					$used_mt[$d['post_mime_type']] = $d['n'];
				}
			}
			set_transient('wpfts_used_mt', $used_mt, 1);
		}
		return $used_mt;
	}

	public function GetWidgetPresets()
	{
		return $this->widget_presets;
	}

	public function AddWidgetPreset($id, $data = array())
	{
		if ($id && (strlen($id) > 0) && (isset($data['title']) && (strlen($data['title']) > 0))) {
			$defaults = array(
				'filter' => '',
				'results_url' => '/',
				'autocomplete_mode' => 0,
				'classname' => '',
			);

			$this->widget_presets[$id] = $data + $defaults;
		}
	}

	public function GetPresetBySearchType($search_type)
	{
		$t = $this->get_option('preset_selector');

		return ($t && is_array($t) && isset($t[$search_type])) ? $t[$search_type] : '';
	}

	public function GetPresetData($preset_ident)
	{
		$t = $this->get_option('presetdata_'.$preset_ident);

		if ($t && is_array($t)) {
			return $t;
		} else {
			return array();
		}
	}

	public function process_form_controlbox($jx, $data)
	{
		if (wp_verify_nonce($data['wpfts_options-nonce_controlbox'], 'wpfts_options_controlbox')) {
				
			$enabled = isset($data['wpfts_enabled']) ? $data['wpfts_enabled'] : 0;
			$autoreindex = isset($data['wpfts_autoreindex']) ? $data['wpfts_autoreindex'] : 0;
			$is_wpadmin = isset($data['wpfts_is_wpadmin']) ? $data['wpfts_is_wpadmin'] : 0;
			$is_fixmariadb = isset($data['wpfts_is_fixmariadb']) ? $data['wpfts_is_fixmariadb'] : 0;
			$is_optimizer = isset($data['wpfts_is_optimizer']) ? $data['wpfts_is_optimizer'] : 0;

			$this->set_option('enabled', $enabled ? 1 : 0);
			$this->set_option('autoreindex', $autoreindex ? 1 : 0);
			$this->set_option('is_wpadmin', $is_wpadmin ? 1 : 0);
			$this->set_option('is_fixmariadb', $is_fixmariadb ? 1 : 0);
			$this->set_option('is_optimizer', $is_optimizer ? 1 : 0);

			$jx->variable('code', 0);

			// We need to refresh status block also
			return true;
			
		} else {
			$jx->alert(__('The form is outdated. Please refresh the page and try again.', 'fulltext-search'));
		}
		
		return false;
	}

	public function process_form_indexingbox($jx, $data)
	{
		if (wp_verify_nonce($data['wpfts_options-nonce_indexingbox'], 'wpfts_options_indexingbox')) {
				
			$content_strip_tags = isset($data['wpfts_content_strip_tags']) ? $data['wpfts_content_strip_tags'] : 0;
			$content_open_shortcodes = isset($data['wpfts_content_open_shortcodes']) ? $data['wpfts_content_open_shortcodes'] : 0;
			$content_is_remove_nodes = isset($data['wpfts_content_is_remove_nodes']) ? $data['wpfts_content_is_remove_nodes'] : 0;

			$this->set_option('content_strip_tags', $content_strip_tags ? 1 : 0);
			$this->set_option('content_open_shortcodes', $content_open_shortcodes ? 1 : 0);
			$this->set_option('content_is_remove_nodes', $content_is_remove_nodes ? 1 : 0);

			$jx->variable('code', 0);

			// We need to refresh status block also
			return true;
			
		} else {
			$jx->alert(__('The form is outdated. Please refresh the page and try again.', 'fulltext-search'));
		}
		
		return false;
	}

	public function process_form_extractionbox($jx, $data)
	{
		if (wp_verify_nonce($data['wpfts_options-nonce_extractionbox'], 'wpfts_options_extractionbox')) {
				
			$jx->variable('code', 0);

			// We need to refresh status block also
			return true;
			
		} else {
			$jx->alert(__('The form is outdated. Please refresh the page and try again.', 'fulltext-search'));
		}
		
		return false;
	}

	public function process_form_step1_query_preprocessing($jx, $data)
	{
		if (wp_verify_nonce($data['wpfts_options-nonce_step1_query_preprocessing'], 'wpfts_options_step1_query_preprocessing')) {
			
			$v = isset($data['wpfts_internal_search_terms']) ? intval($data['wpfts_internal_search_terms']) : 0;

			$this->set_option('internal_search_terms', $v);

			$jx->variable('code', 0);

			// We need to refresh status block also
			return true;
			
		} else {
			$jx->alert(__('The form is outdated. Please refresh the page and try again.', 'fulltext-search'));
		}
		
		return false;
	}

	public function process_form_step2_find_records($jx, $data)
	{
		if (wp_verify_nonce($data['wpfts_options-nonce_step2_find_records'], 'wpfts_options_step2_find_records')) {

			$deflogic = isset($data['wpfts_deflogic']) ? intval($data['wpfts_deflogic']) : 0;
			$deeper_search = isset($data['wpfts_deeper_search']) ? intval($data['wpfts_deeper_search']) : 0;

			$this->set_option('deflogic', $deflogic ? 1 : 0);
			$this->set_option('deeper_search', $deeper_search ? 1 : 0);

			$jx->variable('code', 0);

			// We need to refresh status block also
			return true;
			
		} else {
			$jx->alert(__('The form is outdated. Please refresh the page and try again.', 'fulltext-search'));
		}
		
		return false;
	}

	public function process_form_step3_calculate_relevance($jx, $data)
	{
		if (wp_verify_nonce($data['wpfts_options-nonce_step3_calculate_relevance'], 'wpfts_options_step3_calculate_relevance')) {
			
			$e = array();

			$cluster_weights = array();
			
			foreach ($data as $k => $d) {
				if (preg_match('~^eclustertype_(.+)$~', $k, $m)) {
					$clname = $m[1];
					$clvalue = floatval($d);
					if ((is_numeric($d)) && ($clvalue >= 0) && ($clvalue <= 1.0)) {
						$cluster_weights[$clname] = $clvalue;
					} else {
						$e[] = array($k, sprintf(__('The weight value of cluster "%s" should be numeric value from 0.0 to 1.0', 'fulltext-search'), $clname));
					}
				}
			}

			if (count($e) > 0) {

				$z = array();
				foreach ($e as $dd) {
					$z[] = '* ' . $dd[1];
				}
				$txt = __('There are errors', 'fulltext-search') . ":\n\n" . implode("\n", $z);

				$jx->alert($txt);
			} else {
				// Validation passed!

				// We need to have post_title and post_content clusters even they are not set
				if (!isset($cluster_weights['post_title'])) {
					$cluster_weights['post_title'] = 0.8;
				}
				if (!isset($cluster_weights['post_content'])) {
					$cluster_weights['post_content'] = 0.5;
				}

				$this->set_option('cluster_weights', $cluster_weights);

				$jx->variable('code', 0);

				// We need to refresh status block also
				return true;
			}

		} else {
			$jx->alert(__('The form is outdated. Please refresh the page and try again.', 'fulltext-search'));
		}
		
		return false;
	}

	public function process_form_step4_sort_results($jx, $data)
	{
		if (wp_verify_nonce($data['wpfts_options-nonce_step4_sort_results'], 'wpfts_options_step4_sort_results')) {
			
			$mainsearch_orderby = isset($data['wpfts_mainsearch_orderby']) ? trim($data['wpfts_mainsearch_orderby']) : '';
			$mainsearch_order = isset($data['wpfts_mainsearch_order']) ? trim($data['wpfts_mainsearch_order']) : '';

			$this->set_option('mainsearch_orderby', $mainsearch_orderby);
			$this->set_option('mainsearch_order', $mainsearch_order);

			$jx->variable('code', 0);

			// We need to refresh status block also
			return true;
			
		} else {
			$jx->alert(__('The form is outdated. Please refresh the page and try again.', 'fulltext-search'));
		}
		
		return false;
	}

	public function process_form_step5_show_results($jx, $data)
	{
		if (wp_verify_nonce($data['wpfts_options-nonce_step5_show_results'], 'wpfts_options_step5_show_results')) {
			
			$e = array();

			foreach ($data as $k => $d) {
				if (preg_match('~^wpfts_(.+)$~', $k, $m)) {
					$key = $m[1];
					switch ($key) {
						case 'is_smart_excerpts':
						case 'is_smart_excerpt_text':
						case 'is_show_score':
						case 'is_not_found_words':
							$v = ($d) ? 1 : 0;
							$this->set_option($key, $v);
							break;
						case 'optimal_length':
							$optlen = intval($d);
							if (($optlen < 10) || ($optlen > 10240)) {
								$e[] = array($key, __('Optimal Length should be a number from 10 to 10240', 'fulltext-search'));
							} else {
								$this->set_option($key, $optlen);
							}
							break;
						case 'se_styles':
							$this->set_option('custom_se_css', $d);
							$this->ReadSEStylesMinimized(true);	// Reset minimization cache
							break;
						default:
					}
				} else {
					// Not valid input name
				}
			}

			if (count($e) > 0) {

				$z = array();
				foreach ($e as $dd) {
					$z[] = '* ' . $dd[1];
				}
				$txt = __('There are errors', 'fulltext-search') . ":\n\n" . implode("\n", $z);

				$jx->alert($txt);
			} else {
				// Validation passed!
				$jx->variable('code', 0);
				
				// We need to refresh status block also
				return true;
			}
			
		} else {
			$jx->alert(__('The form is outdated. Please refresh the page and try again.', 'fulltext-search'));
		}
		
		return false;
	}

	public function ajax_smartform()
	{
		$jx = new WPFTS_jxResponse();
		
		if (($data = $jx->getData()) !== false) {
			
			$time = time();

			$is_form_processed = apply_filters('wpfts_submit_settings_before', false, $data, $jx);

			if (!$is_form_processed) {
				$form_name = isset($data['form_name']) ? trim($data['form_name']) : false;

				if ($form_name) {

					switch ($form_name) {
						case 'form_controlbox':
							$is_form_processed = $this->process_form_controlbox($jx, $data);
							break;
						case 'form_indexingbox':
							$is_form_processed = $this->process_form_indexingbox($jx, $data);
							break;
						case 'form_extractionbox':
							$is_form_processed = $this->process_form_extractionbox($jx, $data);
							break;
						case 'form_step1_query_preprocessing':
							$is_form_processed = $this->process_form_step1_query_preprocessing($jx, $data);
							break;
						case 'form_step2_find_records':
							$is_form_processed = $this->process_form_step2_find_records($jx, $data);
							break;
						case 'form_step3_calculate_relevance':
							$is_form_processed = $this->process_form_step3_calculate_relevance($jx, $data);
							break;
						case 'form_step4_sort_results':
							$is_form_processed = $this->process_form_step4_sort_results($jx, $data);
							break;
						case 'form_step5_show_results':
							$is_form_processed = $this->process_form_step5_show_results($jx, $data);
							break;
						default:
							$is_form_processed = false;
							//
					}

					$is_form_processed = apply_filters('wpfts_submit_settings_after', $is_form_processed, $data, $jx);
					
					if ($is_form_processed) {
						// Force status recalculation
						$this->set_option('status_next_ts', 0);				
					}
				}			
			}
		}
		
		echo $jx->getJSON();
		wp_die();
	}

	/***************************************** */
	/* Addons                                  */
	/***************************************** */
	public function RegisterAddon($ident, $data = null)
	{
		if (!$data) {
			return false;
		}

		$t = $this->GetActiveAddon($ident);
		if ($t) {
			// This addon was already registered!
			return false;
		} else {
			$this->active_addons[$ident] = $data;

			return true;
		}
	}
	
	public function GetActiveAddon($ident)
	{
		if (isset($this->active_addons[$ident])) {
			return $this->active_addons[$ident];
		}

		return false;
	}

	/***************************************** */

	/**
	 * This ajax call is forcing index loop to run
	 */
	public function ajax_force_index()
	{
		$this->IndexerStart();
		echo 'ok';
		wp_die();
	}

	/**
	 * Unfortunately this call may fail sometimes in case
	 * the server run in the local environment or DNS was configured incorrectly.
	 * 
	 */
	public function CallIndexerStartNoBlocking()
	{
		$url = admin_url('admin-ajax.php');

		$packet = array(
			'body' => array(
				'action' => 'wpfts_force_index',
			),
			'blocking' => false,
			'timeout' => 0.01,
		);

		$wpres = wp_remote_post($url, $packet);

		return $wpres;
	}

	public function MakePostsSync($is_force_sync = false)
	{
		global $wpdb;
		
		$time = time();
		$last_sync_ts = intval($this->get_option('last_sync_ts'));

		if (($last_sync_ts <= $time) || ($is_force_sync)) {
			$idx = $this->GetDBPrefix();
		
			// Find index records that linked with changed or removed posts
			$q = 'update `'.$idx.'index` wi
				left join `'.$wpdb->posts.'` p
					on p.ID = wi.tid and wi.tsrc = "wp_posts"
				set wi.force_rebuild = 1
				where
					(wi.force_rebuild = 0) and 
					((p.ID is null) or (wi.tdt != p.post_modified))
				';
			$this->db->query($q);

			// Find post records that have no index records yet and initialize them
			$start_from = 0;
			$chunk_length = 1000;

			$is_exit = false;

			while (!$is_exit) {

				$q = 'select 
						p.ID
					from `'.$wpdb->posts.'` p
					left join `'.$idx.'index` wi
						on p.ID = wi.tid and wi.tsrc = "wp_posts"
					where 
						(wi.id is null)
						limit '.$start_from.', '.$chunk_length.'
					';
				$r2 = $this->db->get_results($q, ARRAY_A);

				if (count($r2) > 0) {
					// We found some post records, we need to create new index records for them
					$vv = array();
					foreach ($r2 as $d) {
						$vv[] = '("'.$d['ID'].'", "wp_posts", "1970-01-01 00:00:00", 0, "1970-01-01 00:00:00", 1, "1970-01-01 00:00:00")';
					}

					if (count($vv) > 0) {
						$q = 'insert into `'.$idx.'index` (`tid`, `tsrc`, `tdt`, `build_time`, `update_dt`, `force_rebuild`, `locked_dt`) values '.implode(', ', $vv);
						$this->db->query($q);
					}

					if (count($r2) >= $chunk_length) {
						// May be there is another chunk?
						$start_from += $chunk_length;
					} else {
						$is_exit = true;
						break;
					}


				} else {
					$is_exit = true;
					break;
				}
			}

			$this->set_option('last_sync_ts', $time + 10 * 60);

			return true;
		}

		return false;
	}

	public function GetPostIndexStatus($post_ids = array())
	{
		$ret = array();

		if ((!is_array($post_ids)) || (count($post_ids) < 1)) {
			return $ret;
		}

		$idx = $this->GetDBPrefix();

		$ids = array();
		foreach ($post_ids as $d) {
			$ids[] = intval($d);
		}

		$q = 'select * from `'.$idx.'index` where tid in ('.implode(',', $ids).') and tsrc = "wp_posts"';
		$r2 = $this->db->get_results($q, ARRAY_A);

		$all_ret = array();
		foreach ($r2 as $dd) {
			$ret = $dd;

			$status_code = 0;
			$status_text = __('Unknown', 'fulltext-search');

			// Get status code and text
			if ($ret['force_rebuild'] > 0) {
				$status_code = 3;
				$status_text = __('Pending', 'fulltext-search');
			} else {
				if ($ret['build_time'] == 0) {
					$status_code = 0;
					$status_text = __('Not Indexed', 'fulltext-search');
				} else {
					if ($ret['build_time'] == 9999) {
						$delta =  strtotime($ret['update_dt']) - time();
						if ($delta < 30) {
							$status_code = 1;
							$status_text = __('Processing', 'fulltext-search');
						} else {
							// Timeout
							$status_code = 2;
							$status_text = __('Error', 'fulltext-search');
						}
					} else {
						if ($ret['build_time'] >= 10000) {
							$status_code = 4;
							$status_text = __('Ok', 'fulltext-search');			
						} else {
							// Strange!
							// @todo
						}
					}
				}
			}

			$ret['status_code'] = $status_code;
			$ret['status_text'] = $status_text;

			$all_ret['p'.$dd['tid']] = $ret;
		}

		foreach ($ids as $d) {
			if (!isset($all_ret['p'.$d])) {
				$all_ret['p'.$d] = array(
					'id' => 0,
					'status_code' => 0,
					'status_text' => 'Not Indexed',
				);
			}
		}

		return $all_ret;
	}

	public function SQLKeyValueLists($a)
	{
		$kk = array();
		$vv = array();
		foreach ($a as $k => $v) {
			$kk[] = '`'.$k.'`';
			$vv[] = '"'.addslashes($v).'"';
		}

		return array($kk, $vv);
	}

	public function SQLSetList($a)
	{
		$vv = array();
		foreach ($a as $k => $v) {
			$vv[] = '`'.$k.'` = "'.addslashes($v).'"';
		}

		return $vv;
	}

	public function GetRecordsToRebuild($n_max = 1)
	{	
		$idx = $this->GetDBPrefix();
		
		// To optimize MySQL query we going to make 2 requests
		$q = 'select 
				id, tid, tsrc
			from `'.$idx.'index` 
			where 
				(force_rebuild != 0)
				order by force_rebuild desc
			limit '.intval($n_max).'';
		$r = $this->db->get_results($q, ARRAY_A);
		
		if (count($r) > 0) {
			return $r;
		}

		// No 'force_rebuild' records. Let's check for 'build_time' records
		$q = 'select 
				id, tid, tsrc
			from `'.$idx.'index` 
			where 
				(build_time = 0)
			order by build_time asc, id asc 
			limit '.intval($n_max).'';
		$r = $this->db->get_results($q, ARRAY_A);
		
		return $r;
	}

	public function UpdateRecordData($id, $data = array())
	{
		$a = array();
		foreach ($data as $k => $d) {
			if (in_array($k, array('tdt', 'build_time', 'update_dt', 'force_rebuild', 'locked_dt'))) {
				$a[$k] = $d;
			}
		}

		if (count($a) > 0) {
			$idx = $this->GetDBPrefix();
				
			$vv = $this->SQLSetList($a);

			$q = 'update `'.$idx.'index` set '.implode(', ', $vv).' where id = "'.addslashes($id).'"';
			$this->db->query($q);
		}
	}

	public function IndexerLogStart($id, $data = array())
	{
		$a = array_filter($data, function($k) {
			return in_array($k, array('start_ts', 'getpost_ts', 'clusters_ts', 'cluster_stats', 'reindex_ts', 'status', 'error'));
		}, ARRAY_FILTER_USE_KEY);

		if (count($a) > 0) {
			$idx = $this->GetDBPrefix();
				
			$a['index_id'] = $id;

			list($kk, $vv) = $this->SQLKeyValueLists($a);

			$q = 'replace into `'.$idx.'ilog` ('.implode(',', $kk).') values ('.implode(',', $vv).')';
			$this->db->query($q);	
		}
	}

	public function IndexerLogUpdate($id, $data = array())
	{
		$a = array_filter($data, function($k) {
			return in_array($k, array('start_ts', 'getpost_ts', 'clusters_ts', 'cluster_stats', 'reindex_ts', 'status', 'error'));
		}, ARRAY_FILTER_USE_KEY);

		if (count($a) > 0) {
			$idx = $this->GetDBPrefix();
				
			$vv = $this->SQLSetList($a);
	
			$q = 'update `'.$idx.'ilog` set '.implode(', ', $vv).' where index_id = "'.addslashes($id).'"';
			$this->db->query($q);
		}
	}

	public function IndexerOneStep($sem = null)
	{
		$maxtime = 20;
		$start_ts = microtime(true);
		
		ignore_user_abort(true);
	
		$flare_period = 5;	// flare each 1 sec
		$sem_period = 5;	// semaphore update each 5 sec
		$series_period = 5;

		$is_loop_was_broken = false;

		$status = $this->get_status(true);

		// Send initial status to Flare
		$this->_flare->SendFire(array(
			'pt' => 'status', 
			'data' => $status
		));
		$next_flare_ts = $start_ts + $flare_period;

		if ($sem) {
			$sem->Update();
			$next_sem_ts = $start_ts + $sem_period;
		}

		$next_ts_series = $start_ts + $series_period;

		$ts_series = array();
		$aa = trim($this->get_option('ts_series', true));
		if (strlen($aa) >= 2) {
			$ts_series = json_decode(true);
			if (is_array($ts_series) && (count($ts_series) == 3)) {
				// Okay
			} else {
				// Initialize new estimator
				$ts_series = array(
					microtime(true),
					microtime(true),
					1
				);
			}
		}

		$n = 0;

		// Avoid this loop in case we have nothing to index
		if ($status['n_pending'] > 0) {

			while ((microtime(true) - $start_ts < $maxtime) && (!$is_loop_was_broken)) {

				$ids = $this->GetRecordsToRebuild(1000);

				if ($this->_dev_debug) {
					$this->_flare->SendFire(array('pt' => 'onestep', 'ids' => count($ids)));
				}

				if (count($ids) < 1) {
					break;
				}

				foreach ($ids as $item) {
				
					$is_break_loop = intval($this->get_option('is_break_loop', true));
					if ($is_break_loop > 0) {
						$is_loop_was_broken = true;
						break;
					}

					if (!(microtime(true) - $start_ts < $maxtime)) {
						break;
					}
				
					// Rebuild this record
					if (true /*$item['tsrc'] == 'wp_posts'*/) {

						$index_id = $item['id'];
						$item_start_ts = microtime(true);

						if (is_array($ts_series) && (count($ts_series) == 3)) {
							$ts_series[1] = $item_start_ts;
							$ts_series[2] ++;
						}

						$this->IndexerLogStart($index_id, array(
							'start_ts' => $item_start_ts,
							'getpost_ts' => 0,
							'clusters_ts' => 0,
							'cluster_stats' => '',
							'reindex_ts' => 0,
							'status' => 0,
							'error' => '',
						));

						// Check if locked and lock if not locked
						$post_id = $item['tid'];

						// Set build time to prevent consecutive rebuilds in case of error
						$time = time();
						$this->UpdateRecordData($item['id'], array(
							'build_time' => 9999,	// Special mark of possible error
							'update_dt' => date('Y-m-d H:i:s', $time),
							'force_rebuild' => 0,
						));

						// Record is prepared, lets index it now
						$post = get_post($post_id);

						$item_getpost_ts = microtime(true);

						$this->IndexerLogUpdate($index_id, array(
							'getpost_ts' => $item_getpost_ts - $item_start_ts,
							'status' => 1,
						));

						if ($post) {
							$modt = $post->post_modified;
							$chunks = $this->getPostChunks($post_id);

							$item_clusters_ts = microtime(true);

							$cluster_stats = array();
							foreach ($chunks as $k => $ch) {
								$cluster_stats[$k] = is_string($ch) ? mb_strlen($ch) : -1;	// -1 in case not a string
							}

							$this->IndexerLogUpdate($index_id, array(
								'clusters_ts' => $item_clusters_ts - $item_start_ts,
								'cluster_stats' => json_encode($cluster_stats),
								'status' => 2,
							));
					
/*								
if ($post_id == 35) {
// Fuckup simulating
header('500 Server Lost');
feoiwjfnowi();
exit();
}
*/
							$this->_index->clearLog();
							$this->_index->clearLogTime();

							// Force status recalculation
							$this->set_option('status_next_ts', 0);

							$res = $this->_index->reindex($item['id'], $chunks, false);

							// Force status recalculation (again!)
							// We need 2nd call because status may be recalculated too early by 
							// an external request
							$this->set_option('status_next_ts', 0);

							$item_reindex_ts = microtime(true);

							$item_status = $res ? 3 : -3;	// -3 means "Indexing error"
					
							$err = array();
							$log1 = $this->_index->getLog();
							if (strlen($log1) > 0) {
								$err['log'] = $log1;
							}
							$log1 = $this->_index->getLogTime();
							if (strlen($log1) > 0) {
								$err['logtime'] = $log1;
							}

							$tt = array(
								'reindex_ts' => $item_reindex_ts - $item_start_ts,
								'status' => $item_status,
								'error' => (count($err) > 0) ? json_encode($err) : '',
							);
							$this->IndexerLogUpdate($index_id, $tt);
	
							// Store some statistic
							$time = time();
							$this->_index->updateRecordData($item['id'], array(
								'tdt' => $modt,
								'build_time' => $time,
								'update_dt' => date('Y-m-d H:i:s', $time),
								'force_rebuild' => 0,
							));
	
							// Let's update status virtually, do not touch DB (to save some time)
							if (isset($status['n_pending']) && isset($status['n_actual']) && isset($status['n_inindex'])) {
								if ($status['n_pending'] > 0) {
									$status['n_pending'] --;
									if ($status['n_actual'] < $status['n_inindex']) {
										$status['n_actual'] ++;
									}
								}
								$status['tsd'] = time();
							}
						} else {
							// No post - remove index
							$this->IndexerLogUpdate($index_id, array(
								'clusters_ts' => -1,
								'cluster_stats' => '',
								'status' => 2,
							));

							$this->_index->clearLog();
							$this->_index->clearLogTime();
	
							// Remove index records
							$this->_index->removeIndexRecordForPost($post_id);
							$this->removeRawCache($post_id);

							// Force status recalculation
							$this->set_option('status_next_ts', 0);

							$item_reindex_ts = microtime(true);
	
							$item_status = 4;	// 4 = "removed"
						
							$err = array();
							$log1 = $this->_index->getLog();
							if (strlen($log1) > 0) {
								$err['log'] = $log1;
							}
							$log1 = $this->_index->getLogTime();
							if (strlen($log1) > 0) {
								$err['logtime'] = $log1;
							}
	
							$tt = array(
								'reindex_ts' => $item_reindex_ts - $item_start_ts,
								'status' => $item_status,
								'error' => (count($err) > 0) ? json_encode($err) : '',
							);
							$this->IndexerLogUpdate($index_id, $tt);
	
							// Let's update status virtually, do not touch DB (to save some time)
							if (isset($status['n_pending']) && isset($status['n_actual']) && isset($status['n_inindex'])) {
								if ($status['n_pending'] > 0) {
									$status['n_pending'] --;
									if ($status['n_actual'] < $status['n_inindex']) {
										$status['n_inindex'] --;
									}
								}
								$status['tsd'] = time();
							}
						}
					} else {
						// tsrc is not 'wp_posts'
						// Custom processing?
						// @todo
					}
				
					$n ++;

					$c_ts = microtime(true);

					// Do we need to store ts_series?
					if ($next_ts_series < $c_ts) {
						$this->set_option('ts_series', json_encode($ts_series));
						$next_ts_series = $c_ts + $series_period;
					}

					// Do we need to update semaphore?
					if ($sem && ($next_sem_ts < $c_ts)) {
						$sem->Update();
						$next_sem_ts = $c_ts + $sem_period;
					}

					// Do we need to update flare?
					if ($next_flare_ts < $c_ts) {
						// Update status cache before sending to flare
						$st_cache = json_decode($this->get_option('status_cache', true), true);
						$st_cache['n_inindex'] = $status['n_inindex'];
						$st_cache['n_pending'] = $status['n_pending'];
						$st_cache['n_actual'] = $status['n_actual'];
						$this->set_option('status_cache', json_encode($st_cache));

						$status = $this->get_status(true);

						$this->_flare->SendFire(array(
							'pt' => 'status', 
							'data' => $status
						));
						$next_flare_ts = $c_ts + $flare_period;	
					}
				}
			
				if ($n < 1) {
					break;
				}

			}

		}

		// Do we need to store ts_series?
		$c_ts = microtime(true);
		if ($next_ts_series < $c_ts) {
			$this->set_option('ts_series', json_encode($ts_series));
			$next_ts_series = $c_ts + $series_period;
		}

		$is_flush = false;
		if (($n < 1) && (microtime(true) - $start_ts < $maxtime) && (!$is_loop_was_broken)) {
			// Check if we have something to flush
			$n_fl = $this->_index->_getTWCount();

			if ($this->_dev_debug) {
				$this->_flare->SendFire(array('pt' => 'onestep2_twcount', 'n_fl' => $n_fl));
			}

			if ($n_fl > 0) {
				$this->_index->_flushTW();
				$is_flush = true;

				// Force status recalculation
				$this->set_option('status_next_ts', 0);

				if ($sem) {
					$sem->Update();
					$next_sem_ts = microtime(true) + $sem_period;
				}
			} else {
				// Nothing to index or flush! We are done!
				$this->set_option('index_ready', 1);
			}
		}

		if (($n > 0) || ($is_loop_was_broken)) {
			// Update the status copy from DB
			// This should be called after flush (otherwise the status will not reflect nw_* correctly)!
			$status = $this->get_status();
		}

		$is_optimizer = intval($this->get_option('is_optimizer', true));
		
		$is_not_acts = false;
		if ($is_optimizer) {
			if ((!$is_flush) && (!$is_loop_was_broken)) {

				if (($n < 1) && (microtime(true) - $start_ts < $maxtime) && ($status['nw_act'] < $status['nw_total'])) {
					// Check and process with vc
					while ((microtime(true) - $start_ts < $maxtime) && (!$is_loop_was_broken)) {
						$not_act = $this->_index->_getVCNotAct(1000);

						if ($this->_dev_debug) {
							$this->_flare->SendFire(array('pt' => 'onestep2_notact', 'not_act' => count($not_act)));
						}

						if (count($not_act) > 0) {
							$is_not_acts = true;
						} else {
							break;
						}

						foreach ($not_act as $wid) {

							$is_break_loop = intval($this->get_option('is_break_loop', true));
							if ($is_break_loop > 0) {
								$is_loop_was_broken = true;
								break;
							}

							if (!(microtime(true) - $start_ts < $maxtime)) {
								break;
							}

							$this->_index->indexWordData($wid);

							// Force status recalculation
							$this->set_option('status_next_ts', 0);

							// Update the status in variable only (to save some time)
							if (isset($status['nw_act']) && isset($status['nw_total'])) {
								if ($status['nw_act'] < $status['nw_total']) {
									$status['nw_act'] ++;
								}
								$status['ts'] = time();
							}

							// Update the semaphore and the flare
							$c_ts = microtime(true);

							// Do we need to update semaphore?
							if ($sem && ($next_sem_ts < $c_ts)) {
								$sem->Update();
								$next_sem_ts = $c_ts + $sem_period;
							}
		
							// Do we need to update flare?
							if ($next_flare_ts < $c_ts) {
								$this->_flare->SendFire(array(
									'pt' => 'status', 
									'data' => $status
								));
								$next_flare_ts = $c_ts + $flare_period;	
							}
		
						}
					}

				}
			}

		}
			
		$finish_ts = microtime(true);
		
		// Do we made anything useful in this pass?
		$is_useful = $is_loop_was_broken || ($n > 0) || $is_flush || $is_not_acts || (microtime(true) - $start_ts >= $maxtime);

		return $is_useful;
	}

	public function IndexerStart()
	{
		global $wpdb_debug;
		//$wpdb_debug = rand(10000, 99999);

		if ($this->_dev_debug) {
			$this->_flare->SendFire(array('pt' => 'indexer_cron'));
		}
		
		// Store last indexerStart ts for further usage
		// For example, we will prevent CallIndexerStartNoBlocking() from the wpfts_ping (get status)
		// in case IndexerStart was called from somewhere in 1,5 minutes or less
		$this->set_option('last_indexerstart_ts', time());

		$is_pause = intval($this->get_option('is_pause', true));
		if ($is_pause > 0) {
			// Indexer loop is on pause. Exit silently.
			if ($this->_dev_debug) {
				$this->_flare->SendFire(array('pt' => 'indexer_on_pause'));
			}
			return;
		}

		// Check the semaphore
		$sem = new WPFTS_Semaphore('inx_cron');
		$sem->timeout = 180;	// 3 minutes

		if (!$sem->Enter()) {
			// Another instance is processing indexing task now
			return;
		}

		if ($this->_dev_debug) {
			$this->_flare->SendFire(array('pt' => 'indexer_cron_enter'));
		}

		// Remove "break_loop" flag in case it was set before
		$this->set_option('is_break_loop', 0);

		// Semaphore is free - go to indexer cycle
		// Check if we need to make posts resync (once in 10 min)
		$time = time();

		// Make posts sync
		if ($this->MakePostsSync()) {
			$sem->Update();
		}

		$is_useful = $this->IndexerOneStep($sem);

		if ($this->_dev_debug) {
			$this->_flare->SendFire(array('pt' => 'indexer_leave'));
		}

		// Free the semaphore instance to let this method to be called again
		$sem->Leave();

		if ($is_useful) {
			// In case this pass was useful (we did something), try to run the task again!
			$this->CallIndexerStartNoBlocking();
		}
	}
}

class WPFTS_Addon_Base
{
	public function __construct()
	{
		
	}
}