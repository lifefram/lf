<?php
/**
 * hgw whiteboard functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @subpackage Hgw_WhiteBoard
 */

/**
 * Table of Contents:
 * Theme Support
 * Register Styles
 * Register Menus
 * Register Sidebars
 * Excerpt Customize
 * Required Files




 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function hgw_whiteboard_theme_support() {

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Set content-width.
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 580;
	}

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */

	add_theme_support( 'post-thumbnails' );

	// Custom logo.
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 40,
			'width'       => 40,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
			'navigation-widgets',
		)
	);

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 */

	// load Tranlate 'hgw-whiteboard'
	load_theme_textdomain( 'hgw-whiteboard', get_template_directory() . '/languages' );

	// Add support for full and wide align images.
	add_theme_support( 'align-wide' );

	// Add support for responsive embeds.
	add_theme_support( 'responsive-embeds' );

}

add_action( 'after_setup_theme', 'hgw_whiteboard_theme_support' );


/**
 * Register and Enqueue Styles.
 */
function hgw_whiteboard_theme_scripts(){

 $theme_version = wp_get_theme()->get( 'Version' );

 wp_enqueue_style( 'hgw-whiteboard-style', get_stylesheet_uri(), array(), $theme_version );

 wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/assets/css/fontawesome.css', array(), $theme_version  );

 wp_enqueue_script( 'hgw-whiteboard-js', get_template_directory_uri() . '/assets/js/hgw-scripts.js', array(), $theme_version, true);

 if ( is_rtl() ) {
		wp_enqueue_style( 'hgw-whiteboard-rtl-style', get_template_directory_uri() . '/assets/css/rtl.css', array(), $theme_version  );
	}
	if ( is_singular() ) {
		wp_enqueue_script( "comment-reply" );
	}

}

add_action( 'wp_enqueue_scripts', 'hgw_whiteboard_theme_scripts' );


/**
 * Register navigation menus uses wp_nav_menu in five places.
 */
function hgw_whiteboard_menus() {

	$locations = array(
		'primary'  => __( 'Primary Menu', 'hgw-whiteboard' ),
	);

	register_nav_menus( $locations );
}

add_action( 'init', 'hgw_whiteboard_menus' );


/**
 * Register widget areas.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function hgw_whiteboard_widgets_init() {

    register_sidebar(

      array(

        'name'          => __( 'Main Sidebar', 'hgw-whiteboard' ),
        'id'            => 'main-sidebar',
        'description'   => __( 'Main Sidebar', 'hgw-whiteboard' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    )

  );

	register_sidebar(

		array(

			'name'          => __( 'Secondary Sidebar', 'hgw-whiteboard' ),
			'id'            => 'secondary-sidebar',
			'description'   => __( 'How to activate? Appearance » Customize » Sidebar » Sidebar Settings » Two sidebars', 'hgw-whiteboard' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)

	);

}
add_action( 'widgets_init', 'hgw_whiteboard_widgets_init' );


// Customize Search Form
function custom_search($form){

		$form = '<form method = "get" id="searchform" action="'.home_url().'" >';
		$form .= '<input type="text" value="'. esc_attr(apply_filters('the_search_query', get_search_query())) .'" name="s" id="s" placeholder="'.esc_attr__( 'Search ...', 'hgw-whiteboard' ).'" />';
		$form .= '<button id="searchsubmit" value="Search"><i class="fa fa-search"></i></button>';
		$form .= '</form>';

	return $form;
}

add_filter('get_search_form', 'custom_search');

/**
 * REQUIRED FILES
 * Include required files.
 */

// Theme Customize
require get_template_directory() . '/inc/customize.php';

// Theme info
require get_template_directory() . '/inc/themeinfo.php';

// Custom tags for the theme.
require get_template_directory() . '/inc/template-tags.php';

// Add recent Posts Widget
require get_template_directory() . '/classes/class-widget-recent-posts.php';
