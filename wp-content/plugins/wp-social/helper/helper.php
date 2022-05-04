<?php

namespace WP_Social\Helper;

defined('ABSPATH') || exit;

class Helper {

	public static function is_true($val1, $val2, $print) {
		return esc_attr($val1 === $val2 ? $print : '');
	}

	public static function sanitize_white_list($val, $def, $white_list) {

		if(in_array($val, $white_list)) {

			return $val;
		}

		return $def;
	}

}
