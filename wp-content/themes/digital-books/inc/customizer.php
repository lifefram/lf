<?php
/**
 * Digital Books Theme Customizer
 *
 * @link: https://developer.wordpress.org/themes/customize-api/customizer-objects/
 *
 * @package Digital Books
 */

use WPTRT\Customize\Section\Digital_Books_Button;

add_action( 'customize_register', function( $manager ) {

    $manager->register_section_type( Digital_Books_Button::class );

    $manager->add_section(
        new Digital_Books_Button( $manager, 'digital_books_pro', [
            'title'       => __( 'Digital Books Pro', 'digital-books' ),
            'priority'    => 0,
            'button_text' => __( 'GET PREMIUM', 'digital-books' ),
            'button_url'  => esc_url( 'https://www.themagnifico.net/themes/book-store-wordpress-theme/', 'digital-books')
        ] )
    );

} );

// Load the JS and CSS.
add_action( 'customize_controls_enqueue_scripts', function() {

    $version = wp_get_theme()->get( 'Version' );

    wp_enqueue_script(
        'digital-books-customize-section-button',
        get_theme_file_uri( 'vendor/wptrt/customize-section-button/public/js/customize-controls.js' ),
        [ 'customize-controls' ],
        $version,
        true
    );

    wp_enqueue_style(
        'digital-books-customize-section-button',
        get_theme_file_uri( 'vendor/wptrt/customize-section-button/public/css/customize-controls.css' ),
        [ 'customize-controls' ],
        $version
    );

} );

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function digital_books_customize_register($wp_customize){
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';
    $wp_customize->get_setting('header_textcolor')->transport = 'postMessage';

    if (isset($wp_customize->selective_refresh)) {
        // Site title
        $wp_customize->selective_refresh->add_partial('blogname', array(
            'selector' => '.site-title',
            'render_callback' => 'digital_books_customize_partial_blogname',
        ));
    }

    // Theme Color
    $wp_customize->add_section('digital_books_color_option',array(
        'title' => esc_html__('Theme Color','digital-books'),
        'description' => esc_html__('Change theme color on one click.','digital-books'),
    ));

    $wp_customize->add_setting( 'digital_books_theme_color', array(
        'default' => '#fbb703',
        'sanitize_callback' => 'sanitize_hex_color'
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'digital_books_theme_color', array(
        'label' => esc_html__('Color Option','digital-books'),
        'section' => 'digital_books_color_option',
        'settings' => 'digital_books_theme_color' 
    )));
    
    // Social Link
    $wp_customize->add_section('digital_books_social_link',array(
        'title' => esc_html__('Social Links','digital-books'),
    ));

    $wp_customize->add_setting('digital_books_facebook_url',array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    )); 
    $wp_customize->add_control('digital_books_facebook_url',array(
        'label' => esc_html__('Facebook Link','digital-books'),
        'section' => 'digital_books_social_link',
        'setting' => 'digital_books_facebook_url',
        'type'  => 'url'
    ));

    $wp_customize->add_setting('digital_books_twitter_url',array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    )); 
    $wp_customize->add_control('digital_books_twitter_url',array(
        'label' => esc_html__('Twitter Link','digital-books'),
        'section' => 'digital_books_social_link',
        'setting' => 'digital_books_twitter_url',
        'type'  => 'url'
    ));

    $wp_customize->add_setting('digital_books_intagram_url',array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    )); 
    $wp_customize->add_control('digital_books_intagram_url',array(
        'label' => esc_html__('Intagram Link','digital-books'),
        'section' => 'digital_books_social_link',
        'setting' => 'digital_books_intagram_url',
        'type'  => 'url'
    ));

    $wp_customize->add_setting('digital_books_linkedin_url',array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    )); 
    $wp_customize->add_control('digital_books_linkedin_url',array(
        'label' => esc_html__('Linkedin Link','digital-books'),
        'section' => 'digital_books_social_link',
        'setting' => 'digital_books_linkedin_url',
        'type'  => 'url'
    ));

    $wp_customize->add_setting('digital_books_pintrest_url',array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw'
    )); 
    $wp_customize->add_control('digital_books_pintrest_url',array(
        'label' => esc_html__('Pinterest Link','digital-books'),
        'section' => 'digital_books_social_link',
        'setting' => 'digital_books_pintrest_url',
        'type'  => 'url'
    ));

    //Slider
    $wp_customize->add_section('digital_books_top_slider',array(
        'title' => esc_html__('Slider Settings','digital-books'),
        'description' => esc_html__('Here you have to add 3 different pages in below dropdown. Note: Image Dimensions 1400 x 550 px','digital-books')
    ));

    for ( $count = 1; $count <= 3; $count++ ) {

        $wp_customize->add_setting( 'digital_books_top_slider_page' . $count, array(
            'default'           => '',
            'sanitize_callback' => 'digital_books_sanitize_dropdown_pages'
        ) );
        $wp_customize->add_control( 'digital_books_top_slider_page' . $count, array(
            'label'    => __( 'Select Slide Page', 'digital-books' ),
            'section'  => 'digital_books_top_slider',
            'type'     => 'dropdown-pages'
        ) );
    }

    //Featured Product
    $wp_customize->add_section('digital_books_home_product_category',array(
        'title' => esc_html__('Featured Product','digital-books'),
        'description' => esc_html__('Here you have to select product category which will display perticular featured product in the home page.','digital-books')
    ));

    $args = array(
       'type'                     => 'product',
        'child_of'                 => 0,
        'parent'                   => '',
        'orderby'                  => 'term_group',
        'order'                    => 'ASC',
        'hide_empty'               => false,
        'hierarchical'             => 1,
        'number'                   => '',
        'taxonomy'                 => 'product_cat',
        'pad_counts'               => false
    );
    $categories = get_categories( $args );
    $cats = array();
    $i = 0;
    foreach($categories as $category){
        if($i==0){
            $default = $category->slug;
            $i++;
        } 
        $cats[$category->slug] = $category->name;
    }
    $wp_customize->add_setting('digital_books_home_product',array(
        'sanitize_callback' => 'digital_books_sanitize_select',
    ));
    $wp_customize->add_control('digital_books_home_product',array(
        'type'    => 'select',
        'choices' => $cats,
        'label' => __('Select Product Category','digital-books'),
        'section' => 'digital_books_home_product_category',
    ));

    for ( $i = 1; $i <= 4; $i++ ) {
        $wp_customize->add_setting('digital_books_home_product_number'.$i,array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field'
        )); 
        $wp_customize->add_control('digital_books_home_product_number'.$i,array(
            'label' => esc_html__('Number','digital-books'),
            'description' => esc_html__('Add Counter Number','digital-books'),
            'section' => 'digital_books_home_product_category',
            'setting' => 'digital_books_home_product_number',
            'type'    => 'text'
        ));
        $wp_customize->add_setting('digital_books_home_product_text'.$i,array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field'
        )); 
        $wp_customize->add_control('digital_books_home_product_text'.$i,array(
            'label' => esc_html__('Text','digital-books'),
            'description' => esc_html__('Add Counter Text','digital-books'),
            'section' => 'digital_books_home_product_category',
            'setting' => 'digital_books_home_product_text',
            'type'    => 'text'
        ));
    }

    // Footer
    $wp_customize->add_section('digital_books_site_footer_section', array(
        'title' => esc_html__('Footer', 'digital-books'),
    ));

    $wp_customize->add_setting('digital_books_footer_text_setting', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('digital_books_footer_text_setting', array(
        'label' => __('Replace the footer text', 'digital-books'),
        'section' => 'digital_books_site_footer_section',
        'priority' => 1,
        'type' => 'text',
    ));
}
add_action('customize_register', 'digital_books_customize_register');

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function digital_books_customize_partial_blogname(){
    bloginfo('name');
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function digital_books_customize_partial_blogdescription(){
    bloginfo('description');
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function digital_books_customize_preview_js(){
    wp_enqueue_script('digital-books-customizer', esc_url(get_template_directory_uri()) . '/assets/js/customizer.js', array('customize-preview'), '20151215', true);
}
add_action('customize_preview_init', 'digital_books_customize_preview_js');