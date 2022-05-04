<?php
/**
 * KnowledgeCenter functions and definitions
 *
 * @package KnowledgeCenter
 * @subpackage KnowledgeCenter
 * @since KnowledgeCenter 1.0
 */

if ( ! defined( '_KnowledgeCenter_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_KnowledgeCenter_VERSION', '0.1' );
}

if ( ! function_exists( 'knowledgecenter_setup' ) ) :
	function knowledgecenter_setup() {

		/*
			* Make theme available for translation.
			* Translations can be filed in the /languages/ directory.
		*/
		load_theme_textdomain( 'knowledgecenter', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
			 * Let WordPress manage the document title.
			 * By adding theme support, we declare that this theme does not use a
			 * hard-coded <title> tag in the document head, and expect WordPress to
			 * provide it for us.
			 */
		add_theme_support( 'title-tag' );

		// Enable Excerpt for pages
		add_post_type_support( 'page', 'excerpt' );

		// Enable support for Custom Logo for site.
		add_theme_support( 'custom-logo', array(
			'height' => 28,
			'width'  => 112,
		) );

		/*
			* Enable support for Post Thumbnails on posts and pages.
		*/
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'first-menu'  => esc_attr__( 'First Menu', 'knowledgecenter' ),
			'second-menu' => esc_attr__( 'Second Menu', 'knowledgecenter' ),
			'footer-menu' => esc_attr__( 'Footer Menu', 'knowledgecenter' ),
		) );

		// Enable support for HTML5 markup.
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		) );
	}
endif;
add_action( 'after_setup_theme', 'knowledgecenter_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function knowledgecenter_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'knowledgecenter_content_width', 640 );
}

add_action( 'after_setup_theme', 'knowledgecenter_content_width', 0 );


function knowledgecenter_scripts() {

	$pre_suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '-min';
	$template_uri = get_template_directory_uri();

	wp_enqueue_style( 'knowledgecenter', $template_uri . '/assets/css/style' . $pre_suffix . '.css', '', _KnowledgeCenter_VERSION );

	wp_enqueue_style( 'knowledgecenter-icons', $template_uri . '/assets/icons/css/style.css', '', _KnowledgeCenter_VERSION );

	wp_enqueue_style( 'knowledgecenter-google-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap', false );

	wp_enqueue_script( 'knowledgecenter', $template_uri . '/assets/js/script' . $pre_suffix . '.js', array(), _KnowledgeCenter_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'knowledgecenter_scripts' );


function knowledgecenter_widgets_init() {
	register_sidebar( array(
		'name'          => esc_attr__( 'Sidebar', 'knowledgecenter' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<div id="%1$s" class="widget %2$s mb-5">',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="title is-size-4">',
		'after_title'   => '</p>',
	) );
}

add_action( 'widgets_init', 'knowledgecenter_widgets_init' );

/**
 * Implement the site main navigation menu.
 */
require get_template_directory() . '/inc/class-navigation.php';

/**
 * Implement the site Comments list.
 */
require get_template_directory() . '/inc/class-comments.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Set customizer variables.
 */
require get_template_directory() . '/inc/customizer-functions.php';


function knowledgecenter_theme_info_notice_notice() {

	if ( get_user_meta( get_current_user_id(), 'knowledgecenter_dismissed_notice', true ) === _KnowledgeCenter_VERSION ) {
		return;
	}

	$message = esc_attr__( 'Some information about WordPress theme Knowledgecenter!', 'knowledgecenter' );
	$links   = '<a href="https://wow-company.com/faq/category/wow-themes/knowledgecenter/" target="_blank">' . esc_html__( 'Documentation', 'knowledgecenter' ) . '</a> | ';
	$links   .= '<a href="https://wow-estore.com/" target="_blank">' . esc_html__( 'Pro Plugins', 'knowledgecenter' ) . '</a> | ';
	$links   .= '<a href="https://profiles.wordpress.org/wpcalc/#content-plugins" target="_blank">' . esc_html__( 'Free Plugins', 'knowledgecenter' ) . '</a> | ';
	$links   .= '<a href="https://wordpress.org/support/theme/knowledgecenter/" target="_blank">' . esc_html__( 'Support Forum', 'knowledgecenter' ) . '</a> | ';
	$links   .= '<a href="https://wordpress.org/support/theme/knowledgecenter/reviews/#new-post" target="_blank">' . esc_html__( 'Rate Theme on WordPress.org', 'knowledgecenter' ) . '</a> | ';
	$links   .= '<a href="' . esc_url( wp_nonce_url( add_query_arg( 'knowledgecenter-dismiss', 'dismiss_admin_notices' ), 'knowledgecenter-dismiss-' . get_current_user_id() ) ) . '">' . esc_html__( 'Dismiss this notice', 'knowledgecenter' ) . '</a>';
	$email   = esc_attr__( 'Have any idea? Write us on email', 'knowledgecenter' ) . ' <a href="mailto:support@wow-company.com">support@wow-company.com</a>';

	$notice = '
	<div class="notice notice-info is-dismissible">
	<p style="color: red;"><strong><u>' . $message . '</u></strong></p>	
	<p><strong>' . $email . '</strong></p>
	<p><strong>' . $links . '</strong></p>
	</div>';

	echo wp_kses_post( $notice );
}

add_action( 'admin_notices', 'knowledgecenter_theme_info_notice_notice' );

function knowledgecenter_theme_info_dismiss_notice() {
	if ( isset( $_GET['knowledgecenter-dismiss'] ) && check_admin_referer( 'knowledgecenter-dismiss-' . get_current_user_id() ) ) {
		update_user_meta( get_current_user_id(), 'knowledgecenter_dismissed_notice', _KnowledgeCenter_VERSION );
	}
}

add_action( 'admin_head', 'knowledgecenter_theme_info_dismiss_notice' );
