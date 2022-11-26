<?php 
    $ss360_plugin = new SiteSearch360Plugin();
    $ss360_acf_exists = $ss360_plugin->usesACFs();
    $ss360_acf_groups = function_exists('acf_get_field_groups') ? acf_get_field_groups() : array();
    $ss360_acf_def = get_option('ss360_acf_def');
	$ss360_updated_flag = false;
    if($ss360_acf_def == null) {
        $ss360_acf_def = array('images' => array(), 'snippets' => array(), 'texts' => array(), 'titles' => array());
    }

    if (!empty($_POST) && isset($_POST['_wpnonce'])) {
        $ss360_acf_def = array('images' => array(), 'snippets' => array(), 'texts' => array(), 'titles' => array()); // reset the settings
        foreach($_POST  as $ss360_post_key => $ss360_post_value) {
            if(strpos($ss360_post_key, 'ss360acf_') === 0) {
                $ss360_parts = explode('_', $ss360_post_key);
                $ss360_acf_id = $ss360_parts[1];
                $ss360_acf_type = $ss360_parts[2];
                $ss360_target_arr = $ss360_acf_def[$ss360_acf_type];
                if($ss360_target_arr == null) {
                    $ss360_target_arr = array();
                }
                $ss360_target_arr[] = $ss360_acf_id;
                $ss360_acf_def[$ss360_acf_type] = $ss360_target_arr;
            }
        }
        update_option('ss360_acf_def', $ss360_acf_def);
        $ss360_updated_flag = true;
    }

    $ss360_custom_field_groups = array();
    foreach($ss360_acf_groups as $ss360_acf_group) {
        if(function_exists('acf_get_fields')) {
            $ss360_field_group_key = isset($ss360_acf_group['key']) ? $ss360_acf_group['key'] : $ss360_acf_group['ID'];
            $ss360_group_fields = acf_get_fields($ss360_field_group_key);
            $ss360_group_filtered = array();
            foreach($ss360_group_fields as $ss360_group) {
                if($ss360_group['type'] == 'text' || $ss360_group['type'] == 'image' || $ss360_group['type'] == 'wysiwyg') {
                    $ss360_group_filtered[] = $ss360_group;
                }
            }
            if(sizeof($ss360_group_filtered) > 0) {
                $ss360_acf_group['fields'] = $ss360_group_filtered;
                $ss360_custom_field_groups[] = $ss360_acf_group;
            }
        }
    }
?>

<section id="ss360" class="wrap wrap--blocky flex flex--column flex--center">
    <?php 
        if($ss360_updated_flag){ ?>
            <section class="wrapper wrapper--narrow bg-g message">
                <div class="block block--first flex">
                    <span><?php esc_html_e('The configuration has been saved. A re-index might be necessary for the changes to be applied.', 'site-search-360'); ?></span>
                    <button class="button button--close message__close" aria-label="<?php esc_html_e('Close', 'site-search-360'); ?>">&times;</button>
                </div>
            </section>
       <?php }
    ?>
    <?php if($ss360_acf_exists) { ?>
    <section class="wrapper wrapper--narrow">
        <form class="block block--first"  method="post" name="ss360_edit_acfs" action="<?php esc_url($_SERVER['REQUEST_URI'])?>">
            <h2><?php esc_html_e('Custom Fields Indexing', 'site-search-360') ?></h2>
            <p class="m-v-1"><?php esc_html_e('Here you can select which custom fields should be indexed.','site-search-360')?></p>
            <?php wp_nonce_field(); ?>
            <?php foreach($ss360_custom_field_groups as $ss360_cf_group) { ?>
                <h3 class="m-b-0 c-b"><?php echo $ss360_cf_group['title']; ?></h3>
                <table class="configuration">
                    <tbody>
                        <?php foreach($ss360_cf_group['fields'] as $ss360_cf) {
                            $ss360_field_id = str_replace('_', 'xxx', $ss360_cf['key'])
                            ?>
                            <tr data-slug="<?php echo $ss360_cf['name'] ?>">
                                <td style="width:200px;"><?php echo $ss360_cf['label'] ?><br/><em>(<?php echo $ss360_cf['type'] ?>)</em></td>
                                <td>
                                    <?php if($ss360_cf['type'] == 'image') { ?>
                                        <label class="checkbox">
                                            <?php esc_html_e('featured image', 'site-search-360') ?>
                                            <input class="fake-hide" type="checkbox" id="<?php echo $ss360_field_id ?>_image" name="ss360acf_<?php echo $ss360_field_id ?>_images" <?php echo in_array($ss360_field_id, $ss360_acf_def['images']) ? 'checked' : '' ?>/>
                                            <span class="checkbox_checkmark"></span>
                                        </label> 
                                    <?php } else { ?>
                                        <label class="checkbox">
                                            <?php esc_html_e('add to content', 'site-search-360') ?>
                                            <input class="fake-hide" type="checkbox" id="<?php echo $ss360_field_id ?>_content" name="ss360acf_<?php echo $ss360_field_id ?>_texts" <?php echo in_array($ss360_field_id, $ss360_acf_def['texts']) ? 'checked' : '' ?>/>
                                            <span class="checkbox_checkmark"></span>
                                        </label>  
                                        <label class="checkbox">
                                            <?php esc_html_e('search result snippet', 'site-search-360') ?>
                                            <input class="fake-hide" type="checkbox" id="<?php echo $ss360_field_id ?>_snippet" name="ss360acf_<?php echo $ss360_field_id ?>_snippets" <?php echo in_array($ss360_field_id, $ss360_acf_def['snippets']) ? 'checked' : '' ?>/>
                                            <span class="checkbox_checkmark"></span>
                                        </label>  
                                        <label class="checkbox">
                                            <?php esc_html_e('title', 'site-search-360') ?>
                                            <input class="fake-hide" type="checkbox" id="<?php echo $ss360_field_id ?>_title" name="ss360acf_<?php echo $ss360_field_id ?>_titles" <?php echo in_array($ss360_field_id, $ss360_acf_def['titles']) ? 'checked' : '' ?>/>
                                            <span class="checkbox_checkmark"></span>
                                        </label>
                                    <?php } ?> 
                                </td>
                            </tr>
                        <?php } ?>  
                    </tbody>
                </table>
            <?php }
            ?>

            <div class="flex flex--center w-100 m-t-1">
                <button id="submit-btn" class="button button--padded" type="submit"><?php esc_html_e('Save', 'site-search-360'); ?></button>
            </div>
        </form>    
    </section>
    <?php } ?>
</section>

<script type="text/javascript">
    (function(){
        jQuery(".message__close").on("click", function(e){
            jQuery(e.target).parents(".message").fadeOut();
        });
    })();
</script>

<script src="<?php echo plugins_url('assets/sitesearch360_admin_scripts.js',  __FILE__)  ?>" async></script>
