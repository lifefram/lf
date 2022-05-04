<?php
/**
 * iknowledgebase Theme Customizer
 *
 * @package iknowledgebase
 */

function iknowledgebase_customize_register( $wp_customize ) {

	// Add Theme settings panel
	$wp_customize->add_panel( 'iknowledgebase_settings', array(
		'title'       => esc_attr__( 'Theme Options', 'iknowledgebase' ),
		'description' => esc_attr__( 'Main theme settings.', 'iknowledgebase' ),
		'priority'    => 10,
	) );

	//region Main colors
	$wp_customize->add_section( 'iknowledgebase_main_settings', array(
		'title'    => esc_attr__( 'Main Settings', 'iknowledgebase' ),
		'priority' => 10,
		'panel'    => 'iknowledgebase_settings',
	) );

	$wp_customize->add_setting( 'iknowledgebase_sidebar_location', array(
		'default'           => 'left',
		'sanitize_callback' => 'iknowledgebase_sanitize_sidebar_location',
	) );
	$wp_customize->add_control( 'iknowledgebase_sidebar_location', array(
		'type'     => 'select',
		'label'    => esc_attr__( 'Sidebar location:', 'iknowledgebase' ),
		'section'  => 'iknowledgebase_main_settings',
		'priority' => '9',
		'choices'  => array(
			'left'  => esc_attr__( 'Left', 'iknowledgebase' ),
			'right' => esc_attr__( 'Right', 'iknowledgebase' ),
		),
	) );

	$wp_customize->add_setting( 'iknowledgebase_settings[body_svg]', array(
		'capability'        => 'edit_theme_options',
		'type'              => 'option',
		'default'           => 0,
		'sanitize_callback' => 'iknowledgebase_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'iknowledgebase_settings[body_svg]', array(
		'label'   => esc_attr__( 'Enable Body background image', 'iknowledgebase' ),
		'section' => 'iknowledgebase_main_settings',
		'type'    => 'checkbox',
	) );

	//endregion

	//region Home Category

	$wp_customize->add_section( 'iknowledgebase_home_settings', array(
		'title'    => esc_attr__( 'Home page Category', 'iknowledgebase' ),
		'priority' => 10,
		'panel'    => 'iknowledgebase_settings',
	) );

	$wp_customize->add_setting( 'iknowledgebase_home_post_number', array(
		'default'           => '5',
		'sanitize_callback' => 'iknowledgebase_sanitize_number',
	) );
	$wp_customize->add_control( 'iknowledgebase_home_post_number', array(
		'type'        => 'number',
		'label'       => esc_attr__( 'Post Number', 'iknowledgebase' ),
		'section'     => 'iknowledgebase_home_settings',
		'description' => esc_attr__( 'Set the numbers of posts on home page.', 'iknowledgebase' ),
	) );

	$wp_customize->add_setting( 'iknowledgebase_home[cat_orderby]', array(
		'default'           => 'name',
		'sanitize_callback' => 'iknowledgebase_sanitize_cat_orderby',
	) );
	$wp_customize->add_control( 'iknowledgebase_home[cat_orderby]', array(
		'type'    => 'select',
		'label'   => esc_attr__( 'Categories Order by:', 'iknowledgebase' ),
		'section' => 'iknowledgebase_home_settings',
		'choices' => array(
			'name' => esc_attr__( 'Name', 'iknowledgebase' ),
			'ID'   => esc_attr__( 'ID', 'iknowledgebase' ),
		),
	) );

	$wp_customize->add_setting( 'iknowledgebase_home[cat_order]', array(
		'default'           => 'ASC',
		'sanitize_callback' => 'iknowledgebase_sanitize_cat_order',
	) );
	$wp_customize->add_control( 'iknowledgebase_home[cat_order]', array(
		'type'    => 'select',
		'label'   => esc_attr__( 'Categories Order:', 'iknowledgebase' ),
		'section' => 'iknowledgebase_home_settings',
		'choices' => array(
			'ASC'  => esc_attr__( 'ASC', 'iknowledgebase' ),
			'DESC' => esc_attr__( 'DESC', 'iknowledgebase' ),
		),
	) );

	$wp_customize->add_setting( 'iknowledgebase_home[cat_exclude]', array(
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'iknowledgebase_home[cat_exclude]', array(
		'type'        => 'text',
		'label'       => esc_attr__( 'Exclude Categories:', 'iknowledgebase' ),
		'section'     => 'iknowledgebase_home_settings',
		'description' => esc_attr__( 'Exclude categories. A comma-separated string of category ids to exclude along with all of their descendant categories.', 'iknowledgebase' ),
	) );

	$wp_customize->add_setting( 'iknowledgebase_home[cat_include]', array(
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'iknowledgebase_home[cat_include]', array(
		'type'        => 'text',
		'label'       => esc_attr__( 'Include Categories:', 'iknowledgebase' ),
		'section'     => 'iknowledgebase_home_settings',
		'description' => esc_attr__( 'Include categories. A comma-separated string of category ids.', 'iknowledgebase' ),
	) );

	//endregion

	//region Navigation Menu Settings

	$wp_customize->add_section( 'iknowledgebase_navbar', array(
		'title'    => esc_attr__( 'Navigation menu', 'iknowledgebase' ),
		'priority' => 10,
		'panel'    => 'iknowledgebase_settings',
	) );

	$wp_customize->add_setting( 'iknowledgebase_settings[menu_space]', array(
		'capability'        => 'edit_theme_options',
		'type'              => 'option',
		'default'           => 0,
		'sanitize_callback' => 'iknowledgebase_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'iknowledgebase_settings[menu_space]', array(
		'label'   => esc_attr__( 'Remove Menu space', 'iknowledgebase' ),
		'section' => 'iknowledgebase_navbar',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'iknowledgebase_settings[menu_shadow]', array(
		'capability'        => 'edit_theme_options',
		'type'              => 'option',
		'default'           => 0,
		'sanitize_callback' => 'iknowledgebase_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'iknowledgebase_settings[menu_shadow]', array(
		'label'   => esc_attr__( 'Add Menu shadow', 'iknowledgebase' ),
		'section' => 'iknowledgebase_navbar',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'iknowledgebase_settings[menu_fixed]', array(
		'capability'        => 'edit_theme_options',
		'type'              => 'option',
		'default'           => 0,
		'sanitize_callback' => 'iknowledgebase_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'iknowledgebase_settings[menu_fixed]', array(
		'label'   => esc_attr__( 'Fixed Menu', 'iknowledgebase' ),
		'section' => 'iknowledgebase_navbar',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'iknowledgebase_settings[menu_transparent]', array(
		'capability'        => 'edit_theme_options',
		'type'              => 'option',
		'default'           => 0,
		'sanitize_callback' => 'iknowledgebase_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'iknowledgebase_settings[menu_transparent]', array(
		'label'   => esc_attr__( 'Remove any hover from the navbar items', 'iknowledgebase' ),
		'section' => 'iknowledgebase_navbar',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'iknowledgebase_settings[menu_hide_logo]', array(
		'capability'        => 'edit_theme_options',
		'type'              => 'option',
		'default'           => 0,
		'sanitize_callback' => 'iknowledgebase_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'iknowledgebase_settings[menu_hide_logo]', array(
		'label'   => esc_attr__( 'Hide Logo', 'iknowledgebase' ),
		'section' => 'iknowledgebase_navbar',
		'type'    => 'checkbox',
	) );

	//endregion

	//region Archives
	$wp_customize->add_section( 'iknowledgebase_archives_settings', array(
		'title'    => esc_attr__( 'Archives', 'iknowledgebase' ),
		'priority' => 10,
		'panel'    => 'iknowledgebase_settings',
	) );

	$wp_customize->add_setting( 'iknowledgebase_settings[archive_title]', array(
		'capability'        => 'edit_theme_options',
		'type'              => 'option',
		'default'           => 0,
		'sanitize_callback' => 'iknowledgebase_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'iknowledgebase_settings[archive_title]', array(
		'label'   => esc_attr__( 'Remove the prefix from the title on the archives page', 'iknowledgebase' ),
		'section' => 'iknowledgebase_archives_settings',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'iknowledgebase_settings[archive_sidebar]', array(
		'capability'        => 'edit_theme_options',
		'type'              => 'option',
		'default'           => 0,
		'sanitize_callback' => 'iknowledgebase_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'iknowledgebase_settings[archive_sidebar]', array(
		'label'   => esc_attr__( 'Category as Archive page (without sidebar)', 'iknowledgebase' ),
		'section' => 'iknowledgebase_archives_settings',
		'type'    => 'checkbox',
	) );

	//endregion

	//region Posts
	$wp_customize->add_section( 'iknowledgebase_posts_settings', array(
		'title'    => esc_attr__( 'Post', 'iknowledgebase' ),
		'priority' => 10,
		'panel'    => 'iknowledgebase_settings',
	) );

	$wp_customize->add_setting( 'iknowledgebase_settings[featured_image]', array(
		'type'              => 'option',
		'default'           => 0,
		'sanitize_callback' => 'iknowledgebase_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'iknowledgebase_settings[featured_image]', array(
		'label'   => esc_attr__( 'Enabled featured image', 'iknowledgebase' ),
		'section' => 'iknowledgebase_posts_settings',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'iknowledgebase_settings[post_hide_sidebar]', array(
		'type'              => 'option',
		'default'           => 0,
		'sanitize_callback' => 'iknowledgebase_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'iknowledgebase_settings[post_hide_sidebar]', array(
		'label'   => esc_attr__( 'Hide sidebar on mobile devices', 'iknowledgebase' ),
		'section' => 'iknowledgebase_posts_settings',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'iknowledgebase_settings_sticky_icon_color', array(
		'default'           => '',
		'sanitize_callback' => 'iknowledgebase_sanitize_default_colors',
	) );

	$wp_customize->add_control( 'iknowledgebase_settings_sticky_icon_color', array(
		'label'   => esc_attr__( 'Icon color for Sticky Post', 'iknowledgebase' ),
		'section' => 'iknowledgebase_posts_settings',
		'type'    => 'select',
		'choices' => array(
			''                 => esc_attr__( 'Default', 'iknowledgebase' ),
			'has-text-black'   => esc_attr__( 'Black', 'iknowledgebase' ),
			'has-text-light'   => esc_attr__( 'Light', 'iknowledgebase' ),
			'has-text-dark'    => esc_attr__( 'Dark', 'iknowledgebase' ),
			'has-text-info'    => esc_attr__( 'Blue', 'iknowledgebase' ),
			'has-text-success' => esc_attr__( 'Green', 'iknowledgebase' ),
			'has-text-warning' => esc_attr__( 'Yellow', 'iknowledgebase' ),
			'has-text-danger'  => esc_attr__( 'Red', 'iknowledgebase' ),
		),
	) );


	//endregion

	//region Breadcrumb

	$wp_customize->add_section( 'iknowledgebase_breadcrumb_settings', array(
		'title'    => esc_attr__( 'Breadcrumb', 'iknowledgebase' ),
		'priority' => 10,
		'panel'    => 'iknowledgebase_settings',
	) );

	$wp_customize->add_setting( 'iknowledgebase_breadcrumb_separators', array(
		'default'           => '',
		'sanitize_callback' => 'iknowledgebase_sanitize_breadcrumb_separators',
	) );
	$wp_customize->add_control( 'iknowledgebase_breadcrumb_separators', array(
		'type'    => 'select',
		'label'   => esc_attr__( 'Breadcrumb separator:', 'iknowledgebase' ),
		'section' => 'iknowledgebase_breadcrumb_settings',
		'choices' => array(
			''         => esc_attr__( 'Default', 'iknowledgebase' ),
			'arrow'    => esc_attr__( 'Arrow', 'iknowledgebase' ),
			'bullet'   => esc_attr__( 'Bullet', 'iknowledgebase' ),
			'dot'      => esc_attr__( 'Dot', 'iknowledgebase' ),
			'succeeds' => esc_attr__( 'Succeeds', 'iknowledgebase' ),
		),
	) );

	//endregion

	//region Media files
	$wp_customize->add_section( 'iknowledgebase_media_settings', array(
		'title'    => esc_attr__( 'Media', 'iknowledgebase' ),
		'priority' => 20,
		'panel'    => 'iknowledgebase_settings',
	) );

	$wp_customize->add_setting( 'iknowledgebase_main_img', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw'
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'iknowledgebase_main_img_control', array(
		'label'         => esc_attr__( 'Upload Main Image', 'iknowledgebase' ),
		'priority'      => 10,
		'section'       => 'iknowledgebase_media_settings',
		'settings'      => 'iknowledgebase_main_img',
		'button_labels' => array(// All These labels are optional
			'select' => esc_attr__( 'Select Image', 'iknowledgebase' ),
			'remove' => esc_attr__( 'Remove Image', 'iknowledgebase' ),
			'change' => esc_attr__( 'Change Image', 'iknowledgebase' ),
		)
	) ) );

	$wp_customize->add_setting( 'iknowledgebase_404_img', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw'
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'iknowledgebase_404_img_control', array(
		'label'         => esc_attr__( 'Upload 404 Error Image', 'iknowledgebase' ),
		'priority'      => 10,
		'section'       => 'iknowledgebase_media_settings',
		'settings'      => 'iknowledgebase_404_img',
		'button_labels' => array(// All These labels are optional
			'select' => esc_attr__( 'Select Image', 'iknowledgebase' ),
			'remove' => esc_attr__( 'Remove Image', 'iknowledgebase' ),
			'change' => esc_attr__( 'Change Image', 'iknowledgebase' ),
		)
	) ) );
	//endregion

	//region Footer Settings
	$wp_customize->add_section( 'iknowledgebase_footer', array(
		'title'    => esc_attr__( 'Footer', 'iknowledgebase' ),
		'priority' => 10,
		'panel'    => 'iknowledgebase_settings',
	) );

	$wp_customize->add_setting( 'iknowledgebase_settings[footer_text]', array(
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'default'           => '',
		'type'              => 'option',
	) );

	$wp_customize->add_control( 'iknowledgebase_settings[footer_text]', array(
		'label'       => esc_attr__( 'Footer Text', 'iknowledgebase' ),
		'section'     => 'iknowledgebase_footer',
		'description' => esc_attr__( 'Enter custom footer text.', 'iknowledgebase' ),
		'type'        => 'text',
		'input_attrs' => array(
			'placeholder' => '&copy; ' . esc_attr( date_i18n( esc_attr__( 'Y', 'iknowledgebase' ) ) ) . ' ' . esc_attr( get_bloginfo( 'name' ) ),
		),
	) );


	//endregion

}

add_action( 'customize_register', 'iknowledgebase_customize_register' );

// Sanitize Checkbox
function iknowledgebase_sanitize_checkbox( $input ) {
	if ( 1 == $input ) {
		return 1;
	} else {
		return 0;
	}
}

function iknowledgebase_sanitize_number( $number, $setting ) {
	// Ensure $number is an absolute integer (whole number, zero or greater).
	$number = absint( $number );

	// If the input is an absolute integer, return it; otherwise, return the default
	return ( $number ? $number : $setting->default );
}

function iknowledgebase_sanitize_cat_orderby( $input ) {
	$valid = array(
		'name' => esc_attr__( 'Name', 'iknowledgebase' ),
		'ID'   => esc_attr__( 'ID', 'iknowledgebase' ),
	);

	if ( array_key_exists( $input, $valid ) ) {
		return $input;
	} else {
		return 'name';
	}
}

function iknowledgebase_sanitize_cat_order( $input ) {
	$valid = array(
		'ASC'  => esc_attr__( 'ASC', 'iknowledgebase' ),
		'DESC' => esc_attr__( 'DESC', 'iknowledgebase' ),
	);

	if ( array_key_exists( $input, $valid ) ) {
		return $input;
	} else {
		return 'ASC';
	}
}

function iknowledgebase_sanitize_sidebar_location( $input ) {
	$valid = array(
		'left'  => esc_attr__( 'Left', 'iknowledgebase' ),
		'right' => esc_attr__( 'Right', 'iknowledgebase' ),
	);

	if ( array_key_exists( $input, $valid ) ) {
		return $input;
	} else {
		return '';
	}
}

function iknowledgebase_sanitize_breadcrumb_separators( $input ) {
	$valid = array(
		''         => esc_attr__( 'Default', 'iknowledgebase' ),
		'arrow'    => esc_attr__( 'Arrow', 'iknowledgebase' ),
		'bullet'   => esc_attr__( 'Bullet', 'iknowledgebase' ),
		'dot'      => esc_attr__( 'Dot', 'iknowledgebase' ),
		'succeeds' => esc_attr__( 'Succeeds', 'iknowledgebase' )
	);

	if ( array_key_exists( $input, $valid ) ) {
		return $input;
	} else {
		return '';
	}
}

function iknowledgebase_sanitize_default_colors( $input ) {
	$valid = array(
		''                 => esc_attr__( 'Default', 'iknowledgebase' ),
		'has-text-black'   => esc_attr__( 'Black', 'iknowledgebase' ),
		'has-text-light'   => esc_attr__( 'Light', 'iknowledgebase' ),
		'has-text-dark'    => esc_attr__( 'Dark', 'iknowledgebase' ),
		'has-text-info'    => esc_attr__( 'Blue', 'iknowledgebase' ),
		'has-text-success' => esc_attr__( 'Green', 'iknowledgebase' ),
		'has-text-warning' => esc_attr__( 'Yellow', 'iknowledgebase' ),
		'has-text-danger'  => esc_attr__( 'Red', 'iknowledgebase' ),
	);

	if ( array_key_exists( $input, $valid ) ) {
		return $input;
	} else {
		return '';
	}
}