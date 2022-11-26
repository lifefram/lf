<?php 
	$ss360_plugin = new SiteSearch360Plugin();
	$ss360_client = new SiteSearch360Client();
	$ss360_type = $ss360_plugin->getType();
	$ss360_updated_flag = false;

	if (!empty($_POST) && isset($_POST['_wpnonce'])) {
		$ss360_updated_flag = true;

        if(isset($_POST['indexCategories']) && $_POST['indexCategories'] == 'on'){
            update_option('ss360_woocommerce_categories', true);
        } else {
			update_option('ss360_woocommerce_categories', false);
		}

		$ss360_index_filters = array();
		$ss360_req_keys = array_keys($_POST);
		foreach($ss360_req_keys as $ss360_req_key) {
			if ((substr($ss360_req_key, 0, 3)) == 'wf_') {
				$ss360_filter_name =  str_replace('__', ' ', substr($ss360_req_key, 3));;
				$ss360_index_filters[] = $ss360_filter_name;
				$ss360_filter_db_key = "ss360_".$ss360_filter_name."_customfilter_id";
				if (get_option($ss360_filter_db_key) == NULL) {
					$filter_id =  $ss360_client->createFilter($ss360_filter_name, 'COLLECTION', 'OR');
					update_option($ss360_filter_db_key, $filter_id);
				}
			}
		}
		update_option('ss360_woocommerce_filters', $ss360_index_filters);
	}
	
	$ss360_woocommerce_attributes = array();
	$ss360_posts = get_posts(array(
		'posts_per_page' => -1,
		'orderby' => 'date',
		'order' => 'ASC',
		'post_type' => 'product',
		'post_status' => 'publish'
	));

	foreach($ss360_posts as $ss360_post) {
		$ss360_product_meta = get_post_meta($ss360_post->ID, '_product_attributes');
		if (sizeof($ss360_product_meta) > 0) {
			$ss360_meta_keys = array_keys($ss360_product_meta[0]);
			foreach($ss360_meta_keys as $ss360_meta_key) {
				$ss360_meta_def = $ss360_product_meta[0][$ss360_meta_key];
				if ($ss360_meta_def['is_visible']) {
					$ss360_woocommerce_attributes[] = $ss360_meta_def['name'];
				}
			}
		}		
	}
	
	$ss360_index_categories = get_option('ss360_woocommerce_categories');
	$ss360_index_filters = get_option('ss360_woocommerce_filters');
	if ($ss360_index_categories == NULL) {
		$ss360_index_categories = FALSE;
	}
	if ($ss360_index_filters == NULL) {
		$ss360_index_filters = array();
	}

	$ss360_arr_idx = 0;
?>

<section id="ss360" class="wrap wrap--blocky flex flex--column flex--center">
    <?php 
        if($ss360_updated_flag){ ?>
            <section class="wrapper wrapper--narrow bg-g message">
                <div class="block block--first flex">
                    <span><?php esc_html_e('The configuration has been saved.', 'site-search-360'); ?></span>
                    <button class="button button--close message__close" aria-label="<?php esc_html_e('Close', 'site-search-360'); ?>">&times;</button>
                </div>
            </section>
       <?php }
    ?>
    <section class="wrapper wrapper--narrow">
        <form class="block block--first"  method="post" name="ss360_woocommerce_config" action="<?php esc_url($_SERVER['REQUEST_URI'])?>">
		<?php wp_nonce_field(); ?>
            <h2><?php esc_html_e('WooCommerce Indexing', 'site-search-360') ?></h2>
			<section>
				<h3 class="m-b-0 c-b"><?php esc_html_e('Indexing', 'site-search-360') ?></h3>
                <table class="configuration">
                    <tbody>
						<tr>
                            <td><strong><label for="indexCategories"><?php esc_html_e('Index Categories', 'site-search-360') ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('index', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="indexCategories" name="indexCategories" <?php echo $ss360_index_categories ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to index WooCommerce categories as content groups and filter values.', 'site-search-360'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </section>
			<?php if(sizeof($ss360_woocommerce_attributes) > 0) { ?>
			<section>
				<h3 class="m-b-0 c-b"><?php esc_html_e('Filters', 'site-search-360') ?></h3>
                <table class="configuration">
                    <tbody>
						<?php foreach($ss360_woocommerce_attributes as $ss360_woocommerce_attribute) { ?>
							<tr>
								<td style="width:285px;"><strong><label for="index_filter_idx_<?php echo $ss360_arr_idx; ?>"><?php esc_html_e($ss360_woocommerce_attribute) ?></label></strong></td>
								<td><label class="checkbox"><?php esc_html_e('index', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="index_filter_idx_<?php echo $ss360_arr_idx; ?>" name="wf_<?php echo str_replace(' ', '__', $ss360_woocommerce_attribute); ?>" <?php echo in_array($ss360_woocommerce_attribute, $ss360_index_filters) ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
							</tr>
						<?php $ss360_arr_idx++; } ?>
                    </tbody>
                </table>
            </section>
			<?php } ?>
			<div class="flex flex--center w-100 m-t-1">
                <button class="button button--padded" type="submit"><?php esc_html_e('Save', 'site-search-360'); ?></button>
            </div>
        </form>    
    </section>
</section>

<script>
(function(){
    jQuery(".message__close").on("click", function(e){
        jQuery(e.target).parents(".message").fadeOut();
    })
}());
</script>
<script src="<?php echo plugins_url('assets/sitesearch360_admin_scripts.js',  __FILE__)  ?>" async></script>