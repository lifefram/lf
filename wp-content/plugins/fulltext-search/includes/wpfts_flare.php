<?php

/**
 * The Fire&Flare microservice is a smart "proxy" that sends short packets from the server backend to 
 * the js-based frontend in a real-time.
 * 
 * This Fire&Flare wrapper class is using to make rapid bi-directional syncronization
 * between the website client part and the server part (controlled by WPFTS).
 * 
 * "Fire" part is controlled by this class
 * "Flare" part is controlled by js script
 * 
 * @author Epsiloncool <epsiloncool@gmail.com>
 */
class WPFTS_Flare
{
	public $target_url = '';
	public $mid = '';

	public function __construct($target_url, $medium_point = '')
	{
		$this->target_url = $target_url;
		$this->mid = $medium_point;
	}

	public function SendFire($data = array(), $is_update = false, $is_blocking = false)
	{
		global $wpfts_core;

		$is_debug = ($wpfts_core) ? $wpfts_core->_dev_debug : false;
		$log_file = dirname(__FILE__).'/../sendfire_log.txt';

		if ($is_debug) {
			file_put_contents($log_file, date('Y-m-d H:i:s', current_time('timestamp')).' : SendFile started'."\n", FILE_APPEND);
		}

		$t0 = microtime(true);

		$packet = array(
			'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
			'body' => json_encode(array(
				'mid' => $this->mid,
				'ts' => time(), // Current actual timestamp
				't' => $is_update ? 'u' : 'f', // Full frame
				'sd' => home_url(),
				'si' => $_SERVER['SERVER_ADDR'],
				'msg' => $data,
			)),
			'blocking' => $is_blocking,	// Do not wait for response if false
			'timeout' => 300,
			'data_format' => 'body',
		);

		$wpres = wp_remote_post($this->target_url, $packet);

		$tt = microtime(true) - $t0;

		if ($is_debug) {
			file_put_contents($log_file, print_r($packet, true)."\n", FILE_APPEND);
			file_put_contents($log_file, 'spent: '.$tt." sec\n\n", FILE_APPEND);	
		}

		return $wpres;
	}

	public static function MakeUniqueMediumID()
	{
		$mid = md5(uniqid('flare_mid')).('|'.home_url().'|'.$_SERVER['SERVER_ADDR']);

		return $mid;
	}
}
