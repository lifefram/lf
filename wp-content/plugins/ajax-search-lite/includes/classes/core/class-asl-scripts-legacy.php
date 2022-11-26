<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

/**
 * @deprecated Will be removed @2022 Q1
 */
class WD_ASL_Scripts_Legacy  {
	private static $_instance;
	private function __construct() {}

	public function enqueue() {
		$com_opt = wd_asl()->o['asl_compatibility'];
		$exit1 = apply_filters('asl_load_css_js', false);
		$exit2 = apply_filters('asl_load_js', false);
		if ( $exit1 || $exit2 )
			return false;

		$single_highlight = false;
		$single_highlight_arr = array();
		$search = wd_asl()->instances->get();
		if (is_array($search) && count($search)>0) {
			foreach ($search as $s) {
				// $style and $id needed in the include
				if ( $s['data']['single_highlight'] == 1 ) {
					$single_highlight = true;
					$single_highlight_arr[] = array(
						'id' => $s['id'],
						'selector' => $s['data']['single_highlight_selector'],
						'scroll' => $s['data']['single_highlight_scroll'] == 1,
						'scroll_offset' => intval($s['data']['single_highlight_offset']),
						'whole' => $s['data']['single_highlightwholewords'] == 1,
					);
				}
			}
		}

		$performance_options = wd_asl()->o['asl_performance'];
		$analytics = wd_asl()->o['asl_analytics'];

		$prereq = 'jquery';
		$js_source = $com_opt['js_source'];
		$scripts = array();

		$load_in_footer = w_isset_def($performance_options['load_in_footer'], 1) == 1 ? true : false;
		$load_mcustom = w_isset_def($com_opt['load_scroll_js'], "yes") == "yes";

		if ($js_source == 'nomin' || $js_source == 'nomin-scoped') {
			if ($js_source == "nomin-scoped") {
				$prereq = "wpdreams-asljquery";
				wp_register_script('wpdreams-asljquery', ASL_URL . 'js/legacy/' . $js_source . '/asljquery.js', array(), ASL_CURR_VER_STRING, $load_in_footer);
				wp_enqueue_script('wpdreams-asljquery');
				$scripts[] = ASL_URL . 'js/legacy/' . $js_source . '/asljquery.js';
			}
			wp_register_script('wpdreams-highlight', ASL_URL . 'js/legacy/' . $js_source . '/jquery.highlight.js', array($prereq), ASL_CURR_VER_STRING, $load_in_footer);
			wp_enqueue_script('wpdreams-highlight');
			$scripts[] = ASL_URL . 'js/legacy/' . $js_source . '/jquery.highlight.js';
			if ( $load_mcustom ) {
				wp_register_script('wpdreams-scroll', ASL_URL . 'js/legacy/' . $js_source . '/simplebar.js', array($prereq), ASL_CURR_VER_STRING, $load_in_footer);
				wp_enqueue_script('wpdreams-scroll');
				$scripts[] = ASL_URL . 'js/legacy/' . $js_source . '/simplebar.js';
			}
			wp_register_script('wpdreams-ajaxsearchlite', ASL_URL . 'js/legacy/' . $js_source . '/jquery.ajaxsearchlite.js', array($prereq), ASL_CURR_VER_STRING, $load_in_footer);
			wp_enqueue_script('wpdreams-ajaxsearchlite');
			$scripts[] = ASL_URL . 'js/legacy/' . $js_source . '/jquery.ajaxsearchlite.js';

			wp_register_script('wpdreams-asl-wrapper', ASL_URL . 'js/legacy/' . $js_source . '/asl_wrapper.js', array($prereq, "wpdreams-ajaxsearchlite"), ASL_CURR_VER_STRING, $load_in_footer);
			wp_enqueue_script('wpdreams-asl-wrapper');
			$scripts[] = ASL_URL . 'js/legacy/' . $js_source . '/asl_wrapper.js';
		} else {
			wp_enqueue_script('jquery');
			wp_register_script('wpdreams-ajaxsearchlite', ASL_URL . "js/legacy/" . $js_source . "/jquery.ajaxsearchlite.min.js", array(), ASL_CURR_VER_STRING, $load_in_footer);
			wp_enqueue_script('wpdreams-ajaxsearchlite');
			$scripts[] = ASL_URL . 'js/legacy/' . $js_source . '/jquery.ajaxsearchlite.min.js';
		}

		$ajax_url = admin_url('admin-ajax.php');
		if ( w_isset_def($performance_options['use_custom_ajax_handler'], 0) == 1 )
			$ajax_url = ASL_URL . 'ajax_search.php';

		if (strpos($com_opt['js_source'], 'min-scoped') !== false) {
			$scope = "asljQuery";
		} else {
			$scope = "jQuery";
		}

		ASL_Helpers::addInlineScript( 'wpdreams-ajaxsearchlite', 'ASL', array(
			'wp_rocket_exception' => 'DOMContentLoaded',	// WP Rocket hack to prevent the wrapping of the inline script: https://docs.wp-rocket.me/article/1265-load-javascript-deferred
			'ajaxurl' => $ajax_url,
			'backend_ajaxurl' => admin_url( 'admin-ajax.php'),
			'js_scope' => $scope,
			'detect_ajax' => $com_opt['detect_ajax'],
			'scrollbar' => $load_mcustom,
			'version' => ASL_CURRENT_VERSION,
			'min_script_src' => $scripts,
			'highlight' => array(
				'enabled' => $single_highlight,
				'data' => $single_highlight_arr
			),
			'analytics' => array(
				'method' => $analytics['analytics'],
				'tracking_id' => $analytics['analytics_tracking_id'],
				'string' => $analytics['analytics_string'],
				'event' => array(
					'focus' => array(
						'active' => $analytics['gtag_focus'],
						'action' => $analytics['gtag_focus_action'],
						"category" => $analytics['gtag_focus_ec'],
						"label" =>  $analytics['gtag_focus_el'],
						"value" => $analytics['gtag_focus_value']
					),
					'search_start' => array(
						'active' => $analytics['gtag_search_start'],
						'action' => $analytics['gtag_search_start_action'],
						"category" => $analytics['gtag_search_start_ec'],
						"label" =>  $analytics['gtag_search_start_el'],
						"value" => $analytics['gtag_search_start_value']
					),
					'search_end' => array(
						'active' => $analytics['gtag_search_end'],
						'action' => $analytics['gtag_search_end_action'],
						"category" => $analytics['gtag_search_end_ec'],
						"label" =>  $analytics['gtag_search_end_el'],
						"value" => $analytics['gtag_search_end_value']
					),
					'magnifier' => array(
						'active' => $analytics['gtag_magnifier'],
						'action' => $analytics['gtag_magnifier_action'],
						"category" => $analytics['gtag_magnifier_ec'],
						"label" =>  $analytics['gtag_magnifier_el'],
						"value" => $analytics['gtag_magnifier_value']
					),
					'return' => array(
						'active' => $analytics['gtag_return'],
						'action' => $analytics['gtag_return_action'],
						"category" => $analytics['gtag_return_ec'],
						"label" =>  $analytics['gtag_return_el'],
						"value" => $analytics['gtag_return_value']
					),
					'facet_change' => array(
						'active' => $analytics['gtag_facet_change'],
						'action' => $analytics['gtag_facet_change_action'],
						"category" => $analytics['gtag_facet_change_ec'],
						"label" =>  $analytics['gtag_facet_change_el'],
						"value" => $analytics['gtag_facet_change_value']
					),
					'result_click' => array(
						'active' => $analytics['gtag_result_click'],
						'action' => $analytics['gtag_result_click_action'],
						"category" => $analytics['gtag_result_click_ec'],
						"label" =>  $analytics['gtag_result_click_el'],
						"value" => $analytics['gtag_result_click_value']
					)
				)
			)
		), 'before', true);
	}

	/**
	 * Get the instane
	 *
	 * @return self
	 */
	public static function getInstance() {
		if (!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
}