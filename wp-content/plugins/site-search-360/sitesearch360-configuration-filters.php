<?php 
    $ss360_plugin = new SiteSearch360Plugin();
    $ss360_config = $ss360_plugin->getConfig();
    $ss360_client = new SiteSearch360Client();
    $ss360_updated_flag = false;

    if(!isset($ss360_config['filters'])){
        $ss360_config['filters'] = array();
    }

    if(!isset($ss360_config['filters']['settings'])){
        $ss360_config['filters']['settings'] = array();
    }

    $ss360_filters = $ss360_client->loadFilters();
    $ss360_jwt = $ss360_client->presign();
    $ss360_range_filters = array();

    if(isset($ss360_filters['filters'])){
        foreach($ss360_filters['filters'] as $ss360_filter_definition){
            if($ss360_filter_definition['type'] == 'SINGLE_NUMERIC'){
                $ss360_range_filters[] = $ss360_filter_definition;
            }
        }
    }
    
    $ss360_default_filter_position = isset($ss360_config['results']) && isset($ss360_config['results']['embedConfig']) && isset($ss360_config['results']['embedConfig']['contentBlock']) ? 'left' : 'top'; 

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
        if(!isset($ss360_config['filters'])){
            $ss360_config['filters'] = array();
        }

        $ss360_config = updateConfigValueBool($ss360_config, 'filters', 'enabled', false);
        $ss360_config = updateConfigValue($ss360_config, 'filters', 'position', $ss360_default_filter_position, false);
        $ss360_config = updateConfigValue($ss360_config, 'filters', 'label', 'Filter', false);
        $ss360_config = updateConfigValueBool($ss360_config, 'filters', 'showCounts', true);
        $ss360_config = updateConfigValueBool($ss360_config, 'filters', 'showQuickDelete', true);
        $ss360_config = updateConfigValue($ss360_config, 'filters', 'deleteAllLabel', 'Reset All', false);

        foreach($ss360_range_filters as $range_filter){
            $range_filter_id = 'fid#'.$range_filter['id'];
            $ss360_config['filters']['settings'][$range_filter_id] = array();
            $ss360_settings = $ss360_config['filters']['settings'];
            $ss360_settings = updateConfigValue($ss360_settings, $range_filter_id, 'unit', '', false);
            $ss360_settings = updateConfigValue($ss360_settings, $range_filter_id, 'step', '', false);
            if(isset($ss360_settings[$range_filter_id]['step'])){
                $ss360_settings[$range_filter_id]['step'] = intval($ss360_settings[$range_filter_id]['step']);
            }
            $ss360_settings = updateConfigValueBool($ss360_settings, $range_filter_id, 'drawHistogram', true);
            if(empty($ss360_settings[$range_filter_id])){
                unset($ss360_settings[$range_filter_id]);
            }
            $ss360_config['filters']['settings'] = $ss360_settings;
        }

        if(empty($ss360_config['filters']['settings'])){
            unset($ss360_config['filters']['settings']);
        }

        if(empty($ss360_config['filters'])){
            unset($ss360_config['filters']);
        }
        
        $ss360_plugin->saveConfig($ss360_config);
        $ss360_updated_flag = true;
        update_option('ss360_config_modifications', ((int) get_option('ss360_config_modifications')) + 1);
    }
    
    $ss360_range_filter_values = array();
    foreach($ss360_range_filters as $ss360_filter_definition){
        $single_filter_config = isset($ss360_config['filters']['settings']['fid#'.$ss360_filter_definition['id']]) ? $ss360_config['filters']['settings']['fid#'.$ss360_filter_definition['id']] : array();
        $single_filter_config['unit'] = isset($single_filter_config['unit']) ? $single_filter_config['unit'] : '';
        $single_filter_config['step'] = isset($single_filter_config['step']) ? $single_filter_config['step'] : '';
        $single_filter_config['drawHistogram'] = isset($single_filter_config['drawHistogram']) ? $single_filter_config['drawHistogram'] : true;
        $ss360_range_filter_values['fid#'.$ss360_filter_definition['id']] = $single_filter_config;
    }  

    $filter_config = $ss360_config['filters'];
    $ss360_enabled = isset($filter_config['enabled']) ? $filter_config['enabled'] : false;
    $ss360_position = isset($filter_config['position']) ? $filter_config['position'] : $ss360_default_filter_position;
    $ss360_label = isset($filter_config['label']) ? $filter_config['label'] : 'Filter';
    $ss360_showCounts = isset($filter_config['showCounts']) ? $filter_config['showCounts'] : true;
    $ss360_showQuickDelete = isset($filter_config['showQuickDelete']) ? $filter_config['showQuickDelete'] : true;
    $ss360_deleteAllLabel = isset($filter_config['deleteAllLabel']) ? $filter_config['deleteAllLabel'] : 'Reset All';
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
            <h2><?php esc_html_e('Filter Configuration', 'site-search-360'); ?></h2>
            <?php if(isset($ss360_filters['filters']) && sizeof($ss360_filters['filters'])>0){ ?>
                <a class="label cp-link" href="<?php echo $ss360_jwt;?>&next=filter" target="_blank"><?php esc_html_e('The filter names and values can be adjusted in the Site Search 360 control panel.', 'site-search-360') ?></a>
            <?php } ?>
            <section>
                <h3 class="m-b-0 c-b"><?php esc_html_e('General Settings', 'site-search-360') ?></h3>
                <table class="configuration">
                    <tbody>
                        <tr>
                            <td><strong><label for="enabled"><?php esc_html_e('Show Filters', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="enabled" name="filters.enabled" <?php echo $ss360_enabled ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show filters.', 'site-search-360') ?></td>
                        </tr>
                        <tr class="if-active" <?php echo $ss360_enabled ? '' : 'style="display:none;"'?>>
                            <td><strong><?php esc_html_e('Position', 'site-search-360'); ?></strong></td>
                            <td>
                                <div class="flex">
                                    <label class="radio m-r-1">
                                        <?php esc_html_e('left', 'site-search-360'); ?>
                                        <input type="radio" name="filters.position" <?php echo $ss360_position == 'left' ? 'checked' : '' ?> value="left" class="fake-hide">
                                        <span class="radio_checkmark"></span>
                                    </label>
                                    <label class="radio">
                                        <?php esc_html_e('top', 'site-search-360'); ?>
                                        <input type="radio" name="filters.position" <?php echo $ss360_position == 'top' ? 'checked' : '' ?> value="top" class="fake-hide">
                                        <span class="radio_checkmark"></span>
                                    </label>
                                </div>
                            </td>
                            <td><?php esc_html_e('The position of the filter options - to the left of search results or above them.', 'site-search-360') ?></td>
                        </tr>
                        <tr class="if-active" <?php echo $ss360_enabled ? '' : 'style="display:none;"'?>>
                            <td><strong><label for="label"><?php esc_html_e('Label', 'site-search-360'); ?></label></strong></td>
                            <td><input class="input input--inline" type="text" name="filters.label" id="label" value="<?php echo htmlspecialchars($ss360_label);?>" placeholder="<?php esc_html_e('Filters', 'site-search-360'); ?>"></td>
                            <td><?php esc_html_e('The label of the filter block, used as screen reader text and on mobile devices..', 'site-search-360') ?></td>
                        </tr>
                        <tr class="if-active" <?php echo $ss360_enabled ? '' : 'style="display:none;"'?>>
                            <td><strong><label for="showCounts"><?php esc_html_e('Show Option Counts', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="showCounts" name="filters.showCounts" <?php echo $ss360_showCounts ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show the number of options for every filter option.', 'site-search-360') ?></td>
                        </tr>
                        <tr class="if-active" <?php echo $ss360_enabled ? '' : 'style="display:none;"'?>>
                            <td><strong><label for="showQuickDelete"><?php esc_html_e('Show Quick Delete Bar', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="showQuickDelete" name="filters.showQuickDelete" <?php echo $ss360_showQuickDelete ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show a bar above the search results which can be used to quickly unselect a selected filter option.', 'site-search-360') ?></td>
                        </tr>
                        <tr id="delete-all-label-row" class="if-active" <?php echo $ss360_enabled && $ss360_showQuickDelete ? '' : 'style="display:none;"'?>>
                            <td><strong><label for="deleteAllLabel"><?php esc_html_e('Delete All Label', 'site-search-360'); ?></label></strong></td>
                            <td><input class="input input--inline" type="text" name="filters.deleteAllLabel" id="deleteAllLabel" value="<?php echo htmlspecialchars($ss360_deleteAllLabel);?>" placeholder="<?php esc_html_e('Clear All', 'site-search-360'); ?>"></td>
                            <td><?php esc_html_e('The label of the "Delete All" option - unselects all selected filter options.', 'site-search-360') ?></td>
                        </tr>
                    </tbody>
                </table>
            </section>
            <?php foreach($ss360_range_filters as $range_filter){ ?>
                <hr/>
                <section>
                    <h3 class="m-b-0 c-b">"<?php esc_html_e($range_filter['name']) ?>" <?php esc_html_e('Range Filter Configuration', 'site-search-360'); ?></h3>
                    <table class="configuration">
                        <tbody>
                            <tr>
                                <td><strong><label for="unit_fid#<?php esc_html_e($range_filter['id']); ?>"><?php esc_html_e('Unit', 'site-search-360');?></label></strong></td>
                                <td><input placeholder="$" style="width:75px;" class="input input--inline" type="text" name="fid#<?php esc_html_e($range_filter['id'])?>.unit" id="unit_fid#<?php esc_html_e($range_filter['id']); ?>" value="<?php htmlspecialchars(esc_html_e($ss360_range_filter_values['fid#'.$range_filter['id']]['unit']));?>"></td>
                                <td><?php esc_html_e('The unit to be shown for this range filter.', 'site-search-360'); ?></td>
                            </tr>
                            <tr>
                                <td><strong><label for="step_fid#<?php esc_html_e($range_filter['id']); ?>"><?php esc_html_e('Step', 'site-search-360');?></label></strong></td>
                                <td><input placeholder="1" style="width:75px;" type="number" min="0" step="1" class="input input--inline" type="text" name="fid#<?php esc_html_e($range_filter['id'])?>.step" id="step_fid#<?php esc_html_e($range_filter['id']); ?>" value="<?php echo $ss360_range_filter_values['fid#'.$range_filter['id']]['step'];?>"></td>
                                <td><?php esc_html_e('The step size of this range filter.', 'site-search-360'); ?></td>
                            </tr>
                            <tr>
                                <td><strong><label for="histogram_fid#<?php esc_html_e($range_filter['id']); ?>"><?php esc_html_e('Show Histogram', 'site-search-360');?></label></strong></td>
                                <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="histogram_fid#<?php esc_html_e($range_filter['id']); ?>" name="fid#<?php esc_html_e($range_filter['id'])?>.drawHistogram" <?php echo $ss360_range_filter_values['fid#'.$range_filter['id']]['drawHistogram'] ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                                <td><?php esc_html_e('Whether to show a histogram of all available filter values for this range filter.', 'site-search-360'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </section>  
            <?php } ?>
          
            <div class="flex flex--center w-100 m-t-1">
                <button class="button button--padded" type="submit"><?php esc_html_e('Save', 'site-search-360'); ?></button>
            </div>
        </section>
    </section>
</section>


<script type="text/javascript">
(function(){
    var toToggle = jQuery(".if-active");
    var deleteAllRow = jQuery("#delete-all-label-row")
    var updateDeleteAllLabelVisibility = function(){
        if(jQuery("#showQuickDelete:checked").length > 0){
            deleteAllRow.show();
        }else {
            deleteAllRow.hide();
        }
    }
    jQuery("#enabled").on("change", function(e){
        if(e.target.checked){
            toToggle.show();
            updateDeleteAllLabelVisibility();
        }else {
            toToggle.hide();
        }
    });
    jQuery("#showQuickDelete").on("change", updateDeleteAllLabelVisibility);
    jQuery(".message__close").on("click", function(e){
        jQuery(e.target).parents(".message").fadeOut();
    });
}());

</script>
<script src="<?php echo plugins_url('assets/sitesearch360_admin_scripts.js',  __FILE__)  ?>" async></script>