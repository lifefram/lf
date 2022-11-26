<?php
/**
	* Client for the Site Search 360 API
	*
	* This class communicates with the Site Search 360 API.
	*
	* @author David Urbansky <david@sitesearch360.com>
	*
	* @since 1.0
	*
	*/

/**
	* The Site Search 360 API client
	*/
	class SiteSearch360Client {

/**
	* The API base URL
	*/
	private $baseUrl = 'https://api.sitesearch360.com/';
	private $searchUrl = "https://global.sitesearch360.com/";
	private $statsUrl = "https://insights.sitesearch360.com/insights/";
	//private $baseUrl = 'http://localhost:8585/';

/**
	* The token to authenticate API calls.
	*/
	private $token = NULL;
	private $siteId = NULL;

	public function __construct() {
		$this->siteId = get_option( 'ss360_siteId' );
		$this->token = get_option( 'ss360_api_token' );
	}

/**
	* Delete a page from the index.
	*
	* @param string $url The publicly accessible URL of the page.
	*/
	public function deletePage( $url ) {
		$url = $this->baseUrl . 'sites/page?url='.urlencode($url).'&token='.urlencode($this->token);
		return $this->callApi($url, 'DELETE', null, true);
	}


	public function indexPage( $post ) {
		$url = $this->baseUrl . 'sites/pageJson?token='.urlencode($this->token).'&ignoreUndefinedFilters=true&source=wp';
		if(defined('JSON_INVALID_UTF8_IGNORE')) {
			$json = json_encode($post, JSON_INVALID_UTF8_IGNORE);
		} else {
			$json = json_encode($post);
		}
		return $this->callApi($url, 'POST', $json, true);
	}

	/**
	* Index or Re-Index a page via crawler.
	*
	* @param string $postUrl The publicly accessible URL of the page.
	*/
	public function notifyCrawler($postUrl){
		$url = $this->baseUrl . 'sites/page?token='.urlencode($this->token).'&url='.urlencode($postUrl);
		return $this->callApi($url, 'POST', array(), true);
	}

	/**
	* Bulk index site
	*
	* @param array $posts All blog posts.
	* @return array - api response if api request is sent, error array otherwise
	*/
	public function bulkIndex($posts){
		$url = $this->baseUrl . 'sites/pagesJson?token='.urlencode($this->token).'&ignoreUndefinedFilters=true&source=wp';
		if(defined('JSON_INVALID_UTF8_IGNORE')) {
			$json = json_encode($posts, JSON_INVALID_UTF8_IGNORE);
		} else {
			$json = json_encode($posts);
		}
		if($json !== false) {
			return $this->callApi($url, 'POST', $json, true);
		} else {
			// don't call API when json is malformed
			return [
				'status' => 'failure',
				'code' => 400,
				'message' => json_last_error_msg(),
			];
		}
	}

	/**
	 * Create filter configuration
	 * 
	 * @param string $name The filter name.
	 * @param string $type The filter type (COLLECTION, SINGLE_NUMERIC).
	 * @param string $logic The filter logic (OR, AND).
	 */
	public function createFilter($name, $type, $logic){
		$url = $this->baseUrl . 'filters?syncConfig=true&token='.urlencode($this->token);
		$payload = array();
		$payload['name'] = $name;
		$payload['type'] = $type;
		$payload['logic'] = $logic;
		$payload['preventDuplicate'] = TRUE;
		$res = $this->callApi($url, 'POST', $payload, true);
		return ('fid#' . $res['filter']['id']);
	}

	/**
	* Register a new user by email and site.
	*
	* @param string $email The email of the user
	* @param string $domain The domain of the site to be indexed
	*/
	public function register($email, $domain) {
		$url = $this->baseUrl . 'users?email='.urlencode($email).'&site='.urlencode($domain).'&source=WORDPRESS';
		return $this->callApi($url, 'POST', null, false);
	}

	/**
	 * Sign in a user to fetch api key.
	 * 
	 * @param string $email The email or the siteId of the user.
	 * @param string $password The password of the user.
	 */
	public function login($email, $password){
		$url = $this->baseUrl . 'users/login';
		$payload = array();
		$payload['siteId'] = $email;
		$payload['password'] = $password;
		return $this->callApi($url, 'POST', $payload, false);
	}

	/**
	 * Search
	 *
	 * @param string $searchterm search term
	 * @param int $limit max # of results you want returned
	 * @param bool $log true to log in dashboard, false otherwise
	 * @return array of articles matching search term
	 */
	public function search($searchterm, $limit, $log) {
		$site       = urlencode($this->siteId);
		$searchterm = urlencode($searchterm);
		$log        = $log ? 'true' : 'false';

		$url = $this->searchUrl ."sites/?site=$site&query=$searchterm&limit=$limit&log=$log&groupResults=false";
		
		return $this->callApi($url, 'GET', null, false);
	}

	public function presign(){
		$url = $this->baseUrl . 'users/jwt?token='.urlencode($this->token);
		$res = $this->callApi($url, 'POST', null, true);
		if (isset($res['appAuthentication'])) {
			return 'https://app.sitesearch360.com/?jwToken=' . $res['appAuthentication'];
		}
		return 'https://control.sitesearch360.com/login?auth=' . $res['authentication'];
	}

	public function planInfo(){
		$plugin = new SiteSearch360Plugin();
		$number_of_posts = $plugin->getAllPostCount();
		$url = $this->baseUrl . 'users/plan?token='.urlencode($this->token).'&pageCount='.$number_of_posts;
		return $this->callApi($url, 'GET', null, true);
	}

	public function syncSearchDesigner(){
		$plugin = new SiteSearch360Plugin();
		$siteId = $plugin->getSiteId();
		$config = $plugin->getConfig();
		$paylod = array();
		$payload['siteId'] = $siteId;
		$payload['ss360Config'] = json_encode($config);
		$url = $this->baseUrl.'users/ss360Config?token='.urlencode($this->token);
		return $this->callApi($url, 'PUT', $payload, true);
	}

	public function getGlobalSS360Config(){
		$url = $this->baseUrl.'users/ss360Config?siteId='.urlencode($this->siteId).'&token='.urlencode($this->token);
		return $this->callApi($url, 'GET', null, true);
	}

	public function loadFilters(){
		$url = $this->baseUrl . 'filters?token='.urlencode($this->token);
		return $this->callApi($url, 'GET', null, true);
	}

	public function ensureSortDataPointExists($name){
		$site_config = $this->loadConfig();
		if(isset($site_config['globalDataPoints'])){
			$data_points = $site_config['globalDataPoints'];
			foreach($data_points as $dp){
				if($dp['sort'] && $dp['key']==$name){
					return;
				}
			}
		}else {
			$data_points = array();
		}
		$sort_dp = array();
		$sort_dp['boost'] = false;
		$sort_dp['key'] = $name;
		$sort_dp['show'] = false;
		$sort_dp['single'] = true;
		$sort_dp['sort'] = true;
		$sort_dp['sortAsc'] = false;
		$sort_dp['xpath'] = '//noxpath';
		$data_points[] = $sort_dp;
		return $this->saveSingleConfig('globalDataPoints', $data_points);
	}

	public function hasContentGroups(){
		$site_config = $this->loadConfig();
		return isset($site_config['pageTypes']) && sizeof($site_config['pageTypes']) > 0;
	}

	public function ensureContentGroupsExist($categories) {
		$site_config = $this->loadConfig();
		if(isset($site_config['pageTypes'])){
			$page_types = $site_config['pageTypes'];
		} else {
			$page_types = array();
		}
		$existing_page_types = array();
		foreach($page_types as $pt){
			$existing_page_types[] = $pt['contentType'];
		}
		$save = false;
		foreach($categories as $category){
			if(!in_array($category->name, $existing_page_types) && strlen($category->name) > 0){
				$save = true;
				$new_page_type = array();
				$condition = array();
				$condition['xPath'] = '//noxpath';
				$structuredData = array();
				$structuredData['generic'] = array(); 
				$new_page_type['conditions'] = $condition;
				$new_page_type['contentExcludeXPaths'] = array();
				$new_page_type['contentIncludeXPaths'] = array();
				$new_page_type['imageXPaths'] = array();
				$new_page_type['titleXPaths'] = array();
				$new_page_type['contentType'] = $category->name;
				$new_page_type['structuredData'] = $structuredData;
				$page_types[] = $new_page_type;
			}
		}
		if($save){
			return $this->saveSingleConfig('pageTypes', $page_types);
		}
	}

	public function disableAutoIndexing(){
		return $this->updateIndexing('no');
	}

	public function enableAutoIndexing($removeSortingDataPoint){
		$res = $this->updateIndexing('crawler');
		// cleanup
		$site_config = $this->loadConfig();
		if(isset($site_config['pageTypes'])){
			$page_types = $site_config['pageTypes'];
		} else {
			$page_types = array();
		}
		$new_page_types = array();
		$save = false;
		foreach($page_types as $pt){
			if(!isset($pt['conditions']) || $pt['conditions']['xPath'] != '//noxpath') {
				$new_page_types[] = $pt;
			} else {
				$save = true;
			}
		}
		if($save){
			$this->saveSingleConfig('pageTypes', $new_page_types);
		}
		if($removeSortingDataPoint) {
			$save = false;
			$new_data_points = array();
			if(isset($site_config['globalDataPoints'])){
				$data_points = $site_config['globalDataPoints'];
				foreach($data_points as $dp){
					if($dp['xpath']!='//noxpath' || !$dp['sort']){
						$new_data_points[] = $dp;
					} else {
						$save = true;
					}
				}
			}
			if($save){
				$this->saveSingleConfig('globalDataPoints', $new_data_points);
			}
		}
		return $res;
	}

	public function loadConfig() {
		$url = $this->baseUrl.'sites/config?token='.urlencode($this->token);
		return $this->callApi($url, 'GET', NULL, true);
	}

	public function saveSingleConfig($key, $json_val) {
		$url = $this->baseUrl.'sites/configSingle?syncConfig=true&token='.urlencode($this->token);
		$payload = array();
		$payload['key'] = $key;
		$payload['value'] = json_encode($json_val);
		return $this->callApi($url, 'PUT', $payload, true);
	}

	public function emptyEntireIndex(){
		$url = $this->baseUrl.'sites/pages?urlPattern=.*&token='.urlencode($this->token);
		return $this->callApi($url, 'DELETE', null, true);
	}

	public function startRecrawl(){
		$url = $this->baseUrl.'sites/reindex?token='.urlencode($this->token).'&pageLimit=-1&siteId='.$this->siteId;
		return $this->callApi($url, 'POST', array(), true);
	}

	private function updateIndexing($indexingType){
		$url = $this->baseUrl.'sites/configSingle?syncConfig=true&token='.urlencode($this->token);
		$payload = array();
		$payload['key'] = 'indexer';
		$payload['value'] = $indexingType;
		return $this->callApi($url, 'PUT', $payload, true);	
	}

	public function detectInputs(){
		$url = $this->baseUrl . 'sites/detectInputs?token='.urlencode($this->token).'&url='.urlencode(get_site_url());
		return $this->callApi($url, 'GET', null, true);
	}
	
	public function getBaseUrl(){
		return $this->baseUrl;
	}

	private function callApi($url, $method, $payload, $protected) {
		if ($protected && ($this->token === NULL || strlen($this->token) === 0)) {
			$failure = array();
			$failure['status'] = 'failure';
			return $failure;
		}
		$headers = array(
			'User-Agent' => 'Site Search 360 Wordpress Plugin/' . SITESEARCH360_VERSION,
			);

		$args = array(
			'method' => $method,
			'headers' => $headers,
			'timeout' => 10,
			'redirection' => 2,
			'httpversion' => '1.0',
			'blocking' => true,
			'body' => null,
			'cookies' => array()
			);

		if(($method=='PUT' || $method=='POST') && $payload!=null){
			$args['body'] = $payload;
		}
		
		$response = wp_remote_request( $url, $args );
		$response_body = wp_remote_retrieve_body( $response );
		return json_decode($response_body,true);
	}

}