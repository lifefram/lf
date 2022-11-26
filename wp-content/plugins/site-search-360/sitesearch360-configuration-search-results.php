<?php 
    $ss360_plugin = new SiteSearch360Plugin();
    $ss360_config = $ss360_plugin->getConfig();

    $ss360_default_caption = 'Found #COUNT# search results for "#QUERY#';
    $ss360_default_correction = 'Did you mean "#CORRECTION#"?';
    $ss360_default_emptySet = 'Sorry, we have not found any matches for your query.';
    $ss360_updated_flag = false;

    $ss360_data_points = get_option('ss360_data_points');
    $ss360_inactive_dp = get_option('ss360_inactive_dp');
    $ss360_renamed_dp = get_option('ss360_renamed_dp');
    if($ss360_data_points==null){
        $ss360_data_points = array();
    }
    if($ss360_inactive_dp==null){
        $ss360_inactive_dp = array();
    }
    if($ss360_renamed_dp==null){
        $ss360_renamed_dp = array();
    }

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

    $ss360_results = isset($ss360_config['results']) ? $ss360_config['results'] : array();
    if (!empty($_POST) && isset($_POST['_wpnonce'])) {
        if(!isset($ss360_results['embedConfig'])){
            $ss360_results['embedConfig'] = array();
        }
        $ss360_results = updateConfigValue($ss360_results, 'embedConfig','contentBlock', '', false);
        $ss360_results = updateConfigValue($ss360_results, 'embedConfig','url', '', false);
        if(empty($ss360_results['embedConfig'])){
            unset($ss360_results['embedConfig']);
        }
        $ss360_config['results'] = $ss360_results;
        if(isset($_POST['redirectOnSingle']) && $_POST['redirectOnSingle'] == 'on'){
            $ss360_config['results']['redirectOnSingle'] = true;
        }else {
            unset($ss360_config['results']['redirectOnSingle']);
        }
        if(isset($_POST['linksOpenNewTab']) && $_POST['linksOpenNewTab'] == 'on'){
            $ss360_config['results']['linksOpenNewTab'] = true;
        }else {
            unset($ss360_config['results']['linksOpenNewTab']);
        }
        if(!isset($_POST['highlightQueryTerms']) || $_POST['highlightQueryTerms'] != 'on'){
            $ss360_config['results']['highlightQueryTerms'] = false;
        }else {
            unset($ss360_config['results']['highlightQueryTerms']);
        }
        if(isset($_POST['infiniteScroll']) && $_POST['infiniteScroll'] == 'on'){
            $ss360_config['results']['infiniteScroll'] = true;
        }else {
            unset($ss360_config['results']['infiniteScroll']);
        }
        if(isset($_POST['hideResultsWithoutImage']) && $_POST['hideResultsWithoutImage'] == 'on'){
            $ss360_config['results']['hideResultsWithoutImage'] = true;
        }else {
            unset($ss360_config['results']['hideResultsWithoutImage']);
        }
        $ss360_config = updateConfigValueInt($ss360_config, 'results', 'num', 96);
        $ss360_config = updateConfigValueInt($ss360_config, 'results', 'moreResultsPagingSize', 12);
        if(isset($_POST['results_moreResultsButton']) && $_POST['results_moreResultsButton'] != ''){
            $ss360_config['results']['moreResultsButton'] = stripslashes($_POST['results_moreResultsButton']);
        }else {
            $ss360_config['results']['moreResultsButton'] = null;
        }
        $ss360_config = updateConfigValue($ss360_config, 'results', 'caption', $ss360_default_caption, false);
        $ss360_config = updateConfigValue($ss360_config, 'results', 'queryCorrectionText', $ss360_default_correction, false);
        $ss360_config = updateConfigValue($ss360_config, 'results', 'noResultsText', $ss360_default_emptySet, false);
        $ss360_config = updateConfigValueBool($ss360_config, 'results', 'group', true);

        if(!isset($ss360_config['contentGroups'])){
            $ss360_config['contentGroups'] = array();
        }
        $ss360_config = updateConfigValue($ss360_config, 'contentGroups', 'otherName', '', false);
        if(empty($ss360_config['contentGroups'])){
            unset($ss360_config['contentGroups']);
        }

        $ss360_config = updateConfigValue($ss360_config, 'results', 'orderByRelevanceText', 'Relevance', false);
        if(empty($ss360_config['results'])){
            unset($ss360_config['results']);
        }

        if(!isset($ss360_config['layout'])){
            $ss360_config['layout'] = array();
        }
        if(!isset($ss360_config['layout']['desktop'])){
            $ss360_config['layout']['desktop'] = array();
        }
        if(!isset($ss360_config['layout']['mobile'])){
            $ss360_config['layout']['mobile'] = array();
        }
        $ss360_layout = $ss360_config['layout'];
        $ss360_layout = updateConfigValue($ss360_layout, 'desktop', 'type', 'list', false);
        $ss360_layout = updateConfigValueBool($ss360_layout, 'desktop', 'showImages', true);
        $ss360_layout = updateConfigValueBool($ss360_layout, 'desktop', 'showSnippet', true);
        $ss360_layout = updateConfigValueBool($ss360_layout, 'desktop', 'showTitle', true);
        $ss360_layout = updateConfigValueBool($ss360_layout, 'desktop', 'showDataPoints', true);
        $ss360_layout = updateConfigValueBool($ss360_layout, 'desktop', 'showUrl', false);
        $ss360_layout = updateConfigValue($ss360_layout, 'mobile', 'type', 'list', false);
        $ss360_layout = updateConfigValueBool($ss360_layout, 'mobile', 'showImages', true);
        $ss360_layout = updateConfigValueBool($ss360_layout, 'mobile', 'showSnippet', true);
        $ss360_layout = updateConfigValueBool($ss360_layout, 'mobile', 'showTitle', true);
        $ss360_layout = updateConfigValueBool($ss360_layout, 'mobile', 'showDataPoints', true);
        $ss360_layout = updateConfigValueBool($ss360_layout, 'mobile', 'showUrl', false);
        $ss360_layout = updateConfigValue($ss360_layout, 'navigation', 'position', 'none', false);
        $ss360_layout = updateConfigValue($ss360_layout, 'navigation', 'type', 'tabs', false);
        $ss360_layout = updateConfigValue($ss360_layout, 'navigation', 'allResultsTabName', 'All Results', true);
        $ss360_layout = updateConfigValueBool($ss360_layout, 'navigation', 'showAllResultsTab', true);

        if(empty($ss360_layout['desktop'])){
            unset($ss360_layout['desktop']);
        }
        if(empty($ss360_layout['mobile'])){
            unset($ss360_layout['mobile']);
        }
        if(empty($ss360_layout['navigation'])){
            unset($ss360_layout['navigation']);
        }
        if(!empty($ss360_layout)){
            $ss360_config['layout'] = $ss360_layout;
        }else {
            unset($ss360_config['layout']);
        }

    
        
        $ss360_plugin->saveConfig($ss360_config);
        $ss360_config = $ss360_plugin->getConfig();
        $ss360_results = isset($ss360_config['results']) ? $ss360_config['results'] : array();
        $ss360_updated_flag = true;

        if(sizeof($ss360_data_points) > 0){
            $ss360_inactive_dp = array();
            $ss360_renamed_dp = array();
            foreach($ss360_data_points as $ss360_data_point){
                $ss360_key = str_replace(array('.',' ','"'), "-", $ss360_data_point);
                $ss360_active_key = $ss360_key.'_active';
                $ss360_view_name_key = $ss360_key.'_viewName';
                if(!isset($_POST[$ss360_active_key]) || $_POST[$ss360_active_key]!='on'){
                    $ss360_inactive_dp[] = $ss360_data_point;
                }
                if(isset($_POST[$ss360_view_name_key]) && strlen($_POST[$ss360_view_name_key]) > 0){
                    $ss360_view_name = stripslashes($_POST[$ss360_view_name_key]);
                    if($ss360_view_name!=$ss360_data_point){
                        $ss360_renamed_dp[$ss360_data_point] = $ss360_view_name;
                    }
                }
            }
            update_option('ss360_inactive_dp', $ss360_inactive_dp);
            update_option('ss360_renamed_dp', $ss360_renamed_dp);
        }

        update_option('ss360_config_modifications', ((int) get_option('ss360_config_modifications')) + 1);
    }

    $embedConfig_contentBlock = isset($ss360_results['embedConfig']) ? $ss360_results['embedConfig']['contentBlock'] : '';
    $embedConfig_url = isset($ss360_results['embedConfig']) && isset($ss360_results['embedConfig']['url']) ? $ss360_results['embedConfig']['url'] : '';
    $redirectOnSingle = isset($ss360_results['redirectOnSingle']) ? $ss360_results['redirectOnSingle'] : false;
    $linksOpenNewTab = isset($ss360_results['linksOpenNewTab']) ? $ss360_results['linksOpenNewTab'] : false;
    $highlightQueryTerms = isset($ss360_results['highlightQueryTerms']) ? $ss360_results['highlightQueryTerms'] : true;
    $ss360_num = isset($ss360_results['num']) ? $ss360_results['num'] : 96;
    $moreResultsPagingSize = isset($ss360_results['moreResultsPagingSize']) ? $ss360_results['moreResultsPagingSize'] : 12;
    $moreResultsButton = isset($ss360_results['moreResultsButton']) ? $ss360_results['moreResultsButton'] : '';
    $orderByRelevanceText = isset($ss360_results['orderByRelevanceText']) ? $ss360_results['orderByRelevanceText'] : 'Relevance';
    $ss360_caption = isset($ss360_results['caption']) ? $ss360_results['caption'] : $ss360_default_caption;
    $queryCorrectionText = isset($ss360_results['queryCorrectionText']) ? $ss360_results['queryCorrectionText'] : $ss360_default_correction;
    $noResultsText = isset($ss360_results['noResultsText']) ? $ss360_results['noResultsText'] : $ss360_default_emptySet;
    $infiniteScroll = isset($ss360_results['infiniteScroll']) ? $ss360_results['infiniteScroll'] : false;
    $hideResultsWithoutImage = isset($ss360_results['hideResultsWithoutImage']) ? $ss360_results['hideResultsWithoutImage'] : false;

    $ss360_groups = isset($ss360_config['contentGroups']) ? $ss360_config['contentGroups'] : array();
    $otherContentGroupName = isset($ss360_groups['otherName']) ? $ss360_groups['otherName'] : '';

    if(!isset($ss360_config['layout'])){
        $ss360_config['layout'] = array();
    }
    if(!isset($ss360_config['layout']['desktop'])){
        $ss360_config['layout']['desktop'] = array();
    }
    if(!isset($ss360_config['layout']['mobile'])){
        $ss360_config['layout']['mobile'] = array();
    }
    if(!isset($ss360_config['layout']['navigation'])){
        $ss360_config['layout']['navigation'] = array();
    }
    

    $ss360_layout_desktop_type = isset($ss360_config['layout']['desktop']['type']) ? $ss360_config['layout']['desktop']['type'] : 'list';
    $ss360_layout_desktop_showImages = isset($ss360_config['layout']['desktop']['showImages']) ? $ss360_config['layout']['desktop']['showImages'] : true;
    $ss360_layout_desktop_showSnippet = isset($ss360_config['layout']['desktop']['showSnippet']) ? $ss360_config['layout']['desktop']['showSnippet'] : true;
    $ss360_layout_desktop_showTitle = isset($ss360_config['layout']['desktop']['showTitle']) ? $ss360_config['layout']['desktop']['showTitle'] : true;
    $ss360_layout_desktop_showDataPoints = isset($ss360_config['layout']['desktop']['showDataPoints']) ? $ss360_config['layout']['desktop']['showDataPoints'] : true;
    $ss360_layout_desktop_showUrl = isset($ss360_config['layout']['desktop']['showUrl']) ? $ss360_config['layout']['desktop']['showUrl'] : false;
    $ss360_layout_mobile_type = isset($ss360_config['layout']['mobile']['type']) ? $ss360_config['layout']['mobile']['type'] : 'list';
    $ss360_layout_mobile_showImages = isset($ss360_config['layout']['mobile']['showImages']) ? $ss360_config['layout']['mobile']['showImages'] : true;
    $ss360_layout_mobile_showSnippet = isset($ss360_config['layout']['mobile']['showSnippet']) ? $ss360_config['layout']['mobile']['showSnippet'] : true;
    $ss360_layout_mobile_showTitle = isset($ss360_config['layout']['mobile']['showTitle']) ? $ss360_config['layout']['mobile']['showTitle'] : true;
    $ss360_layout_mobile_showDataPoints = isset($ss360_config['layout']['mobile']['showDataPoints']) ? $ss360_config['layout']['mobile']['showDataPoints'] : true;
    $ss360_layout_mobile_showUrl = isset($ss360_config['layout']['mobile']['showUrl']) ? $ss360_config['layout']['mobile']['showUrl'] : false;

    $singleLineGridTitle = isset($ss360_config['layout']['singleLineGridTitle']) ? $ss360_config['layout']['singleLineGridTitle'] : false;
   

    $ss360_results_group = isset($ss360_config['results']['group']) ? $ss360_config['results']['group'] : true;
    $ss360_navigation_position = isset($ss360_config['layout']['navigation']['position']) ? $ss360_config['layout']['navigation']['position'] : 'none';
    $ss360_navigation_type = isset($ss360_config['layout']['navigation']['type']) ? $ss360_config['layout']['navigation']['type'] : 'tabs';
    $ss360_all_results = isset($ss360_config['layout']['navigation']['showAllResultsTab']) ? $ss360_config['layout']['navigation']['showAllResultsTab'] : true;
    $ss360_all_results_title =  isset($ss360_config['layout']['navigation']['allResultsTabName']) ? $ss360_config['layout']['navigation']['allResultsTabName'] : 'All Results';
    
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
            <h2><?php esc_html_e('Search Result Configuration', 'site-search-360'); ?></h2>
            <section>
                <h3 class="m-b-0 c-b"><?php esc_html_e('General Settings', 'site-search-360') ?></h3>
                <table class="configuration">
                    <tbody>
                        <tr>
                            <td><strong><label for="embedConfig-contentBlock"><?php esc_html_e('Content Block', 'site-search-360'); ?></label></strong></td>
                            <td><input id="embedConfig-contentBlock" class="input input--inline" type="text" value="<?php echo $embedConfig_contentBlock;?>" placeholder=".site-main" name="embedConfig.contentBlock"></td>
                            <td><?php esc_html_e('The CSS selector to the block where search results should be rendered. Leave blank if search results should be shown in a layover.', 'site-search-360') ?></td>
                        </tr>
                        <tr class="url-block" <?php echo $embedConfig_contentBlock=='' ? 'style="display:none;"' : '' ?>>
                            <td><strong><label for="embedConfig-url"><?php esc_html_e('Search Result Page', 'site-search-360'); ?></label></strong></td>
                            <td><input id="embedConfig-url" class="input input--inline" type="text" value="<?php echo $embedConfig_url;?>" placeholder="/search-results" name="embedConfig.url"></td>
                            <td><?php esc_html_e('The URL of the search result page. Leave blank if search results should rendered on any page.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="redirectOnSingle"><?php esc_html_e('Redirect on Single Result', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('redirect', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="redirectOnSingle" name="redirectOnSingle" <?php echo $redirectOnSingle ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to redirect the user to the search result page if only one search result was retrieved.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="linksOpenNewTab"><?php esc_html_e('Open Links in a New Tab', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('open in new tab', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="linksOpenNewTab" name="linksOpenNewTab" <?php echo $linksOpenNewTab ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to open a new tab after the user selects a search result.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="highlightQueryTerms"><?php esc_html_e('Highlight Query Terms', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('highlight', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="highlightQueryTerms" name="highlightQueryTerms" <?php echo $highlightQueryTerms ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to highlight parts of the query in the search results.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="num"><?php esc_html_e('Number of Results', 'site-search-360'); ?></label></strong></td>
                            <td><input id="num" class="input input--inline" type="number" min="1" max="1000" step="1" value="<?php echo $ss360_num;?>" name="results.num" placeholder="96"></td>
                            <td><?php esc_html_e('The maximum number of search results to be shown.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="moreResultsPagingSize"><?php esc_html_e('Page Size', 'site-search-360'); ?></label></strong></td>
                            <td><input id="moreResultsPagingSize" class="input input--inline" type="number" min="1" max="24" step="1" value="<?php echo $moreResultsPagingSize;?>" name="results.moreResultsPagingSize" placeholder="12"></td>
                            <td><?php esc_html_e('The number of new results to show each time the more results button is pressed.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="moreResultsButton"><?php esc_html_e('More Results Button Text', 'site-search-360'); ?></label></strong></td>
                            <td><input id="moreResultsButton" class="input input--inline" type="text" value="<?php echo htmlspecialchars($moreResultsButton);?>" name="results.moreResultsButton" placeholder="<?php esc_html_e('See more','site-search-360');?>"></td>
                            <td><?php esc_html_e('The text of the more results button. Leave blank if all search results should be shown at once.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="infiniteScroll"><?php esc_html_e('Infinite Scroll', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('on', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="infiniteScroll" name="infiniteScroll" <?php echo $infiniteScroll ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show more search results once the user scrolls to the end of the result block. Only available for tabbed navigation or when only one content group has been retrieved.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="hideResultsWithoutImage"><?php esc_html_e('Hide Results Without Image', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('hide', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="hideResultsWithoutImage" name="hideResultsWithoutImage" <?php echo $hideResultsWithoutImage ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to hide all results that do not have any image or have a broken image.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="singleLineGridTitle"><?php esc_html_e('Single line grid Title', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('on', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="singleLineGridTitle" name="singleLineGridTitle" <?php echo $singleLineGridTitle ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show the search result title on a single line, only for "grid" layout.', 'site-search-360') ?></td>
                        </tr>
                    </tbody>
                </table>
            </section>
            <hr/>
            <section>
                <h3 class="m-b-0 c-b"><?php esc_html_e('Desktop Layout', 'site-search-360') ?></h3>
                <table class="configuration">
                    <tbody>
                        <tr>
                            <td><strong><?php esc_html_e('Layout Type', 'site-search-360'); ?></strong></td>
                            <td>
                                <div class="flex">
                                    <label class="radio m-r-1">
                                        <?php esc_html_e('List', 'site-search-360'); ?>
                                        <input type="radio" name="desktop.type" <?php echo $ss360_layout_desktop_type == 'list' ? 'checked' : '' ?> value="list" class="fake-hide">
                                        <span class="radio_checkmark"></span>
                                    </label>
                                    <label class="radio m-r-1">
                                        <?php esc_html_e('Grid', 'site-search-360'); ?>
                                        <input type="radio" name="desktop.type" <?php echo $ss360_layout_desktop_type == 'grid' ? 'checked' : '' ?> value="grid" class="fake-hide">
                                        <span class="radio_checkmark"></span>
                                    </label>
                                    <label class="radio">
                                        <?php esc_html_e('Masonry', 'site-search-360'); ?>
                                        <input type="radio" name="desktop.type" <?php echo $ss360_layout_desktop_type == 'masonry' ? 'checked' : '' ?> value="masonry" class="fake-hide">
                                        <span class="radio_checkmark"></span>
                                    </label>
                                </div>
                            </td>
                            <td><?php esc_html_e('How to display the search results: grid (4 columns on large screens, 3 columns on smaller ones) vs. masonry (6 columns on large screens, 5 on smaller ones) vs. list.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="layout-desktop-showImages"><?php esc_html_e('Show Images', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="layout-desktop-showImages" name="desktop.showImages" <?php echo $ss360_layout_desktop_showImages ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show images in the desktop search results.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="layout-desktop-showSnippet"><?php esc_html_e('Show Text Snippet', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="layout-desktop-showSnippet" name="desktop.showSnippet" <?php echo $ss360_layout_desktop_showSnippet ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show the search result snippet in the desktop search results.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="layout-desktop-showTitle"><?php esc_html_e('Show Title', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="layout-desktop-showTitle" name="desktop.showTitle" <?php echo $ss360_layout_desktop_showTitle ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show the search result title in the desktop search results.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="layout-desktop-showDataPoints"><?php esc_html_e('Show Data Points', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="layout-desktop-showDataPoints" name="desktop.showDataPoints" <?php echo $ss360_layout_desktop_showDataPoints ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show data points like the author or publication date in the desktop search results.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="layout-desktop-showUrl"><?php esc_html_e('Show Result URL', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="layout-desktop-showUrl" name="desktop.showUrl" <?php echo $ss360_layout_desktop_showUrl ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show the search result URL in the desktop search results.', 'site-search-360') ?></td>
                        </tr>
                    </tbody>
                </table>
            </section>
            <hr/>
            <section>
                <h3 class="m-b-0 c-b"><?php esc_html_e('Mobile Layout', 'site-search-360') ?></h3>
                <table class="configuration">
                    <tbody>
                        <tr>
                            <td><strong><?php esc_html_e('Layout Type', 'site-search-360'); ?></strong></td>
                            <td>
                                <div class="flex">
                                    <label class="radio m-r-1">
                                        <?php esc_html_e('List', 'site-search-360'); ?>
                                        <input type="radio" name="mobile.type" <?php echo $ss360_layout_mobile_type == 'list' ? 'checked' : '' ?> value="list" class="fake-hide">
                                        <span class="radio_checkmark"></span>
                                    </label>
                                    <label class="radio m-r-1">
                                        <?php esc_html_e('Grid', 'site-search-360'); ?>
                                        <input type="radio" name="mobile.type" <?php echo $ss360_layout_mobile_type == 'grid' ? 'checked' : '' ?> value="grid" class="fake-hide">
                                        <span class="radio_checkmark"></span>
                                    </label>
                                    <label class="radio">
                                        <?php esc_html_e('Masonry', 'site-search-360'); ?>
                                        <input type="radio" name="mobile.type" <?php echo $ss360_layout_mobile_type == 'masonry' ? 'checked' : '' ?> value="masonry" class="fake-hide">
                                        <span class="radio_checkmark"></span>
                                    </label>
                                </div>
                            </td>
                            <td><?php esc_html_e('How to display the search results: grid (2 columns on larger screens like tablets, 1 column on smaller ones like mobile phones) vs. masonry (3 columns on larger screens like tablets, 2 column on smaller ones like mobile phones) vs. list.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="layout-mobile-showImages"><?php esc_html_e('Show Images', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="layout-mobile-showImages" name="mobile.showImages" <?php echo $ss360_layout_mobile_showImages ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show images in the mobile search results.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="layout-mobile-showSnippet"><?php esc_html_e('Show Text Snippet', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="layout-mobile-showSnippet" name="mobile.showSnippet" <?php echo $ss360_layout_mobile_showSnippet ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show the search result snippet in the mobile search results.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="layout-mobile-showTitle"><?php esc_html_e('Show Title', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="layout-mobile-showTitle" name="mobile.showTitle" <?php echo $ss360_layout_mobile_showTitle ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show the search result title in the mobile search results.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="layout-mobile-showDataPoints"><?php esc_html_e('Show Data Points', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="layout-mobile-showDataPoints" name="mobile.showDataPoints" <?php echo $ss360_layout_mobile_showDataPoints ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show data points like the author or publication date in the mobile search results.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="layout-mobile-showUrl"><?php esc_html_e('Show Result URL', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="layout-mobile-showUrl" name="mobile.showUrl" <?php echo $ss360_layout_mobile_showUrl ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show the search result URL in the mobile search results.', 'site-search-360') ?></td>
                        </tr>
                    </tbody>
                </table>
            </section>
            <hr/>
            <section>
                <h3 class="m-b-0 c-b"><?php esc_html_e('Localization', 'site-search-360') ?></h3>
                <table class="configuration">
                    <tbody>
                        <tr>
                            <td><strong><label for="results-caption"><?php esc_html_e('Caption', 'site-search-360'); ?></label></strong></td>
                            <td><input id="results-caption" class="input input--inline" type="text" value="<?php echo htmlspecialchars($ss360_caption);?>" placeholder="<?php htmlspecialchars(esc_html_e('Found #COUNT# search results for "#QUERY#".', 'site-search-360')); ?>" name="results.caption"></td>
                            <td><?php esc_html_e('The caption of the search results page. #QUERY# will be replaced with the search query and #COUNT# with the number of retrieved search results.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="results-queryCorrectionText"><?php esc_html_e('Query Correction Text', 'site-search-360'); ?></label></strong></td>
                            <td><input id="results-queryCorrectionText" class="input input--inline" type="text" value="<?php echo htmlspecialchars($queryCorrectionText);?>" placeholder="<?php htmlspecialchars(esc_html_e('Did you mean "#CORRECTION#?', 'site-search-360')); ?>" name="results.queryCorrectionText"></td>
                            <td><?php esc_html_e('The text to be shown in case Site Search 360 detects a spelling error. #CORRECTION# will be replaced with the corrected text.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="results-noResultsText"><?php esc_html_e('No Results Text', 'site-search-360'); ?></label></strong></td>
                            <td><input id="results-noResultsText" class="input input--inline" type="text" value="<?php echo htmlspecialchars($noResultsText);?>" placeholder="<?php esc_html_e('Sorry, we have not found any matches for your query.', 'site-search-360') ?>" name="results.noResultsText"></td>
                            <td><?php esc_html_e('The text to be shown in case no search results were retrieved.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="orderByRelevanceText"><?php esc_html_e('Order By Relevance Text', 'site-search-360'); ?></label></strong></td>
                            <td><input id="orderByRelevanceText" class="input input--inline" type="text" value="<?php echo htmlspecialchars($orderByRelevanceText);?>" name="results.orderByRelevanceText" placeholder="<?php esc_html_e('Relevance','site-search-360');?>"></td>
                            <td><?php esc_html_e('The text to be shown in the select box to describe the "order by relevance" option.', 'site-search-360') ?></td>
                        </tr>
                    </tbody>
                </table>
            </section>
            <hr/>
            <section>
                <h3 class="m-b-0 c-b"><?php esc_html_e('Navigation', 'site-search-360') ?></h3>
                <table class="configuration">
                    <tbody>
                        <tr>
                            <td><strong><label for="results-group"><?php esc_html_e('Group Results', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('group', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="results-group" name="results.group" <?php echo $ss360_results_group ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether the results should be grouped into content groups (by default your WordPress site categories).', 'site-search-360') ?></td>
                        </tr>
                        <tr id="navigation-position-row" <?php echo $ss360_results_group ? '' : 'style="display: none;"' ?>>
                            <td><strong><?php esc_html_e('Navigation Position', 'site-search-360'); ?></strong></td>
                            <td>
                                <div class="flex">
                                    <label class="radio m-r-1">
                                        <?php esc_html_e('none', 'site-search-360'); ?>
                                        <input type="radio" name="navigation.position" <?php echo $ss360_navigation_position == 'none' ? 'checked' : '' ?> value="none" class="fake-hide">
                                        <span class="radio_checkmark"></span>
                                    </label>
                                    <label class="radio m-r-1">
                                        <?php esc_html_e('top', 'site-search-360'); ?>
                                        <input type="radio" name="navigation.position" <?php echo $ss360_navigation_position == 'top' ? 'checked' : '' ?> value="top" class="fake-hide">
                                        <span class="radio_checkmark"></span>
                                    </label>
                                    <label class="radio">
                                        <?php esc_html_e('left', 'site-search-360'); ?>
                                        <input type="radio" name="navigation.position" <?php echo $ss360_navigation_position == 'left' ? 'checked' : '' ?> value="left" class="fake-hide">
                                        <span class="radio_checkmark"></span>
                                    </label>
                                </div>
                            </td>
                            <td><?php esc_html_e('Where to show the content group navigation (relative to the search result block).', 'site-search-360') ?></td>
                        </tr>
                        <tr id="navigation-type-row" <?php echo $ss360_results_group && $ss360_navigation_position!='none' ? '' : 'style="display: none;"' ?>>
                            <td><strong><?php esc_html_e('Navigation Type', 'site-search-360'); ?></strong></td>
                            <td>
                                <div class="flex">
                                    <label class="radio m-r-1">
                                        <?php esc_html_e('scroll', 'site-search-360'); ?>
                                        <input type="radio" name="navigation.type" <?php echo $ss360_navigation_type == 'scroll' ? 'checked' : '' ?> value="scroll" class="fake-hide">
                                        <span class="radio_checkmark"></span>
                                    </label>
                                    <label class="radio">
                                        <?php esc_html_e('tabs', 'site-search-360'); ?>
                                        <input type="radio" name="navigation.type" <?php echo $ss360_navigation_type == 'tabs' ? 'checked' : '' ?> value="tabs" class="fake-hide">
                                        <span class="radio_checkmark"></span>
                                    </label>
                                </div>
                            </td>
                            <td><?php esc_html_e('The type of the navigation. Scroll navigation will scroll the page to the relevant content, tabbed navigation hides inactive content groups.', 'site-search-360') ?></td>
                        </tr>
                        <tr class="tab-only" style="<?php echo $ss360_navigation_type == 'tabs' ? '' : 'style="display:none";' ?>">
                            <td><strong><label for="all-results"><?php esc_html_e('Show all results tab', 'site-search-360'); ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('show', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="all-results" name="navigation.showAllResultsTab" <?php echo $ss360_all_results ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to show tab grouping all results.', 'site-search-360') ?></td>
                        </tr>  
                        <tr class="tab-only" style="<?php echo $ss360_navigation_type == 'tabs' ? '' : 'style="display:none";' ?>">
                            <td><strong><label for="all-results-title"><?php esc_html_e('All results tab title', 'site-search-360'); ?></label></strong></td>
                            <td><input id="all-results-title" class="input input--inline" type="text" value="<?php echo htmlspecialchars($ss360_all_results_title);?>" name="navigation.allResultsTabName" placeholder="<?php esc_html_e('All Results','site-search-360');?>"></td>
                            <td><?php esc_html_e('The title of the All Results tab, #COUNT# will be replaced with the number of available search results.', 'site-search-360') ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="otherContentGroupName"><?php esc_html_e('Other Content Group Name', 'site-search-360'); ?></label></strong></td>
                            <td><input id="otherContentGroupName" class="input input--inline" type="text" value="<?php echo htmlspecialchars($otherContentGroupName);?>" name="contentGroups.otherName" placeholder="<?php esc_html_e('Other','site-search-360');?>"></td>
                            <td><?php esc_html_e('The name of the content group to be shown for all uncategorized results.', 'site-search-360') ?></td>
                        </tr>
                    </tbody>
                </table>
            </section>
            <?php if(sizeof($ss360_data_points)>0){?>
                <hr/>
                <section>
                <h3  class="m-b-0 c-b"><?php esc_html_e('Data Points', 'site-search-360'); ?></h3>
                <span class="m-t-1"><?php esc_html_e('Data Points enhance your search results by including additional information in form of a structured data table. Please note that any changes to the data point configuration require a re-index to be applied.', 'site-search-360'); ?></span>
                <table class="configuration">
                    <?php foreach($ss360_data_points as $ss360_data_point){
                            $ss360_dp = htmlspecialchars($ss360_data_point);
                            $ss360_key = str_replace(array('.',' ','"'), "-", $ss360_data_point);
                            $ss360_view_name = isset($ss360_renamed_dp[$ss360_data_point]) && $ss360_renamed_dp[$ss360_data_point] != null 
                                ? $ss360_renamed_dp[$ss360_data_point] : $ss360_data_point;
                            $ss360_view_name = htmlspecialchars($ss360_view_name);
                            $ss360_is_active = !in_array($ss360_data_point, $ss360_inactive_dp);
                        ?>
                        <tr>
                            <td style="max-width:250px;width:250px;"><input type="text" class="input input--inline" placeholder="<?php echo $ss360_dp?>" value="<?php echo $ss360_view_name;?>" name="<?php echo $ss360_key . '.viewName' ?>"></td>                        
                            <td><label class="checkbox"><?php esc_html_e('active', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="<?php echo $ss360_key . '.active' ?>" name="<?php echo $ss360_key . '.active' ?>" <?php echo $ss360_is_active ? 'checked' : ''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td></td>
                        </tr>
                    <?php } ?>
                </table>
                </section>
            <?php } ?>
            <div class="flex flex--center w-100 m-t-1">
                <button class="button button--padded" type="submit"><?php esc_html_e('Save', 'site-search-360'); ?></button>
            </div>
        </form>
    </section>
</section>


<script type="text/javascript">
(function(){
    var urlRow = jQuery(".url-block");
    var navigationPositionRow = jQuery("#navigation-position-row");
    var navigationTypeRow = jQuery("#navigation-type-row");
    jQuery("#embedConfig-contentBlock").on("input", function(e){
        if(!e.target.value){
            urlRow.hide();
        }else {
            urlRow.show();
        }
    });
    var updateTypeVisibility = function(){
        var currentPosition = jQuery("input[name='navigation.position']:checked").val();
        if(currentPosition==="none"){
            navigationTypeRow.hide();
        }else {
            navigationTypeRow.show();
        }
    }
    jQuery("#results-group").on("change", function(e){
        if(e.target.checked){
            navigationPositionRow.show();
            updateTypeVisibility();
        }else {
            navigationPositionRow.hide();
            navigationTypeRow.hide();
        }
    });
    jQuery('input[name="navigation.type"]').on("change", function(e) {
        if(e.target.value === 'scroll') {
            jQuery('.tab-only').hide();
        } else {
            jQuery('.tab-only').show();
        }
    });
    jQuery("input[name='navigation.position']").on("change", updateTypeVisibility);
    jQuery(".message__close").on("click", function(e){
        jQuery(e.target).parents(".message").fadeOut();
    });
}());

</script>
<script src="<?php echo plugins_url('assets/sitesearch360_admin_scripts.js',  __FILE__)  ?>" async></script>