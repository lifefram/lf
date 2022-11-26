<?php
/*
Plugin Name: Search In Place
Plugin URI: https://searchinplace.dwbooster.com
Version: 1.0.103
Author: CodePeople
Author URI: https://searchinplace.dwbooster.com
Text Domain: search-in-place
Description: Search in Place improves blog search by displaying query results in real time. Search in place displays a list with results dynamically as you enter the search criteria. Search in place groups search results by their type, labeling them as post, page, or attachment. To get started: 1) Click the "Activate" link to the left of this description.
*/

if ( ! defined( 'SEARCH_IN_PLACE_VERSION' ) ) {
	define( 'SEARCH_IN_PLACE_VERSION', '1.0.103' );
}

require_once 'banner.php';
$codepeople_promote_banner_plugins['codepeople-search-in-place'] = array(
	'plugin_name' => 'Search in Place',
	'plugin_url'  => 'https://wordpress.org/support/plugin/search-in-place/reviews/#new-post',
);

require 'php/searchinplace.clss.php';
add_filter( 'option_sbp_settings', 'search_in_place_troubleshoot' );
if ( ! function_exists( 'search_in_place_troubleshoot' ) ) {
	function search_in_place_troubleshoot( $option ) {
		if ( ! is_admin() ) {
			// Solves a conflict caused by the "Speed Booster Pack" plugin
			if ( is_array( $option ) && isset( $option['jquery_to_footer'] ) ) {
				unset( $option['jquery_to_footer'] );
			}
		}
		return $option;
	} // End search_in_place_troubleshoot
}

	// Initialize the admin panel
if ( ! function_exists( 'CodePeopleSearchInPlace_admin' ) ) {
	function CodePeopleSearchInPlace_admin() {
		global $codepeople_search_in_place_obj;
		if ( ! isset( $codepeople_search_in_place_obj ) ) {
			return;
		}
		if ( function_exists( 'add_options_page' ) ) {
			$slug = basename( __FILE__ );
			add_options_page( 'Search In Place', 'Search In Place', 'manage_options', $slug, array( &$codepeople_search_in_place_obj, 'printAdminPage' ) );

			add_menu_page( 'Search In Place', 'Search In Place', 'manage_options', $slug, array( &$codepeople_search_in_place_obj, 'printAdminPage' ) );

			add_submenu_page( $slug, 'Upgrade', 'Upgrade', 'manage_options', 'search_in_place_upgrade', array( &$codepeople_search_in_place_obj, 'printAdminPage' ) );

			add_submenu_page( $slug, 'Online Help', 'Online Help', 'manage_options', 'search_in_place_help', array( &$codepeople_search_in_place_obj, 'printAdminPage' ) );
		}
	}
}

	// Initialize the public website code
if ( ! function_exists( 'CodePeopleSearchInPlace' ) ) {
	function CodePeopleSearchInPlace() {
		global $codepeople_search_in_place_obj;

		add_shortcode( 'search-in-place-form', array( $codepeople_search_in_place_obj, 'get_search_form' ) );

		if ( is_admin() ) {
			return false;
		}

		wp_enqueue_style( 'codepeople-search-in-place-style', plugin_dir_url( __FILE__ ) . 'css/codepeople_shearch_in_place.min.css', array(), SEARCH_IN_PLACE_VERSION );
		wp_enqueue_script( 'codepeople-search-in-place', plugin_dir_url( __FILE__ ) . 'js/codepeople_shearch_in_place.min.js', array( 'jquery' ), SEARCH_IN_PLACE_VERSION );
		wp_localize_script( 'codepeople-search-in-place', 'codepeople_search_in_place', $codepeople_search_in_place_obj->javascriptVariables() );
	}
}

global $codepeople_search_in_place_obj;
$codepeople_search_in_place_obj = new CodePeopleSearchInPlace();
$codepeople_search_in_place_obj->init();

$cpsp_plugin = plugin_basename( __FILE__ );

// Feedback system
require_once 'feedback/cp-feedback.php';
new CP_FEEDBACK( plugin_basename( dirname( __FILE__ ) ), __FILE__, 'https://searchinplace.dwbooster.com/contact-us' );


add_filter( 'plugin_action_links_' . $cpsp_plugin, array( &$codepeople_search_in_place_obj, 'customizationLink' ) );
add_filter( 'plugin_action_links_' . $cpsp_plugin, array( &$codepeople_search_in_place_obj, 'settingsLink' ) );

add_action( 'init', 'CodePeopleSearchInPlace' );
add_action( 'admin_menu', 'CodePeopleSearchInPlace_admin' );
add_action( 'wp_ajax_nopriv_search_in_place', array( &$codepeople_search_in_place_obj, 'populate' ) );
add_action( 'wp_ajax_search_in_place', array( &$codepeople_search_in_place_obj, 'populate' ) );
if ( ! get_option( 'search_in_place_own_only' ) || ! empty( $_REQUEST['search_in_place_form'] ) ) {
	add_action( 'pre_get_posts', array( &$codepeople_search_in_place_obj, 'modifySearch' ) );
	add_filter( 'posts_request', array( &$codepeople_search_in_place_obj, 'modifySearchQuery' ), 99, 2 );
}
add_action( 'wp_head', array( &$codepeople_search_in_place_obj, 'setStyles' ) );
