<?php
/**
 * Functions which use the customizer variables
 *
 * @package KnowledgeCenter
 * @subpackage KnowledgeCenter
 * @since KnowledgeCenter 1.0
 */

function knowledgecenter_set_home_title() {
	$settings = get_theme_mod( 'knowledgecenter_settings', '' );
	$text     = isset( $settings['home_title'] ) ? $settings['home_title'] : get_bloginfo( 'name' );
	echo esc_attr( $text );
}

function knowledgecenter_set_home_subtitle() {
	$settings = get_theme_mod( 'knowledgecenter_settings', '' );
	$text     = isset( $settings['home_subtitle'] ) ? $settings['home_subtitle'] : get_bloginfo( 'description' );
	echo esc_attr( $text );
}

function knowledgecenter_set_home_cat_title() {
	$settings = get_theme_mod( 'knowledgecenter_settings', '' );
	$text     = isset( $settings['home_cat_title'] ) ? $settings['home_cat_title'] : esc_attr__( 'Browse categories', 'knowledgecenter' );
	echo esc_attr( $text );
}

function knowledgecenter_filter_categories_homepage( $arg ) {
	$settings = get_theme_mod( 'knowledgecenter_home', '' );

	$orderby = ! empty( $settings['cat_orderby'] ) ? $settings['cat_orderby'] : 'name';
	$order   = ! empty( $settings['cat_order'] ) ? $settings['cat_order'] : 'ASC';
	$exclude = ! empty( $settings['cat_exclude'] ) ? $settings['cat_exclude'] : '';
	$include = ! empty( $settings['cat_include'] ) ? $settings['cat_include'] : '';

	$new_args = array(
		'orderby'    => $orderby,
		'order'      => $order,
		'hide_empty' => 1,
		'exclude'    => $exclude,
		'include'    => $include,
	);

	$arg = wp_parse_args( $new_args, $arg );

	return $arg;
}

add_filter( 'knowledgecenter_category_home_args', 'knowledgecenter_filter_categories_homepage' );

function knowledgecenter_footer_copyrite_text() {
	$settings = get_theme_mod( 'knowledgecenter_footer', '' );
	$text     = isset( $settings['copyright'] ) ? $settings['copyright'] : '&copy; ' . date_i18n( esc_attr__( 'Y', 'knowledgecenter' ) ) . ' ' . get_bloginfo( 'name' );
	echo esc_attr( $text );
}

function knowledgecenter_footer_title() {
	$settings = get_theme_mod( 'knowledgecenter_footer', '' );
	$text     = isset( $settings['blogname'] ) ? $settings['blogname'] : get_bloginfo( 'name' );
	echo esc_attr( $text );
}
