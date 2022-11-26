<?php

return array(
	'callback' => function()
	{
		global $wpfts_core;

		$dbscheme = array(
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
