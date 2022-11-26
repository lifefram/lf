<?php

return array(
	'callback' => function()
	{
		global $wpfts_core;

		$dbscheme = array(
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

		$index = $wpfts_core->GetIndex();

		$qr = $index->CreateMySQLQuery($dbscheme);

		$success = true;
		foreach ($qr as $k => $d) {
			
			$q = 'drop table if exists `'.$index->dbprefix().$k.'`';
			$wpfts_core->db->query($q);
			
			$wpfts_core->db->query($d['create2']);
			if ($wpfts_core->db->get_last_error()) {
				$index->log('Can\'t create table "'.$index->dbprefix().$k.'": '.$wpfts_core->db->get_last_error());
				$success = false;
			}	
		}

		return $success;
	},
);
