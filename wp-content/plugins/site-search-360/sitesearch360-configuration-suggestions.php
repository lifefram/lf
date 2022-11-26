<?php 
    $ss360_plugin = new SiteSearch360Plugin();
    $ss360_config = $ss360_plugin->getConfig();
    $ss360_updated_flag = false;
    
    function updateConfigValue($ss360_configuration, $ss360_path1, $ss360_path2, $ss360_default, $ss360_case_insensitive){
        $ss360_post_key = $ss360_path1.'_'.$ss360_path2;
        $ss360_val = stripslashes($_POST[$ss360_post_key]);
        if(isset($_POST[$ss360_post_key]) && (($ss360_case_insensitive && strtolower($ss360_val)!=strtolower($ss360_default)) || ($ss360_val!=$ss360_default))){
            $ss360_configuration[$ss360_path1][$ss360_path2] = $ss360_val;
        }else {
            unset($ss360_configuration[$ss360_path1][$ss360_path2]);
        }
        return $ss360_configuration;
    }

    function updateConfigValueInt($ss360_configuration, $ss360_path1, $ss360_path2, $ss360_default){
        $ss360_post_key = $ss360_path1.'_'.$ss360_path2;
        if(isset($_POST[$ss360_post_key]) && intval($_POST[$ss360_post_key])!=$ss360_default){
            $ss360_configuration[$ss360_path1][$ss360_path2] = intval($_POST[$ss360_post_key]);
        }else {
            unset($ss360_configuration[$ss360_path1][$ss360_path2]);
        }
        return $ss360_configuration;
    }

    function updateConfigValueBool($ss360_configuration, $ss360_path1, $ss360_path2, $ss360_default){
        $ss360_post_key = $ss360_path1.'_'.$ss360_path2;
        $ss360_val = isset($_POST[$ss360_post_key]) && $_POST[$ss360_post_key]=='on';
        if($ss360_val == $ss360_default){
            unset($ss360_configuration[$ss360_path1][$ss360_path2]);
        }else {
            $ss360_configuration[$ss360_path1][$ss360_path2] = $ss360_val ? true : false;
        }
        return $ss360_configuration;
    }

    if (!empty($_POST) && isset($_POST['_wpnonce'])) {
        if(!isset($ss360_config['suggestions'])){
            $ss360_config['suggestions'] = array();
        }
        if(!isset($ss360_config['specialMobileSuggest'])){
            $ss360_config['specialMobileSuggest'] = array();
        }

        $ss360_config = updateConfigValueBool($ss360_config, 'suggestions', 'show', true);
        $ss360_config = updateConfigValueInt($ss360_config, 'suggestions', 'minChars', 3);
        $ss360_config = updateConfigValueInt($ss360_config, 'suggestions', 'num', 6);
        $ss360_config = updateConfigValueBool($ss360_config, 'suggestions', 'showImages', true);
        $ss360_config = updateConfigValue($ss360_config, 'suggestions', 'viewAllLabel', '', false);
        $ss360_config = updateConfigValueBool($ss360_config, 'specialMobileSuggest', 'enabled', false);
        
        if(empty($ss360_config['suggestions'])){
            unset($ss360_config['suggestions']);
        }
        if(empty($ss360_config['specialMobileSuggest'])){
            unset($ss360_config['specialMobileSuggest']);
        }
        
        $ss360_plugin->saveConfig($ss360_config);
        $ss360_updated_flag = true;
        update_option('ss360_config_modifications', ((int) get_option('ss360_config_modifications')) + 1);
    }

    if(!isset($ss360_config['suggestions'])){
        $ss360_config['suggestions'] = array();
    }
    if(!isset($ss360_config['specialMobileSuggest'])){
        $ss360_config['specialMobileSuggest'] = array();
    }

    $ss360_suggest_config = $ss360_config['suggestions'];
    $ss360_show = isset($ss360_suggest_config['show']) ? $ss360_suggest_config['show'] : true;
    $ss360_minChars = isset($ss360_suggest_config['minChars']) ? $ss360_suggest_config['minChars'] : 3;
    $ss360_num = isset($ss360_suggest_config['num']) ? $ss360_suggest_config['num'] : 6;
    $ss360_showImages = isset($ss360_suggest_config['showImages']) ? $ss360_suggest_config['showImages'] : true;
    $ss360_viewAllLabel = isset($ss360_suggest_config['viewAllLabel']) ? $ss360_suggest_config['viewAllLabel'] : '';
    $speciaMobileSuggest_enabled = isset($ss360_config['specialMobileSuggest']['enabled']) ? $ss360_config['specialMobileSuggest']['enabled'] : false;
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
        <form class="block block--first" method="post" name="ss360_basic_config" action="<?php esc_url($_SERVER['REQUEST_URI'])?>">
            <?php wp_nonce_field(); ?>
            <h2><?php esc_html_e('Search Suggestion Configuration', 'site-search-360'); ?></h2>
            <section>
                <table class="configuration">
                    <tbody>
                        <tr>
                            <td><strong><label for="show"><?php esc_html_e('Show Suggestions', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="show" name="suggestions.show" <?php echo $ss360_show ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show search suggestions.', 'site-search-360') ?></td>
                        </tr>
                        <tr class="if-active" <?php echo $ss360_show ? '' : 'style="display:none;"'?>>
                            <td><strong><label for="minChars"><?php esc_html_e('Min. Characters', 'site-search-360'); ?></label></strong></td>
                            <td><input class="input input--inline" type="number" name="suggestions.minChars" id="minChars" min="1" max="10" value="<?php echo $ss360_minChars;?>" placeholder="3"></td>
                            <td><?php esc_html_e('The minimum number of characters before the suggestions are shown.', 'site-search-360') ?></td>
                        </tr>
                        <tr class="if-active" <?php echo $ss360_show ? '' : 'style="display:none;"'?>>
                            <td><strong><label for="num"><?php esc_html_e('Number of Suggestions', 'site-search-360'); ?></label></strong></td>
                            <td><input class="input input--inline" type="number" name="suggestions.num" id="num" min="1" max="20" value="<?php echo $ss360_num;?>" placeholder="6"></td>
                            <td><?php esc_html_e('The maximum number of search suggestions to be shown.', 'site-search-360') ?></td>
                        </tr>
                        <tr class="if-active" <?php echo $ss360_show ? '' : 'style="display:none;"'?>>
                            <td><strong><label for="showImages"><?php esc_html_e('Show Images', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="showImages" name="suggestions.showImages" <?php echo $ss360_showImages ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show images in search suggestions.', 'site-search-360') ?></td>
                        </tr>
                        <tr class="if-active" <?php echo $ss360_show ? '' : 'style="display:none;"'?>>
                            <td><strong><label for="viewAllLabel"><?php esc_html_e('Search Button Text', 'site-search-360'); ?></label></strong></td>
                            <td><input class="input input--inline" type="text" name="suggestions.viewAllLabel" id="viewAllLabel" value="<?php echo htmlspecialchars($ss360_viewAllLabel);?>" placeholder="<?php esc_html_e('Show All Results', 'site-search-360'); ?>"></td>
                            <td><?php esc_html_e('The text of the search button displayed at the bottom of the search suggestion block (leave blank if you don\'t want to show a search button).', 'site-search-360') ?></td>
                        </tr>
                        <tr class="if-active" <?php echo $ss360_show ? '' : 'style="display:none;"'?>>
                            <td><strong><label for="specialMobileSuggest-enabled"><?php esc_html_e('Special Mobile Suggestions', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show as layover', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="specialMobileSuggest-enabled" name="specialMobileSuggest.enabled" <?php echo $speciaMobileSuggest_enabled ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show mobile search suggestions in a layover.', 'site-search-360') ?></td>
                        </tr>
                    </tbody>
                </table>
            </section>
            <div class="flex flex--center w-100 m-t-1">
                <button class="button button--padded" type="submit"><?php esc_html_e('Save', 'site-search-360'); ?></button>
            </div>
        </section>
    </div>
</section>


<script type="text/javascript">
(function(){
    var toToggle = jQuery(".if-active");
    jQuery("#show").on("change", function(e){
        if(e.target.checked){
            toToggle.show();
        }else {
            toToggle.hide();
        }
    });
    jQuery(".message__close").on("click", function(e){
        jQuery(e.target).parents(".message").fadeOut();
    });
}());

</script>
<script src="<?php echo plugins_url('assets/sitesearch360_admin_scripts.js',  __FILE__)  ?>" async></script>