<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Register the categories
Plugin::$instance->elements_manager->add_category(
	'search-in-place-cat',
	array(
		'title' => 'Search in Place',
		'icon'  => 'fa fa-plug',
	),
	2 // position
);
