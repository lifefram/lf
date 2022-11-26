<?php

$ss360_result = array('action' => 'init');

function ss360GetInputPath($detected){
    return $detected!=null && !empty($detected) && $detected!='#searchBox' ? $detected : null;
}

if ((!empty($_POST) && isset($_POST['_wpnonce'])) || (!empty($_GET) && isset($_GET['action']) && $_GET['action'] == 'init')) {

    if ($_POST['action'] == 'ss360_register') {

        $ss360_client = new SiteSearch360Client();
        $ss360_result = $ss360_client->register($_POST['email'], $_POST['domain']);
        
        if ($ss360_result['status'] == 'success') {
            update_option('ss360_account_created', true);
            update_option('ss360_siteId', $ss360_result['siteId']);
            update_option('ss360_api_token', $ss360_result['apiKey']);
            update_option('ss360_config_modifications', 0);
			update_option('ss360_woocommerce_categories', true);
            
            $ss360_client = new SiteSearch360Client();
            $ss360_client->ensureSortDataPointExists(esc_html__('Date', 'site-search-360'));            
            
            $ss360_plugin = new SiteSearch360Plugin();
            // isset($ss360_result['searchFieldPath']) && $ss360_result['searchFieldPath'] != '#searchBox' ? $ss360_result['searchFieldPath'] : null
            $ss360_configuration = $ss360_plugin->createInitialConfig($ss360_result['siteId'], ss360GetInputPath($ss360_result['searchFieldPath']), ss360GetInputPath($ss360_result['searchButtonPath']), sizeof(get_categories())>1);
            $ss360_plugin->saveConfig($ss360_configuration);
            
            $ss360_result['action'] = $_POST['action'];

            
            include('views/sitesearch360-intro-carousel.php');
            
        } else {
            update_option('ss360_account_created', false);
            include('views/sitesearch360-login-page.php');
        }
        
    } else if($_POST['action']=='ss360_login'){
        $ss360_client = new SiteSearch360Client();
        $ss360_result = $ss360_client->login($_POST['email'], $_POST['password']);
        
        if($ss360_result['status'] == 'success'){
            $ss360_plugin = new SiteSearch360Plugin();
            update_option('ss360_account_created', true);
            update_option('ss360_siteId', $ss360_result['siteId']);
            update_option('ss360_api_token', $ss360_result['apiKey']);
			update_option('ss360_config_modifications', 0);
			update_option('ss360_woocommerce_categories', true);

            $ss360_was_logged_in = get_option('ss360_config') !== NULL && get_option('ss360_config')!==FALSE;
            if($ss360_was_logged_in){
                $ss360_config = $ss360_plugin->getConfig();
                $ss360_config['siteId'] = $ss360_result['siteId'];
                $ss360_plugin->saveConfig($ss360_config);
                update_option('ss360_is_configured', true);
            } else {
                $ss360_client = new SiteSearch360Client();
                $detected_components = $ss360_client->detectInputs();
                $has_content_groups = $ss360_client->hasContentGroups();
                $detected_searchBox = isset($detected_components['searchBox']) ? ss360GetInputPath($detected_components['searchBox']) : NULL;
                $detected_searchButton = isset($detected_components['searchBox']) ? ss360GetInputPath($detected_components['searchButton']) : NULL;
                $ss360_configuration = $ss360_plugin->createInitialConfig($ss360_result['siteId'], $detected_searchBox, $detected_searchButton, $has_content_groups); 
                $ss360_plugin->saveConfig($ss360_configuration);
                update_option('ss360_indexing_mode', 'crawler');
            }
            
            update_option('ss360_is_indexed', true);
            
            include('views/sitesearch360-dashboard.php');
        }else {
            include('views/sitesearch360-login-page.php');
        }
        
    } else if($_POST['action']=='ss360_setType'){
        if(isset($_POST['ss360_sr_type'])){
            update_option('ss360_sr_type', $_POST['ss360_sr_type']);
        }
        include('views/sitesearch360-dashboard.php');
    }else if($_POST['action']=='ss360_updateInputs'){
        $ss360_configuration = json_decode(get_option('ss360_config'), true);
        $ss360_configuration['searchBox']['selector'] = isset($_POST['searchBoxSelector']) ? stripslashes($_POST['searchBoxSelector']) : '#searchBox, form[role="search"] input[name="s"]';
        $ss360_configuration['searchBox']['searchButton'] = isset($_POST['searchButtonSelector']) ? stripslashes($_POST['searchButtonSelector']) : 'form[role="search"] input.search-submit';
        $ss360_plugin = new SiteSearch360Plugin();
        $ss360_plugin->saveConfig($ss360_configuration);
        include('views/sitesearch360-dashboard.php');
    }else if($_POST['action']=='ss360_personalize'){
        $ss360_configuration = json_decode(get_option('ss360_config'), true);
        $changed = array();
        if(isset($_POST['themeColor'])){
            if($_POST['themeColor'] != '#4A4F62'){
                $changed[] = 'color';
            }
            $ss360_configuration['style']['themeColor'] = $_POST['themeColor'];
        }
        if(isset($_POST['accentColor'])){
            if($_POST['accentColor'] != '#3D8FFF') {
                $changed[] = 'accentColor';
            }
            $ss360_configuration['style']['accentColor'] = $_POST['accentColor'];
        }
        if(isset($_POST['layout'])){
            if($_POST['layout']!='list'){
                $changed[] = 'layout';
            }
            $ss360_configuration['layout']['mobile']['type'] = $_POST['layout'];
            $ss360_configuration['layout']['desktop']['type'] = $_POST['layout'];
        }
        if(isset($_POST['loader'])){
            if($_POST['loader']!='skeleton'){
                $changed[] = 'loader';
            }
            $ss360_configuration['style']['loaderType'] = $_POST['loader'];
        }
        if(!isset($ss360_configuration['voiceSearch'])){
            $ss360_configuration['voiceSearch'] = array();
        }
        if(isset($_POST['voiceSearch']) && $_POST['voiceSearch']=='on'){
            $ss360_configuration['voiceSearch']['enabled'] = true;
        }else {
            $changed[] = 'voiceSearch';
            $ss360_configuration['voiceSearch']['enabled'] = false;
        }
        $ss360_plugin = new SiteSearch360Plugin();
        $ss360_plugin->saveConfig($ss360_configuration);
        include('views/sitesearch360-dashboard.php');
    } else if($_POST['action']=='ss360_logout') {
        update_option('ss360_account_created', false);
        delete_option('ss360_siteId');
        delete_option('ss360_api_token');
        $ss360_is_logging_in = true;
        include('views/sitesearch360-login-page.php');
    } else if($_POST['action'] === 'ss360_enablePlugin') {
		update_option('ss360_inject_search', isset($_POST['ss360Enabled']) ? 1 : 0);
        include('views/sitesearch360-dashboard.php');
	} else {
        include('views/sitesearch360-dashboard.php');
    }
} else {
    // user was already registered and wants to use same email address, so we set this as a success here
    if (!empty($_GET) && isset($_GET['action']) && $_GET['action'] == 'configure') {
        update_option('ss360_account_created', true);
    }

    $ss360_accountCreated = get_option('ss360_account_created');
    if ($ss360_accountCreated) {
        include('views/sitesearch360-dashboard.php');
    } else {
        include('views/sitesearch360-login-page.php');
    }
}