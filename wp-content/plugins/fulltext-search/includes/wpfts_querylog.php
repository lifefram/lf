<?php

class WPFTS_QueryLog
{
	function __construct()
	{
		$this->add_hooks();

	}

	public function add_hooks()
	{
		/*
		add_action('wpfts_init_addons', function()
		{
			if (is_admin()) {
				add_action('wp_ajax_wpftsi_get_qlog_data', array($this, 'ajax_get_qlog_data'));
				add_action('wp_ajax_wpftsi_get_qlog_settings', array($this, 'ajax_get_qlog_settings'));
			}
		});

		add_action('parse_query', array($this, 'Start'), -32768, 1);	// Guarantee first call
		add_action('pre_get_posts', array($this, 'GoSearch'), 32768, 1);	// Guarantee last call before pre_get_posts exis
		*/
	}

	public function createQLogDB($query, $params = array(), $search_type = '', $preset_ident = '')
	{
		global $wpfts_core, $wpdb;

		$pfx = $wpfts_core->GetDBPrefix();

		$wpfts_core->db->insert($pfx.'qlog', array(
			'query' => $query,
			'query_type' => $search_type,
			'preset' => $preset_ident,
			'n_results' => -1,
			'q_time' => -1,
			'max_ram' => -1,
			'user_id' => 0,
			'req_ip' => '',
			'ref_url' => '',
			'insert_dt' => date('Y-m-d H:i:s', current_time('timestamp')),
			'ext' => '',
			'wpq_params' => json_encode($params),
		));

		return $wpdb->insert_id;
	}
	
	public function simplifyBacktrace($full_backtrace = array())
	{
		$res = array();
		if ($full_backtrace && is_array($full_backtrace)) {
			foreach ($full_backtrace as $k => $item) {
				$r = array();
				foreach (array('file', 'line', 'function', 'class', 'object', 'type', 'args') as $t) {
					if (isset($item[$t])) {
						if ($t == 'object') {
							$trr = get_object_vars($item[$t]);
							$zz = array();
							foreach ($trr as $kk => $dd) {
								if (is_object($dd)) {
									$zz[$kk] = '*OBJECT::'.get_class($dd);
								} elseif (is_array($dd)) {
									$zz[$kk] = '*ARRAY::*';
								} else {
									$zz[$kk] = $dd;
								}
							}
							$r[$t] = $zz;
						} elseif ($t == 'args') {
							
						} else {
							$r[$t] = $item[$t];
						}
					}
				}
				$res[$k] = $r;
			}
		}

		return $res;
	}

	public function GetAllowedOrderBy()
	{
		return array(
			'datetime' => array('desc', 'insert_dt'), 
			'query' => array('asc', 'query'),
			'type' => array('asc', 'query_type'),
			'preset' => array('asc', 'preset'),
			'n_results' => array('desc', 'n_results'),
			'time_spent' => array('desc', 'q_time'),
			'max_ram' => array('desc', 'max_ram'),
			'user_id' => array('asc', 'user_id'),
			'req_ip' => array('asc', 'req_ip'),
			'ref_url' => array('asc', 'ref_url'),
			'widget_name' => array('asc', 'widget_name'),
		);
	}

	public function GetValidSettings()
	{
		global $wpfts_core;

		$sts = json_decode($wpfts_core->get_option('qlog_settings'), true);

		$allowed_orderby = $this->GetAllowedOrderBy();

		$column_list = array(
			'datetime',
			'query',
			'type',
			'preset',
			'n_results',
			'time_spent',
		);

		$sts['lines_per_page'] = (isset($sts['lines_per_page'])) ? intval($sts['lines_per_page']) : 50;
		$sts['order'] = (isset($sts['order']) && ($sts['order'] == 'asc')) ? 'asc' : 'desc';
		$sts['orderby'] = (isset($sts['orderby']) && isset($allowed_orderby[$sts['orderby']])) ? $sts['orderby'] : 'datetime';

		$sts['columns'] = $column_list;

		$sts['querylog_enabled'] = (isset($sts['querylog_enabled']) && ($sts['querylog_enabled'] == 0)) ? 0 : 1;
		$sts['nonwpfts_queries'] = (isset($sts['nonwpfts_queries']) && ($sts['nonwpfts_queries'] != 0)) ? 1 : 0;
		$sts['nontext_queries'] = (isset($sts['nontext_queries']) && ($sts['nontext_queries'] != 0)) ? 1 : 0;
		$sts['detailed_log'] = (isset($sts['detailed_log']) && ($sts['detailed_log'] != 0)) ? 1 : 0;
		

		return $sts;
	}

	public function ajax_get_qlog_data()
	{
		global $wpfts_core;

		$sts = $this->GetValidSettings();

		// Detect set_props mode
		if (isset($_POST['set_props'])) {
			$set_props = json_decode(stripslashes($_POST['set_props']), true);

			$is_updated = false;
			foreach ($set_props as $k => $d) {
				switch ($k) {
					case 'lines_per_page':
					case 'order':
					case 'orderby':
						$sts[$k] = $d;
						$is_updated = true;
						break;
				}
			}

			if ($is_updated) {
				$wpfts_core->set_option('qlog_settings', json_encode($sts));
			}

			$sts = $this->GetValidSettings();
		}

		$page_num = isset($_POST['current_page']) ? max(1, intval($_POST['current_page'])) : 1;

		$allowed_orderby = $this->GetAllowedOrderBy();

		$idx = $wpfts_core->GetDBPrefix();

		$lines_per_page = $sts['lines_per_page'];
		
		$limit_start = ($page_num - 1) * $lines_per_page;
		$limit_length = $lines_per_page;

		// Create 'where'
		$w = '1';

		// Calculate total records
		$q = 'select count(*) n from `'.$idx.'qlog` where '.$w;
		$res2 = $wpfts_core->db->get_results($q, ARRAY_A);

		$n_total = (count($res2) > 0) ? $res2[0]['n'] : 0;

		$q = 'select 
				`id`, 
				`insert_dt`,
				`query`,
				`query_type`,
				`preset`,
				`n_results`,
				`q_time`,
				`max_ram`,
				`user_id`,
				`req_ip`,
				`ref_url`,
				`widget_name`
			from `'.$idx.'qlog'.'` where '.$w;
		if (isset($allowed_orderby[$sts['orderby']])) {
			$q .= ' order by `'.$allowed_orderby[$sts['orderby']][1].'` '.$sts['order'];
		}
		$q .= ' limit '.$limit_start.', '.$limit_length;

		$res = $wpfts_core->db->get_results($q, ARRAY_A);

		$result = array(
			'code' => 0,
			'error' => 'OK',
			'lines' => $res,
			'page_num' => $page_num,
			'n_total' => $n_total,
			'settings' => $sts,
		);

		echo json_encode($result);
		wp_die();
	}

	public function ajax_get_qlog_settings()
	{
		global $wpfts_core;

		$sts = $this->GetValidSettings();

		// Detect set_props mode
		if (isset($_POST['set_props'])) {
			$set_props = json_decode(stripslashes($_POST['set_props']), true);

			$is_updated = false;
			foreach ($set_props as $k => $d) {
				switch ($k) {
					case 'querylog_enabled':
					case 'nonwpfts_queries':
					case 'nontext_queries':
					case 'detailed_log':
						$sts[$k] = $d;
						$is_updated = true;
						break;
				}
			}

			if ($is_updated) {
				$wpfts_core->set_option('qlog_settings', json_encode($sts));
			}

			$sts = $this->GetValidSettings();
		}

		$result = array(
			'code' => 0,
			'error' => 'OK',
			'settings' => $sts,
		);

		echo json_encode($result);
		wp_die();
	}

	/**
	 * Start QueryLog functionality and prepare main data
	 */
	public function Start($wpq)
	{
		global $wpfts_core;

		if ($wpq && is_object($wpq) && isset($wpq->query_vars)) {
			// Valid WP_Query object
			if (!isset($wpq->wpftsi_session['qlog_token'])) {
				// Start the new qlog session
				$qlog_token = sha1(uniqid('qlog_token_').'|'.time());
				$wpq->wpftsi_session = array(
					'qlog_token' => $qlog_token,
					'qlog_enabled' => 0,	// To be changed below
				);
				
				$qlog_settings = $this->GetValidSettings();

				$is_qlog_enabled = $qlog_settings['querylog_enabled'];
				// Override by wp_query
				if (isset($wpq->query_vars['wpfts_querylog_enabled'])) {
					$is_qlog_enabled = ($wpq->query_vars['wpfts_querylog_enabled'] == 0) ? 0 : 1;
				}

				$is_detailed_log = $qlog_settings['detailed_log'];
				// Override by wp_query
				if (isset($wpq->query_vars['wpfts_detailed_log'])) {
					$is_detailed_log = ($wpq->query_vars['wpfts_detailed_log'] == 0) ? 0 : 1;
				}

				$is_text_search = (isset($wpq->query_vars['s']) && (strlen($wpq->query_vars['s']) > 0)) ? 1 : 0;
				// Check if we need to reset is_qlog_enabled
				if ((!$is_text_search) && ($qlog_settings['nontext_queries'] == 0)) {
					$is_qlog_enabled = 0;	// Disable, because we don't allow to log non-text queries
				}

				$is_admin = is_admin();
				$is_main_query = $wpq->is_main_query();

				$full_bktr = debug_backtrace();
				$bktr = $this->simplifyBacktrace($full_bktr);
				$wpq->wpftsi_session['start_bktrace'] = $full_bktr;

				$sysvars = array(
					'qlog_settings' => $qlog_settings,
					'is_detailed_log' => $is_detailed_log,
					'is_qlog_enabled' => $is_qlog_enabled,
					'is_main_query' => $is_main_query,
					'is_admin' => $is_admin,
					'is_text_search' => $is_text_search,
				);

				$sysvars = apply_filters('wpfts_start_sysvars', $sysvars, $wpq);

				$wpq->wpftsi_session['sysvars'] = $sysvars;

				// Detect search type
				$search_type = '';	// Not detected
				if (isset($sysvars['is_text_search']) && ($sysvars['is_text_search'])) {
					if (isset($sysvars['is_main_query']) && ($sysvars['is_main_query'])) {
						// It could be admin or not admin WP main query
						if (isset($sysvars['is_admin']) && ($sysvars['is_admin'])) {
							// Admin mode
							$search_type = 'wpmainsearch_admin';
						} else {
							// Not admin
							$search_type = 'wpmainsearch_frontend';
						}
					} else {
						// Not main query
						$search_type = '';
					}	
				} else {
					// Not text search
					$search_type = '';
				}

				$search_type = apply_filters('wpfts_start_search_type', $search_type, $wpq);

				// The search_type can be overriden by query_vars
				if (isset($wpq->query_vars['wpfts_search_type'])) {
					$search_type = $wpq->query_vars['wpfts_search_type'];
				}
				$wpq->wpftsi_session['search_type'] = $search_type;

				// Get preset based on search_type
				$preset_ident = '';
				if (isset($wpq->query_vars['wpfts_preset']) && (strlen($wpq->query_vars['wpfts_preset']) > 0)) {
					$preset_ident = trim($wpq->query_vars['wpfts_preset']);
				} else {
					$preset_ident = $wpfts_core->GetPresetBySearchType($search_type);
				}
				// A chance to change preset
				$preset_ident = apply_filters('wpfts_preset_detected', $preset_ident, $wpq);

				$wpq->wpftsi_session['preset_ident'] = $preset_ident;

				// Get preset data (only for defined presets)
				$preset_data = array();
				if (strlen($preset_ident) > 0) {
					$preset_data = $wpfts_core->GetPresetData($preset_ident);
				}

				$wpq->wpftsi_session['preset_data'] = $preset_data;

				// In case this preset does not support WPFTS search, check if we need to disable logging
				$preset_is_wpfts_search_enabled = (isset($preset_data['is_wpfts_search_enabled']) && ($preset_data['is_wpfts_search_enabled'] != 0)) ? 1 : 0;
				$is_nonwpfts_queries = $qlog_settings['nonwpfts_queries'];

				if ((!$preset_is_wpfts_search_enabled) && (!$is_nonwpfts_queries)) {
					$is_qlog_enabled = 0;	// Disable query logging, because we do not allow non-WPFTS queries to be logged
					$sysvars['is_qlog_enabled'] = $is_qlog_enabled;
				}

				$wpq->wpftsi_session['start_ts'] = microtime(true);
				$wpq->wpftsi_session['start_ram'] = memory_get_usage();
				$wpq->wpftsi_session['start_ram_peak'] = memory_get_peak_usage();
				
				if ($is_qlog_enabled) {
					// Okay, we are enabled to use qlog
					$wpq->wpftsi_session['qlog_enabled'] = 1;
					$qlog_id = $this->createQLogDB($wpq->query_vars['s'], $wpq->query_vars, $search_type, $preset_ident);

					if ($qlog_id > 0) {
						// Let's log start variables
						$start_data = array(
							'token' => $wpq->wpftsi_session['qlog_token'],
							'start_ts' => $wpq->wpftsi_session['start_ts'],
							'start_ram' => $wpq->wpftsi_session['start_ram'],
							'start_ram_peak' => $wpq->wpftsi_session['start_ram_peak'],
							'query' => (Array)$wpq->query,
							'query_vars' => (Array)$wpq->query_vars,
							'bktr' => $bktr,
							'sysvars' => $sysvars,
							'preset_data' => $preset_data,
						);
						self::AddLog($qlog_id, 'wpfts_qlog_start', $start_data);



					} else {
						// Failed to create qlog record in DB!
						$qlog_id = rand(95000000, 95999999);
					}

				} else {
					// QueryLog not used, but we still need for unique q_id
					$qlog_id = rand(99000000, 99999999);
				}
				$wpq->wpftsi_session['q_id'] = $qlog_id;

			} else {
				// The wpfts session was already started for this WP_Query object, do nothing
			}
		}
	}

	public function GoSearch($wpq)
	{
		global $wpfts_core;

		if ($wpq && is_object($wpq) && isset($wpq->query_vars)) {
			$ses = $wpq->wpftsi_session;

/*			echo '1234@@';
print_r($wpq->wpftsi_session['token']);
if (isset($wpq->wpftsi_session['token'])) {
	print_r($wpq->wpftsi_session);
}*/
			if (isset($ses['qlog_enabled']) && ($ses['qlog_enabled'] == 1)) {
//echo '5678##';				
//print_r($wpq->wpftsi_session);
				$q_id = isset($ses['q_id']) ? $ses['q_id'] : 0;
				if ($q_id > 0) {
					// Log current data
					$data = array(
						'ram' => memory_get_usage(),
						'ram_peak' => memory_get_peak_usage(),
						'query' => (Array)$wpq->query,
						'query_vars' => (Array)$wpq->query_vars,
					);
					self::AddLog($q_id, 'wpfts_qlog_gosearch', $data);
				}
			}
		}
	}

	/**
	 * This method adds some log data to the 'ext' column of the wpftsi_qlog table
	 * It can be any data, basically, and they will be decoded in the Query Log viewer
	 * 
	 */
	public static function AddLog($qlog_id, $step_ident, $data = array())
	{
		if (($qlog_id < 1) && ($qlog_id > 90000000)) {
			// $qlog_id is none or virtual, so do not record data to DB!
			return false;
		}

		$step_ident = trim(str_replace('%$%', '\x25\x24\x25', $step_ident));

		if (strlen($step_ident) < 1) {
			// We require step_ident to be valid non-empty string
			return false;
		}

		global $wpfts_core;
		$idx = $wpfts_core->GetDBPrefix();

		$timestamp = microtime(true);
		$json1 = '|'.$step_ident.'|'.$timestamp.'|'.json_encode($data);
		
		$q = 'update `'.$idx.'qlog` set `ext` = concat(`ext`, "%$%", "'.strlen($json1).'", "'.addslashes($json1).'") where `id` = "'.addslashes($qlog_id).'"';
		$wpfts_core->db->query($q);

		return true;
	}
}