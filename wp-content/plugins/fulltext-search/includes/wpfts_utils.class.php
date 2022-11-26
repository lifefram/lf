<?php

class WPFTS_Utils 
{
	public static function GetRawCache($object_id, $object_type, $mtime, $is_force_reindex, $cb)
	{
		global $wpfts_core;

		if (!$wpfts_core) {
			return array();
		}
	
		$idx = $wpfts_core->GetDBPrefix();

		$q = 'select 
				* 
			from `'.$idx.'rawcache`
			where 
				`object_id` = "'.addslashes($object_id).'" and 
				`object_type` = "'.addslashes($object_type).'"';
		$res = $wpfts_core->db->get_results($q, ARRAY_A);

		if ((count($res) < 1) || ($res[0]['cached_dt'] != $mtime) || ($is_force_reindex)) {
			// use callback to extract text data
			if ($cb && is_callable($cb)) {

				$v = $cb();

				if ($v) {
				
					$dbarr = array(
						'object_id' => $object_id,
						'object_type' => $object_type,
						'data' => serialize(isset($v['raw_data']) ? $v['raw_data'] : 'No raw data provided'),
						'insert_dt' => date('Y-m-d H:i:s', current_time('timestamp')),
						'cached_dt' => isset($v['modified_time']) ? $v['modified_time'] : '1970-01-01 00:00:00',
						'error' => isset($v['error']) ? $v['error'] : '',
						'filename' => isset($v['filename']) ? $v['filename'] : '',
						'method_id' => isset($v['method_id']) ? $v['method_id'] : '',
					);

					if (count($res) > 0) {
						// Update
						$wpfts_core->db->update(
							$idx.'rawcache', 
							$dbarr,
							array(
								'id' => $res[0]['id']
							));
					} else {
						// Insert
						$wpfts_core->db->insert(
							$idx.'rawcache', ///
							$dbarr
						);
					}

					return $v['raw_data'];

				} else {
					// Something went wrong!
					return array(
						'extract_error' => 'The callback returned false',
					);
				}

			} else {
				// Not callable
				return array(
					'extract_error' => 'The callback not set or not callable',
				);
			}
		} else {
			// Return from cache
			return @unserialize($res[0]['data']);
		}
	}

	public static function GetURLInfo($url, $is_local_file = false)
	{
		$ret = array(
			'is_valid' => false,
			'is_local' => false,
			'local_path' => '',
			'file_ext' => '',
		);

		if ($is_local_file) {
			// Local file
			if (strlen($url) > 0) {
				$ret['is_valid'] = true;
				$ret['is_local'] = true;
				$ret['local_path'] = $url;

				$rem = basename($url);
				$ext = (($p = strrpos($rem, '.')) !== false) ? str_replace(array('/', "\\"), '', substr($rem, $p + 1)) : '';
				$ret['file_ext'] = $ext;
			}
		} else {
			// URL
			$hurl = home_url();

			$p_hurl = parse_url($hurl);
			$purl = parse_url($url);
	
			if (isset($purl['host']) && (strlen($purl['host']) > 0)) {
				$ret['is_valid'] = true;
	
				// Get extension of the file (if present)
				$rem = basename($purl['path']);
				$ext = (($p = strrpos($rem, '.')) !== false) ? str_replace(array('/', "\\"), '', substr($rem, $p + 1)) : '';
				$ret['file_ext'] = $ext;

				// Check if URL local
				if (isset($p_hurl['host']) && (strlen($p_hurl['host']) > 0)) {
					if (mb_strtolower($p_hurl['host']) == mb_strtolower($purl['host'])) {
						// Same domain, ok. Now check path
						$url_path = isset($purl['path']) ? trim(trim($purl['path']), '/') : '';
						$hurl_path = isset($p_hurl['path']) ? trim(trim($p_hurl['path']), '/') : '';
	
						if ((strlen($hurl_path) < 1) || (substr($url_path, 0, strlen($hurl_path)) == $hurl_path)) {
							// Okay, subpath is the same
							$ret['is_local'] = true;
	
							$ret['local_path'] = rtrim(trim(ABSPATH), '/').'/'.ltrim(substr($url_path, strlen($hurl_path)), '/');
						}
					}
				}
			}	
		}

		return $ret;
	}

	/**
	 * This method with return cached content of the local file by it's given LINK or direct PATH.
	 * If no cache exists yet, it will extract the content and create the cache first.
	 * The modify timestamp of the file is checked, so if the file was reloaded, it will be re-extracted.
	 * 
	 * @param $url The URL of the file (file should be located on the same domain) or the direct full path to the file
	 * @param $is_force_reindex Setting this TRUE will reset cache and re-extract the file content
	 * @param $is_local_file Setting this to TRUE allows to set $url to the LOCAL PATH instead of URL
	 * 
	 */
	public static function GetCachedFileContent_ByLocalLink($url, $is_force_reindex = false, $is_local_file = false, $is_enable_external_links = false)
	{
		$chunks = array();

		return apply_filters('wpfts_get_cached_content_by_local_link', $chunks, $url, $is_force_reindex, $is_local_file, $is_enable_external_links);
	}
}