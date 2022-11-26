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
 *  @package Wordpress Fulltext Search Pro
 *  @author Epsiloncool <info@e-wm.org>
 */

global $wpfts_context;

$wpfts_context = false;

class WPFTS_Context
{
	public $index_post = false;
	public $index_token = false;
}

class WPFTS_Index
{
	public $prefix = 'wpftsi_';
	public $max_word_length = 255;
	public $error = '';
	public $lock_time = 300;	// 5 min

	protected $stops = array();
	
	protected $_log = array();
	protected $_logtime = array();
	
	protected $_islock = true;
	
	// Logging
	public $_lastt0 = 0;
	public $_is_log = 0;
	public $_is_show = 0;

	public $t_words_rows = -1;

	function __construct()
	{
		//
	}

	function dbprefix()
	{
		global $wpdb;

		if (function_exists('is_multisite') && is_multisite()) {
			$blog_id = $wpdb->blogid;
			if ($blog_id > 1) {
				return $this->prefix.$blog_id.'_';
			}
		}
		return $this->prefix;
	}

	protected function load_stops()
	{	
		global $wpfts_core;
		
		$q = 'select `word` from `'.$this->dbprefix().'stops`';
		$res = $wpfts_core->db->get_result($q, ARRAY_A);
		
		$z = array();
		foreach ($res as $d) {
			$z[mb_strtolower($d['word'])] = 1;
		}
		$this->stops = $z;
	}
	
	public function log($message)
	{
		$this->_log[] = $message;
	}
	
	public function clearLog()
	{
		$this->_log = array();
	}
	
	public function getLog()
	{
		return implode("\n", $this->_log);
	}
	
	public function getLogTime()
	{
		return implode("\n", $this->_logtime);
	}

	public function clearLogTime()
	{
		$this->_logtime = array();
		$this->_lastt0 = microtime(true);
	}
	
	public function check_db_tables()
	{
		global $wpfts_core;
		
		$sch = $this->getDbScheme();
		
		// Check all tables
		$wrongs = array();
		foreach ($sch as $k => $d) {
			$q = 'SHOW TABLES LIKE "'.$this->dbprefix().$k.'"';
			$res = $wpfts_core->db->get_results($q, ARRAY_A);
			if (count($res) > 0) {
				// Table exists
				
				// Check columns
				$q = 'show columns from `'.$this->dbprefix().$k.'`';
				$res = $wpfts_core->db->get_results($q, ARRAY_A);
				
				if (count($d['cols']) === count($res)) {
					
					foreach ($res as $dd) {
						if (isset($d['cols'][$dd['Field']])) {
							
							$cl = $d['cols'][$dd['Field']];
							if ($cl[0] != $dd['Type']) {
								$wrongs[$k] = 'ntype:'.$dd['Field'];
								break;
							}
							if ($cl[1] != $dd['Null']) {
								$wrongs[$k] = 'nnull:'.$dd['Field'];
								break;
							}
							/*
							Key
							Default
							Extra
							*/	
							
						} else {
							$wrongs[$k] = 'ncol:'.$dd['Field'];
							break;
						}
					}
					
				} else {
					$wrongs[$k] = 'nrows';
				}
				
			} else {
				$wrongs[$k] = 'nexist';
			}
		}
		
		return $wrongs;
	}
	
	public function create_db_tables($only_listed = false) 
	{
		global $wpfts_core;
		
		$success = true;
		
		$sch = $this->getDbScheme();

		foreach ($sch as $k => $d) {
			
			if ((!is_array($only_listed)) || (is_array($only_listed) && in_array($k, $only_listed))) {
				$q = 'drop table if exists `'.$this->dbprefix().$k.'`';
				$wpfts_core->db->query($q);
			
				$wpfts_core->db->query($d['create2']);
				if ($wpfts_core->db->get_last_error()) {
					$this->log('Can\'t create table "'.$this->dbprefix().$k.'": '.$wpfts_core->db->get_last_error());
					$success = false;
				}
			}
		}
		if ($success) {
			$wpfts_core->set_option('current_db_version', WPFTS_VERSION);
		}
		
		return $success;
	}

	public function CreateMySQLQuery($dbscheme)
	{
		global $wpdb;

		$engine_type = 'InnoDB';
	
		// Make Mysql Db creation queries
		foreach ($dbscheme as $k => $d) {
			
			$s = 'CREATE TABLE `'.$this->dbprefix().$k.'` ('."\n";
			
			$cs = array();
			$ai = false;
			foreach ($d['cols'] as $kk => $dd) {
				$ss = '`'.$kk.'` '.$dd[0].' '.($dd[1] == 'NO' ? 'NOT NULL' : 'NULL');
				if (isset($dd[3])) {
					$ss .= ' default \''.$dd[3].'\'';
				}
				if ((isset($dd[4])) && ($dd[4] == 'auto_increment')) {
					$ss .= ' auto_increment';
					$ai = true;
				}
				$cs[] = $ss;
			}
		
			$iz = array();
			foreach ($d['index'] as $kk => $dd) {
				$ss = '';
				if ($kk == 'PRIMARY') {
					$ss = 'PRIMARY KEY';
				} else {
					if ($dd[0] == 0) {
						$ss = 'UNIQUE KEY `'.$kk.'`';
					} else {
						$ss = 'KEY `'.$kk.'`';
					}
				}
				$ws = explode(',', $dd[1]);
				$zz = array();
				foreach ($ws as $z) {
					$zz[] = ''.$z.'';
				}
				$ss .= ' ('.implode(',', $zz).')';
				
				$iz[] = $ss;
			}
			
			$s .= implode(",\n", $cs);
			
			if (count($iz) > 0) {
				$s .= ",\n".implode(",\n", $iz);
			}
			
			$s .= "\n".') ENGINE='.$engine_type.($ai ? ' AUTO_INCREMENT=1' : ''); // .' DEFAULT CHARSET=utf8';
			
			$collate = $wpdb->get_charset_collate();
			if (strlen($collate) > 0) {
				$s .= ' '.$collate;
			}

			$dbscheme[$k]['create2'] = $s;
		}
		
		return $dbscheme;		
	}

	public function getDbScheme()
	{
		$dbscheme = array(
			'docs' => array(
				'cols' => array(
					// name => type, isnull, keys, default, extra
					'id' => array('int(11)', 'NO', 'PRI', null, 'auto_increment'),
					'index_id' => array('int(11)', 'NO', 'MUL', '0'),
					'token' => array('varchar(255)', 'NO', 'MUL'),
					'n' => array('int(10) unsigned', 'NO', '', '0'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'token' => array(1, '`token`(190)'),
					'index_id' => array(1, '`index_id`'),
				),
				'create' => "CREATE TABLE `wpftsi_docs` (
								`id` int(11) NOT NULL auto_increment,
								`index_id` int(11) NOT NULL,
								`token` varchar(255) NOT NULL,
								`n` int(10) unsigned NOT NULL,
								PRIMARY KEY  (`id`),
								KEY `token` (`token`),
								KEY `index_id` USING BTREE (`index_id`)
							) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8",
			),
			'index' => array(
				'cols' => array(
					'id' => array('int(10) unsigned', 'NO', 'PRI', null, 'auto_increment'),
					'tid' => array('bigint(10) unsigned', 'NO', 'MUL'),		
					'tsrc' => array('varchar(255)', 'NO', 'MUL'),		
					'tdt' => array('datetime', 'NO', '', '1970-01-01 00:00:00'),
					'build_time' => array('int(11)', 'NO', 'MUL', '0'),	
					'update_dt' => array('datetime', 'NO', '', '1970-01-01 00:00:00'),
					'force_rebuild' => array('tinyint(4)', 'NO', 'MUL', '0'),
					'locked_dt' => array('datetime', 'NO', 'MUL', '1970-01-01 00:00:00'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'tid_tsrc_unique' => array(0, '`tid`,`tsrc`(100)'),
					'tid' => array(1, '`tid`'),
					'build_time' => array(1, '`build_time`'),
					'force_rebuild' => array(1, '`force_rebuild`'),
					'locked_dt' => array(1, '`locked_dt`'),
					'tsrc' => array(1, '`tsrc`(100)'),
				),
				'create' => "CREATE TABLE `wpftsi_index` (
								`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
								`tid` bigint(10) unsigned NOT NULL,
								`tsrc` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
								`tdt` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
								`build_time` int(11) NOT NULL DEFAULT '0',
								`update_dt` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
								`force_rebuild` tinyint(4) NOT NULL DEFAULT '0',
								`locked_dt` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
								PRIMARY KEY (`id`),
								UNIQUE KEY `tid_tsrc_unique` (`tid`,`tsrc`(100)) USING BTREE,
								KEY `tid` (`tid`),
								KEY `build_time` (`build_time`),
								KEY `force_rebuild` (`force_rebuild`),
								KEY `locked_dt` (`locked_dt`),
								KEY `tsrc` (`tsrc`(100)) USING BTREE
				  			) ENGINE=InnoDB AUTO_INCREMENT=45645 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci",
				),
			'stops' => array(
				'cols' => array(
					'id' => array('int(10) unsigned', 'NO', 'PRI', null, 'auto_increment'),
					'word' => array('varchar(255)', 'NO', 'UNI'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'word' => array(0, '`word`(190)'),
				),
				'create' => 'CREATE TABLE `wpftsi_stops` (
								`id` int(10) unsigned NOT NULL auto_increment,
								`word` varchar(32) character set utf8 collate utf8_bin NOT NULL,
								PRIMARY KEY  (`id`),
								UNIQUE KEY `word` (`word`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8',
			),
			'vectors' => array(
				'cols' => array(
					'wid' => array('int(11)', 'NO', 'PRI'),
					'did' => array('int(11)', 'NO', 'PRI'),
					'wn' => array('int(11)', 'NO', '', '0'),
				),
				'index' => array(
					'did_wn' => array(0, '`did`,`wn`'),
					'wid' => array(1, '`wid`'),
					'did' => array(1, '`did`'),
				),
				'create' => 'CREATE TABLE `wpftsi_vectors` (
								`wid` int(11) NOT NULL,
								`did` int(11) NOT NULL,
								`wn` int(11) NOT NULL,
								UNIQUE KEY `wid` (`wid`,`did`,`wn`),
								KEY `wid_2` (`wid`),
								KEY `did` (`did`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8',
			),
			'words' => array(
				'cols' => array(
					'id' => array('int(11)', 'NO', 'PRI', null, 'auto_increment'),
					'word' => array('varchar(255)', 'NO', 'UNI'),
					'act' => array('int', 'NO', '', '-1'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'word' => array(0, '`word`(190)'),
					'act' => array(1, '`act`'),
				),
				'create' => 'CREATE TABLE `wpftsi_words` (
								`id` int(11) NOT NULL auto_increment,
								`word` varchar(255) character set utf8 collate utf8_bin NOT NULL,
								PRIMARY KEY  (`id`),
								UNIQUE KEY `word` (`word`)
							) ENGINE=MyISAM AUTO_INCREMENT=173320 DEFAULT CHARSET=utf8',
			),
			'tw' => array(
				'cols' => array(
					'id' => array('int(11)', 'NO', 'PRI', null, 'auto_increment'),
					'w' => array('varchar(255)', 'NO'),
					'did' => array('int(11)', 'NO', '', '0'),
					'wn' => array('int(11)', 'NO', '', '0'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'w' => array(1, '`w`(190)'),
				),
				'create' => "CREATE TABLE `wpfts_tw` (
								`w` varchar(255) NOT NULL,
								`did` int(11) NOT NULL DEFAULT '0',
								`wn` int(11) NOT NULL DEFAULT '0',
								KEY `w` (`w`)
				  			) ENGINE=InnoDB DEFAULT CHARSET=utf8",
			),
			'vc' => array(
				'cols' => array(
					'id' => array('int(11)', 'NO', 'PRI', null, 'auto_increment'),
					'wid' => array('int(11)', 'NO', '', '0'),
					'upd_dt' => array('datetime', 'NO', '', '1970-01-01 00:00:00'),
					'vc' => array('longblob'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'wid' => array(1, '`wid`'),
				),
				'create' => "CREATE TABLE `wpftsi_vc` (
  								`id` int(11) NOT NULL AUTO_INCREMENT,
  								`wid` int(11) NOT NULL DEFAULT '0',
  								`upd_dt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  								`vc` longblob,
  								PRIMARY KEY (`id`),
  								KEY `wid` (`wid`)
							) ENGINE=InnoDB AUTO_INCREMENT=456101 DEFAULT CHARSET=utf8",
			),
			'tp' => array(
				'cols' => array(
					'q_id' => array('int(11)', 'NO', '', '0'),
					'did' => array('int(11)', 'NO', '', '0'),
					'pow' => array('int(11)', 'NO', '', '0'),
					'res' => array('float(10,6)', 'NO', '', '0'),
					'ts' => array('timestamp', 'NO', '', '1970-01-02 00:00:00'),
				),
				'index' => array(
					'did' => array(1, '`did`'),
					'q_id' => array(1, '`q_id`'),
				),
				'create' => "CREATE TABLE `wpftsi_tp` (
								`q_id` int(11) NOT NULL,
								`did` int(11) NOT NULL,
								`pow` int(11) NOT NULL,
								`res` float(10,6) NOT NULL,
								`ts` timestamp NOT NULL DEFAULT '1970-01-02 00:00:00',
								KEY `did` (`did`),
								KEY `q_id` (`q_id`)
				  			) ENGINE=InnoDB DEFAULT CHARSET=utf8",
			),
			'qlog' => array(
				'cols' => array(
					'id' => array('int(11)', 'NO', 'PRI', null, 'auto_increment'),
					'query' => array('longtext', 'YES_NULL'),
					'n_results' => array('int(11)', 'NO', '', '0'),
					'q_time' => array('float(10,6)', 'NO', '', '0'),
					'insert_dt' => array('datetime', 'NO', '', '1970-01-01 00:00:00'),
					'wpq_params' => array('longtext'),
					'ext' => array('longtext'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
				),
				'create' => "CREATE TABLE `wpftsi_qlog` (
								`id` int(11) NOT NULL AUTO_INCREMENT,
								`query` longtext,
								`n_results` int(11) NOT NULL DEFAULT '0',
								`q_time` float(10,6) NOT NULL DEFAULT '0.000000',
								`insert_dt` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
								`wpq_params` longtext,
								`ext` longtext,
								PRIMARY KEY (`id`)
				  			) ENGINE=InnoDB DEFAULT CHARSET=utf8",
			),
			'map' => array(
				'cols' => array(
					'id' => array('bigint(20) unsigned', 'NO', 'PRI', null, 'auto_increment'),
					'post_id' => array('bigint(20) unsigned', 'NO', '', '0'),
					'obj_id' => array('bigint(20) unsigned', 'NO', '', '0'),
					'obj_type' => array('varchar(50)', 'NO', '', ''),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'post_id' => array(0, '`post_id`'),
					'obj_id' => array(0, '`obj_id`,`obj_type`'),
				),
				'create' => "CREATE TABLE `wpftsi_map` (
								`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
								`post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
								`obj_id` bigint(20) unsigned NOT NULL DEFAULT '0',
								`obj_type` varchar(50) NOT NULL DEFAULT '',
								PRIMARY KEY (`id`),
								UNIQUE KEY `post_id` (`post_id`),
								UNIQUE KEY `obj_id` (`obj_id`,`obj_type`)
				  			) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4",
			),
			'queue' => array(
				'cols' => array(
					'id' => array('bigint(20) unsigned', 'NO', 'PRI', null, 'autoincrement'),
					'post_id' => array('bigint(20) unsigned', 'NO', '', '0'),
					'remark' => array('varchar(255)', 'NO', '', ''),
					'insert_dt' => array('datetime', 'NO', '', '1970-01-01 00:00:00'),
				),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'post_id' => array(1, '`post_id`'),
				),
				'create' => "CREATE TABLE `wpftsi_queue` (
								`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
								`post_id` int(11) NOT NULL DEFAULT '0',
								`remark` varchar(255) NOT NULL DEFAULT '',
								`insert_dt` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
								PRIMARY KEY (`id`),
								KEY `post_id` (`post_id`)
				  			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
			),
			'rawcache' => array(
				'cols' => array(
					'id' => array('int(10) unsigned', 'NO', 'PRI', null, 'auto_increment'),
					'object_id' => array('int(11)', 'NO', 'MUL', 0),
					'object_type' => array('varchar(150)', 'YES'),
					'cached_dt' => array('datetime', 'YES'),
					'insert_dt' => array('datetime', 'YES'),
					'method_id' => array('varchar(150)', 'YES', '', ''),
					'data' => array('longtext', 'YES'),
					'error' => array('text', 'YES'),
					'filename' => array('text', 'YES'),
					),
				'index' => array(
					'PRIMARY' => array(0, '`id`'),
					'object_id_and_type' => array(0, '`object_id`,`object_type`'),
				),
				'create' => "CREATE TABLE `wpftsi_rawcache` (
  					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  					`object_id` int(11) NOT NULL DEFAULT '0',
  					`object_type` varchar(255) DEFAULT NULL,
  					`cached_dt` datetime DEFAULT NULL,
  					`insert_dt` datetime DEFAULT NULL,
  					`method_id` varchar(255) DEFAULT '',
  					`data` longtext,
  					`error` text,
  					`filename` text,
  					PRIMARY KEY (`id`),
  					UNIQUE KEY `object_id_and_type` (`object_id`,`object_type`) USING BTREE
				  ) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8",

			),
			'ilog' => array(
				'cols' => array(
					'index_id' => array('int(10) unsigned', 'NO', '', '0'),
					'start_ts' => array('double(18,6)', 'NO', '', '0.000000'),
					'getpost_ts' => array('double(18,6)', 'NO', '', '0.000000'),
					'clusters_ts' => array('double(18,6)', 'NO', '', '0.000000'),
					'cluster_stats' => array('longtext', 'YES'),
					'reindex_ts' => array('double(18,6)', 'NO', '', '0.000000'),
					'status' => array('int(11)', 'NO', '', '0'),
					'error' => array('longtext', 'YES'),
				),
				'index' => array(
					'index_id' => array(0, '`index_id`'),
					'status' => array(1, '`status`'),
				),
				'create' => "CREATE TABLE `wpftsi_ilog` (
					`index_id` int(10) unsigned NOT NULL,
					`start_ts` double(18,6) NOT NULL DEFAULT '0.000000',
					`getpost_ts` double(18,6) NOT NULL DEFAULT '0.000000',
					`clusters_ts` double(18,6) NOT NULL DEFAULT '0.000000',
					`cluster_stats` longtext,
					`reindex_ts` double(18,6) NOT NULL,
					`status` int(11) NOT NULL DEFAULT '0',
					`error` longtext,
					UNIQUE KEY `index_id` (`index_id`),
					KEY `status` (`status`)
				  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
			),
		);

		return $this->CreateMySQLQuery($dbscheme);
	}
	
	public function split_to_words($str)
	{
		// Replace UTF-8 apostrophes/quotes with ASCII ones
		$str2 = preg_replace("~[\x{00b4}\x{2018}\x{2019}]~u", "'", mb_strtolower($str));

		// Replace quotes also (intentionally commented out for future usage)
		//$str2 = str_replace(array("\x{201C}", "\x{201D}"), '"', $str2);
	
		//preg_match_all("~([\x{00C0}-\x{1FFF}\x{2C00}-\x{D7FF}\w][\x{00C0}-\x{1FFF}\x{2C00}-\x{D7FF}\w'\-]*[\x{00C0}-\x{1FFF}\x{2C00}-\x{D7FF}\w]+|[\x{00C0}-\x{1FFF}\x{2C00}-\x{D7FF}\w]+)~u", $str2, $matches);
		preg_match_all("~([\x{00C0}-\x{1FFF}\x{2C00}-\x{D7FF}\w][\x{00C0}-\x{1FFF}\x{2C00}-\x{D7FF}\w']*[\x{00C0}-\x{1FFF}\x{2C00}-\x{D7FF}\w]+|[\x{00C0}-\x{1FFF}\x{2C00}-\x{D7FF}\w]+)~u", $str2, $matches);
		if (isset($matches[1])) {
			$ws = $matches[1];
		} else {
			$ws = array();
		}
		return apply_filters('wpfts_split_to_words', $ws, $str);
	}

	public function reindex($index_id, $chunks, $is_force_flush = true)
	{	
		global $wpdb, $wpfts_context, $wpfts_core;
		
		if (!is_array($chunks)) {
			$this->log(__('Reindex: wrong chunks format', 'fulltext-search'));
			return false;
		}

		$log_chunks = array();
		foreach ($chunks as $kk => $dd) {
			$log_chunks[] = ''.$kk.' => '.(is_string($dd) ? mb_strlen($dd) : '');
		}

		$wpfts_context = new WPFTS_Context();
		
		$wpfts_context->index_post = $index_id;
		$wpfts_context->index_token = false;

		$pfx = $this->dbprefix();

		$this->logtime('*** Start Reindex index_id='.$index_id);
		$this->logtime('chunks = '.implode(', ', $log_chunks));

		$t0 = microtime(true);

		$q = 'delete from `'.$pfx.'vectors`, `'.$pfx.'docs` using `'.$pfx.'vectors`
				inner join `'.$pfx.'docs`
					on `'.$pfx.'docs`.id = `'.$pfx.'vectors`.did
				where 
					`'.$pfx.'docs`.`index_id` = "'.addslashes($index_id).'"';
		$wpfts_core->db->query($q);

		$t1 = microtime(true);
		$this->logtime('delete1 = '.sprintf('%.6f', $t1 - $t0));

		foreach ($chunks as $k => $d) {

			$wpfts_context->index_token = $k;

			$q = 'select 
					`id` 
				from `'.$pfx.'docs` 
				where 
					`index_id` = "'.addslashes($index_id).'" and 
					`token` = "'.addslashes($k).'"';
			$res = $wpfts_core->db->get_results($q, ARRAY_A);
			
			if (!isset($res[0]['id'])) {

				$t0 = microtime(true);
				
				// Insert token record
				$wpfts_core->db->insert($pfx.'docs', array(
					'index_id' => $index_id,
					'token' => $k,
					'n' => 0,
				));
				
				$t1 = microtime(true);
				$this->logtime('insert token record = '.sprintf('%.6f', $t1 - $t0));

				$doc_id = $wpdb->insert_id;
				
			} else {
				$doc_id = $res[0]['id'];
			}
			
			$t0 = microtime(true);

			$r2 = $this->add(array($doc_id => $d));

			$wpfts_context->index_token = false;

			$t1 = microtime(true);
			$this->logtime('add('.$doc_id.' => '.mb_strlen($d).') = '.sprintf('%.6f', $t1 - $t0));

			if (!$r2) {
				$wpfts_context = false;
				return false;
			}
		}

		if ($is_force_flush) {
			$this->_flushTW();
		}

		$wpfts_context = false;

		return true;
	}
	
	public function logtime($s)
	{
		if ($this->_is_log || $this->_is_show) {

			$t0 = $this->_lastt0;
			$t1 = microtime(true);
	
			$ss = sprintf('%.6f', $t1 - $t0).' : '.$s;

			if ($this->_is_log) {
				//$log_fn = dirname(__FILE__).'/index_log.txt';

				//file_put_contents($log_fn, $ss."\n\n", FILE_APPEND);
				$this->_logtime[] = $ss;
			}
			if ($this->_is_show) {
				echo $ss."\n";
			}
			$this->_lastt0 = $t1;
		}
	}

	public function _getTWCount($force = false)
	{
		global $wpfts_core;

		// Let's count rows in t_words
		// Only need to count one time (in the start of the PHP script)
		if ($force || ($this->t_words_rows < 0)) {
			$pfx = $this->dbprefix();

			$q = 'select count(*) n from `'.$pfx.'tw`';
			$rr = $wpfts_core->db->get_results($q, ARRAY_A);
	
			$this->t_words_rows = $rr[0]['n'];

			$this->logtime('Read number of t_words rows: '.$this->t_words_rows);
		}
		
		return $this->t_words_rows;
	}

	public function _getVCNotAct($max_n = 1)
	{
		global $wpfts_core;

		$pfx = $this->dbprefix();

		$q = 'select 
				id
			from `'.$pfx.'words`
			where
				`act` = -1
			limit '.intval($max_n).'
		';
		$rr = $wpfts_core->db->get_results($q, ARRAY_A);

		$a = array();
		foreach ($rr as $row) {
			$a[] = $row['id'];
		}

		return $a;
	}

	public function indexWordData($wid)
	{
		global $wpdb, $wpfts_core;

		$pfx = $this->dbprefix();

		$time = current_time('timestamp');

		// We need to process this part with chunks because of possible large amount of
		// same words.
		$max_chunk = 10000;

		$c_off = 0;
		$is_finished = false;
		$tt = array();

		$q = 'select 
				v.did,
				v.wn
			from `'.$pfx.'vectors` v
			where 
				v.wid = '.intval($wid);

		while (!$is_finished) {
			$wpfts_core->db->query($q.' limit '.$c_off.','.$max_chunk);
		
			if (count($wpdb->last_result) > 0) {
				foreach ($wpdb->last_result as $row3) {
					if (isset($tt[$row3->did])) {
						$tt[$row3->did][] = $row3->wn;
					} else {
						$tt[$row3->did] = array($row3->wn);
					}
				}
			
			} else {
				$is_finished = true;
				break;
			}
		
			if (count($wpdb->last_result) < $max_chunk) {
				$is_finished = true;
				break;
			}
			$c_off += count($wpdb->last_result);
		}
		
		$q = 'delete from `'.$pfx.'vc` where `wid` = "'.addslashes($wid).'"';
		$wpfts_core->db->query($q);

		if (count($tt) <= 0) {
			// It looks like this word do not used anymore
			// @todo May be we need to remove this word

			$q = 'update `'.$pfx.'words` set `act` = 0 where `id` = "'.addslashes($wid).'"';
			$wpfts_core->db->query($q);
	
			return true;
		}

		// Okay, let's create VC

		$pk_size = 0;
		$pk = '';
		foreach ($tt as $kk => $dd) {
			$pk .= pack('l', $kk);
			$zz = count($dd) - 1;
			for ($i = 0; $i <= $zz; $i ++) {
				$pk .= pack('l', ($i == $zz) ? -$dd[$i] : $dd[$i]);
			}
			$pk_size ++;

			if (strlen($pk) > 1000 * 4) {
				// Drop this pack to the vcache
				$pk = pack('l', $pk_size).$pk;

				$q = 'insert into `'.$pfx.'vc` (`wid`,`upd_dt`,`vc`) values ("'.$wid.'", "'.date('Y-m-d H:i:s', $time).'", "'.$wpdb->_real_escape($pk).'")';
				$wpfts_core->db->query($q);

				if (strlen($wpfts_core->db->get_last_error()) < 1) {
					$this->logtime('set from '.count($tt).' doc ids ('.$pk_size.','.strlen($pk).')');
				} else {
					$this->logtime('MySQL Error: '.$wpfts_core->db->get_last_error());
					return false;
				}

				$pk = '';
				$pk_size = 0;
			}
		}

		if (strlen($pk) > 0) {
			// Drop remaining
			$pk = pack('l', $pk_size).$pk;

			$q = 'insert into `'.$pfx.'vc` (`wid`,`upd_dt`,`vc`) values ("'.$wid.'", "'.date('Y-m-d H:i:s', $time).'", "'.$wpdb->_real_escape($pk).'")';
			$wpfts_core->db->query($q);

			if (strlen($wpfts_core->db->get_last_error()) < 1) {
				$this->logtime('set remaining from '.count($tt).' doc ids ('.$pk_size.','.strlen($pk).')');
			} else {
				$this->logtime('MySQL Error: '.$wpfts_core->db->get_last_error());
				return false;
			}
		}

		$q = 'update `'.$pfx.'words` set `act` = '.count($tt).' where `id` = "'.addslashes($wid).'"';
		$wpfts_core->db->query($q);

		return true;
	}
	
	public function _flushTW()
	{
		// Let's FLUSH!
		global $wpfts_core;

		$pfx = $this->dbprefix();
		
		$t0 = microtime(true);

		// Insert new words only
		$q = 'insert into `'.$pfx.'words`
				(`word`)
			select distinct 
				tw.w
			from `'.$pfx.'tw` tw
			left outer join `'.$pfx.'words` w
				on tw.w = w.word
			where 
				isnull(w.id)
			';
		$wpfts_core->db->query($q);

		$t1 = microtime(true);
		$this->logtime('Insert new words only: '.$wpfts_core->db->get_last_error().', dt = '.sprintf('%.4f', $t1 - $t0));

		$t0 = microtime(true);

		// Insert vectors
		$q = 'insert into `'.$pfx.'vectors`
					(`did`, `wid`, `wn`)
				select
					tw.did,
					w.id,
					tw.wn
				from `'.$pfx.'tw` tw
				STRAIGHT_JOIN `'.$pfx.'words` w
					on tw.w = w.word
		';
		$wpfts_core->db->query($q);

		$t1 = microtime(true);
		$this->logtime('Insert vectors: '.$wpfts_core->db->get_last_error().', dt = '.sprintf('%.4f', $t1 - $t0));

		// Touch all used words (right join is important here)
		$t0 = microtime(true);
		$q = 'update `'.$pfx.'words` w
			right join `'.$pfx.'tw` tw
				on tw.w = w.word
			set `act` = -1 
		';
		$wpfts_core->db->query($q);

		$t1 = microtime(true);
		$this->logtime('Touch words: '.$wpfts_core->db->get_last_error().', dt = '.sprintf('%.4f', $t1 - $t0));

		// Okay, clear the temp table
		$t0 = microtime(true);

		$q = 'CREATE TABLE `'.$pfx.'tw2` LIKE `'.$pfx.'tw`';
		$wpfts_core->db->query($q);

		$q = 'RENAME TABLE `'.$pfx.'tw` TO `'.$pfx.'tw0`, `'.$pfx.'tw2` TO `'.$pfx.'tw`';
		$wpfts_core->db->query($q);

		$q = 'DROP TABLE `'.$pfx.'tw0`';
		$wpfts_core->db->query($q);

		//$q = 'truncate table `'.$pfx.'tw`';
		//$wpfts_core->db->query($q);

		$t1 = microtime(true);
		$this->logtime('Truncate table: '.$wpfts_core->db->get_last_error().', dt = '.sprintf('%.4f', $t1 - $t0));

		$this->t_words_rows = 0;
	}

	/** Using bulk insert for vectors **/
	public function add($docs = array()) 
	{	
		//ini_set('display_errors', 1);
		//error_reporting(E_ALL);

		global $wpfts_core;
		
		// Validate
		if (!is_array($docs)) {
			$this->log(__('Add document: parameter should be an array', 'fulltext-search'));
			return false;
		}
		
		if (count($docs) < 1) {
			// Nothing to do
			return true;
		}
		
		foreach ($docs as $id => $doc) {
			if (!is_numeric($id)) {
				$this->log(sprintf(__('Add document: bad index "%s" given.', 'fulltext-search'), $id));
				return false;
			} else {
				$a_ids[] = $id;
			}
		}
		
		$pfx = $this->dbprefix();

		$this->_lastt0 = microtime(true);

		$this->logtime('Current number of ROWS: '.$this->t_words_rows);

		$this->_getTWCount();

/*
		$q = 'create temporary table `t_words` (`w` varchar(255) not null, `wn` int(11) not null, key `w` (`w`))';
		$wpfts_core->db->query($q);

		$this->logtime('create temp table: '.$wpfts_core->db->get_last_error());
*/
/*
		// Okay, clear the temp table
		$q = 'truncate table `t_words`';
		$wpfts_core->db->query($q);

		$this->logtime('Initial truncate table: '.$wpfts_core->db->get_last_error());
*/

		$wordlist = array();
		$doclist = array();
		foreach ($docs as $id => $doc) {
			
			if (!isset($doc) || (mb_strlen($doc) < 1)) {
				continue;
			}
			
			$t0 = microtime(true);

			$words = $this->split_to_words($doc);
			$num_of_words = count($words);
			$doclist[$id] = $num_of_words;

			$wpfts_core->db->update($pfx.'docs', array('n' => $num_of_words), array('id' => $id));

			$this->logtime('break to words ('.$id.', '.count($wordlist).')');

			// Remove old vectors for this doc_id
			$q = 'delete from '.$pfx.'vectors
					where `did` = "'.$id.'"';
			$wpfts_core->db->query($q);
	
			$this->logtime('Remove old vectors: '.$wpfts_core->db->get_last_error());
		
			$ws = array_chunk($words, 1000);

			$wn = 1;
			foreach ($ws as $ws_chunk) {
				$t = array();
				foreach ($ws_chunk as $d) {
					$t[] = '("'.addslashes($d).'", "'.$id.'", "'.$wn.'")';
					$wn ++;
				}
	
				$q = 'insert into `'.$pfx.'tw` (`w`, `did`, `wn`) values '.implode(',', $t);
				$wpfts_core->db->query($q);

				$this->logtime('Insert word chunk ('.count($ws_chunk).'): '.$wpfts_core->db->get_last_error());
			}

			$this->t_words_rows += $wn - 1;

			if ($this->t_words_rows > 25000) {
				$this->_flushTW();
			}

		}
/*
		// Delete temp table (just in case)
		$q = 'drop temporary table `t_words`';
		$wpfts_core->db->query($q);

		$this->logtime('Drop temp table: '.$wpfts_core->db->get_last_error());
*/

		return true;
	}

	function is_stop_word($word)
	{
		return isset($this->stoplist[mb_strtolower($word)]);
	}

	function parse_search_terms($a, &$wpq)
	{	
		global $wpfts_core;

		$z = array();
		foreach ($a as $d) {
			$v = mb_strtolower(trim($d), 'utf-8');
			$is_quoted = (mb_strlen($v) > 0) && ($v[0] == '"');
			if ($is_quoted) {
				$v = trim($v, '"');
			}
			if (mb_strlen($v) > 0) {
				if (($wpfts_core != null) && ($wpfts_core->get_option('internal_search_terms') != 0)) {
					$vv = $this->split_to_words($d);
					$v = implode(' ', $vv);
					if ($is_quoted) {
						$v = '"'.$v.'"';
					}
				}	
				$z[] = $v;
			}
		}
		
		return apply_filters('wpfts_search_terms', $z, $wpq);
	}
	
	function count1s32($i)
	{
		$count = 0;
		$i = $i - (($i >> 1) & 0x55555555);
		$i = ($i & 0x33333333) + (($i >> 2) & 0x33333333);
		$i = ($i + ($i >> 4)) & 0x0f0f0f0f;
		$i = $i + ($i >> 8);
		$i = $i + ($i >> 16);
		$count += $i & 0x3f;
	
		return $count;
	}

	function sql_parts(&$wpq, $cw, $issearch, $nocache, $qlog_id)
	{
		global $wpdb, $wpfts_core;
		
		$pfx = $this->dbprefix();

		$q = &$wpq->query_vars;
		
		$is_use_ttable = false;

		$join = '';
		$fields = '';
		$orderby = '';
		$where_part = '';
		$matches = array();
		$good_doc_masks = array();
		$is_use_doc_masks = false;

		if ((!empty($q['s'])) && ($issearch)) {
			
			$qs = stripslashes($q['s']);
			if ( empty( $_GET['s'] ) && $wpq->is_main_query() ) {
				$qs = urldecode( $qs );
			}

			$qs = str_replace( array( "\r", "\n" ), '', $qs );
			$q['search_terms_count'] = 1;
			if ( ! empty( $q['sentence'] ) ) {
				$q['search_terms'] = array( $qs );
			} else {
				if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $qs, $matches ) ) {
					$q['search_terms_count'] = count( $matches[0] );
					$q['search_terms'] = $this->parse_search_terms( $matches[0], $wpq );
					// if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence
					if ( empty( $q['search_terms'] ) || count( $q['search_terms'] ) > 9 ) {
						$q['search_terms'] = array( $qs );
					}
				} else {
					$q['search_terms'] = array( $qs );
				}
			}

			// Decode terms
			$ts = array();
			foreach ($q['search_terms'] as $t) {
				$f = !empty($q['exact']) ? 1 : 0;
				if (!empty($q['sentence'])) {
					$ts[] = array($f, trim($t));
				} else {
					if (mb_substr($t, 0, 1, 'utf-8') == '"') {
						$t2 = explode(' ', trim($t, '"'));
						$f = 1;
					} else {
						$t2 = explode(' ', trim($t));
					}
					if (is_array($t2)) {
						foreach ($t2 as $tt) {
							$ts[] = array($f, mb_strtolower(trim($tt)));
						}
					}
				}
			}
			$q['search_terms'] = $ts;
			$q['search_terms_count'] = count($ts);

			$is_deeper_search = false;
			if ($wpfts_core->get_option('deeper_search') != 0) {
				$is_deeper_search = true;
			}

			$i = 1;
			if (count($ts) > 0) {
				// Ok, let's create mysql pieces
				$masks = array();
			
				$st_msk = '0';
				$st_q = array(0);
				$st_msk_bit = 1;
				$full_mask = 0;
				foreach ($ts as $ts_item) {
					$word = $ts_item[1];
					$f = $ts_item[0];
					if (mb_strlen($word) >= 3) {
						if ($f) {
							$st_q[] = '(w.word = "'.$wpdb->esc_like($word).'")';
							$st_msk = 'if(w.word = "'.$wpdb->esc_like($word).'", '.$st_msk_bit.', '.$st_msk.')';
						} else {
							$st_q[] = '(w.word like "'.($is_deeper_search ? '%' : '').$wpdb->esc_like($word).'%")';
							$st_msk = 'if(w.word like "'.($is_deeper_search ? '%' : '').$wpdb->esc_like($word).'%", '.$st_msk_bit.', '.$st_msk.')';							
						}
					} else {
						if ((mb_strlen($word) <= 2) && (mb_strlen($word) >= 1)) {
							$tt = '';
							if ($f) {
								$tt = 'w.word = "'.$wpdb->esc_like($word).'"';
							} else {
								$maxn = 5 * mb_strlen($word);
								$tt = '(w.word like "'.($is_deeper_search ? '%' : '').$wpdb->esc_like($word).'%") and (char_length(w.word) <= "'.$maxn.'")';
							}
							$st_q[] = '('.$tt.')';
							$st_msk = 'if('.$tt.', '.$st_msk_bit.', '.$st_msk.')';
						}
					}
					$masks[$st_msk_bit] = array($word, mb_strlen($word));
			
					$full_mask |= $st_msk_bit;

					$st_msk_bit = $st_msk_bit << 1;
				}
			
				if ($wpq->get('word_logic') == 'and') {
					// Require full mask
					$is_use_doc_masks = true;
					$good_doc_masks = array(
						't'.$full_mask => 1,
					);
				}
			
				$n_words = count($ts);
			
				$this->_lastt0 = microtime(true);
			
				// We going to use a little bit different algorithm for x86-based machines
				// Because 'Q'-packing only supported on x64.
				$is_x64 = (PHP_INT_SIZE == 8);	// Simple check for x64 support

				$is_optimizer = intval($wpfts_core->get_option('is_optimizer'));

				$vecdata = array();
				$cntrs = array();
				$wordz1 = array();

				if ($is_optimizer) {
					// Full algorithm
					$qr = '
						SELECT
							w.id,
							char_length(w.word) len,
							'.$st_msk.' mask,
							w.act,
							vc.vc
						FROM
							`'.$pfx.'words` w
						left join `'.$pfx.'vc` vc
							on vc.wid = w.id
						WHERE
							'.implode(' or ', $st_q).'
						ORDER BY
							NULL
					';
				} else {
					// Short algorithm
					$qr = '
						SELECT
							w.id,
							char_length(w.word) len,
							'.$st_msk.' mask,
							w.act
						FROM
							`'.$pfx.'words` w
						WHERE
							'.implode(' or ', $st_q).'
						ORDER BY
							NULL
					';
				}
				// Query for word data
				$res1 = $wpfts_core->db->get_results($qr, ARRAY_A);				
			
				$this->logtime($qr." | ".'Totally found '.count($res1).' word and subwords. Taken memory: '.memory_get_usage());
			
				// Okay, get the vcache records
				// key = doc_id
				// value:
				// -- 0 = total mask
				// -- 1 = num_words
				// -- 2 = relevance1
				$nacts = array();
				foreach ($res1 as $dd) {
					if (!isset($wordz1[$dd['id']])) {
						$wordz1[$dd['id']] = array(
							$dd['len'], 
							$dd['mask'], 
							$masks[$dd['mask']][1] / $dd['len'],
						);
					}
			
					if (($dd['act'] >= 0) && ($is_optimizer)) {
						// Valid word cache
						$ns = unpack('l*', $dd['vc']);
					
						$zz = 2;
						while ($zz <= count($ns)) {
							$did = $ns[$zz++];
							// Read positions
							while ($zz <= count($ns)) {
								$ofs = $ns[$zz++];
								if (!isset($vecdata[$did])) {
									$vecdata[$did] = '';
									$cntrs[$did] = 0;
								}
			
								$vecdata[$did] .= $is_x64 ? pack('Q', (abs($ofs) << 32) + $dd['id']) : pack('ll', abs($ofs), $dd['id']);
								$cntrs[$did] ++;
				
								if ($ofs < 0) {
									break;
								}
							}
						}
					} else {
						// Non-valid word cache, use alternative algorithm
						$nacts[] = $dd['id'];
					}
				}
				if (count($nacts) > 0) {
					// Get vecdata for nacts
					//echo 'Found '.count($nacts).' nacts'."\n";

					// In order to lower memory consumption we HAVE to process data by chunks
					$nacts_ch = array_chunk($nacts, 1000);

					foreach ($nacts_ch as $n_ch) {
						$qr = 'select 
								v.did,
								v.wn,
								v.wid
							from `'.$pfx.'vectors` v
							where 
								v.wid in ('.implode(',', $n_ch).')';
							$r6 = $wpfts_core->db->get_results($qr, ARRAY_A);

						$tt = array();
						foreach ($r6 as $row6) {
							$did = $row6['did'];
							if (!isset($vecdata[$did])) {
								$vecdata[$did] = '';
								$cntrs[$did] = 0;
							}

							$vecdata[$did] .= $is_x64 ? pack('Q', (intval($row6['wn']) << 32) + $row6['wid']) : pack('ll', intval($row6['wn']), $row6['wid']);
							$cntrs[$did] ++;
						}
					}
				}
				// We don't need for this query result anymore
				unset($res1);
			
				$this->logtime('Total docs found: '.count($vecdata).', taken memory: '.memory_get_usage());

				// Let's calculate the basic relevance for each document
				$calcd = array();
				foreach ($vecdata as $kv => $dv_str) {
					if ($is_x64) {
						$dv = unpack('Q*', $dv_str);
					} else {
						$t4 = unpack('l*', $dv_str);
						$dv = array();
						$i = 1;
						$t4_l = count($t4);
						while ($i < $t4_l) {
							$dv[] = ($t4[$i++] << 32) + $t4[$i++];
						}
					}
					usort($dv, function($v1, $v2)
					{
						return ($v1 < $v2) ? -1 : (($v1 > $v2) ? 1 : 0);
					});
					
					// Iterate over all found words of this document
					$rown = 0;
					$cmask = 0;
					$lastwn = -100;
			//		$totalrows = count($dv);
					$doc_sum = 0;
					$sent_pow = 10;
					$sent_len = 0;
					$max_sent_len = 0;
					$doc_mask = 0;	// Total mask of the document
					$sent_num = 0;	// Number of sentences (do we need it?)
					foreach ($dv as $w) {

						$wn = $w >> 32;	// offset
						$w32 = $w & 0xffffffff;
						$mask = $wordz1[$w32][1];	// word mask
						$kword = $wordz1[$w32][2];	// word kword
			
						$rown ++;
						$doc_mask = $doc_mask | $mask;
			
						$brk = ((($cmask & $mask) != 0) || ($wn >= $lastwn + 5)) ? 1 : 0;
			
						if ($brk) {
							// The sentence is over
							$max_sent_len = max($max_sent_len, $sent_len);
							$sent_num ++;
							$doc_sum = $doc_sum + $sent_pow;
			
							// Reset sentence counters
							$cmask = $mask;
							$sent_pow = 10 * $kword;
							$sent_len = 1;
						} else {
							// Still the same sentence
							$cmask = $cmask | $mask;
							$sent_pow = ($sent_pow * 10 * $kword * (12.7 + 2.7 * ($lastwn - $wn)) );
							$sent_len ++;
						}
			
						$lastwn = $wn;
					}
					// Finish last sentence
					$max_sent_len = max($max_sent_len, $sent_len);
					$doc_sum = $doc_sum + $sent_pow;
			
					if ((!$is_use_doc_masks) || (isset($good_doc_masks['t'.$doc_mask]))) {
						$calcd[$kv] = array(
							$doc_sum, // res
							($this->count1s32($doc_mask) - 1) * $n_words + $max_sent_len // pow
						);
					}
				}
			
				$this->logtime('Relevance calculation finished');
			
				$tname = $pfx.'trel';
				if ($is_use_ttable) {
					// Remove previous temporary table
					$qr = 'drop temporary table if exists `'.$tname.'`';
					$wpfts_core->db->query($qr);
			
					// Create temporary table
					$qr = 'create temporary table `'.$tname.'` (
								`did` int(11) not null, 
								`pow` int(11) not null, 
								`res` float(10,6) not null,
								key `did` (`did`) )';
					$wpfts_core->db->query($qr);

					$calcd_ch = array_chunk($calcd, 1000, true);
					foreach ($calcd_ch as $cc) {
						$aa = array();
						foreach ($cc as $kk => $dd) {
							$aa[] = '("'.$kk.'","'.$dd[1].'","'.$dd[0].'")';
						}
						$qr = 'insert into `'.$tname.'` (`did`,`pow`,`res`) values '.implode(',', $aa);
						$wpfts_core->db->query($qr);
					}
			
					$this->logtime('Fill temp table finished.');

				} else {
					// Use static table
					$tname = $pfx.'tp';
					$calcd_ch = array_chunk($calcd, 1000, true);
					foreach ($calcd_ch as $cc) {
						$aa = array();
						foreach ($cc as $kk => $dd) {
							$aa[] = '("'.$qlog_id.'","'.$kk.'","'.$dd[1].'","'.$dd[0].'")';
						}
						$qr = 'insert into `'.$tname.'` (`q_id`,`did`,`pow`,`res`) values '.implode(',', $aa);
						$wpfts_core->db->query($qr);
					}
			
					$this->logtime('Fill tp table finished.');
				}
			
				// Getting max relevance
				$qr = 'select 
						max(trel.`res` / LOG(tbase.n + 1)) mx_rel
					from `'.$tname.'` trel
					straight_join `'.$pfx.'docs` tbase
						on (trel.did = tbase.id)'.($is_use_ttable ? '' : ' and (trel.q_id = "'.addslashes($qlog_id).'")');
				$r5 = $wpfts_core->db->get_results($qr, ARRAY_A);

				$this->logtime('Calculated max relevance...');

				$mxrel = (isset($r5[0]) && isset($r5[0]['mx_rel'])) ? $r5[0]['mx_rel'] : 1;
			
				$rcv = 1;
				if (count($cw) > 1) {
					$x = array();
					foreach ($cw as $k => $d) {
						//if(t1.token = "post_title", 100, 50)
						$x[] = ' when "'.$k.'" then '.str_replace(',', '.', floatval($d));
					}
					$rcv = ' (case tbase.token '.implode('', $x).' else 1 end)';
				}

				// Okay, now we can execute the main query :)
				$join = '
					inner join ( 
						select 
							fi.tid, 
							t_end.relev 
						from `'.$pfx.'index` fi 
						straight_join ( 
							select 
								tbase.index_id, 
								sum((trel.`pow` + (trel.`res` / LOG(tbase.n + 1)) / '.$mxrel.') * '.$rcv.' / ('.($n_words * $n_words + 1).')) relev
							from `'.$tname.'` trel
							straight_join `'.$pfx.'docs` tbase
								on (trel.did = tbase.id)'.($is_use_ttable ? '' : ' and (trel.q_id = "'.addslashes($qlog_id).'")').'
							group by tbase.index_id 
							order by NULL 
						) t_end 
							on t_end.index_id = fi.id 
						order by null
					) '.$pfx.'t 
						on '.$pfx.'t.tid = '.$wpdb->posts.'.ID 
					';
				
					$fields = ', '.$pfx.'t.relev ';
					$orderby = ' ('.$pfx.'t.relev)';
					$where_part = ' and ('.$pfx.'t.relev > 0)';
				}

		} else {
			$issearch = 0;
		}

		$parts = array(
				'token' => md5(time().'|'.uniqid('session')),
				'issearch' => $issearch,
				'nocache' => $nocache,
				'join' => $join.' ',
				'select' => ' and (((1)))'.$where_part,
				'orderby' => $orderby,
				'fields' => $fields,
				'sql_no_cache' => $nocache ? ' SQL_NO_CACHE' : '',
				'is_use_ttable' => $is_use_ttable,
			);
		
		return $parts;
	}
	
	function sql_joins($join, &$wpq, $cw)
	{	
		if ((isset($wpq->wpftsi_session['token'])) && ($wpq->wpftsi_session['issearch'])) {
			return $join.$wpq->wpftsi_session['join'];
		}
		return $join;
	}
	
	/**
	 * Constructing SQL search part
	 * 
	 * @param string $search Search SQL from WP
	 * @param WP_Query $wpq WP query object
	 */
	function sql_select($search, &$wpq)
	{	
		if ((isset($wpq->wpftsi_session['token'])) && ($wpq->wpftsi_session['issearch'])) {
			
			//$search = $search.' '.$wpq->wpftsi_session['select'];	// This way enables custom plugin's search, but it also enables standard WP Query "where" part using AND, which disables most of WPFTS functionality

			$search = $wpq->wpftsi_session['select'];
		}
		
		return $search;
	}
	
	function sql_orderby($orderby, &$wpq)
	{	
		if ((isset($wpq->wpftsi_session['token'])) && ($wpq->wpftsi_session['issearch']) && (strlen($wpq->wpftsi_session['orderby']) > 2)) {

			// Only replace if orderby = empty or orderby = relevance
			$t = $wpq->get('orderby');
			if (((is_string($t)) && (strlen(trim($t)) < 1)) || ($t == 'relevance')) {

				$t2 = $wpq->get('order');
				if ($t2 != 'ASC') {
					$t2 = 'DESC';
				}
				$orderby = $wpq->wpftsi_session['orderby'].' '.$t2;
			}
		}
		
		return $orderby;
	}
	
	function sql_pre_posts(&$wpq, $cw)
	{	
		global $wpfts_core;

		if ((!isset($wpq->wpftsi_session['token'])) || (!$wpq->wpftsi_session['token'])) {
			
			$disable = (isset($wpq->query_vars['wpfts_disable']) && ($wpq->query_vars['wpfts_disable'])) ? 1 : 0;
			$nocache = (isset($wpq->query_vars['wpfts_nocache']) && ($wpq->query_vars['wpfts_nocache'])) ? 1 : 0;
			
			if ((!empty($wpq->query_vars['s'])) && (!$disable)) {

				$qlog_id = isset($wpq->wpftsi_session['q_id']) ? intval($wpq->wpftsi_session['q_id']) : rand(10000, 99999);

				/*
				$fields = isset($wpq->query_vars['fields']) ? trim($wpq->query_vars['fields']) : '';
				if (($fields == 'ids') || ($fields == 'id=>parent')) {
					// Normal workflow
				} else {

				}
				*/

				// Calculate data
				$sql_parts = $this->sql_parts($wpq, $cw, $disable ? 0 : 1, $nocache, $qlog_id);
				$sql_parts['q_id'] = $qlog_id;
				$wpq->wpftsi_session = $sql_parts;
			}

		}
	}
	
	function sql_post_limits($limits, &$wpq)
	{
		if ((isset($wpq->wpftsi_session['token'])) && ($wpq->wpftsi_session['issearch'])) {
			// Save $limits to set up 'split_mode'
			$wpq->wpftsi_session['limits'] = $limits;
			return $limits;
		}

		return $limits;
	}

	function sql_posts_fields($fields, &$wpq)
	{
		global $wpdb;

		if ((isset($wpq->wpftsi_session['token'])) && ($wpq->wpftsi_session['issearch'])) {
			// Save old $fields to generate correct 'split_the_query' response
			$wpq->wpftsi_session['old_fields'] = $fields;

			// Decide if we make split_query or not
			$limits = $wpq->wpftsi_session['limits'];
			$ppp = isset($wpq->query_vars['posts_per_page']) ? $wpq->query_vars['posts_per_page'] : 0;
			
			$is_split_query = false;
			if ((!empty($limits)) && ($ppp < 500) && ("{$wpdb->posts}.*" == $fields)) {
				$is_split_query = true;
			}
			$wpq->wpftsi_session['is_split_query'] = $is_split_query;

			if ($is_split_query) {
				// Use ID, relev for the main query
				return "{$wpdb->posts}.ID".$wpq->wpftsi_session['fields'];
			}

			return $fields.$wpq->wpftsi_session['fields'];
		}
		
		return $fields;
	}
	
	function sql_posts_pre_query($posts, &$wpq)
	{
		global $wpdb, $wpfts_core;

		// Implement query splitting algorithm with relevance preservation
		if ((isset($wpq->wpftsi_session['token'])) && ($wpq->wpftsi_session['issearch'])) {
			if (isset($wpq->wpftsi_session['is_split_query']) && $wpq->wpftsi_session['is_split_query']) {
				$post_idrev = $wpfts_core->db->get_results( $wpq->request );

				$this->wpq_set_found_posts($wpq, $wpq->query_vars, $wpq->wpftsi_session['limits'] );

				$res2 = array();
				if (count($post_idrev) > 0) {
					// Retrieve full posts
					// We have to use MySQL query (not get_post()) because get_post() will cache the post without relev
					$ids = array();
					$ords = array();
					$i = 1;
					foreach ($post_idrev as $d) {
						$ids[$d->ID] = $d->relev;
						$ords[$d->ID] = $i ++;
					}

					$q = 'select * from `'.$wpdb->posts.'` where ID in ('.implode(',', array_keys($ids)).')';
					$res2 = $wpfts_core->db->get_results($q);

					// We have to reorder post to make the same order as ID list was before
					usort($res2, function($v1, $v2) use (&$ords)
					{
						$t1 = $ords[$v1->ID];
						$t2 = $ords[$v2->ID];

						return ($t1 < $t2 ? -1 : ($t1 > $t2 ? 1 : 0));
					});

					foreach ($res2 as $k => $row) {
						if (isset($row->ID) && isset($ids[$row->ID])) {
							$res2[$k]->relev = floatval($ids[$row->ID]);
						}
					}

				}
				return $res2;
			}
		}
		return $posts;
	}

	function sql_posts_distinct($distinct, &$wpq)
	{
		if ((isset($wpq->wpftsi_session['token'])) /*&& ($wpq->wpftsi_session['issearch'])*/) {
			return str_replace('SQL_NO_CACHE', '', $distinct).$wpq->wpftsi_session['sql_no_cache'];
		}
		
		return $distinct;
	}
	
	function sql_the_posts($posts, &$wpq)
	{ 
		global $wpfts_core;

		if (isset($wpq->wpftsi_session)) {
			$sess = $wpq->wpftsi_session;

			//print_r($sess);

			if (isset($sess['is_use_ttable']) && (!$sess['is_use_ttable'])) {
				if ((isset($sess['q_id'])) && ($sess['q_id'] > 0)) {
					// Clear the tp
					$pfx = $this->dbprefix();

					$q = 'delete from `'.$pfx.'tp` where `q_id` = "'.addslashes($sess['q_id']).'"';
					$wpfts_core->db->query($q);
				}
			}

			$wpq->wpftsi_session = null;
		}
		return $posts;
	}
	
	function sql_split_the_query($split_the_query, &$wpq)
	{
		if (isset($wpq->wpftsi_session['token']) && isset($wpq->wpftsi_session['old_fields']) && (!$split_the_query)) {
			// Check if we need to switch the split_the_query on
			$split_the_query = isset($wpq->wpftsi_session['is_split_query']) ? $wpq->wpftsi_session['is_split_query'] : false;
		}
		return $split_the_query;
	}
	
	/**
	 * Set up the amount of found posts and the number of pages (if limit clause was used)
	 * for the current query.
	 * 
	 * Notice: this method was completely copied from WP_Query(), because the original one is 'private' so
	 * we can not use them.
	 *
	 * @since 3.5.0
	 *
	 * @param array  $q      Query variables.
	 * @param string $limits LIMIT clauses of the query.
	 */
	function wpq_set_found_posts(&$wpq, $q, $limits)
	{
		global $wpfts_core;

		// Bail if posts is an empty array. Continue if posts is an empty string,
		// null, or false to accommodate caching plugins that fill posts later.
		if ( $q['no_found_rows'] || ( is_array( $wpq->posts ) && ! $wpq->posts ) ) {
			return;
		}

		if ( ! empty( $limits ) ) {
			/**
			 * Filters the query to run for retrieving the found posts.
			 *
			 * @since 2.1.0
			 *
			 * @param string   $found_posts The query to run to find the found posts.
			 * @param WP_Query $wpq        The WP_Query instance (passed by reference).
			 */
			$wpq->found_posts = $wpfts_core->db->get_var( apply_filters_ref_array( 'found_posts_query', array( 'SELECT FOUND_ROWS()', &$wpq ) ) );
		} else {
			if ( is_array( $wpq->posts ) ) {
				$wpq->found_posts = count( $wpq->posts );
			} else {
				if ( null === $wpq->posts ) {
					$wpq->found_posts = 0;
				} else {
					$wpq->found_posts = 1;
				}
			}
		}

		/**
		 * Filters the number of found posts for the query.
		 *
		 * @since 2.1.0
		 *
		 * @param int      $found_posts The number of posts found.
		 * @param WP_Query $wpq        The WP_Query instance (passed by reference).
		 */
		$wpq->found_posts = apply_filters_ref_array( 'found_posts', array( $wpq->found_posts, &$wpq ) );

		if ( ! empty( $limits ) ) {
			$wpq->max_num_pages = ceil( $wpq->found_posts / $q['posts_per_page'] );
		}
	}

	function getRecordsToRebuild($n_max = 1)
	{	
		global $wpfts_core;
		
		$idx = $this->dbprefix();
		
		$time = time();
		$time2 = date('Y-m-d H:i:s', $time - $this->lock_time);
		
		$q = 'select 
					id, tid, tsrc 
			from `'.$idx.'index` 
			where 
				((force_rebuild != 0) or (build_time = 0)) and 
				((locked_dt = "1970-01-01 00:00:00") or (locked_dt < "'.$time2.'"))
			order by build_time asc, id asc 
			limit '.intval($n_max).'';
		$r = $wpfts_core->db->get_results($q, ARRAY_A);
		
		return $r;
	}
	
	function checkAndSyncWPPosts($current_build_time)
	{	
		global $wpdb, $wpfts_core;
		
		$idx = $this->dbprefix();
		
		// Step 1. Mark index rows contains old posts and posts with wrong date of post or build time.
		$q = 'update `'.$idx.'index` wi
				left join `'.$wpdb->posts.'` p
					on p.ID = wi.tid
				set 
					wi.force_rebuild = if(p.ID is null, 2, if ((wi.build_time = "'.addslashes($current_build_time).'") and (wi.tdt = p.post_modified), 0, 1))
				where 
					(wi.tsrc = "wp_posts") and (wi.force_rebuild = 0)';
		$wpfts_core->db->query($q);
		
		// Step 2. Find and add new posts // @todo need to be optimized!
		$q = 'insert ignore into `'.$idx.'index` 
				(`tid`, `tsrc`, `tdt`, `build_time`, `update_dt`, `force_rebuild`, `locked_dt`) 
				select 
					p.ID tid,
					"wp_posts" tsrc,
					"1970-01-01 00:00:00" tdt,
					0 build_time,
					"1970-01-01 00:00:00" update_dt,
					1 force_rebuild,
					"1970-01-01 00:00:00" locked_dt
				from `'.$wpdb->posts.'` p';
		$wpfts_core->db->query($q);
		
		// Step 3. What else?
	}
	
	function get_status()
	{	
		global $wpdb, $wpfts_core;
		
		$time = time();

		$status_next_ts = intval($wpfts_core->get_option('status_next_ts'));
		if ($status_next_ts <= $time) {
			// It's time to refresh status
			$idx = $this->dbprefix();

			$q = 'select 
					count(*) n_inindex, 
					sum(if ((force_rebuild = 0) and (build_time != 0), 1, 0)) n_actual
				from `'.$idx.'index` 
				where `tsrc` = "wp_posts"';
			$res = $wpfts_core->db->get_results($q, ARRAY_A);
			
			$ret = array();
			if (isset($res[0]['n_inindex'])) {
				$ret = array(
					'n_inindex' => intval($res[0]['n_inindex']),
					'n_actual' => intval($res[0]['n_actual']),
					'n_pending' => intval($res[0]['n_inindex']) - intval($res[0]['n_actual']),
				);
			} else {
				$ret = array(
					'n_inindex' => 0,
					'n_actual' => 0,
					'n_pending' => 0,
				);
			}
	
			$ret['nw_act'] = 0;
			$ret['nw_total'] = 0;
	
			$is_optimizer = intval($wpfts_core->get_option('is_optimizer'));
	
			if ($ret['n_pending'] < 1) {
	
				// Check if we have any records in TW cache
				$q = 'select count(*) n from `'.$idx.'tw`';
				$rr = $wpfts_core->db->get_results($q, ARRAY_A);
		
				$ret['n_tw'] = isset($rr[0]) ? intval($rr[0]['n']) : 0;
		
				if ($ret['n_tw'] < 1) {
					if ($is_optimizer) {
						// Get number of non-indexed words
						$q = 'select 
								sum(if(`act` = -1, 0, 1)) nw_act,
								count(id) nw_total
							from `'.$idx.'words`';
						$res = $wpfts_core->db->get_results($q, ARRAY_A);
	
						$ret['nw_act'] = intval($res[0]['nw_act']);
						$ret['nw_total'] = intval($res[0]['nw_total']);
	
					} else {
						$q = 'select 
								count(id) nw_total
							from `'.$idx.'words`';
						$res = $wpfts_core->db->get_results($q, ARRAY_A);
		
						$ret['nw_act'] = intval($res[0]['nw_total']);
						$ret['nw_total'] = intval($res[0]['nw_total']);
					}
				}
			}
			$ret['tsd'] = time();

			$wpfts_core->set_option('status_next_ts', $time + 5 * 60);
			$wpfts_core->set_option('status_cache', json_encode($ret));

			$ret['is_cached'] = false;

			return $ret;
		} else {
			$ret = json_decode($wpfts_core->get_option('status_cache'), true);

			$ret['is_cached'] = true;

			return $ret;
		}
	}
	
	function getClusters()
	{	
		global $wpfts_core;
		
		$idx = $this->dbprefix();
		
		$z = array('post_title' => 1, 'post_content' => 1);
		
		$q = 'select distinct `token` from `'.$idx.'docs` limit 100';
		$res = $wpfts_core->db->get_results($q, ARRAY_A);
		
		$z = array();
		foreach ($res as $d) {
			if (!isset($z[$d['token']])) {
				$z[$d['token']] = 1;
			}
		}
		
		return array_keys($z);
	}

	function lockUnlockedRecord($id) {
		
		global $wpfts_core;
		
		$idx = $this->dbprefix();
		
		$time = time();
		$time2 = date('Y-m-d H:i:s', $time - $this->lock_time);
		$new_time = date('Y-m-d H:i:s', $time);
		
		$q = 'select id, if((locked_dt = "1970-01-01 00:00:00") or (locked_dt < "'.$time2.'"), 0, 1) islocked from `'.$idx.'index` where id = "'.addslashes($id).'"';
		$res = $wpfts_core->db->get_results($q, ARRAY_A);
		
		if (isset($res[0])) {
			if ($res[0]['islocked']) {
				// Already locked
				return false;
			} else {
				// Lock it
				$wpfts_core->db->update($idx.'index', array('locked_dt' => $new_time), array('id' => $id));
				return true;
			}
		} else {
			// Record not found
			return false;
		}
	}
	
	function unlockRecord($id) {
		
		global $wpfts_core;
		
		$idx = $this->dbprefix();
		
		$wpfts_core->db->update($idx.'index', array('locked_dt' => '1970-01-01 00:00:00'), array('id' => $id));
	}
	
	function updateRecordData($id, $data = array()) {
		
		global $wpfts_core;
		
		$idx = $this->dbprefix();
		
		$a = array();
		foreach ($data as $k => $d) {
			if (in_array($k, array('tdt', 'build_time', 'update_dt', 'force_rebuild', 'locked_dt'))) {
				$a[$k] = $d;
			}
		}
		$wpfts_core->db->update($idx.'index', $a, array('id' => $id));
	}
	
	function insertRecordData($data = array()) {
		
		global $wpdb, $wpfts_core;
		
		$idx = $this->dbprefix();
		
		$a = array();
		foreach ($data as $k => $d) {
			if (in_array($k, array('tdt', 'build_time', 'update_dt', 'force_rebuild', 'locked_dt', 'tid', 'tsrc'))) {
				$a[$k] = $d;
			}
		}
		$wpfts_core->db->insert($idx.'index', $a);
		
		return $wpdb->insert_id;
	}
	
	function updateIndexRecordForPost($post_id, $modt, $build_time, $time = false, $force_rebuild = 0) {
		
		global $wpfts_core;
		
		if ($time === false) {
			$time = time();
		}
		
		$q = 'select * from `'.$this->dbprefix().'index` where (`tid` = "'.$post_id.'") and (`tsrc` = "wp_posts")';
		$res = $wpfts_core->db->get_results($q, ARRAY_A);
		
		if (isset($res[0])) {

			// Update existing record
			$this->updateRecordData(
					$res[0]['id'], 
					array(
						'tdt' => $modt,
						'build_time' => $build_time,
						'update_dt' => date('Y-m-d H:i:s', $time),
						'force_rebuild' => $force_rebuild,
						'locked_dt' => '1970-01-01 00:00:00',
						)
			);
			
			return $res[0]['id'];
		} else {
			// Insert new record
			$insert_id = $this->insertRecordData(
					array(
						'tid' => $post_id,
						'tsrc' => 'wp_posts',
						'tdt' => $modt,
						'build_time' => $build_time,
						'update_dt' => date('Y-m-d H:i:s', $time),
						'force_rebuild' => $force_rebuild,
						'locked_dt' => '1970-01-01 00:00:00',
						)
			);
			
			return $insert_id;
		}
	}
	
	function getColumn($a, $col) {
		$r = array();
		foreach ($a as $d) {
			if (isset($d[$col])) {
				$r[] = $d[$col];
			}
		}
		return $r;
	}
	
	function removeIndexRecordForPost($post_id) {
		
		global $wpfts_core;
		
		$idx = $this->dbprefix();
		
		$q = 'select `id` from `'.$idx.'index` where (`tid` = "'.addslashes($post_id).'") and (`tsrc` = "wp_posts")';
		$res_index = $wpfts_core->db->get_results($q, ARRAY_A);
		
		if (isset($res_index[0])) {
			$q = 'select `id` from `'.$idx.'docs` where `index_id` in ('.implode(',', $this->getColumn($res_index, 'id')).')';
			$res_docs = $wpfts_core->db->get_results($q, ARRAY_A);
			
			if (isset($res_docs[0])) {
				$q = 'delete from `'.$idx.'vectors` where `did` in ('.implode(',', $this->getColumn($res_docs, 'id')).')';
				$wpfts_core->db->query($q);
				
				$q = 'delete from `'.$idx.'docs` where `index_id` in ('.implode(',', $this->getColumn($res_index, 'id')).')';
				$wpfts_core->db->query($q);
			}
			
			$q = 'delete from `'.$idx.'index` where (`tid` = "'.addslashes($post_id).'") and (`tsrc` = "wp_posts")';
			$wpfts_core->db->query($q);
		}
		
		return true;
	}
}
