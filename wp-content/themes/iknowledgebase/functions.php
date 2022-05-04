<?php
/**
 * Functions and definitions
 *
 * @package iknowledgebase
 */

if ( ! defined( 'IKNOWLEDGEBASE_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( 'IKNOWLEDGEBASE_VERSION', '1.2' );
}

// Theme setup
if ( ! function_exists( 'iknowledgebase_setup' ) ) :
	function iknowledgebase_setup() {
		if ( ! isset( $content_width ) ) {
			$content_width = 762;
		}

		load_theme_textdomain( 'iknowledgebase', get_template_directory() . '/languages' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'custom-logo', array() );
		add_post_type_support( 'page', 'excerpt' );
		add_theme_support( 'post-thumbnails' );

		register_nav_menus( array(
			'start-nav'   => esc_attr__( 'Left Main Menu', 'iknowledgebase' ),
			'end-nav'     => esc_attr__( 'Right Main Menu', 'iknowledgebase' ),
			'footer-menu' => esc_attr__( 'Footer Menu', 'iknowledgebase' ),
		) );

		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );


	}
endif;
add_action( 'after_setup_theme', 'iknowledgebase_setup' );

// Include styles and scripts
function iknowledgebase_scripts() {
	$pre_suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$template_uri = get_template_directory_uri();

	wp_enqueue_style( 'google-font', 'https://fonts.googleapis.com/css2?family=PT+Sans:wght@700&family=Raleway:wght@400;700&display=swap' );
	wp_enqueue_style( 'iknowledgebase', $template_uri . '/assets/css/style' . $pre_suffix . '.css', '', IKNOWLEDGEBASE_VERSION );


	$custom_css = apply_filters( 'iknowledgebase_extra_css', '' );
	if(!empty($custom_css)) {
		wp_add_inline_style( 'iknowledgebase', $custom_css );
	}

	wp_enqueue_script( 'iknowledgebase', $template_uri . '/assets/js/script' . $pre_suffix . '.js', array(), IKNOWLEDGEBASE_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'iknowledgebase_scripts' );

function iknowledgebase_admin_scripts() {
	$pre_suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$template_uri = get_template_directory_uri();

	wp_enqueue_script( 'iknowledgebase-admin', $template_uri . '/assets/js/admin-script' . $pre_suffix . '.js', array( 'jquery' ), IKNOWLEDGEBASE_VERSION, true );

}
add_action( 'admin_enqueue_scripts', 'iknowledgebase_admin_scripts' );

// Register sidebar
function iknowledgebase_widgets_init() {
	register_sidebar( array(
		'name'          => esc_attr__( 'Sidebar', 'iknowledgebase' ),
		'id'            => 'sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s box">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="title is-size-4">',
		'after_title'   => '</h4>',
	) );
}

add_action( 'widgets_init', 'iknowledgebase_widgets_init' );
// Include Theme Info page.
require get_template_directory() . '/includes/theme-info.php';

// Include Theme navigation Class.
require get_template_directory() . '/includes/class-navigation.php';

// Include Theme comments Class.
require get_template_directory() . '/includes/class-comments.php';

// Include Extra Functions.
require get_template_directory() . '/includes/template-functions.php';

// Include Filters.
require get_template_directory() . '/includes/template-tags.php';

// Include posts and categories on Homepage.
require get_template_directory() . '/includes/home-posts.php';

// Include Theme Customizer Options.
require get_template_directory() . '/includes/customizer.php';

// Include Widget Files.
require get_template_directory() . '/includes/widgets.php';

require get_template_directory() . '/includes/customizer-info.php';