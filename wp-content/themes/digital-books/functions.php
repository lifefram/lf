<?php
/**
 * Digital Books functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Digital Books
 */

include get_theme_file_path( 'vendor/wptrt/autoload/src/Digital_Books_Loader.php' );

$digital_books_loader = new \WPTRT\Autoload\Digital_Books_Loader();

$digital_books_loader->digital_books_add( 'WPTRT\\Customize\\Section', get_theme_file_path( 'vendor/wptrt/customize-section-button/src' ) );

$digital_books_loader->digital_books_register();

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function digital_books_setup() {

	add_theme_support( 'responsive-embeds' );

	add_theme_support( 'woocommerce' );
	
	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	add_image_size('digital-books-featured-header-image', 2000, 660, true);

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary','digital-books' ),
		'footer'=> esc_html__( 'Footer Menu','digital-books' ),
	) );

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		* to output valid HTML5.
		*/
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'digital_books_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support( 'custom-logo', array(
		'height'      => 50,
		'width'       => 50,
		'flex-width'  => true,
	) );

	add_editor_style( array( '/editor-style.css' ) );
	
	add_theme_support( 'wp-block-styles' );

	add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'digital_books_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function digital_books_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'digital_books_content_width', 1170 );
}
add_action( 'after_setup_theme', 'digital_books_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function digital_books_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'digital-books' ),
		'id'            => 'sidebar',
		'description'   => esc_html__( 'Add widgets here.', 'digital-books' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h5 class="widget-title">',
		'after_title'   => '</h5>',
	) );
}
add_action( 'widgets_init', 'digital_books_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function digital_books_scripts() {

	wp_enqueue_style('digital-books-font', digital_books_font_url(), array());

	wp_enqueue_style( 'digital-books-block-editor-style', get_theme_file_uri('/assets/css/block-editor-style.css') );

	// load bootstrap css
    wp_enqueue_style( 'flatly-css', esc_url(get_template_directory_uri()) . '/assets/css/flatly.css');

	wp_enqueue_style( 'digital-books-style', get_stylesheet_uri() );

	wp_style_add_data('digital-books-style', 'rtl', 'replace');

	// fontawesome
	wp_enqueue_style( 'fontawesome-css', esc_url(get_template_directory_uri()).'/assets/css/fontawesome/css/all.css' );

	wp_enqueue_style( 'owl.carousel-css', esc_url(get_template_directory_uri()).'/assets/css/owl.carousel.css' );

    wp_enqueue_script('owl.carousel-js', esc_url(get_template_directory_uri()) . '/assets/js/owl.carousel.js', array('jquery'), '', true );

    wp_enqueue_script('digital-books-theme-js', esc_url(get_template_directory_uri()) . '/assets/js/theme-script.js', array('jquery'), '', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'digital_books_scripts' );

/**
 * Enqueue theme color style.
 */
function digital_books_theme_color() {

    $theme_color_css = '';
    $digital_books_theme_color = get_theme_mod('digital_books_theme_color');

	$theme_color_css = '
		.sticky .entry-title::before,.main-navigation .sub-menu,#button,.sidebar input[type="submit"],.comment-respond input#submit,.post-navigation .nav-previous a:hover, .post-navigation .nav-next a:hover, .posts-navigation .nav-previous a:hover, .posts-navigation .nav-next a:hover,.woocommerce .woocommerce-ordering select,.woocommerce ul.products li.product .onsale, .woocommerce span.onsale,.pro-button a, .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt,.wp-block-button__link,.serv-box:hover,.woocommerce-account .woocommerce-MyAccount-navigation ul li,.btn-primary,.sidebar h5,.toggle-nav i,span.onsale,.slide-btn a,.serach_inner [type="submit"] {
			background: '.esc_attr($digital_books_theme_color).';
		}
		a,.sidebar ul li a:hover,#colophon a:hover, #colophon a:focus,p.price, .woocommerce ul.products li.product .price, .woocommerce div.product p.price, .woocommerce div.product span.price,.woocommerce-message::before, .woocommerce-info::before,.slider-inner-box a h2 {
			color: '.esc_attr($digital_books_theme_color).';
		}
		.woocommerce-message, .woocommerce-info,.wp-block-pullquote,.wp-block-quote, .wp-block-quote:not(.is-large):not(.is-style-large), .wp-block-pullquote,.btn-primary{
			border-color: '.esc_attr($digital_books_theme_color).';
		}
	';
    wp_add_inline_style( 'digital-books-style',$theme_color_css );	

}
add_action( 'wp_enqueue_scripts', 'digital_books_theme_color' );

function digital_books_font_url(){
	$font_url = '';
	$Libre = _x('on','Libre Baskerville:on or off','digital-books');
	$Ubuntu = _x('on','Ubuntu:on or off','digital-books');
	
	if('off' !== $Libre ){
		$font_family = array();
		if('off' !== $Libre){
			$font_family[] = 'Libre Baskerville:ital,wght@0,400;0,700;1,400';
		}
		$font_family = array();
		if('off' !== $Ubuntu){
			$font_family[] = 'Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700';
		}
		$query_args = array(
			'family'	=> urlencode(implode('|',$font_family)),
		);
		$font_url = add_query_arg($query_args,'//fonts.googleapis.com/css');
	}
	return $font_url;
}

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/*dropdown page sanitization*/
function digital_books_sanitize_dropdown_pages( $page_id, $setting ) {
	$page_id = absint( $page_id );
	return ( 'publish' == get_post_status( $page_id ) ? $page_id : $setting->default );
}

function digital_books_string_limit_words($string, $word_limit) {
	$words = explode(' ', $string, ($word_limit + 1));
	if(count($words) > $word_limit)
	array_pop($words);
	return implode(' ', $words);
}

function digital_books_sanitize_select( $input, $setting ){
    $input = sanitize_key($input);
    $choices = $setting->manager->get_control( $setting->id )->choices;
    return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}