<?php 
   
    $ss360_plugin = new SiteSearch360Plugin();
    $ss360_config = $ss360_plugin->getConfig();
    $ss360_is_full = $ss360_plugin->getType() == 'full';
    $ss360_update_flag = false;
	$ss360_menus = get_registered_nav_menus();

    function updateConfigValue($ss360_configuration, $ss360_path1, $ss360_path2, $ss360_default, $ss360_case_insensitive){
        $ss360_post_key = $ss360_path1.'_'.$ss360_path2;
        if(isset($_POST[$ss360_post_key]) && (($ss360_case_insensitive && strtolower($_POST[$ss360_post_key])!=strtolower($ss360_default)) || ($_POST[$ss360_post_key]!=$ss360_default))){
            $ss360_configuration[$ss360_path1][$ss360_path2] = stripslashes($_POST[$ss360_post_key]);
        }else {
            unset($ss360_configuration[$ss360_path1][$ss360_path2]);
        }
        return $ss360_configuration;
    }

    if (!empty($_POST) && isset($_POST['_wpnonce'])) {
        if(!isset($ss360_config['searchBox'])){
            $ss360_config['searchBox'] = array();
        }
        $ss360_config['searchBox']['selector']  = isset($_POST['searchBox_selector']) ? stripslashes($_POST['searchBox_selector']) : '#searchBox, form[role="search"] input[name="s"]';
        $ss360_config = updateConfigValue($ss360_config, 'searchBox', 'searchButton', '', false);
        $ss360_config = updateConfigValue($ss360_config, 'searchBox', 'placeholder', '', false);
        $ss360_config = updateConfigValue($ss360_config, 'searchBox', 'searchButtonLabel', '', false);
        if(empty($ss360_config['searchBox'])){
            unset($ss360_config['searchBox']);
        }
        
        if(isset($_POST['language']) && $_POST['language'] != 'en') {
            $ss360_config['language'] = $_POST['language'];
        } else {
            unset($ss360_config['language']);
        }

        if(!isset($ss360_config['style'])){
            $ss360_config['style'] = array();
        }

        $ss360_config = updateConfigValue($ss360_config, 'style', 'themeColor', '#4A4F62', true);
        $ss360_config = updateConfigValue($ss360_config, 'style', 'accentColor', '#3D8FFF', true);
        $ss360_config = updateConfigValue($ss360_config, 'style','loaderType', 'skeleton', true);
        if(isset($_POST['style_animationSpeed']) && intval($_POST['style_animationSpeed']) != 250){
            $ss360_config['style']['animationSpeed'] = intval($_POST['style_animationSpeed']);
        }else {
            unset($ss360_config['style']['animationSpeed']);
        }
        if(empty($ss360_config['style'])){
            unset($ss360_config['style']);
        }
        if(!isset($_POST['allowCookies']) || $_POST['allowCookies']!='on'){
            $ss360_config['allowCookies'] = false;
        }else {
            unset($ss360_config['allowCookies']);
        }

        if(!isset($ss360_config['tracking'])){
            $ss360_config['tracking'] = array();
        }
        $ss360_config['tracking']['providers'] = array();
        if(isset($_POST['tracking_providers_ga']) && $_POST['tracking_providers_ga']=='on'){
            $ss360_config['tracking']['providers'][] = 'GA';
        }
        if(isset($_POST['tracking_providers_gtm']) && $_POST['tracking_providers_gtm']=='on'){
            $ss360_config['tracking']['providers'][] = 'GTM';
        }
        if(empty($ss360_config['tracking']['providers'])){
            unset($ss360_config['tracking']['providers']);
        }
        if(empty($ss360_config['tracking'])){
            unset($ss360_config['tracking']);
        }
        
        if(!isset($ss360_config['voiceSearch'])){
            $ss360_config['voiceSearch'] = array();
        }
        if(isset($_POST['voiceSearch_enabled']) && $_POST['voiceSearch_enabled']=='on'){
            $ss360_config['voiceSearch']['enabled'] = true;
        }else {
            unset($ss360_config['voiceSearch']['enabled']);
        }
        $ss360_config = updateConfigValue($ss360_config, 'voiceSearch', 'lang', 'en-US', false);
        if(empty($ss360_config['voiceSearch'])){
            unset($ss360_config['voiceSearch']);
		}
		
		$ss360_selected_menus = array();
		foreach(array_keys($ss360_menus) as $ss360_menu_key) {
			if (isset($_POST['menu_'.$ss360_menu_key]) && $_POST['menu_'.$ss360_menu_key] == 'on') {
				$ss360_selected_menus[] = $ss360_menu_key;
			}
		}
		update_option('ss360_selected_menus', $ss360_selected_menus);

        $ss360_plugin->saveConfig($ss360_config);
        $ss360_update_flag = true;
        update_option('ss360_config_modifications', ((int) get_option('ss360_config_modifications')) + 1);
    }

    $language = isset($ss360_config['language']) ? $ss360_config['language'] : 'en';
    // configuration entries
    $searchBox_selector = isset($ss360_config['searchBox']) && isset($ss360_config['searchBox']['selector']) ?  htmlspecialchars($ss360_config['searchBox']['selector']) : '#searchBox';
    $searchBox_searchButton = isset($ss360_config['searchBox']) && isset($ss360_config['searchBox']['searchButton']) ? htmlspecialchars($ss360_config['searchBox']['searchButton']) : '';
    $searchBox_placeholder = isset($ss360_config['searchBox']) && isset($ss360_config['searchBox']['placeholder']) ? htmlspecialchars($ss360_config['searchBox']['placeholder']) : '';
    $searchBox_searchButtonLabel = isset($ss360_config['searchBox']) && isset($ss360_config['searchBox']['searchButtonLabel']) ? htmlspecialchars($ss360_config['searchBox']['searchButtonLabel']) : '';

    $style_themeColor = isset($ss360_config['style']) && isset($ss360_config['style']['themeColor']) ? $ss360_config['style']['themeColor'] : '#4A4F62';
    $style_accentColor = isset($ss360_config['style']) && isset($ss360_config['style']['accentColor']) ? $ss360_config['style']['accentColor'] : '#3D8FFF';
    $style_loaderType = isset($ss360_config['style']) && isset($ss360_config['style']['loaderType']) ? $ss360_config['style']['loaderType'] : 'skeleton'; 
    $style_animationSpeed = isset($ss360_config['style']) && isset($ss360_config['style']['animationSpeed']) ? $ss360_config['style']['animationSpeed'] : 250;

    $ss360_allowCookies = isset($ss360_config['allowCookies']) ? $ss360_config['allowCookies'] : true;
    if(!isset($ss360_config['tracking'])){
        $ss360_config['tracking'] = array();
    }
    if(!isset($ss360_config['tracking']['providers']) || !is_array($ss360_config['tracking']['providers'])){
        $ss360_config['tracking']['providers'] = array();
    }
    $tracking_providers_gtm =  in_array('GTM', $ss360_config['tracking']['providers']);
    $tracking_providers_ga = in_array('GA', $ss360_config['tracking']['providers']);
    $voiceSearch_enabled = isset($ss360_config['voiceSearch']) && isset($ss360_config['voiceSearch']['enabled']) ? $ss360_config['voiceSearch']['enabled'] : false;
	$voiceSearch_lang = isset($ss360_config['voiceSearch']) && isset($ss360_config['voiceSearch']['lang']) ? $ss360_config['voiceSearch']['lang'] : 'en-US';

	$ss360_selected_menus = get_option('ss360_selected_menus');
	if ($ss360_selected_menus == NULL) {
		$ss360_selected_menus = array();
	}
    

    // helpers
    $ss360_animationDuration = $style_animationSpeed == 0 ? 0 : ((1000 + $style_animationSpeed)/1000) . 's'; 
    $ss360_langs = array(
        array("cs", "Czech"),
        array("da", "Danish"),
        array("nl", "Dutch"),
        array("en-gb", "English (UK)"),
        array("en-US", "English (US)"),
        array("fi", "Finnish"),
        array("de", "German"),
        array("el", "Greek"),
        array("hu", "Hungarian"),
        array("it", "Italian"),
        array("lv", "Latvian"),
        array("lt", "Lithuanian"),
        array("no", "Norwegian"),
        array("pt", "Portugese"),
        array("ro", "Romanian"),
        array("ru", "Russian"),
        array("es", "Spanish"),
        array("sv", "Swedish"),
        array("tr", "Turkish")
    )
?>

<section id="ss360" class="wrap wrap--blocky flex flex--column flex--center">
    <?php 
        if($ss360_update_flag){ ?>
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
            <h2><?php esc_html_e('Basic Configuration', 'site-search-360'); ?></h2>
            <section>
                <h3 class="m-b-0 c-b"><?php esc_html_e('Search Box', 'site-search-360') ?></h3>
                <table class="configuration">
                    <tbody>
                        <tr>
                            <td><strong><label for="searchBox--selector"><?php esc_html_e('Search Box Selector', 'site-search-360') ?></label></strong></td>
                            <td><input id="searchBox--selector" name="searchBox.selector" type="text" placeholder="#searchBox" class="input input--inline" value="<?php echo $searchBox_selector; ?>"></td>
                            <td><?php esc_html_e('The CSS selector to the search input.', 'site-search-360'); ?></td>
                        </tr>
                        <tr <?php echo $ss360_is_full ? '': 'style="display:none;"' ?>>
                            <td><strong><label for="searchBox--searchButton"><?php esc_html_e('Search Button Selector', 'site-search-360') ?></label></strong></td>
                            <td><input id="searchBox--searchButton" name="searchBox.searchButton" type="text" placeholder="#searchButton" class="input input--inline" value="<?php echo $searchBox_searchButton; ?>"></td>
                            <td><?php esc_html_e('The CSS selector to the search button (optional).', 'site-search-360'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="searchBox--placeholder"><?php esc_html_e('Search Box Placeholder', 'site-search-360') ?></label></strong></td>
                            <td><input id="searchBox--placeholder" name="searchBox.placeholder" type="text" placeholder="<?php esc_html_e('Search', 'site-search-360') ?>" class="input input--inline" value="<?php echo $searchBox_placeholder ?>"></td>
                            <td><?php esc_html_e('The placeholder of the search input (will only be shown if no placeholder is defined in the HTML Markup).', 'site-search-360'); ?></td>
                        </tr>                      
                        <tr>
                            <td><strong><label for="searchBox--searchButtonLabel"><?php esc_html_e('Search Button Label', 'site-search-360') ?></label></strong></td>
                            <td><input id="searchBox--searchButtonLabel" name="searchBox.searchButtonLabel" type="text" placeholder="<?php esc_html_e('Search', 'site-search-360') ?>" class="input input--inline" value="<?php echo $searchBox_searchButtonLabel ?>"></td>
                            <td><?php esc_html_e('Label of Site Search 360 search buttons — will only be applied in search layover and for buttons added using shortcodes or Site Search 360 Form widget. Leave empty to show a magnifying glass icon.', 'site-search-360'); ?></td>
                        </tr>
						<?php if (!empty($ss360_menus)) { ?>
						<tr>
							<td style="vertical-align: top; padding-top: 8px;"><strong><?php esc_html_e('Add Search Box to Menu', 'site-search-360') ?></strong></td>
							<td>
							<?php foreach(array_keys($ss360_menus) as $ss360_menu) { ?>
								<label class="checkbox p-b-0-5 p-r-0-5"><?php esc_html_e($ss360_menus[$ss360_menu]) ?><input class="fake-hide" type="checkbox" name="menu.<?php echo $ss360_menu?>" <?php echo in_array($ss360_menu, $ss360_selected_menus) ? 'checked' : ''?>><span class="checkbox_checkmark"></span></label>
							<?php } ?>
							</td>
                            <td style="vertical-align: top; padding-top: 8px;"><?php esc_html_e('Site Search 360 custom search form will be added to the selected menus.', 'site-search-360'); ?></td>
						</tr>
						<?php } ?> 
                    </tbody>
                </table>
            </section>
            <hr/>
            <section>
                <h3 class="m-b-0 c-b"><?php esc_html_e('Style', 'site-search-360') ?></h3>
                <table class="configuration">
                    <tbody>
                        <tr>
                            <td><strong><label for="style--themeColor"><?php esc_html_e('Theme Color', 'site-search-360') ?></label></strong></td>
                            <td class="flex"><input id="style--themeColor" class="fake-hide" type="color" value="<?php echo $style_themeColor?>" name="style.themeColor"><div id="color--p" class="input input--inline" style="width:37px;height:37px;background:<?php echo $style_themeColor?>;cursor:pointer;"></div><input id="color--i" class="input input--inline" type="text" value="<?php echo $style_themeColor?>" placeholder="#4A4F62" pattern="(^#[0-9A-Fa-f]{6}$)|(^#[0-9A-Fa-f]{3}$)"></td>
                            <td><?php esc_html_e('The theme color (used for loaders, inactive elements, etc.).', 'site-search-360'); ?></td>
                        </tr>   
                        <tr>
                            <td><strong><label for="style--accentColor"><?php esc_html_e('Accent Color', 'site-search-360') ?></label></strong></td>
                            <td class="flex"><input id="style--accentColor" class="fake-hide" type="color" value="<?php echo $style_accentColor?>" name="style.accentColor"><div id="color--pa" class="input input--inline" style="width:37px;height:37px;background:<?php echo $style_accentColor?>;cursor:pointer;"></div><input id="color--ia" class="input input--inline" type="text" value="<?php echo $style_accentColor?>" placeholder="#3D8FFF" pattern="(^#[0-9A-Fa-f]{6}$)|(^#[0-9A-Fa-f]{3}$)"></td>
                            <td><?php esc_html_e('The accent color (used for result titles, see more results buttons, hover effects, etc.).', 'site-search-360'); ?></td>
                        </tr>
                        <tr <?php echo $ss360_is_full ? '': 'style="display:none;"' ?>>
                            <td><strong><?php esc_html_e('Loading Animation', 'site-search-360') ?></strong></td>
                            <td>
                                <div class="flex">
                                    <label class="radio m-r-1" aria-label="<?php esc_html_e('Skeleton', 'site-search-360') ?>">
                                        Skeleton
                                        <input id="loading-circle--i" type="radio" name="style.loaderType" <?php echo $style_loaderType == 'skeleton' ? 'checked' : '' ?> value="skeleton" class="fake-hide">
                                        <span class="radio_checkmark"></span>
                                    </label>
                                    <label class="radio" aria-label="<?php esc_html_e('Pulsating Circle', 'site-search-360') ?>">
                                        <div class="ss360-spinner-circle ss360-loader" style="width:25px; height:25px;">
                                            <div class="ss360-double-bounce1 colorize animate" style="background:<?php echo $style_themeColor?>;animation-duration:<?php echo $ss360_animationDuration;?>"></div>
                                            <div class="ss360-double-bounce2 colorize animate" style="background:<?php echo $style_themeColor?>;animation-duration:<?php echo $ss360_animationDuration;?>"></div>
                                        </div>
                                        <input id="loading-circle--i" type="radio" name="style.loaderType" <?php echo $style_loaderType == 'circle' ? 'checked' : '' ?> value="circle" class="fake-hide">
                                        <span class="radio_checkmark"></span>
                                    </label>
                                    <label class="radio m-l-1" aria-label="<?php esc_html_e('Flipping Square', 'site-search-360') ?>">
                                        <div class="ss360-loader ss360-spinner-square colorize animate" style="width:25px; height:25px;background:<?php echo $style_themeColor?>;animation-duration:<?php echo $ss360_animationDuration;?>"></div>
                                        <input id="loading-square--i" type="radio" class="fake-hide" name="style.loaderType" value="square" <?php echo $style_loaderType == 'square' ? 'checked' : '' ?>>
                                        <span class="radio_checkmark"></span>
                                    </label>
                                </div>
                            </td>
                            <td><?php esc_html_e('The animation to show when search results are loading (by default a grey skeleton screen - outlining the future search layout - will be shown).', 'site-search-360')?></td>
                        </tr>
                        <tr>
                            <td><strong><label for="style--animationSpeed"><?php esc_html_e('Animation Speed', 'site-search-360') ?></label></strong></td>
                            <td><input value="<?php echo $style_animationSpeed; ?>" id="style--animationSpeed" name="style.animationSpeed" min="0" max="1000" step="50" type="number" placeholder="250" class="input input--inline"></td>
                            <td><?php esc_html_e('The animation duration in milliseconds (0: no animation; 1: fast animation; 1000: slow animation).', 'site-search-360'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </section>
            <hr/>
            <section>
                <h3 class="m-b-0 c-b"><?php esc_html_e('Other', 'site-search-360') ?></h3>
                <table class="configuration">
                    <tbody>
                        <tr>
                            <td><strong><label for="language"><?php esc_html_e('Interface language', 'site-search-360') ?></label></strong></td>
                            <td>
                                <select id="language" name="language" class="input select m-b-0" value="<?php echo $language; ?>" style="min-width:150px;">
                                    <option value="en" <?php echo $language == 'en' ? 'selected' : '' ?>><?php esc_html_e('English', 'site-search-360'); ?></option>
                                    <option value="de" <?php echo $language == 'de' ? 'selected' : '' ?>><?php esc_html_e('German', 'site-search-360'); ?></option>
                                    <option value="fr" <?php echo $language == 'fr' ? 'selected' : '' ?>><?php esc_html_e('French', 'site-search-360'); ?></option>
                                    <option value="nl" <?php echo $language == 'nl' ? 'selected' : '' ?>><?php esc_html_e('Dutch', 'site-search-360'); ?></option>
                                </select>
                            <td>
                                <?php esc_html_e('The language of the search interface.', 'site-search-360'); ?><br/>
                                <strong><?php esc_html_e('Please note:', 'site-search-360'); ?></strong>
                                <?php esc_html_e('The selected option won\'t be reflected on the configuration pages.', 'site-search-360') ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><label for="allowCookies"><?php esc_html_e('Cookies', 'site-search-360') ?></label></strong></td>
                            <td><label class="checkbox"><?php esc_html_e('enabled', 'site-search-360') ?><input class="fake-hide" type="checkbox" id="allowCookies" name="allowCookies" <?php echo $ss360_allowCookies ? 'checked':''?>/><span class="checkbox_checkmark"></span></label></td>
                            <td><?php esc_html_e('Whether to allow Site Search 360 to create cookies.', 'site-search-360'); ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php esc_html_e('Tracking', 'site-search-360') ?></strong></td>
                            <td class="flex" style="flex-wrap: wrap;padding-top:2em;">
                                <label <?php echo $ss360_is_full ? '': 'style="display:none;"' ?> class="checkbox p-b-0-5 p-r-0-5"><?php esc_html_e('Google Tag Manager', 'site-search-360') ?><input class="fake-hide" type="checkbox" name="tracking.providers.gtm" <?php echo $tracking_providers_gtm ? 'checked' : ''?>/><span class="checkbox_checkmark"></span></label>
                                <label <?php echo $ss360_is_full ? '': 'style="display:none;"' ?> class="checkbox p-b-0-5 p-r-0-5"><?php esc_html_e('Google Analytics', 'site-search-360') ?><input class="fake-hide" type="checkbox" name="tracking.providers.ga" <?php echo $tracking_providers_ga ? 'checked' : ''?>/><span class="checkbox_checkmark"></span></label>
                            </td>
                            <td><?php esc_html_e('Which tracking providers to use to track search interactions.', 'site-search-360'); ?></td>
                        </tr>
                        <tr <?php echo $ss360_is_full ? '': 'style="display:none;"' ?>>
                            <td><strong><label for="voiceSearch-enabled"><?php esc_html_e('Voice Search', 'site-search-360') ?></label></strong></td>
                            <td>
                                <label class="checkbox"><?php esc_html_e('enabled', 'site-search-360') ?><input id="voiceSearch-enabled" class="fake-hide" type="checkbox" name="voiceSearch.enabled" <?php echo $voiceSearch_enabled ? 'checked' : ''?>/><span class="checkbox_checkmark"></span></label>
                            </td>
                            <td><?php esc_html_e('Whether to add the voice search option (if enabled, a microphone icon will be automatically added to your search box, only available for certain browsers).', 'site-search-360'); ?></td>
                        </tr>
                        <tr class="voicesearch-lang" <?php echo $voiceSearch_enabled && $ss360_is_full ? '' : 'style="display:none;"'?>>
                            <td><strong><label for="voiceSearch--lang"><?php esc_html_e('Voice Search Language', 'site-search-360') ?></label></strong></td>
                            <td>
                                <select id="voiceSearch--lang" name="voiceSearch.lang" class="input select m-b-0" value="<?php echo $voiceSearch_lang; ?>" style="min-width:150px;">
                                    <?php foreach($ss360_langs as $ss360_lang){ ?>
                                        <option value="<?php echo $ss360_lang[0]; ?>" <?php echo $ss360_lang[0] == $voiceSearch_lang ? 'selected' : ''?>><?php echo $ss360_lang[1]; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td><?php esc_html_e('The language of the voice search.', 'site-search-360'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </section>
            <div class="flex flex--center w-100 m-t-1">
                <button class="button button--padded" type="submit"><?php esc_html_e('Save', 'site-search-360'); ?></button>
            </div>
        </form>
    </section>
</section>


<script type="text/javascript">
(function(){
    // colors
    var hiddenColor = jQuery("#style--themeColor");
    var color = jQuery("#color--i");
    var preview = jQuery("#color--p");
    var colorize = jQuery(".colorize");
    preview.on("click", function(){
        hiddenColor.click();
    })
    hiddenColor.on("change", function(e){
        color.val(e.target.value);
        preview.css("background", e.target.value);
        colorize.css("background", e.target.value);    
    });
    color.on("keyup", function(e){
        var color = e.target.value;
        if(color.indexOf("#")!==0){
            color = "#" + color;
        }
        var isValidInput  = /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(color);
        if(isValidInput){
            if(color.length===4){
                color = "#" + color[1] + color[1] + color[2] + color[2] + color[3] + color[3];
            }
            hiddenColor.val(color);
            preview.css("background", color);
            colorize.css("background", color);
        }
    });
    var hiddenColorAccent = jQuery('#style--accentColor');
    var accentColor = jQuery('#color--ia');
    var accentColorPreview = jQuery('#color--pa');
    accentColorPreview.on("click", function(){
        hiddenColorAccent.click();
    })
    hiddenColorAccent.on("change", function(e){
        accentColor.val(e.target.value);
        accentColorPreview.css("background", e.target.value);
    });
    accentColor.on("keyup", function(e){
        var color = e.target.value;
        if(color.indexOf("#")!==0){
            color = "#" + color;
        }
        var isValidInput  = /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(color);
        if(isValidInput){
            if(color.length===4){
                color = "#" + color[1] + color[1] + color[2] + color[2] + color[3] + color[3];
            }
            hiddenColorAccent.val(color);
            accentColorPreview.css("background", color);
        }
    });

    // animations
    var animations = jQuery(".animate");
    jQuery("#style--animationSpeed").on("change", function(e){
        var animationSpeed = 1000 + parseInt(e.target.value);
        if(animationSpeed === 1000){
            animationSpeed = 0;
        }
        animations.css("animation-duration", (animationSpeed/1000) + 's');
    });

    // voice search
    var voiceSearchLang = jQuery(".voicesearch-lang");
    jQuery("#voiceSearch-enabled").on("change", function(e){
        if(e.target.checked){
            voiceSearchLang.show();
        }else {
            voiceSearchLang.hide();
        }
    });
    
    jQuery(".message__close").on("click", function(e){
        jQuery(e.target).parents(".message").fadeOut();
    });
}());
</script>
<script src="<?php echo plugins_url('assets/sitesearch360_admin_scripts.js',  __FILE__)  ?>" async></script>