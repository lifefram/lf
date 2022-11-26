<?php 
    class SiteSearch360Indexer {
        
        private $content_type = NULL;
        private $offset = NULL;
        private $count = NULL;
        private $client = NULL;
        private $category_filter_id = NULL;
        private $tag_filter_id = NULL;
        private $defined_data_points = NULL;
        private $data_point_names = NULL;
        private $data_points_inactive = NULL;
        private $acfs = NULL;

        public function __construct($content_type, $offset, $count) {
            $this->content_type = $content_type;
            $this->offset = $offset;
            $this->count = $count;
            $this->client = new SiteSearch360Client();
            $this->category_filter_id = get_option("ss360_category_filter_id");
            $this->tag_filter_id = get_option("ss360_tag_filter_id"); 
            if($this->category_filter_id==NULL){
                $this->category_filter_id = $this->client->createFilter(esc_html__('Category','site-search-360'), 'COLLECTION', 'OR');
                update_option("ss360_category_filter_id", $this->category_filter_id);
            }
            if($this->tag_filter_id==NULL){
                $this->tag_filter_id = $this->client->createFilter(esc_html__('Tag','site-search-360'), 'COLLECTION', 'OR');
                update_option("ss360_tag_filter_id", $this->tag_filter_id);
            }
            $this->defined_data_points = get_option('ss360_data_points');
            $this->data_point_names = get_option('ss360_renamed_dp');
            $this->data_points_inactive = get_option('ss360_inactive_dp');
            $this->acfs = get_option('ss360_acf_def');
            if($this->defined_data_points==null){
                $this->defined_data_points = array();
            }
            if($this->data_point_names==null){
                $this->data_point_names = array();
            }
            if($this->data_points_inactive==null){
                $this->data_points_inactive = array();
            }
            if($this->acfs==null){
                $this->acfs = array('images' => array(), 'snippets' => array(), 'texts' => array(), 'titles' => array());
            }
            $ss360_indexing_mode = get_option('ss360_indexing_mode');
            if($ss360_indexing_mode=='db' || $ss360_indexing_mode==FALSE || $ss360_indexing_mode==NULL){
                $date_key = esc_html__('Date', 'site-search-360');
                if(isset($this->data_point_names[$date_key])){
                    $this->client->ensureSortDataPointExists($this->data_point_names[$date_key]);
				}
				$all_categories = get_categories();
				if (get_option('ss360_woocommerce_categories')) {
					$all_woocommerce_categories = get_terms('product_cat');
					foreach($all_woocommerce_categories as $woo_cat) {
						$all_categories[] = $woo_cat;
					}
				}
                $this->client->ensureContentGroupsExist($all_categories);
            }
            
        }    

        public function index(){
            $posts = get_posts(array(
                'posts_per_page' => $this->count,
                'offset' => $this->offset,
                'orderby' => 'date',
                'order' => 'ASC',
                'post_type' => $this->content_type,
                'post_status' => 'publish'
            ));
            $pages = array();
            foreach($posts as $post){
                $pages[] = $this->preparePage($post);
            }
            
           return $this->client->bulkIndex($pages);
        }

        public function indexSingle($postId){
            $ss360_indexing_mode = get_option('ss360_indexing_mode');
            if($ss360_indexing_mode==null || $ss360_indexing_mode==FALSE){
                $ss360_indexing_mode = 'db';
            }
            $post = get_post($postId);
            if($ss360_indexing_mode == 'db'){
                $this->content_type = $post->post_type;
                $ss360_plugin = new SiteSearch360Plugin();
                $ss360_post_types = $ss360_plugin->getPostTypes();
                if($ss360_post_types!=NULL && $post->post_type !== NULL && array_search($post->post_type, $ss360_post_types)!==FALSE){
                    $page = $this->preparePage($post);
                    $this->client->indexPage($page);
                }
            } else {
                $this->client->notifyCrawler(get_permalink($post));
            }
        }

        private function isDataPointActive($key){
            return !in_array($key, $this->data_points_inactive);
        }

        private function preparePage($post){
            $page = array();
            $page['title'] = $post->post_title;

            $excerpt = $post->post_excerpt;
            $content = $post->post_content;

            if(function_exists('get_field')) {
                foreach($this->acfs['texts'] as $text_field_id) {
                    $ss360_field = get_field(str_replace('xxx','_',$text_field_id), $post->ID);
                    if($ss360_field) {
                        $content = $content . $ss360_field;
                    }
                }  
                foreach($this->acfs['snippets'] as $text_field_id) {
                    $ss360_field = get_field(str_replace('xxx','_' ,$text_field_id), $post->ID);
                    if($ss360_field) {
                       $excerpt = $ss360_field;
                    }
                }  
                foreach($this->acfs['titles'] as $text_field_id) {
                    $ss360_field = get_field(str_replace('xxx','_',$text_field_id), $post->ID);
                    if($ss360_field) {
                        $page['title'] = $ss360_field;
                    }
                }
            }

            $content = strip_shortcodes(wp_filter_nohtml_kses($content));
            $content = preg_replace('/<!--(.|\s)*?-->/', '', $content); // some comments like wp-paragraph are still being indexed
            $content = preg_replace("~(?:\[/?)[^\]]+/?\]~s", '', $content);
            $publish_date = $post->post_date;
			$categories = get_the_category($post->ID);

			if ($this->content_type == 'product' && get_option('ss360_woocommerce_categories')) {
				$woocommerce_categories = get_the_terms($post->ID, 'product_cat');
				foreach($woocommerce_categories as $woocommerce_category) {
					$categories[] = $woocommerce_category;
				}
			}

            $author = get_the_author_meta('display_name', $post->post_author);
            $tags = wp_get_post_tags($post->ID);

            // basic information
            $page['url'] = get_permalink($post);
            $page['snippet'] = $excerpt ? strip_shortcodes(wp_filter_nohtml_kses($excerpt)) : substr($content, 0, 350);
            $page['content'] = $content;
            
            $imageUrl = get_the_post_thumbnail_url($post);
            if(function_exists('get_field')) {
                foreach($this->acfs['images'] as $image_field_id) {
                    $ss360_field = get_field(str_replace('xxx', '_', $image_field_id), $post->ID);
                    if($ss360_field && isset($ss360_field['sizes']) && isset($ss360_field['sizes']['thumbnail'])) { 
                        $imageUrl = $ss360_field['sizes']['thumbnail'];
                    } else {
                        $ss360_image_data = wp_get_attachment_image_src($ss360_field, 'thumbnail');
                        if($ss360_image_data !== FALSE) {
                            $imageUrl = $ss360_image_data[0];
                        }
                    }
                }
            }


            if($imageUrl!==FALSE){
                $page['imageUrl'] = $imageUrl;
            }

            // data points and content group
            $structuredData = array();
            $author_key = esc_html__('Author', 'site-search-360');
            $publish_date_key = esc_html__('Publication Date', 'site-search-360');
            $date_key = esc_html__('Date', 'site-search-360');
            
            $dp_needs_update = false;
            if(!in_array($author_key, $this->defined_data_points)){
                $this->defined_data_points[] = $author_key;
                $dp_needs_update = true;
            }
            if(!in_array($publish_date_key, $this->defined_data_points)){
                $this->defined_data_points[] = $publish_date_key;
                $dp_needs_update = true;
            }
            if(!in_array($date_key, $this->defined_data_points)){
                $this->defined_data_points[] = $date_key;
                $dp_needs_update = true;
            }

            if($this->isDataPointActive($author_key)){
                $structuredData[] = $this->createDataPoint($author_key, $author);
            }
            if($this->isDataPointActive($publish_date_key)){ 
                $structuredData[] = $this->createDataPoint($publish_date_key, $publish_date);
            }
            if($this->isDataPointActive($date_key)){//TODO: make sure global sort data point exists
                $structuredData[] = $this->createDataPoint($date_key, strtotime($publish_date), false, true);
            }
            $page['contentGroup'] = $this->getTopCategory($categories);

            $imageTextsString = '';			
            $attached_images = get_attached_media('image', $post->ID);
            if($attached_images) {
                foreach($attached_images as $attached_image) {
					$image_alt = get_post_meta($attached_image->ID, '_wp_attachment_image_alt', TRUE);
					if($image_alt != NULL && $image_alt != '') {
						if($imageTextsString != '') {
							$imageTextsString = $imageTextsString . ', ';
						}
						$imageTextsString = $imageTextsString . $image_alt;
					}
                }
            }
            $structuredData[] = $this->createDataPoint('Image Texts', $imageTextsString, false);
            
            // filters
            $filters = array();
            $tag_filter_values = array();
            $category_filter_values = array();
            foreach($tags as $tag){
                $tag_filter_values[] = $tag->name;
			}
            foreach($categories as $category){
                $category_filter_values[] = $category->name;
                $structuredData[] = $this->createDataPoint('Category', $category->name, false);
            }
            $filters[] = $this->createFilter($this->tag_filter_id, $tag_filter_values);
            $filters[] = $this->createFilter($this->category_filter_id, $category_filter_values);
            foreach($tag_filter_values as $tag_name) {
                $structuredData[] = $this->createDataPoint('Tags', $tag_name, false);
            }

            if($this->content_type == 'product'){ 
                $product_price = get_post_meta($post->ID, '_price');
                if(sizeof($product_price) > 0){ // Price filter
                    $price_filter_id = get_option("ss360_price_filter_id");
                    if($price_filter_id==null){
                        $price_filter_id = $this->client->createFilter(esc_html__('Price','site-search-360'), 'SINGLE_NUMERIC', '');
                        update_option("ss360_price_filter_id", $price_filter_id);
                    }
                    $filters[] = $this->createFilter($price_filter_id, $product_price[0]);
                    $price_key = esc_html__('Price', 'site-search-360');
                    if($this->isDataPointActive($price_key)){
                        $structuredData[] = $this->createDataPoint($price_key, $product_price[0]);
                    }
                    if(!in_array($price_key, $this->defined_data_points)){
                        $this->defined_data_points[] = $price_key;
                        $dp_needs_update = true;
                    }
				}
				
				$woocommerce_filters = get_option('ss360_woocommerce_filters');
				if ($woocommerce_filters != NULL && sizeof($woocommerce_filters) > 0) {
					$product_meta = get_post_meta($post->ID, '_product_attributes');
					if (sizeof($product_meta) > 0) {
						$keys = array_keys($product_meta[0]);
						foreach($keys as $key) {
							$meta_def = $product_meta[0][$key];
							if (in_array($meta_def['name'], $woocommerce_filters)) {
								$filter_db_key = 'ss360_' . $meta_def['name'] . '_customfilter_id';
								$filter_id = get_option($filter_db_key);
								if ($filter_id != NULL) {
									$filter_vals = explode('|', $meta_def['value']);
									$filters[] = $this->createFilter($filter_id, $filter_vals);
								}
							}
						}
					
					}
				}
				
            }

            $ss360_taxonomies = get_taxonomies(array('public'=>true, '_builtin'=>false), 'objects');
            foreach($ss360_taxonomies as $ss360_taxonomy) {
                $ss360_is_taxonomy_visible = $this->isDataPointActive($ss360_taxonomy->name);
                if(!in_array($ss360_taxonomy->name, $this->defined_data_points)){
                    $this->defined_data_points[] = $ss360_taxonomy->name;
                    $this->data_point_names[$ss360_taxonomy->name] = $ss360_taxonomy->labels->singular_name;
                    update_option('ss360_renamed_dp', $this->data_point_names);
                    $dp_needs_update = true;
                }
               $ss360_post_taxonomies = wp_get_post_terms($post->ID, $ss360_taxonomy->name);
               foreach($ss360_post_taxonomies as $ss360_post_taxonomy_value) {
                   $structuredData[] = $this->createDataPoint($ss360_taxonomy->name, $ss360_post_taxonomy_value->name, $ss360_is_taxonomy_visible);
               }
            }

            if($dp_needs_update){
                update_option('ss360_data_points', $this->defined_data_points);
            }

            $page['structuredData'] = $structuredData; 
            $page['filters'] = $filters;

            return $page;
        }

        private function getTopCategory($categories){
			$category_by_parent = array();
            foreach($categories as $category){ // try to find top level category
                $parentCategoryId = $category->category_parent;
                if($parentCategoryId==0 && strlen($category->name) > 0){
                    return $category->name;
                }
                $categoryId = $category->cat_ID;
                $category_by_parent[$parentCategoryId] = $categoryId; //TODO: might be an array (for future reference, but right now sufficient)
            }
            foreach($categories as $category){ // try to find category which does not have any parent among the categories
                if(!isset($category_by_parent[$category->category_parent]) && strlen($category->name) > 0){
                    return $category->name;
                }
            }
            return "_";
        }

        private function createFilter($key, $values){
            $filter = array();
            $filter['key'] = $key;
            $filter['value'] = $values;
            return $filter;
        }

        private function createDataPoint($key, $value, $show=true, $sort=false){
            $dataPoint = array();
            $dataPoint['key'] = isset($this->data_point_names[$key]) && $this->data_point_names[$key]!=null ? $this->data_point_names[$key] : $key;
            $dataPoint['show'] = $show;
            $dataPoint['value'] = $value;
            $dataPoint['boost'] = false;
            $dataPoint['sort'] = $sort;
            $dataPoint['xpath'] = '//noxpath';
            if($sort){
                $dataPoint['single'] = true;
            }
            return $dataPoint;
        }
    }
?>