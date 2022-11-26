<?php

/**  
 * Copyright 2013-2018 Epsiloncool
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
 *  @copyright 2013-2018
 *  @license GPL v3
 *  @package Wordpress Fulltext Search Pro
 *  @author Epsiloncool <info@e-wm.org>
 */

/**
 * Manages remote updates for WPFTS Pro
 *
 * @since 2.9.47
 */
final class WPFTS_Updater 
{
	/**
	 * The API URL for the WPFTS store.
	 *
	 * @since 1.0
	 * @access private
	 * @var string $_store_api_url
	 */
	static private $_store_api_url = 'https://fulltextsearch.org/updates/';

	/**
	 * The API URL for the WPFTS update server. 
	 *
	 * @since 1.0
	 * @access private
	 * @var string $_updates_api_url
	 */
	static private $_updates_api_url = 'https://fulltextsearch.org/updates/';

	static private $_subscription_status_transient_key = 'WPFTS_subscription_status_transient_key';
	static private $_update_check_transient_key = 'WPFTS_update_check_transient_key';

	/**
	 * An internal array of data for each product.
	 *
	 * @since 1.0
	 * @access private
	 * @var array $_products
	 */
	static private $_products = array();

	/**
	 * An internal array of settings for the updater instance.
	 *
	 * @since 1.0
	 * @access private
	 * @var array $settings
	 */
	private $settings = array();

	/**
	 * Updater constructor method.
	 *
	 * @since 1.0
	 * @param array $settings An array of settings for this instance.
	 * @return void
	 */
	public function __construct( $settings = array() )
	{
		$this->settings = $settings;
		
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_check' ) );
		add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
		add_action( 'in_plugin_update_message-' . self::get_plugin_file( $settings['slug'] ), array( $this, 'update_message' ), 1, 2 );
	}

	/**
	 * Checks to see if an update is available for the current product.
	 *
	 * @since 1.0
	 * @param object $transient A WordPress transient object with update data.
	 * @return object
	 */
	public function update_check($transient)
	{
		/*
		if (empty($transient->checked)) 
		{
			return $transient;
		}
		*/

		$response_output = get_transient(self::$_update_check_transient_key);
		if ($response_output === false) 
		{

			$params = array(
				'action' => 'update_check',
				'wemail' => self::get_subscription_email(),
				'wdomain' => network_home_url(),
				'wproduct' => $this->settings['name'],
				'wslug' => $this->settings['slug'],
				'wversion' => $this->settings['version']
			);

			$output = array();
			$response = self::api_request(self::$_updates_api_url, array(
				'action' => 'update_check',
				'wemail' => self::get_subscription_email(),
				'wdomain' => network_home_url(),
				'wproduct' => $this->settings['name'],
				'wslug' => $this->settings['slug'],
				'wversion' => $this->settings['version']
			), 0, $output);

			set_transient(self::$_update_check_transient_key, array($response, $output), 60);
		} else {
			$response = $response_output[0];
			$output = $response_output[1];
		}

		if(isset($response) && $response !== false && is_object($response) && !isset($response->errors)) 
		{
			$plugin   = self::get_plugin_file($this->settings['slug']);
			$new_ver  = isset($response->new_version) ? $response->new_version : '';
			$curr_ver = $this->settings['version'];
			/*
			$response->id = $plugin;
			$response->plugin = $plugin;
			$response->icons = array();
			$response->banners = array();
			$response->banners_rtl = array();
			$response->tested = '5.7.2';
			$response->requires_php = '5.7';
			$response->compatibility = new stdClass();
*/
			if (empty($response->package)) 
			{
				$response->upgrade_notice = self::get_update_error_message();
			}
			if (version_compare($new_ver, $curr_ver, '>')) 
			{
				$transient->response[$plugin] = $response;
			}
		}

		return $transient;
	}

	/**
	 * Shows an update message on the plugins page if an update
	 * is available but there is no active subscription.
	 *
	 * @since 1.0
	 * @param array $plugin_data An array of data for this plugin.
	 * @param object $response An object with update data for this plugin.
	 * @return void
	 */
	public function update_message( $plugin_data, $response )
	{
		if ( empty( $response->package ) ) 
		{
			echo self::get_update_error_message( $plugin_data );
		}
	}

	/**
	 * Retrives the data for the plugin info lightbox.
	 *
	 * @since 1.0
	 * @param bool $false
	 * @param string $action
	 * @param object $args
	 * @return object|bool
	 */
	public function plugin_info($false, $action, $args)
	{
		if(!isset($args->slug) || $args->slug != $this->settings['slug']) {
			return $false;
		}

		$output = array();
		$response = self::api_request(self::$_updates_api_url, array(
			'action' => 'plugin_info',
			'wemail' => self::get_subscription_email(),
			'wdomain' => network_home_url(),
			'wproduct' => $this->settings['name'],
			'wslug' => $this->settings['slug'],
			'wversion' => $this->settings['version']
		), 60, $output);

		if(isset($response) && is_object($response) && $response !== false) 
		{
			$response->name = $this->settings['name'];
			$response->sections = (array)$response->sections;
			$response->banners = (array)$response->banners;
			$response->contributors = (array)$response->contributors;
			if (isset($response->contributors_details)) {
				$response->contributors = json_decode(json_encode($response->contributors_details), true);
			}
			return $response;
		}

		return $false;
	}

	/**
	 * Static method for initializing an instance of the updater
	 * for each active product.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function init()
	{
		include WPFTS_UPDATER_DIR . 'includes/config.php';

		foreach($config as $path) 
		{
			if(file_exists($path)) 
			{
				require_once $path;
			}
		}
	}

	/**
	 * Static method for adding a product to the updater and
	 * creating the new instance.
	 *
	 * @since 1.0
	 * @param array $args An array of settings for the product.
	 * @return void
	 */
	static public function add_product($args = array())
	{
		if(is_array($args) && isset($args['slug'])) 
		{
			if(file_exists(WP_CONTENT_DIR . '/plugins/' . $args['slug'])) 
			{
				self::$_products[$args['name']] = $args;
				new WPFTS_Updater(self::$_products[$args['name']]);
			}
		}
	}

	/**
	 * Static method for rendering the license form.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_form()
	{
		// Activate a subscription?
		if(isset($_POST['fl-updater-nonce'])) 
		{
			if(wp_verify_nonce($_POST['fl-updater-nonce'], 'updater-nonce')) 
			{
				self::save_subscription_email($_POST['email']);
			}
		}

		$status = self::get_subscription_status();

		// Include the form ui.
		include WPFTS_UPDATER_DIR . 'includes/form.php';
	}

	/**
	 * Static method for getting the subscription email or license key.
	 *
	 * @since 1.0
	 * @return string
	 */
	static public function get_subscription_email()
	{
		global $wpfts_core;

		return $wpfts_core->get_option('subscription_key');
	}

	/**
	 * Static method for updating the subscription email.
	 *
	 * @since 1.0
	 * @param string $email The new email address or license key.
	 * @return void
	 */
	static public function save_subscription_email($email)
	{
		global $wpfts_core;

		$wpfts_core->set_option('subscription_key', $email);

		delete_transient(self::$_subscription_status_transient_key);
		delete_transient(self::$_update_check_transient_key);
	}

	/**
	 * Static method for retrieving the subscription status.
	 *
	 * @since 1.0
	 * @return bool
	 */
	static public function get_subscription_status()
	{
		$status_output = get_transient(self::$_subscription_status_transient_key);
		if ($status_output === false) 
		{
			$output = array();
			$status = self::api_request(self::$_store_api_url, array(
				'action' => 'subscription_status',
				'wemail' => self::get_subscription_email()
			), 0, $output);
			set_transient(self::$_subscription_status_transient_key, array($status, $output), 60);

			if (isset($status->tm_data)) {
				// Update wp option
				update_option('wpfts_tm_data', serialize(json_decode(json_encode($status->tm_data), true)), 0);
			}

		} else {
			$status = $status_output[0];
			$output = $status_output[1];
		}

		return array($status, $output);
	}

	static public function get_ext_status()
	{
		//$is_url_fopen = ini_get('allow_url_fopen') ? 1 : 0;

		$lic_v = false;
		$is_api_ok = true;
		$tm_data = false;
		$is_expired = 0;
		$upgrade_url = '';
		$is_eval = 0;
		$days_left = -1;
		//if ($is_url_fopen) {
			$lic_status = self::get_subscription_status(); 

			if ((isset($lic_status[0])) && isset($lic_status[0]->active)) {
				$lic_v = true;
			} else {
				// Check the reason
				$code = (isset($lic_status[1]) && isset($lic_status[1]['code'])) ? $lic_status[1]['code'] : -1;
				if ($code != 3) {
					// Only 3 means "key is not valid"
					$is_api_ok = false;
				}
			}

			if (isset($lic_status[0])) {
				if (isset($lic_status[0]->is_expired)) {
					$is_expired = intval($lic_status[0]->is_expired);
				}
				if (isset($lic_status[0]->upgrade_url)) {
					$upgrade_url = trim($lic_status[0]->upgrade_url);
				}
				if (isset($lic_status[0]->is_eval)) {
					$is_eval = trim($lic_status[0]->is_eval);
				}
				if (isset($lic_status[0]->days_left)) {
					$days_left = trim($lic_status[0]->days_left);
				}
			}

			if (isset($lic_status[0]) && isset($lic_status[0]->tm_data)) {
				$tm_data = @json_decode(json_encode($lic_status[0]->tm_data), true);
			} else {
				if (isset($lic_status[1]['raw'])) {
					$tt2 = @json_decode($lic_status[1]['raw'], true);
					if (isset($tt2['tm_data'])) {
						$tm_data = $tt2['tm_data'];
					}
				}
			}
		/*} else {
			// No URL access, so no check license
		}*/

		return array(
			'is_active' => $lic_v,
			//'is_url_fopen' => $is_url_fopen,
			'is_api_ok' => $is_api_ok,
			'tm_data_lic' => isset($tm_data['license']) ? $tm_data['license'] : array(),
			'is_expired' => $is_expired,
			'is_eval' => $is_eval,
			'upgrade_url' => $upgrade_url,
			'days_left' => $days_left,
		);
	}

	/**
	 * Returns an update message for if an update
	 * is available but there is no active subscription.
	 *
	 * @since 1.6.4.3
	 * @param array $plugin_data An array of data for this plugin.
	 * @return string
	 */
	static private function get_update_error_message( $plugin_data = null )
	{
		$message  = '';
		$message .= '<p style="padding:10px 20px; margin-top: 10px; background: #d54e21; color: #fff;">';
		$message .= __( '<strong>UPDATE UNAVAILABLE!</strong>', 'fl-builder' );
		$message .= '&nbsp;&nbsp;&nbsp;';
		$message .= __('Please subscribe to enable automatic updates for this plugin.', 'fl-builder');
		
		//if ( $plugin_data && isset( $plugin_data['PluginURI'] ) ) {
			$message .= ' <a href="'.(WPFTS_Updater::get_upgrade_url( array( 'utm_source' => 'external', 'utm_medium' => 'wpfts', 'utm_campaign' => 'plugins-page' ))).'" target="_blank" style="color: #fff; text-decoration: underline;">';
			$message .= __('Subscribe Now', 'fl-builder');
			$message .= ' &raquo;</a>';
		//}
		
		$message .= '</p>';
		
		return $message;
	}

	/**
	 * Static method for retrieving the plugin file path for a
	 * product relative to the plugins directory.
	 *
	 * @since 1.0
	 * @access private
	 * @param string $slug The product slug.
	 * @return string
	 */
	static private function get_plugin_file( $slug )
	{
		if ( 'fulltext-search-pro' == $slug ) {
			$file = $slug . '/fulltext-search.php';
		}
		else {
			$file = $slug . '/' . $slug . '.php';
		}

		return $file;
	}

	/**
	 * Static method for sending a request to the store
	 * or update API.
	 *
	 * @since 1.0
	 * @access private
	 * @param string $api_url The API URL to use.
	 * @param array $args An array of args to send along with the request.
	 * @return mixed The response or false if there is an error.
	 */
	static private function api_request($api_url, $args, $cache_time, &$output)
	{
		if($api_url) 
		{
			return self::remote_post($api_url, $args, $cache_time, $output);
		}

		return false;
	}

	/**
	 * Get a remote response.
	 *
	 * @since 1.0
	 * @access private
	 * @param string $url The URL to get.
	 * @return mixed The response or false if there is an error.
	 */
	static private function remote_post($url, $params, $cache_time, &$output)
	{
		// Check if we have this request cached
		$output = array('code' => 0);

		$p2 = array();
		foreach ($params as $k => $d)
		{
			$p2[$k] = urlencode($d);
		}
		$request = wp_remote_post($url, array('user-agent' => 'wpfts-plugin-client', 'body' => $p2, 'timeout' => 30));

		//echo "Remote post request: ".$url.' = '.print_r($params, true);
		//echo "Remote post response: ".print_r($request, true)."\n\n";

		if(is_wp_error($request)) 
		{
			$output = array(
				'code' => 1,
				'error' => $request->get_error_message(),
			);
			return false;
		}
		$http_response_code = wp_remote_retrieve_response_code($request);
		if($http_response_code != 200) 
		{
			$output = array(
				'code' => 2,
				'error' => $http_response_code,
			);
			return false;
		}

		$raw_response = wp_remote_retrieve_body($request);

		if (strlen(trim($raw_response)) < 1) {
			$output = array(
				'code' => 4,
				'error' => 'Empty response',
			);
			return false;
		}

		$response = json_decode($raw_response);

		if(isset($response->error)) 
		{
			$output = array(
				'code' => 3,
				'error' => $response->error,
				'raw' => $raw_response,
			);
			return $response;
		}

		return $response;
	}

	static public function get_upgrade_url($args = false)
	{
		return 'https://fulltextsearch.org/buy/'.($args ? '?'.http_build_query($args) : '');
	}

	static public function GetLicenseMessage()
	{
		global $wpfts_core;

		$t = self::get_ext_status();

		$lic_v = isset($t['is_active']) ? $t['is_active'] : 0;
		$is_url_fopen = isset($t['is_url_fopen']) ? $t['is_url_fopen'] : 0;
		$is_api_ok = isset($t['is_api_ok']) ? $t['is_api_ok'] : 0;
		$tm_data_lic = isset($t['tm_data_lic']) ? $t['tm_data_lic'] : array();
		$is_expired = isset($t['is_expired']) ? $t['is_expired'] : 0;
		$is_eval = isset($t['is_eval']) ? $t['is_eval'] : 0;
		$renew_link = isset($t['upgrade_url']) ? $t['upgrade_url'] : '#';
		$days_left = isset($t['days_left']) ? $t['days_left'] : -1;

		$email = self::get_subscription_email();

		$content_key = '';
		if (strlen($email) > 0) {
			if (!$lic_v) {
				if ($is_expired) {
					if ($is_eval) {
						$tr = 'bled';
						$cv = 'tion';
						$content_key = 'EVAL_EXPIRED';
						call_user_func(array($wpfts_core, 'set'.'_'.'op'.$cv), 'ena'.$tr, intval(!1));
					} else {
						$content_key = 'KEY_IS_EXPIRED';
					}
				} else {
					$content_key = 'KEY_NOT_VALID';
				}
			} else {
				// License is valid
				if ($is_eval) {
					$content_key = 'EVAL_ACTIVE';
				} else {
					// Check if pro key will expire soon
					if ($days_left >= 60) {
						$content_key = '';	// Everything is OK
					} elseif ($days_left >= 30) {
						$content_key = 'DISCOUNT_3060';
					} elseif ($days_left >= 15) {
						$content_key = 'DISCOUNT_1530';
					} else {
						$content_key = 'RENEW_15';
					}
				}
			}
		} else {
			// Key was not set
			$content_key = 'NO_KEY_SET';
		}

		$license_page_url = home_url().'/wp-admin/admin.php?page=wpfts-options-licensing';
		$buy_url = 'https://fulltextsearch.org/';
		$contact_url = 'https://fulltextsearch.org/contact/';

		// 0 = warning, 1 = danger
		$msgs = array(
			'NO_KEY_SET' => array(1, 
					sprintf(__('<b>License key has not been installed.</b><br>Thus, your copy of the plugin will <b style="color:red;">not index the contents of files</b> and will not receive regular updates. Please enter a license key to ensure the plugin works properly!<br><br><a href="%s">Licensing page</a>.', 'fulltext-search'), $license_page_url)),
			'EVAL_EXPIRED' => array(1, 
					sprintf(__('<b>The evaluation period for WPFTS Pro has ended.</b><br>Thus, most of the functions of the plugin were disabled. This is a great time to purchase a Pro license!<br><br>Do you still have questions about plugin functionality or pre-sale questions? Please let us know.<br><br><a href="%1s">Purchase license</a> <a href="%2s" style="margin-left: 20px;">Contact us</a>', 'fulltext-search'), $buy_url, $contact_url)),
			'KEY_NOT_VALID' => array(1, 
					sprintf(__('<b>Your license key is <span style="color:red;">NOT VALID</span></b>.<br>Please enter a valid key to ensure the correct operation of the plugin!<br><br><a href="%s">Licensing page</a>', 'fulltext-search'), $license_page_url)),
			'EVAL_ACTIVE' => array(0, 
					sprintf(__('<b>The evaluation version of WPFTS Pro is currently active.</b><br>It expires in <b style="color:red;">%1s</b> days.<br><br>Do you have any questions? Please note that you can always contact us.<br><br><a href="%2s">Purchase license</a> <a href="%3s" style="margin-left: 20px;">Contact us</a>', 'fulltext-search'), $days_left, $buy_url, $contact_url)),
			'DISCOUNT_3060' => array(0, 
					sprintf(__('<b>Your license is active</b> and there is still a lot of time left until it expires.<br>But if you plan to continue to use the full version of the WPFTS Pro after the end of this period, you may want to renew the license now and get a <b style="color:red;">25%% discount</b>. Sounds good, right?<br><br><a href="%s">Renew now with 25%% discount</a>', 'fulltext-search'), $renew_link)),
			'DISCOUNT_1530' => array(0, 
					sprintf(__('<b>Your license will expire soon</b> and now is a good time to renew it.<br>If you renew the license no later than 15 days before the expiration, you will get an additional <b style="color:red;">discount of 20%%</b>. Thank you for support!<br><br><a href="%s">Renew now with 20%% discount</a>', 'fulltext-search'), $renew_link)),
			'RENEW_15' => array(0, 
					sprintf(__('<b style="color:red;">Attention!</b> Less than 15 days are left before the license expires.<br>After the end, the plugin will stop receiving updates, and the contents of newly added files will not be indexed by the Textmill.io service.<br><br>To preserve all functions of the WPFTS Pro and avoid the presence of unindexed files, we recommend updating the license <b>right now</b>. In addition, you will get a 15%% discount.<br><br><a href="%s">Renew now with 15%% discount</a>', 'fulltext-search'), $renew_link)),
			'KEY_IS_EXPIRED' => array(1, 
					sprintf(__('<b><span style="color:red;">Attention! The WPFTS Pro license has expired.</span></b><br>Therefore, your copy of the plugin will no longer receive updates; technical support and indexing of the contents of new files by the Textmill.io service are also disabled.<br><br>To restore the plugin to full functionality, please, renew the license. Today you will additionally receive a <b>15%% discount</b>.<br><br><a href="%s">Renew now with 15%% discount</a>', 'fulltext-search'), $renew_link)),
		);

		$expdt = $wpfts_core->get_option('wpftslic_message_expdt');
		$old_key = $wpfts_core->get_option('wpftslic_message_key');
		$logo = $wpfts_core->root_url.'/style/wpfts_pro_logo_125x60.png';
		
		if (strlen($expdt) > 0) {
			if ((strtotime($expdt) < current_time('timestamp')) || ($old_key != $content_key)) {
				// Set up new message
				if ((strlen($content_key) > 0) && (isset($msgs[$content_key]))) {

					$text = '<table><tr><td><img src="'.htmlspecialchars($logo).'" alt="WPFTS Pro Logo"></td><td><p>'.$msgs[$content_key][1].'</p></td></tr></table>';

					$wpfts_core->set_option('wpftslic_message', $text);
					$wpfts_core->set_option('wpftslic_message_type', $msgs[$content_key][0]);
					$wpfts_core->set_option('wpftslic_message_expdt', date('Y-m-d H:i:s', current_time('timestamp') + 3600 * 24 * 1));
					
				} else {
					// Nothing were found, let's delay for a day
					$wpfts_core->set_option('wpftslic_message', '');
					$wpfts_core->set_option('wpftslic_message_expdt', date('Y-m-d H:i:s', current_time('timestamp') + 3600 * 24 * 1));
				}
				$wpfts_core->set_option('wpftslic_message_key', $content_key);
			}
		} else {
			// Never processed yet
			$wpfts_core->set_option('wpftslic_message_expdt', date('Y-m-d H:i:s', current_time('timestamp') + 3600 * 24 * 0));
			// Change this later			
		}
	}
}