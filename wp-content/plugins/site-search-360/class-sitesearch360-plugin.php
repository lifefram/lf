<?php

/**
 * Site Search 360 Wordpress Plugin
 *
 * This class takes care of the communication between Wordpress and the Site Search 360 API.
 *
 * @author David Urbansky <david@sitesearch360.com>
 *
 * @since 1.0
 *
 */
class SiteSearch360Plugin
{

    private $client = NULL;
    private $document_type_slug = 'posts';
    private $excluded_post_types = array('scheduled-action', 'nav_menu_item');

    public function __construct()
    {
		$this->client = new SiteSearch360Client();

        add_action('admin_menu', array($this, 'sitesearch360Menu'));
        add_action('admin_init', array($this, 'initializeAdminPage'));

        // hooks for sending post updates to the Site Search 360 API
        $syncOnSave = get_option('ss360_sync_on_save');
        $syncOnStatus = get_option('ss360_sync_on_status');
        $syncOnFuture = get_option('ss360_sync_on_future');
        $syncOnDelete = get_option('ss360_sync_on_delete');
        if($syncOnFuture==null || $syncOnFuture){
            add_action('future_to_publish', array($this, 'handleFutureToPublish'));
        }
        if($syncOnSave==null || $syncOnSave){
            add_action('save_post', array($this, 'handleSavePost'), 99, 1);
        }
        if($syncOnStatus==null || $syncOnStatus){
            add_action('transition_post_status', array($this, 'handleTransitionPostStatus'), 99, 3);
        }
        if($syncOnDelete==null || $syncOnDelete){
            add_action('trashed_post', array($this, 'deletePost'));
        }

        if($this->getType()!="filter"){
            add_action('wp_enqueue_scripts', array($this, 'enqueueSitesearch360Assets'));
        }
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_ajax_ss360_index', array($this, 'sitesearch360Index'));
        add_action('plugins_loaded', array($this, 'sitesearch360CheckVersion'));
        if(get_option('ss360_review_interaction')==null){
            add_action('wp_ajax_ss360_review', array($this, 'sitesearch360ReviewInteracted'));
        }
        // override wordpress default search engine
        if($this->getType()!="full"){
            add_filter('the_posts', array( $this, 'overrideSearch' ), 99, 2 );
        }
		add_filter( 'plugin_action_links', array($this, 'addPluginActionLinks'), 10, 2);
		add_filter('wp_nav_menu_items', array($this, 'addMenuSearchForms'), 9999999, 2);

        // shortcodes
        add_shortcode('ss360-searchbox', array($this, 'searchbox_shortcode'));
        add_shortcode('ss360-searchbutton', array($this, 'searchbutton_shortcode'));
        add_shortcode('ss360-form', array($this, 'form_shortcode'));
        add_shortcode('ss360-resultblock', array($this, 'embed_shortcode'));
        add_action( 'widgets_init', array($this, 'ss360_load_widget') );
    }


    function ss360_load_widget() {
        register_widget( 'SiteSearch360Widget' );
    }

    public function searchbox_shortcode($attrs) {
		$keys = ['include', 'exclude', 'include-suggest', 'exclude-suggest'];
		$a = shortcode_atts(array('style' => '', 'include' => NULL, 'exclude' => NULL, 'include-suggest' => NULL, 'exclude-suggest' => NULL, 'placeholder' => NULL), $attrs);
		
		$dataStr = '';
		foreach($keys as $key) {
			if (isset($a[$key]) && $a[$key] !== NULL) {
				$dataStr = $dataStr . ' data-ss360-' . $key . '="[' . $a[$key] . ']"';
			}
		}

		$placeholder = '';
		if ($a['placeholder'] != null) {
			$placeholder = ' data-ss360-keep-placeholder="true" placeholder="' . $a['placeholder'] . '"'; 
		}
		
        return '<input class="ss360-searchbox" type="search"'. $placeholder .' style="'.$a['style'].'"'.($this->getType()!='full'?' name="s"':'').$dataStr.'>';
    }

    public function searchbutton_shortcode($attrs, $content = null) {
        $a = shortcode_atts(array('style' => ''), $attrs);
        $text = $content != null ? $content : '';
        return '<button class="ss360-searchbutton" style="'.$a['style'].'">'.$text.'</button>';
    }

    public function form_shortcode($attrs, $content = null) {
        $customStyling = !isset($attrs['plain']);
        $defaultStyle = 'display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-ms-flex-direction:row;flex-direction:row;-webkit-box-align:center;-ms-flex-align:center;align-items:center';
        if ($customStyling) {
            $defaultStyle = $defaultStyle . ';margin-left:auto;margin-right:auto';
        }
        $a = shortcode_atts(array('style' => $defaultStyle), $attrs);
        $inner = do_shortcode($content != null ? $content : '');
        $result = '';
        if($this->getType() != 'full') {
            $result = '<form role="search" method="get" class="ss360-search-form search-form" action="'.esc_url(home_url('/')).'"';
        } else {
            $result = '<section role="search" class="ss360-search-form"';
        }
        if($customStyling) {
            $result = $result . ' data-ss360="true"';
        }
        $result = $result . ' style="'.$a['style'].'">';
        $result = $result . $inner;
        if($this->getType() != 'full') {
            $result = $result . '</form>';
        } else {
            $result = $result . '</section>';
        }
        return $result;
    }

    public function embed_shortcode($attrs) {
        $a = shortcode_atts(array('style' => 'width:100%;display:block'), $attrs);
        return '<section class="ss360-search-results-block" style="'.$a['style'].'"></section>';
    }

    public function addPluginActionLinks($links, $plugin_base_name) {
        if(strpos($plugin_base_name, 'sitesearch360.php')) {
            return array_merge([
                '<a href="'.admin_url('admin.php?page=sitesearch360').'">'. __('Settings', 'site-search-360') .'</a>'
            ], $links);
        }
        return $links;
	}
	
	public function addMenuSearchForms($menu, $attrs) {
		$location = isset($attrs->theme_location) ? $attrs->theme_location : null;
		if ($location != null) {
			$ss360_selected_menus = get_option('ss360_selected_menus');
			if ($ss360_selected_menus == null) {
				$ss360_selected_menus = array();
			}
			if (in_array($location, $ss360_selected_menus)) {
				$menu = $menu . '<li class="ss360-search-menu-item">' . $this->form_shortcode(array(), $this->searchbox_shortcode(array()) . $this->searchbutton_shortcode(array())) . '</li>';
			}
		}

		return $menu;
	}

    /**
     * Initialize the Site Search 360 Search plugin's admin screen
     */
    public function initializeAdminPage()
    {
        load_plugin_textdomain( 'site-search-360', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); // load localized texts
    }

    public function createInitialConfig($siteId, $searchBox, $searchButton, $hasContentGroups){
        $configuration = array();
        $configuration['siteId'] = $siteId;
        $configuration['showErrors'] = false;

        $configuration['searchBox'] = array();
        $configuration['searchBox']['selector'] = $searchBox!=null ?  $searchBox : '#searchBox, form[role="search"] input[name="s"]';
        if($searchButton!=null){
            $configuration['searchBox']['searchButton'] = $searchButton;
        }else {
            $configuration['searchBox']['searchButton'] = 'form[role="search"] input.search-submit';

        }
        $configuration['layout'] = array();
        $configuration['layout']['navigation'] = array();
        $configuration['layout']['navigation']['position'] = 'top';
        
        $configuration['results'] = array();
        $configuration['results']['moreResultsButton'] = esc_html__('See more', 'site-search-360');
        $configuration['results']['embedConfig'] = array(); // we can safely set this for all customers, as we show layover as fallback
        $configuration['results']['embedConfig']['contentBlock'] = '.ss360-search-results-block';

        if($hasContentGroups){
            $configuration['contentGroups'] = array();
            $configuration['contentGroups']['otherName'] = esc_html__('Other', 'site-search-360');
        }
        return $configuration;
    }

    public function sitesearch360CheckVersion(){
        $current_version = get_option('ss360_plugin_version');
        if($current_version==null){
            if(get_option('ss360_sr_type')!=null || get_option('ss360_account_created')!=null){ // the version was updated
                update_option('ss360_is_configured', true);
                update_option('ss360_is_indexed', true);
                update_option('ss360_old_indexing_notice', true);
                delete_option('ss360_password');
            }
         
            update_option('ss360_plugin_version', SITESEARCH360_VERSION);
            update_option('ss360_installation_id', uniqid());
        }
    }

    public function sitesearch360ReviewInteracted(){
        update_option('ss360_review_interaction', true);
        $res = array();
        $res['status'] = 'success';
        wp_send_json_success($res);
    }

    public function getPostCount($content_type){
        $ss360_count_posts = wp_count_posts($content_type); 
        if(!isset($ss360_count_posts->publish)){
            return 0;
        }
        $count = $ss360_count_posts->publish;
        if($count==null){
            return 0;
        }
        return $count;
    }

    public function getAllPostCount(){
        return $this->getPostCount('post') + $this->getPostCount('page') +$this->getPostCount('product');
    }

    public function getPostTypes(){
        $post_types = get_post_types( array('public' => true, '_builtin' => false, 'exclude_from_search' => false), 'names', 'and' ); 
        if($post_types == NULL){
            $post_types = array();
        }
        $post_types[] = 'post';
        $post_types[] = 'page';
        if(array_search('product', $post_types)===FALSE){
            $post_types[] = 'product';
        }
        if(array_search('scheduled-action', $post_types)!==FALSE){
            $idx = array_search('scheduled_action', $post_types);
            array_splice($post_types, $idx, 1);
        }
        if(array_search('nav_menu_item', $post_types)!==FALSE){
            $idx = array_search('nav_menu_item', $post_types);
            array_splice($post_types, $idx, 1);
        }
        return $post_types;
    }

    /**
     * Index up to $index_chunk_size posts/pages.
     */
    public function sitesearch360Index(){
        $index_chunk_size = 100;
        if(!empty($_POST) && isset($_POST['chunkSize'])){
            $index_chunk_size = (int) $_POST['chunkSize'];
        }
  

        $post_types = $this->getPostTypes();
        
        $posts_to_index = array();
        $posts_index_offsets = array();
        $schedule = array();

        $scheduled = 0;
        $remaining_total = 0;
		$total = 0;
		$page_limit = get_option('ss360_page_limit');

        foreach($post_types as $post_type){
            $to_db_key = 'ss360_'.$post_type.'_to_index';
            $offset_db_key = 'ss360_'.$post_type.'_index_offset';
            $to_index = get_option($to_db_key);
            if($to_index==null){
                $to_index = $this->getPostCount($post_type);
                update_option($to_db_key, $to_index);
            }
            $total += $to_index;
            $posts_to_index[$post_type] = $to_index;
            $index_offset = get_option($offset_db_key);
            if($index_offset == null){
                $index_offset = 0;
            }

            $posts_index_offsets[$post_type] = $index_offset;

            $remaining = $to_index - $index_offset;
            if($remaining > 0 && $scheduled < $index_chunk_size){
				$to_schedule = max(0, min($index_chunk_size - $scheduled, $remaining));
				
				$scheduled +=  $to_schedule;

                $remaining_total += ($remaining - $to_schedule);

                $indexer = new SiteSearch360Indexer($post_type, $index_offset, $to_schedule);
                $res = $indexer->index();

                if ($res === null || (isset($res['status']) && $res['status'] === 'success')){
                    // error_log(__FUNCTION__ .' - OK');
                } else {
                    error_log(__FUNCTION__ .' - FAILED - status - '. $res['status'] .' - message - '. $res['message']);
                }

                $index_offset += $to_schedule;
                update_option($offset_db_key, $index_offset);
            } else if($remaining > 0){
                $remaining_total += $remaining;
            }            
        }
        
		$res = array();
		$indexed = $total - $remaining_total;
		if ($page_limit != NULL) {
			$total = min($total, $page_limit);
		}
        $res['total'] = $total;
        $res['indexed'] = $indexed;

        if($remaining_total <= 0 || ($page_limit != NULL && $indexed >= $page_limit)){
            update_option('ss360_is_indexed', true);
        }
        wp_send_json_success($res);
    }


    private function shouldIndex($post) {
        $post_type = get_post_type($post);
        return array_search($post_type, $this->excluded_post_types)==FALSE;
    }

    /**
     * Deletes a post from Site Search 360's search index any time the post's status transitions from 'publish' to anything else.
     *
     * @param int $new_status The new status of the post
     * @param int $old_status The old status of the post
     * @param int $post The post
     */
    public function handleTransitionPostStatus($new_status, $old_status, $post)
    {
        if ("publish" == $old_status && "publish" != $new_status && $this->shouldIndex($post)) {
            $this->deletePost($post->ID);
        }
    }

    /**
     * Index a post when its state changes from "future" to "publish"
     *
     * @param int $post The post
     */
    public function handleFutureToPublish($post)
    {
        if ("publish" == $post->post_status && $this->shouldIndex($post)) {
            $this->indexPost($post->ID);
        }
    }

    /**
     * Sends a post to the Site Search 360 API (only if post status is "publish").
     *
     * @param int $postId The id of the post to be indexed.
     */
    public function handleSavePost($postId)
    {
        $post = get_post($postId);
        if("publish" == $post->post_status && $this->shouldIndex($post)){
            $this->indexPost($postId);
        }
    }

	private function checkRate() {
		$current = get_option('ss360_rate_checker', true);
		$minuteStamp = strval(floor(time() / 60));
		if ($current == NULL) {
			update_option('ss360_rate_checker', $minuteStamp . '<#>1');
			return true;
		} else {
			$parts = explode('<#>', $current);
			$minutePart = $parts[0];
			if ($minutePart != $minuteStamp) {
				update_option('ss360_rate_checker', $minuteStamp . '<#>1');
				return true;
			}
			$count = intval($parts[1]);
			if ($count > 300) {
				return false;
			}
			update_option('ss360_rate_checker', $minuteStamp . '<#>' . ($count + 1));
			return true;
		}				
	}

    /**
     * Sends a request to the Site Search 360 API index a specific post in the server-side search engine.
     *
     * @param int $postId The ID of the post to be indexed.
     */
    public function indexPost($postId)
    {
        try {
			if ($this->checkRate()) {
				$indexer = new SiteSearch360Indexer(1,0,'');
				$indexer->indexSingle($postId);
			}
        } catch (Error $e) {
            return;
        }
    }

    /**
     * Sends a request to the Site Search 360 API remove a specific post from the server-side search engine.
     *
     * @param int $postId The ID of the post to be deleted.
     */
    public function deletePost($postId)
    {
        $url = str_replace('__trashed','',get_permalink($postId));
        $this->client->deletePage($url);
    }

    public function getType()
    {
        $type = get_option("ss360_sr_type");
        if ($type == null) {
            return "full";
        }
        return $type;
    }

    public function getSiteId(){
        return get_option('ss360_siteId');
    }

    public function getConfig()
    {
        return json_decode(get_option("ss360_config"), true);
    }

    public function saveConfig($configObj){
        $configuration = json_encode($configObj);
        update_option('ss360_config', $configuration);
        $this->client->syncSearchDesigner();
    }

    /**
     * Override default wordpress search
     * @param $posts
     * @param $query
     */
    public function overrideSearch($posts, $query = false)
    {
        if(get_option('ss360_siteId') == NULL) { // not logged in yet
            return $posts;
        }

        if (!$query)
            return $posts;

        if (!$query->is_search())
            return $posts;

        if (!$query->is_main_query())
            return $posts;

        if ($query->is_paged())
            return $posts;

        if($this->getType()=="full")
            return $posts;

        $searchterm = trim($_GET['s']);

        if (empty($searchterm)) {
            return array();
        }

        $limit = 60;
        $log = true;

        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            $limit = 6;
            $log = false;
        }

        try {
            $results = $this->client->search($searchterm, $limit, $log);
        } catch (Error $e) {
            return $posts;
        }
 
        $totalResults = $results['totalResults'];
        if ($totalResults == 0) {
            return array();
        }

        $suggests = $results['suggests']['_'];
        $ids = array();
        if (is_array($suggests) || is_object($suggests)) {
            foreach ($suggests as &$suggests) {
                $permalink = $suggests['link'];
                $ids[] = url_to_postid($permalink);
            }
        }
        else{
            return array();
        }

        if (!count($ids))
            return array();
        return get_posts(array('include' => $ids, 'orderby' => 'post__in', 'post_type'=>'any', 'post_status' => array('Import List', 'publish')));
    }

    /**
     * Includes the Site Search 360 Search plugin's admin page
     */
    public function sitesearch360AdminPage()
    {
        include('sitesearch360-admin-page.php');
    }

    public function sitesearch360BaseConfigPage(){
        include('sitesearch360-configuration-basics.php');
    }

    public function sitesearch360SearchResultPage(){
        include('sitesearch360-configuration-search-results.php');
    }

    public function sitesearch360SuggestionPage(){
        include('sitesearch360-configuration-suggestions.php');
    }

    public function sitesearch360FilterPage(){
        include('sitesearch360-configuration-filters.php');
    }

    public function sitesearch360EditorPage(){
        include('sitesearch360-editor-page.php');
    }

    public function siteSearch360SearchDesignerPage(){
        include('sitesearch360-search-designer.php');
    }  
    
    public function siteSearch360CallbackPage(){
        include('sitesearch360-callback-page.php');
    }
    public function sitesearch360IndexingPage(){
        include('sitesearch360-indexing-page.php');
	}
	
	public function sitesearch360WooCommercePage() {
		include('sitesearch360-woocommerce-page.php');
	}

    /**
     * Create a menu in the WordPress admin for the Site Search 360 Search plugin.
     */
    public function sitesearch360Menu()
    {
        add_menu_page('Site Search 360', 'Site Search 360', 'manage_options', "sitesearch360", array($this, 'sitesearch360AdminPage'), plugins_url('assets/ss360_logo_menu.png', __FILE__));
        $baseConfigLabel = esc_html__('Basic Configuration', 'site-search-360');
        $searchResultsLabel = esc_html__('Search Results', 'site-search-360');
        $suggestionsLabel = esc_html__('Suggestions', 'site-search-360');
        $filterLabel = esc_html__('Filters', 'site-search-360');
        $editorLabel = esc_html__('Configuration Editor', 'site-search-360');
        $searchDesignerLabel = esc_html__('Search Designer', 'site-search-360');
        $callbacksLabel = esc_html__('Custom Callbacks', 'site-search-360');
		$indexingLabel = esc_html__('Indexing', 'site-search-360');
		$woocommerceLabel = esc_html__('WooCommerce Settings', 'site-search-360');
        $is_configured = get_option("ss360_is_configured");
        $integration_type = $this->getType();
        $ss360_indexing_mode = get_option("ss360_indexing_mode");
        if($ss360_indexing_mode == null) {
            $ss360_indexing_mode = 'db';
        }
        if($is_configured==null && !empty($_POST) && isset($_POST['page'])){ // configuration finished, but option not updated yet
            $page = $_POST['page'];
            $is_configured = ($page > 2 && $integration_type=='filter') || ($page > 3);
        }
        if(!empty($_POST) && isset($_POST['action']) && isset($_POST['_wpnonce']) && $_POST['action']=='ss360_setType' && isset($_POST['ss360_sr_type'])){ // updating search result type
            $integration_type = $_POST['ss360_sr_type'];
        }
        if(!empty($_POST) && isset($_POST['action']) && $_POST['action'] == 'reindex' && isset($_POST['ss360_indexing_mode'])) {
            $ss360_indexing_mode = $_POST['ss360_indexing_mode'];
        }
        add_submenu_page('sitesearch360', 'Site Search 360', esc_html__('Dashboard', 'site-search-360'), 'manage_options', 'sitesearch360', array($this, 'sitesearch360AdminPage'));
        if($is_configured){
            if($integration_type!='filter'){
                add_submenu_page('sitesearch360', $baseConfigLabel.' - Site Search 360', $baseConfigLabel, 'manage_options', 'sitesearch360-basic-configuration', array($this, 'sitesearch360BaseConfigPage'));
            }
            if($integration_type=='full'){
                add_submenu_page('sitesearch360', $searchResultsLabel.' - Site Search 360', $searchResultsLabel, 'manage_options','sitesearch360-search-results', array($this, 'sitesearch360SearchResultPage'));
            }
            if($integration_type!='filter'){
                add_submenu_page('sitesearch360', $suggestionsLabel.' - Site Search 360', $suggestionsLabel, 'manage_options','sitesearch360-suggestions', array($this, 'sitesearch360SuggestionPage'));
            }
            $current_plan = get_option('ss360_active_plan');
            if($integration_type=='full' && $current_plan!='FREE' && $current_plan!='COLUMBO'){
                add_submenu_page('sitesearch360', $filterLabel.' - Site Search 360', $filterLabel, 'manage_options','sitesearch360-filter', array($this, 'sitesearch360FilterPage'));
            }
            if($integration_type!='filter'){
                add_submenu_page('sitesearch360', $editorLabel.' - Site Search 360', $editorLabel, 'manage_options','sitesearch360-editor', array($this, 'sitesearch360EditorPage'));
            }
            if($integration_type=='full'){
                add_submenu_page('sitesearch360', $searchDesignerLabel.' - Site Search 360', $searchDesignerLabel, 'manage_options','sitesearch360-search-designer', array($this, 'siteSearch360SearchDesignerPage'));
            } 
            if($integration_type!='filter'){
                add_submenu_page('sitesearch360', $callbacksLabel.' - Site Search 360', $callbacksLabel, 'manage_options','sitesearch360-callbacks', array($this, 'sitesearch360CallbackPage'));
            }
            if($ss360_indexing_mode == 'db' && $this->usesACFs()) {
                add_submenu_page('sitesearch360', $indexingLabel.' - Site Search 360', $indexingLabel, 'manage_options', 'sitesearch360-indexing', array($this, 'sitesearch360IndexingPage'));
			}
			if (class_exists('WooCommerce')) {
				add_submenu_page('sitesearch360', $woocommerceLabel.' - Site Search 360', $woocommerceLabel, 'manage_options', 'sitesearch360-woocommerce', array($this, 'sitesearch360WooCommercePage'));
			}
        }
    }

    /**
     * Enqueues the styles used by the plugin's admin page.
     * This method is called by the admin_enqueue_scripts action.
     */
    public function enqueue_admin_assets($hook)
    {
        if (strpos($hook, 'sitesearch360') == false)
            return;
        wp_enqueue_style('admin_styles', plugins_url('assets/ss360_admin_styles.min.css', __FILE__));
    }

    public function usesACFs() {
        return (function_exists('is_plugin_active') && (is_plugin_active('advanced-custom-fields/acf.php') || is_plugin_active('advanced-custom-fields-pro/acf.php'))) || function_exists('acf_get_field_groups');
    }

    /**
     * Enqueues the javascripts and styles to be used by the plugin on the primary website.
     * This method is called by the wp_enqueue_scripts action.
     */
    public function enqueueSitesearch360Assets()
    {
        if (!function_exists('ss360Config'))   {
			
            function ss360Config()
            {
				$configuration = json_decode(get_option("ss360_config"), true);
				$type = get_option("ss360_sr_type");

				$should_inject_search = get_option("ss360_inject_search");
				if ($should_inject_search == null) {
					$should_inject_search = true;
				} else {
					$should_inject_search = $should_inject_search == 1;
				}
				
				if ($type == null) {
					$type = "full";
				}

                if ($type != "filter"  && $should_inject_search) {
                    if($type!='full'){
                        if(isset($configuration['searchBox'])){unset($configuration['searchBox']['searchButton']);}
                        if(isset($configuration['voiceSearch'])){unset($configuration['voiceSearch']['enabled']);}
                    }
                    ?>
                    <!-- Site Search 360 WP v<?php $ss360_v = defined('SITESEARCH360_VERSION') ? SITESEARCH360_VERSION : '1.1.28'; echo $ss360_v; ?> -->
                    <script type="text/javascript">
                        var ss360Config = <?php echo json_encode($configuration); ?>;
                        if(ss360Config.searchBox === undefined) {
                            ss360Config.searchBox = {};
                        }
                        (function() { // shortcodes support
                            var sbSelector = ss360Config.searchBox.selector || '';
                            var sbutSelector = ss360Config.searchBox.searchButton || '';
                            if(sbSelector.trim().length > 0) {
                                sbSelector += ',';
                            }    
                            if(sbutSelector.trim().length > 0) {
                                sbutSelector += ',';
                            }
                            sbSelector += '.ss360-searchbox';
                            sbutSelector += '.ss360-searchbutton';
                            ss360Config.searchBox.selector = sbSelector;
                            ss360Config.searchBox.searchButton = sbutSelector;
                            if(document.querySelector('.ss360-search-results-block') !== null) { // always prefer the content block defined via shortcode
                                ss360Config.results = ss360Config.results || {};
                                ss360Config.results.embedConfig = ss360Config.results.embedConfig || {};
                                var contentBlockSelector = ss360Config.results.embedConfig.contentBlock || '';
                                if(contentBlockSelector.indexOf('.ss360-search-results-block') === -1) {
                                    if(contentBlockSelector.trim().length > 0) {
                                        contentBlockSelector += ',';
                                    }
                                    contentBlockSelector += '.ss360-search-results-block';
                                    ss360Config.results.embedConfig.contentBlock = contentBlockSelector;
                                }
                            }
                        })();
                    </script>
                    <script type="text/javascript">
                        if (ss360Config.callbacks === undefined) {
                            ss360Config.callbacks = {};
                        }
                        ss360Config.callbacks.preSearch = function (selectedText) {
                            if (selectedText === undefined || selectedText.length === 0) {
                                return false;
                            }
                            return true;
                        }
                        <?php 
                        $ss360_callbacks = get_option('ss360_callbacks');
                        if($ss360_callbacks == NULL) {
                            $ss360_callbacks = array();
                        } else {
                            $ss360_callbacks = json_decode($ss360_callbacks, true);
                        }
                    ?>
                    <?php if(isset($ss360_callbacks['init']) && $ss360_callbacks['init'] != ''){ ?>
                        ss360Config.callbacks.init = function(){<?php echo $ss360_callbacks['init']; ?>};
                    <?php } ?>
                    <?php if(isset($ss360_callbacks['preSuggest']) && $ss360_callbacks['preSuggest'] != ''){ ?>
                        ss360Config.callbacks.preSuggest = function(query, searchBox){<?php echo $ss360_callbacks['preSuggest']; ?>};
                    <?php } ?> 
                    <?php if($type == 'full' && isset($ss360_callbacks['preSearch']) && $ss360_callbacks['preSearch'] != ''){ ?>
                        ss360Config.callbacks.preSearch = function(query, sorting, searchBox){if(searchBox!==undefined){searchBox=searchBox.get()[0]};<?php echo $ss360_callbacks['preSearch']; ?>};
                    <?php } ?>
                    <?php if($type == 'full' && isset($ss360_callbacks['postSearch']) && $ss360_callbacks['postSearch'] != ''){ ?>
                        ss360Config.callbacks.postSearch = function(data){<?php echo $ss360_callbacks['postSearch']; ?>};
                    <?php } ?>   
					<?php if($type == 'full' && isset($ss360_callbacks['navigationClick']) && $ss360_callbacks['navigationClick'] != ''){ ?>
                        ss360Config.callbacks.navigationClick = function(contentGroup){<?php echo $ss360_callbacks['navigationClick']; ?>};
                    <?php } ?>					
					<?php if($type == 'full' && isset($ss360_callbacks['preRender']) && $ss360_callbacks['preRender'] != ''){ ?>
                        ss360Config.callbacks.preRender = function(suggests, result){<?php echo $ss360_callbacks['preRender']; ?>};
                    <?php } ?>

                    </script>
                    <?php if ($type == 'suggestions') { ?>
                        <script type="text/javascript">
                            ss360Config.callbacks.preSearch = function (selectedText, sort, selectedSearchBox) {
                                var form = selectedSearchBox !== undefined ? selectedSearchBox.parents("form")[0] : undefined;
                                if(form !== undefined) {
                                    form.submit();
                                } else {
                                    var redirectBase = '<?php echo esc_url(home_url('/')); ?>';
                                    window.location.href = redirectBase + "?s="+encodeURIComponent(selectedText);
                                }
                                return false;
                            }
                            ss360Config.searchBox.preventFormParentSubmit = false;
                        </script>
                    <?php } ?>
                    <script src="https://cdn.sitesearch360.com/v13/sitesearch360-v13.min.js" async></script>
                <?php } ?>
                <?php
            }
        }
        if(get_option('ss360_account_created')){
            add_action('wp_footer', 'ss360Config');
        }
    }
}

register_activation_hook(__FILE__, array('SiteSearch360Plugin', 'on_activate'));
register_deactivation_hook(__FILE__, array('SiteSearch360Plugin', 'on_deactivate'));