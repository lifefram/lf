<?php
/**
 * knowledgecenter Theme Customizer
 *
 * @package knowledgecenter
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function knowledgecenter_customize_register( $wp_customize ) {

	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-name',
				'render_callback' => 'knowledgecenter_customize_partial_blogname',
			)
		);

	}

	$wp_customize->add_panel( 'knowledgecenter_settings', array(
		'title'       => esc_attr__( 'Theme Settings', 'knowledgecenter' ),
		'description' => esc_attr__( 'Main theme settings.', 'knowledgecenter' ),
		'priority'    => 10,
	) );


	//region Home Category
	$wp_customize->add_section( 'knowledgecenter_home_category', array(
		'title'    => esc_attr__( 'Home page', 'knowledgecenter' ),
		'priority' => 10,
		'panel'    => 'knowledgecenter_settings',
	) );

	$wp_customize->add_setting( 'knowledgecenter_settings[home_title]', array(
		'default'           => get_bloginfo('name'),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'knowledgecenter_settings[home_title]', array(
		'type'        => 'text',
		'label'       => esc_attr__( 'Home Title', 'knowledgecenter' ),
		'section'     => 'knowledgecenter_home_category',
		'description' => esc_attr__( 'Enter the home title.', 'knowledgecenter' ),
	) );

	$wp_customize->add_setting( 'knowledgecenter_settings[home_subtitle]', array(
		'default'           => get_bloginfo('description'),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'knowledgecenter_settings[home_subtitle]', array(
		'type'        => 'text',
		'label'       => esc_attr__( 'Home Sub Title', 'knowledgecenter' ),
		'section'     => 'knowledgecenter_home_category',
		'description' => esc_attr__( 'Enter the home sub title.', 'knowledgecenter' ),
	) );

	$wp_customize->add_setting( 'knowledgecenter_settings[home_cat_title]', array(
		'default'           => esc_attr__( 'Browse categories', 'knowledgecenter' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'knowledgecenter_settings[home_cat_title]', array(
		'type'        => 'text',
		'label'       => esc_attr__( 'Home Category Title', 'knowledgecenter' ),
		'section'     => 'knowledgecenter_home_category',
		'description' => esc_attr__( 'Enter the home categories title.', 'knowledgecenter' ),
	) );


	$wp_customize->add_setting( 'knowledgecenter_home[cat_orderby]', array(
		'default'           => 'name',
		'sanitize_callback' => 'knowledgecenter_sanitize_cat_orderby',
	) );
	$wp_customize->add_control( 'knowledgecenter_home[cat_orderby]', array(
		'type'    => 'select',
		'label'   => esc_attr__( 'Categories Order by:', 'knowledgecenter' ),
		'section' => 'knowledgecenter_home_category',
		'choices' => array(
			'name' => esc_attr__( 'Name', 'knowledgecenter' ),
			'ID'   => esc_attr__( 'ID', 'knowledgecenter' ),
		),
	) );

	$wp_customize->add_setting( 'knowledgecenter_home[cat_order]', array(
		'default'           => 'ASC',
		'sanitize_callback' => 'knowledgecenter_sanitize_cat_order',
	) );
	$wp_customize->add_control( 'knowledgecenter_home[cat_order]', array(
		'type'    => 'select',
		'label'   => esc_attr__( 'Categories Order:', 'knowledgecenter' ),
		'section' => 'knowledgecenter_home_category',
		'choices' => array(
			'ASC'  => esc_attr__( 'ASC', 'knowledgecenter' ),
			'DESC' => esc_attr__( 'DESC', 'knowledgecenter' ),
		),
	) );

	$wp_customize->add_setting( 'knowledgecenter_home[cat_exclude]', array(
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'knowledgecenter_home[cat_exclude]', array(
		'type'        => 'text',
		'label'       => esc_attr__( 'Exclude Categories:', 'knowledgecenter' ),
		'section'     => 'knowledgecenter_home_category',
		'description' => esc_attr__( 'Exclude categories. A comma-separated string of category ids to exclude along with all of their descendant categories.', 'knowledgecenter' ),
	) );

	$wp_customize->add_setting( 'knowledgecenter_home[cat_include]', array(
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'knowledgecenter_home[cat_include]', array(
		'type'        => 'text',
		'label'       => esc_attr__( 'Include Categories:', 'knowledgecenter' ),
		'section'     => 'knowledgecenter_home_category',
		'description' => esc_attr__( 'Include categories. A comma-separated string of category ids.', 'knowledgecenter' ),
	) );
	//endregion


	//region Footer Settings
	$wp_customize->add_section( 'knowledgecenter_footer', array(
		'title'    => esc_attr__( 'Footer', 'knowledgecenter' ),
		'priority' => 10,
		'panel'    => 'knowledgecenter_settings',
	) );

	$wp_customize->add_setting( 'knowledgecenter_footer[blogname]', array(
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => get_bloginfo( 'name' ),
	) );

	$wp_customize->add_control( 'knowledgecenter_footer[blogname]', array(
		'label'       => esc_attr__( 'Footer Title', 'knowledgecenter' ),
		'section'     => 'knowledgecenter_footer',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'knowledgecenter_footer[copyright]', array(
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => '&copy; ' . date_i18n( esc_attr__( 'Y', 'knowledgecenter' ) ) . ' ' . get_bloginfo( 'name' ) . '. ' . esc_attr__('All right reserved.', 'knowledgecenter'),
	) );

	$wp_customize->add_control( 'knowledgecenter_footer[copyright]', array(
		'label'       => esc_attr__( 'Copyright Text', 'knowledgecenter' ),
		'section'     => 'knowledgecenter_footer',
		'description' => esc_attr__( 'Enter custom copyright text.', 'knowledgecenter' ),
		'type'        => 'text',
	) );
	//endregion




}

add_action( 'customize_register', 'knowledgecenter_customize_register' );


function knowledgecenter_sanitize_cat_orderby( $input ) {
	$valid = array(
		'name' => esc_attr__( 'Name', 'knowledgecenter' ),
		'ID'   => esc_attr__( 'ID', 'knowledgecenter' ),
	);

	if ( array_key_exists( $input, $valid ) ) {
		return $input;
	} else {
		return 'name';
	}
}

function knowledgecenter_sanitize_cat_order( $input ) {
	$valid = array(
		'ASC'  => esc_attr__( 'ASC', 'knowledgecenter' ),
		'DESC' => esc_attr__( 'DESC', 'knowledgecenter' ),
	);

	if ( array_key_exists( $input, $valid ) ) {
		return $input;
	} else {
		return 'ASC';
	}
}


function knowledgecenter_sanitize_checkbox( $input ) {
	if ( 1 == $input ) {
		return 1;
	} else {
		return 0;
	}
}

function knowledgecenter_sanitize_number($number, $setting) {
	// Ensure $number is an absolute integer (whole number, zero or greater).
	$number = absint( $number );

	// If the input is an absolute integer, return it; otherwise, return the default
	return ( $number ? $number : $setting->default );
}

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function knowledgecenter_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function knowledgecenter_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function knowledgecenter_customize_preview_js() {
	wp_enqueue_script( 'knowledgecenter-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), _KnowledgeCenter_VERSION, true );
}

add_action( 'customize_preview_init', 'knowledgecenter_customize_preview_js' );

