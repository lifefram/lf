<?php
/**
 * Library Books functions and definitions
 *
 * @package Library Books
 */

global $content_width;
 if ( ! isset( $content_width ) )
	$content_width = 640; /* pixels */ 
 
if (!function_exists('library_books_setup')):
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which runs
	 * before the init hook. The init hook is too late for some features, such as indicating
	 * support post thumbnails.
	 */
	function library_books_setup()
	{
		load_theme_textdomain('library-books', get_template_directory() . '/languages');
		add_theme_support('automatic-feed-links');
		add_theme_support('woocommerce');
		add_theme_support('post-thumbnails');
		add_theme_support('custom-header');
		add_theme_support('title-tag');
		add_theme_support('custom-logo', array(
			'height' => 52,
			'width' => 268,
			'flex-height' => true,
		));
		register_nav_menus(array(
			'primary' => esc_html__('Primary Menu', 'library-books') ,
		));
		add_theme_support('custom-background', array(
			'default-color' => 'ffffff'
		));
		add_editor_style( 'editor-style.css' );
		add_post_type_support( 'page', 'excerpt' );
	}
endif; // library_books_setup
add_action('after_setup_theme', 'library_books_setup');
/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function library_books_widgets_init()
{
	register_sidebar(array(
		'name' => esc_html__('Sidebar', 'library-books') ,
		'description' => esc_html__('Appears on page/post sidebar', 'library-books') ,
		'id' => 'sidebar-1',
		'before_widget' => '<div class="widgetbox">',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3><aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside></div>',
	));
	register_sidebar(array(
		'name' => esc_html__('Footer Column 1', 'library-books') ,
		'description' => esc_html__('Appears on footer', 'library-books') ,
		'id' => 'fc-1',
		'before_widget' => '',
		'before_title' => '<h5>',
		'after_title' => '</h5><aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
	));
	register_sidebar(array(
		'name' => esc_html__('Footer Column 2', 'library-books') ,
		'description' => esc_html__('Appears on footer', 'library-books') ,
		'id' => 'fc-2',
		'before_widget' => '',
		'before_title' => '<h5>',
		'after_title' => '</h5><aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
	));
	register_sidebar(array(
		'name' => esc_html__('Footer Column 3', 'library-books') ,
		'description' => esc_html__('Appears on footer', 'library-books') ,
		'id' => 'fc-3',
		'before_widget' => '',
		'before_title' => '<h5>',
		'after_title' => '</h5><aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
	));
}
add_action('widgets_init', 'library_books_widgets_init');
/**
 * Register custom fonts.
 */
function library_books_font_url()
{
	$font_url = '';
	/* Translators: If there are any character that are not
	* supported by Roboto Condensed, trsnalate this to off, do not
	* translate into your own language.
	*/
	$robotocondensed = _x('on', 'robotocondensed:on or off', 'library-books');
	/* Translators: If there has any character that are not supported
	*  by Scada, translate this to off, do not translate
	*  into your own language.
	*/
	$scada = _x('on', 'Scada:on or off', 'library-books');
	/* Translators: If there has any character that are not supported
	*  by Roboto Slab, translate this to off, do not translate
	*  into your own language.
	*/
	$robotoslab = _x('on', 'Roboto Slab:on or off', 'library-books');
	/* Translators: If there has any character that are not supported
	*  by Merriweather, translate this to off, do not translate
	*  into your own language.
	*/
	$merriweather = _x('on', 'Merriweather:on or off', 'library-books');
	/* Translators: If there has any character that are not supported
	*  by Roboto, translate this to off, do not translate
	*  into your own language.
	*/
	$roboto = _x('on', 'Roboto:on or off', 'library-books');
	/* Translators: If there has any character that are not supported
	*  by Lato, translate this to off, do not translate
	*  into your own language.
	*/
	$lato = _x('on', 'Lato:on or off', 'library-books');
	/* Translators: If there has any character that are not supported
	*  by Playfair Display, translate this to off, do not translate
	*  into your own language.
	*/
	$playfairdisplay = _x('on', 'Playfair Display:on or off', 'library-books');	
	/* Translators: If there has any character that are not supported
	*  by Assistant , translate this to off, do not translate
	*  into your own language.
	*/
	$assistant = _x('on', 'Assistant:on or off', 'library-books');		
	
	
	if ('off' !== $robotocondensed)
	{
		$font_family = array();
		if ('off' !== $robotocondensed)
		{
			$font_family[] = 'Roboto Condensed:300,400,600,700,800,900';
		}
		if ('off' !== $robotoslab)
		{
			$font_family[] = 'Roboto Slab:300,400,700';
		}
		if ('off' !== $merriweather)
		{
			$font_family[] = 'Merriweather:300,400,400i,700,700i,900,900i';
		}
		if ('off' !== $roboto)
		{
			$font_family[] = 'Roboto:100,300,300i,400,400i,500,500i,700,700i,900,900i';
		}
		if ('off' !== $lato)
		{
			$font_family[] = 'Lato:300,300i,400,400i,700,700i,900,900i';
		}
		if ('off' !== $playfairdisplay)
		{
			$font_family[] = 'Playfair Display:400,400i,700,700i,900,900i';
		}	
		if ('off' !== $assistant)
		{
			$font_family[] = 'Assistant:200,300,400,600,700,800';
		}			
		$query_args = array(
			'family' => urlencode(implode('|', $font_family)) ,
		);
		$font_url = add_query_arg($query_args, '//fonts.googleapis.com/css');
	}
	return $font_url;
}
/**
 * Enqueue scripts and styles.
 */
function library_books_scripts()
{
	wp_enqueue_style('library-books-font', library_books_font_url() , array());
	wp_enqueue_style('library-books-basic-style', get_stylesheet_uri());
	wp_enqueue_style('library-books-print-style', get_template_directory_uri() . "/print.css");
	wp_enqueue_style('nivo-slider', get_template_directory_uri() . "/css/nivo-slider.css");
	wp_enqueue_style('font-awesome', get_template_directory_uri() . "/css/font-awesome.css");
	wp_enqueue_style('library-books-main-style', get_template_directory_uri() . "/css/responsive.css");
	wp_enqueue_style('library-books-base-style', get_template_directory_uri() . "/css/style_base.css");
	wp_enqueue_script('jquery-nivo', get_template_directory_uri() . '/js/jquery.nivo.slider.js', array(
		'jquery'
	));
	wp_enqueue_script('library-books-custom-js', get_template_directory_uri() . '/js/custom.js');
	if (is_singular() && comments_open() && get_option('thread_comments'))
	{
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'library_books_scripts');

define('LIBRARY_BOOKS_URL', 'https://www.pinnaclethemes.net/');
define('LIBRARY_BOOKS_PRO_THEME_URL', 'https://www.pinnaclethemes.net/product/library-wordpress-theme/');
define('LIBRARY_BOOKS_FREE_THEME_URL', 'https://www.pinnaclethemes.net/product/free-bookstore-wordpress-theme/');
define('LIBRARY_BOOKS_THEME_DOC', 'https://pinnaclethemes.net/themedocumentation/library-documentation/');
define('LIBRARY_BOOKS_LIVE_DEMO', 'https://www.pinnaclethemes.net/themedemos/librarybooks/');
define('LIBRARY_BOOKS_THEMES', 'https://www.pinnaclethemes.net/cool-wordpress-themes/');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';
/**
 * Custom template for about theme.
 */
require get_template_directory() . '/inc/about-themes.php';
/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';
/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';
/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';
/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function library_books_pingback_header()
{
	if (is_singular() && pings_open())
	{
		printf( '<link rel="pingback" href="%s">' . "\n", esc_html(get_bloginfo( 'pingback_url' ) ));
	}
}
add_action('wp_head', 'library_books_pingback_header');

// Add class in body if slide option enable
function library_books_body_class( $classes ) {
 	$hideslide = get_theme_mod('hide_slides', 1);
	if (!is_home() && is_front_page()) {
		if( $hideslide == '') {
			$classes[] = 'visibleslide';
		}
	}
    return $classes;
}
add_filter( 'body_class','library_books_body_class' );

// get slug by id
function library_books_get_slug_by_id($id)
{
	$post_data = get_post($id, ARRAY_A);
	$slug = $post_data['post_name'];
	return $slug;
}
require_once get_template_directory() . '/upgrade-pro/example-1/class-customize.php';

/**
 * Filter the except length to 20 words.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
function library_books_custom_excerpt_length( $excerpt_length ) {
    return 20;
}
add_filter( 'excerpt_length', 'library_books_custom_excerpt_length', 999 );

// WordPress wp_body_open backward compatibility
if ( ! function_exists( 'wp_body_open' ) ) {
    function wp_body_open() {
        do_action( 'wp_body_open' );
    }
}