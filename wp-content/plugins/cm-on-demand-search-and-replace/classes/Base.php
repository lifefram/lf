<?php

class CMODSAR_Base
{
    protected static $filePath = '';
    protected static $cssPath = '';
    protected static $jsPath = '';
    public static $lastQueryDetails = array();
    public static $calledClassName;

    const PAGE_YEARLY_OFFER = 'https://www.cminds.com/store/cm-wordpress-plugins-yearly-membership/';

    public static function init()
    {
        self::setupConstants();

        self::includeFiles();

        self::initFiles();

        self::addOptions();

        if( empty(self::$calledClassName) )
        {
            self::$calledClassName = __CLASS__;
        }

        $file = basename(__FILE__);
        $folder = CMODSAR_PLUGIN_DIR;
        $hook = "in_plugin_update_message-{$folder}/{$file}";
        add_action($hook, array(self::$calledClassName, 'cmodsar_warn_on_upgrade'));

        self::$filePath = CMODSAR_PLUGIN_URL;
        self::$cssPath = self::$filePath . 'assets/css/';
        self::$jsPath = self::$filePath . 'assets/js/';

        add_action('admin_menu', array(self::$calledClassName, 'cmodsar_admin_menu'));

        add_action('admin_enqueue_scripts', array(self::$calledClassName, 'cmodsar_custom_admin_settings_scripts'));
        add_action('admin_enqueue_scripts', array(self::$calledClassName, 'cmodsar_custom_admin_edit_scripts'));

        add_action('admin_notices', array(self::$calledClassName, 'cmodsar_custom_admin_notice_wp33'));
        add_action('admin_notices', array(self::$calledClassName, 'cmodsar_custom_admin_notice_mbstring'));
    }

    /**
     * Include the files
     */
    public static function includeFiles()
    {
        do_action('cmodsar_include_files_before');

        include_once CMODSAR_PLUGIN_DIR . "/package/cminds-free.php";
        include_once CMODSAR_PLUGIN_DIR . '/classes/Replacement.php';
        include_once CMODSAR_PLUGIN_DIR . "functions.php";

        do_action('cmodsar_include_files_after');
    }

    /**
     * Initialize the files
     */
    public static function initFiles()
    {
        do_action('cmodsar_init_files_before');

        CMODSAR_Replacement::init();

        do_action('cmodsar_init_files_after');
    }

    /**
     * Adds options
     */
    public static function addOptions()
    {
        /*
         * General settings
         */
        add_option('cmodsar_searchAndReplaceOnPosttypes', array('post', 'page'));
        do_action('cmodsar_add_options');
    }

    /**
     * Setup plugin constants
     *
     * @access private
     * @since 1.1
     * @return void
     */
    public static function setupConstants()
    {
        if( !defined('CMODSAR_MENU_OPTION') )
        {
            define('CMODSAR_MENU_OPTION', 'cmodsar_menu_options');
        }

        define('CMODSAR_ABOUT_OPTION', 'outputAbout');
        define('CMODSAR_SETTINGS_OPTION', 'cmodsar_settings');
        define('CMODSAR_PRO_OPTION', 'cmodsar_pro');

        do_action('cmodsar_setup_constants_after');
    }

    public static function cmodsar_admin_menu()
    {
        global $submenu;
        add_menu_page('Search And Replace', CMODSAR_NAME, 'edit_posts', CMODSAR_SETTINGS_OPTION, '', CMODSAR_PLUGIN_URL . 'assets/css/images/cm-custom-cm-search-and-replace-icon.png');

        add_submenu_page(CMODSAR_SETTINGS_OPTION, 'Settings', 'Settings', 'manage_options', CMODSAR_SETTINGS_OPTION, array(self::$calledClassName, 'outputOptions'));

        $customItemsPerPage = get_user_meta(get_current_user_id(), 'edit_custom_per_page', true);
        if( $customItemsPerPage && intval($customItemsPerPage) > 100 )
        {
            update_user_meta(get_current_user_id(), 'edit_custom_per_page', 100);
        }

        add_filter('views_edit-custom', array(self::$calledClassName, 'cmodsar_filter_admin_nav'), 10, 1);
    }

    /**
     * Function enqueues the scripts and styles for the admin Settings view
     * @global type $parent_file
     * @return type
     */
    public static function cmodsar_custom_admin_settings_scripts()
    {
        global $parent_file;
        if( CMODSAR_SETTINGS_OPTION !== $parent_file )
        {
            return;
        }

        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-tooltip');
        wp_enqueue_script('jquery-ui-tabs');

        wp_enqueue_style('jqueryUIStylesheet', self::$cssPath . 'jquery-ui-1.10.3.custom.css');
        wp_enqueue_style('cm-search-and-replace', self::$cssPath . 'cm-search-and-replace.css');
        wp_enqueue_style('cm-search-and-replace-timepicker-css', self::$cssPath . 'jquery-ui-timepicker-addon.min.css');

        wp_enqueue_script('cm-search-and-replace-timepicker-js', self::$jsPath . 'jquery-ui-timepicker-addon.min.js', array('jquery-ui-core' ,'jquery-ui-datepicker', 'jquery-ui-slider'));
        wp_enqueue_script('cm-search-and-replace-admin-js', self::$jsPath . 'cm-search-and-replace-admin.js', array('cm-search-and-replace-timepicker-js'));

        $searchAndReplace['ajaxurl'] = admin_url('admin-ajax.php');
        wp_localize_script('cm-search-and-replace-admin-js', 'cmodsar_data', $searchAndReplace);
    }

    /**
     * Function outputs the scripts and styles for the edit views
     * @global type $typenow
     * @return type
     */
    public static function cmodsar_custom_admin_edit_scripts()
    {
        global $typenow;

        $defaultPostTypes = get_option('cmodsar_allowed_terms_metabox_all_post_types') ? get_post_types() : array('post', 'page');
        $allowedTermsBoxPostTypes = apply_filters('cmodsar_allowed_terms_metabox_posttypes', $defaultPostTypes);

        if( !in_array($typenow, $allowedTermsBoxPostTypes) )
        {
            return;
        }

        wp_enqueue_style('cm-search-and-replace', self::$cssPath . 'cm-search-and-replace.css');
    }

    /**
     * Filters admin navigation menus to show horizontal link bar
     * @global string $submenu
     * @global type $plugin_page
     * @param type $views
     * @return string
     */
    public static function cmodsar_filter_admin_nav($views)
    {
        global $submenu, $plugin_page;
        $scheme = is_ssl() ? 'https://' : 'http://';
        $adminUrl = str_replace($scheme . $_SERVER['HTTP_HOST'], '', admin_url());
        $currentUri = str_replace($adminUrl, '', $_SERVER['REQUEST_URI']);
        $submenus = array();
        if( isset($submenu[CMODSAR_SETTINGS_OPTION]) )
        {
            $thisMenu = $submenu[CMODSAR_SETTINGS_OPTION];

            $firstMenuItem = $thisMenu[0];
            unset($thisMenu[0]);

            $secondMenuItem = array('Trash', 'edit_posts', 'edit.php?post_status=trash&post_type=custom', 'Trash');

            array_unshift($thisMenu, $firstMenuItem, $secondMenuItem);

            foreach($thisMenu as $item)
            {
                $slug = $item[2];
                $isCurrent = ($slug == $plugin_page || strpos($item[2], '.php') === strpos($currentUri, '.php'));
                $isExternalPage = strpos($item[2], 'http') !== FALSE;
                $isNotSubPage = $isExternalPage || strpos($item[2], '.php') !== FALSE;
                $url = $isNotSubPage ? $slug : get_admin_url(null, 'admin.php?page=' . $slug);
                $target = $isExternalPage ? '_blank' : '';
                $submenus[$item[0]] = '<a href="' . $url . '" target="' . $target . '" class="' . ($isCurrent ? 'current' : '') . '">' . $item[0] . '</a>';
            }
        }
        return $submenus;
    }

    /**
     * Displays the horizontal navigation bar
     * @global string $submenu
     * @global type $plugin_page
     */
    public static function cmodsar_showNav()
    {
        global $submenu, $plugin_page;
        $submenus = array();
        $scheme = is_ssl() ? 'https://' : 'http://';
        $adminUrl = str_replace($scheme . $_SERVER['HTTP_HOST'], '', admin_url());
        $currentUri = str_replace($adminUrl, '', $_SERVER['REQUEST_URI']);

        if( isset($submenu[CMODSAR_SETTINGS_OPTION]) )
        {
            $thisMenu = $submenu[CMODSAR_SETTINGS_OPTION];
            foreach($thisMenu as $item)
            {
                $slug = $item[2];
                $isCurrent = ($slug == $plugin_page || strpos($item[2], '.php') === strpos($currentUri, '.php'));
                $isExternalPage = strpos($item[2], 'http') !== FALSE;
                $isNotSubPage = $isExternalPage || strpos($item[2], '.php') !== FALSE;
                $url = $isNotSubPage ? $slug : get_admin_url(null, 'admin.php?page=' . $slug);
                $submenus[] = array(
                    'link'    => $url,
                    'title'   => $item[0],
                    'current' => $isCurrent,
                    'target'  => $isExternalPage ? '_blank' : ''
                );
            }
            require CMODSAR_PLUGIN_DIR . 'views/backend/admin_nav.php';
        }
    }

    /**
     * Adds a notice about wp version lower than required 3.3
     * @global type $wp_version
     */
    public static function cmodsar_custom_admin_notice_wp33()
    {
        global $wp_version;

        if( version_compare($wp_version, '3.3', '<') )
        {
            $message = sprintf(CMODSAR_Base::__('%s requires Wordpress version 3.3 or higher to work properly.'), CMODSAR_NAME);
            cminds_show_message($message, true);
        }
    }

    /**
     * Adds a notice about mbstring not being installed
     * @global type $wp_version
     */
    public static function cmodsar_custom_admin_notice_mbstring()
    {
        $mb_support = function_exists('mb_strtolower');

        if( !$mb_support )
        {
            $message = sprintf(CMODSAR_Base::__('%s since version 2.6.0 requires "mbstring" PHP extension to work! '), CMODSAR_NAME);
            $message .= '<a href="http://www.php.net/manual/en/mbstring.installation.php" target="_blank">(' . CMODSAR_Base::__('Installation instructions.') . ')</a>';
            cminds_show_message($message, true);
        }
    }

    /**
     * Function responsible for saving the options
     */
    public static function saveOptions()
    {
        $messages = '';
        $_POST = array_map('stripslashes_deep', $_POST);
        $post = $_POST;

        if( isset($post["cmodsar_customSave"]) )
        {
            do_action('cmodsar_save_options_berfore', $post, $messages);

            function cmodsar_get_the_option_names($k)
            {
                return strpos($k, 'cmodsar_') === 0;
            }

            $options_names = apply_filters('cmodsar_thirdparty_option_names', array_filter(array_keys($post), 'cmodsar_get_the_option_names'));

            foreach($options_names as $option_name)
            {
                if( !isset($post[$option_name]) )
                {
                    update_option($option_name, 0);
                }
                else
                {
                    if( $option_name == 'cmodsar_index_letters' )
                    {
                        $optionValue = explode(',', $post[$option_name]);
                        $optionValue = array_map('mb_strtolower', $optionValue);
                    }
                    else
                    {
                        $optionValue = is_array($post[$option_name]) ? $post[$option_name] : trim($post[$option_name]);
                    }
                    update_option($option_name, $optionValue);
                }
            }
            do_action('cmodsar_save_options_after_on_save', $post, array(&$messages));
        }

        do_action('cmodsar_save_options_after', $post, array(&$messages));

        if( isset($post['cmodsar_pluginCleanup']) )
        {
            self::_cleanup();
            $messages = 'CM On Demand Search And Replace data  have been removed from the database.';
        }

        return array('messages' => $messages);
    }

    public static function outputPro()
    {
        ob_start();
        require CMODSAR_PLUGIN_DIR . 'views/backend/admin_pro.php';
        $content = ob_get_contents();
        ob_end_clean();

        require CMODSAR_PLUGIN_DIR . 'views/backend/admin_template.php';
    }

    public static function outputAbout()
    {
        ob_start();
        require CMODSAR_PLUGIN_DIR . 'views/backend/admin_about.php';
        $content = ob_get_contents();
        ob_end_clean();

        require CMODSAR_PLUGIN_DIR . 'views/backend/admin_template.php';
    }

    /**
     * Displays the options screen
     */
    public static function outputOptions()
    {
        $result = self::saveOptions();
        $messages = $result['messages'];

        ob_start();
        require CMODSAR_PLUGIN_DIR . 'views/backend/admin_settings.php';
        $content = ob_get_contents();
        ob_end_clean();
        require CMODSAR_PLUGIN_DIR . 'views/backend/admin_template.php';
    }

    public static function cmodsar_warn_on_upgrade()
    {
        ?>
        <div style="margin-top: 1em"><span style="color: red; font-size: larger">
                STOP!</span> Do <em>not</em> click &quot;update automatically&quot; as you will be <em>downgraded</em> to the free version of <?php echo CMODSAR_NAME; ?>.
            Instead, download the Pro update directly from <a href="https://www.cminds.com/guest-account/">https://www.cminds.com/guest-account/</a>.
        </div>
        <div style="font-size: smaller"><?php echo CMODSAR_NAME; ?> does not use WordPress's standard update mechanism. We apologize for the inconvenience!</div>
        <?php
    }

    /**
     * Function renders (default) or returns the setttings tabs
     *
     * @param type $return
     * @return string
     */
    public static function renderSettingsTabs($return = false)
    {
        $content = '';
        $settingsTabsArrayBase = array();

        $settingsTabsArray = apply_filters('cmodsar-settings-tabs-array', $settingsTabsArrayBase);

        if( $settingsTabsArray )
        {
            foreach($settingsTabsArray as $tabKey => $tabLabel)
            {
                $filterName = 'cmodsar-custom-settings-tab-content-' . $tabKey;

                $content .= '<div id="tabs-' . $tabKey . '">';
                $tabContent = apply_filters($filterName, '');
                $content .= $tabContent;
                $content .= '</div>';
            }
        }

        if( $return )
        {
            return $content;
        }
        echo $content;
    }

    /**
     * Function renders (default) or returns the setttings tabs
     *
     * @param type $return
     * @return string
     */
    public static function renderSettingsTabsControls($return = false)
    {
        $content = '';
        $settingsTabsArrayBase = array(
           '55' => 'Upgrade',
            '99' => 'Installation Tutorial',
        );

        $settingsTabsArray = apply_filters('cmodsar-settings-tabs-array', $settingsTabsArrayBase);

        ksort($settingsTabsArray);

        if( $settingsTabsArray )
        {
            $content .= '<ul>';
            foreach($settingsTabsArray as $tabKey => $tabLabel)
            {
                $content .= '<li><a href="#tabs-' . $tabKey . '">' . $tabLabel . '</a></li>';
            }
            $content .= '</ul>';
        }

        if( $return )
        {
            return $content;
        }
        echo $content;
    }

    /**
     * Function cleans up the plugin, removing the terms, resetting the options etc.
     *
     * @return string
     */
    protected static function _cleanup($force = true)
    {
        /*
         * Remove the data from the other tables
         */
        do_action('cmodsar_do_cleanup');

        /*
         * Remove the options
         */
        $optionNames = wp_load_alloptions();

        function cmodsar_get_the_option_names($k)
        {
            return strpos($k, 'cmodsar_') === 0;
        }

        $options_names = array_filter(array_keys($optionNames), 'cmodsar_get_the_option_names');
        foreach($options_names as $optionName)
        {
            delete_option($optionName);
        }
    }

    /**
     * Plugin activation
     */
    protected static function _activate()
    {
        do_action('cmodsar_do_activate');
    }

    /**
     * Plugin installation
     *
     * @global type $wpdb
     * @param type $networkwide
     * @return type
     */
    public static function _install($networkwide)
    {
        global $wpdb;

        if( function_exists('is_multisite') && is_multisite() )
        {
            // check if it is a network activation - if so, run the activation function for each blog id
            if( $networkwide )
            {
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM {$wpdb->blogs}"));
                foreach($blogids as $blog_id)
                {
                    switch_to_blog($blog_id);
                    self::_activate();
                }
                switch_to_blog($old_blog);
                return;
            }
        }

        self::_activate();
    }

    /**
     * Get localized string.
     *
     * @param string $msg
     * @return string
     */
    public static function __($msg)
    {
        return __($msg, CMODSAR_SLUG_NAME);
    }

}