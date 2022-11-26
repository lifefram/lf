<?php
/*
Plugin Name: CM On Demand Search And Replace
Plugin URI: http://www.cminds.com/
Description: Searches and replaces the words and phrases throughout the content.
Version: 1.2.8
Author: CreativeMindsSolutions
Author URI: http://www.cminds.com/
*/

/**
 * Define Plugin Version
 *
 * @since 1.0
 */

if( !defined('CMODSAR_VERSION') )
{
    define('CMODSAR_VERSION', '1.2.8');
}

/**
 * Define Plugin name
 *
 * @since 1.0
 */
if( !defined('CMODSAR_NAME') )
{
    define('CMODSAR_NAME', 'CM On Demand Search And Replace');
}

/**
 * Define Plugin canonical name
 *
 * @since 1.0
 */
if( !defined('CMODSAR_CANONICAL_NAME') )
{
    define('CMODSAR_CANONICAL_NAME', 'CM On Demand Search And Replace');
}

/**
 * Define Plugin license name
 *
 * @since 1.0
 */
if( !defined('CMODSAR_LICENSE_NAME') )
{
    define('CMODSAR_LICENSE_NAME', 'CM On Demand Search And Replace');
}

/**
 * Define Plugin File Name
 *
 * @since 1.0
 */
if( !defined('CMODSAR_PLUGIN_FILE') )
{
    define('CMODSAR_PLUGIN_FILE', __FILE__);
}

/**
 * Define Plugin release notes url
 *
 * @since 1.0
 */
if( !defined('CMODSAR_RELEASE_NOTES') )
{
    define('CMODSAR_RELEASE_NOTES', '');
}

/**
 * Define Plugin Directory
 *
 * @since 1.0
 */
if( !defined('CMODSAR_PLUGIN_DIR') )
{
    define('CMODSAR_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

/**
 * Define Plugin URL
 *
 * @since 1.0
 */
if( !defined('CMODSAR_PLUGIN_URL') )
{
    define('CMODSAR_PLUGIN_URL', plugin_dir_url(__FILE__));
}

/**
 * Define Plugin Slug name
 *
 * @since 1.0
 */
if( !defined('CMODSAR_SLUG_NAME') )
{
    define('CMODSAR_SLUG_NAME', 'cm-on-demand-search-and-replace');
}

/**
 * Define Plugin basename
 *
 * @since 1.0
 */
if( !defined('CMODSAR_PLUGIN') )
{
    define('CMODSAR_PLUGIN', plugin_basename(__FILE__));
}

include_once plugin_dir_path(__FILE__) . "classes/Base.php";
register_activation_hook(__FILE__, array('CMODSAR_Base', '_install'));

CMODSAR_Base::init();