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

/**
 * This class is used as an intermediate wrapper for PHP8+ to catch MySQLi errors
 */

class WPFTS_DB
{
	public function get_results($query = null, $output = OBJECT)
	{
		global $wpdb, $wpfts_core;

		try {
			$res = $wpdb->get_results($query, $output);
		} catch (Exception $e) {
			$error = $e->getMessage();
			$wpfts_core->logError('wpdb::get_results', $error, array('query' => $query));
			$res = null;
		}

		return $res;
	}

	public function query($query)
	{
		global $wpdb, $wpfts_core;

		try {
			$res = $wpdb->query($query);
		} catch (Exception $e) {
			$error = $e->getMessage();
			$wpfts_core->logError('wpdb::query', $error, array('query' => $query));
			$res = null;
		}

		return $res;
	}

	public function update($table, $data, $where, $format = null, $where_format = null)
	{
		global $wpdb, $wpfts_core;

		try {
			$res = $wpdb->update($table, $data, $where, $format, $where_format);
		} catch (Exception $e) {
			$error = $e->getMessage();
			$wpfts_core->logError('wpdb::update', $error, array('table' => $table, 'data' => $data, 'where' => $where, 'format' => $format, 'where_format' => $where_format));
			$res = null;
		}

		return $res;
	}

	public function insert($table, $data, $format = null)
	{
		global $wpdb, $wpfts_core;

		try {
			$res = $wpdb->insert($table, $data, $format);
		} catch (Exception $e) {
			$error = $e->getMessage();
			$wpfts_core->logError('wpdb::insert', $error, array('table' => $table, 'data' => $data, 'format' => $format));
			$res = null;
		}

		return $res;
	}

	public function get_var($query = null, $x = 0, $y = 0) 
	{
		global $wpdb, $wpfts_core;

		try {
			$res = $wpdb->get_var($query, $x, $y);
		} catch (Exception $e) {
			$error = $e->getMessage();
			$wpfts_core->logError('wpdb::get_var', $error, array('query' => $query, 'x' => $x, 'y' => $y));
			$res = null;
		}

		return $res;
	}

	public function get_last_error()
	{
		global $wpdb;

		return $wpdb->last_error;
	}
}
