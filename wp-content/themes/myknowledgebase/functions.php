<?php
/*
 * Theme functions and definitions.
 */

// Sets up theme defaults and registers various WordPress features that theme supports
function myknowledgebase_setup() {
	// Set max content width for img, video, and more
	global $content_width;
	if ( ! isset( $content_width ) )
	$content_width = 780;

	// Make theme available for translation
	load_theme_textdomain('myknowledgebase', get_template_directory() . '/languages');

	// Register Menu
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'myknowledgebase' ),
	) );

	// Add document title
	add_theme_support( 'title-tag' );

	// Add support for editor styles
	add_theme_support( 'editor-styles' );

	// Add editor styles
	add_editor_style( array( 'custom-editor-style.css', myknowledgebase_font_url() ) );

	// Custom header
	$header_args = array(
		'width' => 600,
		'height' => 400,
		'default-image' => get_template_directory_uri() . '/images/boats.jpg',
		'header-text' => false,
		'uploads' => true,
	);
	add_theme_support( 'custom-header', $header_args );

	// Default header
	register_default_headers( array(
		'boats' => array(
			'url' => get_template_directory_uri() . '/images/boats.jpg',
			'thumbnail_url' => get_template_directory_uri() . '/images/boats.jpg',
			'description' => __( 'Default header', 'myknowledgebase' )
		)
	) );

	// Post thumbnails
	add_theme_support( 'post-thumbnails' );

	// Resize thumbnails
	set_post_thumbnail_size( 250, 250 );

	// This feature adds RSS feed links to html head
	add_theme_support( 'automatic-feed-links' );

	// Switch default core markup for search form, comment form, comments and caption to output valid html5
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'caption' ) );

	// Background color
	$background_args = array(
		'default-color' => 'ffffff',
	);
	add_theme_support( 'custom-background', $background_args );

	// Post formats
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'gallery', 'audio' ) );
}
add_action( 'after_setup_theme', 'myknowledgebase_setup' );

// Set max content width for full width page and post
function myknowledgebase_extra_content_width() {
	global $content_width;
	if ( is_page_template( 'page-full.php' ) || is_page_template( 'page-knowledge-four.php' ) || is_page_template( 'single-full.php' ) )
	$content_width = 1200;
}
add_action( 'template_redirect', 'myknowledgebase_extra_content_width' );

// Enqueues scripts and styles for front-end
function myknowledgebase_scripts() {
	wp_enqueue_style( 'myknowledgebase-style', get_stylesheet_uri() );
	wp_enqueue_script( 'myknowledgebase-nav', get_template_directory_uri() . '/js/nav.js' );
	wp_enqueue_style( 'myknowledgebase-googlefonts', myknowledgebase_font_url() );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'myknowledgebase_scripts' );

// Font family
function myknowledgebase_font_url() {
	$font_url = '//fonts.googleapis.com/css?family=Open+Sans';
	return esc_url_raw( $font_url );
}

// Widget areas
function myknowledgebase_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Primary Sidebar', 'myknowledgebase' ),
		'id' => 'primary',
		'description' => __( 'You can add one or multiple widgets here.', 'myknowledgebase' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Homepage Sidebar', 'myknowledgebase' ),
		'id' => 'homepage',
		'description' => __( 'You can add one or multiple widgets here.', 'myknowledgebase' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Footer Right', 'myknowledgebase' ),
		'id' => 'footer-right',
		'description' => __( 'You can add one or multiple widgets here.', 'myknowledgebase' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Footer Middle', 'myknowledgebase' ),
		'id' => 'footer-middle',
		'description' => __( 'You can add one or multiple widgets here.', 'myknowledgebase' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Footer Left', 'myknowledgebase' ),
		'id' => 'footer-left',
		'description' => __( 'You can add one or multiple widgets here.', 'myknowledgebase' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'myknowledgebase_widgets_init' );

// Add class to post nav
function myknowledgebase_post_next() {
	return 'class="nav-next"';
}
add_filter('next_posts_link_attributes', 'myknowledgebase_post_next', 999);

function myknowledgebase_post_prev() {
	return 'class="nav-prev"';
}
add_filter('previous_posts_link_attributes', 'myknowledgebase_post_prev', 999);

// Add class to comment nav
function myknowledgebase_comment_next() {
	return 'class="comment-next"';
}
add_filter('next_comments_link_attributes', 'myknowledgebase_comment_next', 999);

function myknowledgebase_comment_prev() {
	return 'class="comment-prev"';
}
add_filter('previous_comments_link_attributes', 'myknowledgebase_comment_prev', 999);

// Custom excerpt lenght (default length is 55 words)
function myknowledgebase_excerpt_length( $length ) {
	return 75;
}
add_filter( 'excerpt_length', 'myknowledgebase_excerpt_length', 999 );

// Meta box for knowledge base page template
function myknowledgebase_theme_metabox() {
	add_meta_box(
		'knowledgebase-metabox',
		__( 'Knowledge Base', 'myknowledgebase' ),
		'myknowledgebase_metabox_callback',
		'page',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'myknowledgebase_theme_metabox' );

function myknowledgebase_metabox_callback( $post ) {
	wp_nonce_field( 'myknowledgebase_meta_box', 'myknowledgebase_nonce' );
	$cats_value = get_post_meta( $post->ID, 'myknowledgebase-exclude-cats', true );
	?>
	<p><?php _e( 'Setting for the Knowledge Base page template. This field will override the one in Customizer.', 'myknowledgebase' ); ?></p>
	<p><strong><label for="myknowledgebase-cats"><?php _e( 'Exclude category by ID', 'myknowledgebase' ); ?></label></strong></p>
	<input class="widefat" id="myknowledgebase-cats" type="text" name="myknowledgebase-cats" value="<?php echo esc_attr( $cats_value ); ?>" />
	<p><?php _e( 'Use a comma to separate multiple categories.', 'myknowledgebase' ); ?></p>
	<?php
}

function myknowledgebase_save_meta( $post_id ) {
	if ( ! isset( $_POST['myknowledgebase_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['myknowledgebase_nonce'], 'myknowledgebase_meta_box' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ( get_post_type() != 'page' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['myknowledgebase-cats'] ) ) {
		update_post_meta( $post_id, 'myknowledgebase-exclude-cats', sanitize_text_field( $_POST['myknowledgebase-cats'] ) );
	}
}
add_action( 'save_post', 'myknowledgebase_save_meta' );

// Theme Customizer
function myknowledgebase_theme_customizer( $wp_customize ) {
	$wp_customize->add_section( 'myknowledgebase_logo_section' , array(
		'title' => __( 'Logo', 'myknowledgebase' ),
		'priority' => 30,
		'description' => __( 'This logo will replace site title and tagline.', 'myknowledgebase' ),
	) );
	$wp_customize->add_setting( 'myknowledgebase_logo', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'myknowledgebase_logo', array(
		'label' => __( 'Logo', 'myknowledgebase' ),
		'section' => 'myknowledgebase_logo_section',
		'settings' => 'myknowledgebase_logo',
	) ) );
	$wp_customize->add_setting( 'myknowledgebase_logo_width', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_logo_width', array(
		'label' => __( 'Width', 'myknowledgebase' ),
		'description' => __( 'Only numeric characters allowed. Leave empty for original size.', 'myknowledgebase' ),
		'section' => 'myknowledgebase_logo_section',
		'type' => 'number',
		'settings' => 'myknowledgebase_logo_width',
		'input_attrs' => array(
			'min' => 20,
			'max' => 1200,
			'step' => 20,
		),
	) ) );
	$wp_customize->add_section( 'myknowledgebase_blog_section' , array(
		'title' => __( 'Blog Page', 'myknowledgebase' ),
		'priority' => 31,
	) );
	$wp_customize->add_setting( 'myknowledgebase_blog_title', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_blog_title', array(
		'label' => __( 'Title', 'myknowledgebase' ),
		'section' => 'myknowledgebase_blog_section',
		'settings' => 'myknowledgebase_blog_title',
	) ) );
	$wp_customize->add_setting( 'myknowledgebase_blog_content', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'wp_kses_post',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_blog_content', array(
		'label' => __( 'Content', 'myknowledgebase' ),
		'type' => 'textarea',
		'section' => 'myknowledgebase_blog_section',
		'settings' => 'myknowledgebase_blog_content',
	) ) );
	$wp_customize->add_section( 'myknowledgebase_post_section' , array(
		'title' => __( 'Posts', 'myknowledgebase' ),
		'priority' => 32,
	) );
	$wp_customize->add_setting( 'myknowledgebase_content_type', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => 'yes',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_content_type', array(
		'label' => __( 'Show a summary', 'myknowledgebase' ),
		'section' => 'myknowledgebase_post_section',
		'settings' => 'myknowledgebase_content_type',
		'type' => 'radio',
		'choices' => array(
			'yes' => __('Yes', 'myknowledgebase'),
			'no' => __('No', 'myknowledgebase'),
		),
	) ) );
	$wp_customize->add_setting( 'myknowledgebase_read_more', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => 'yes',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_read_more', array(
		'label' => __( 'Show Read More button', 'myknowledgebase' ),
		'section' => 'myknowledgebase_post_section',
		'settings' => 'myknowledgebase_read_more',
		'type' => 'radio',
		'choices' => array(
			'yes' => __('Yes', 'myknowledgebase'),
			'no' => __('No', 'myknowledgebase'),
		),
	) ) );
	$wp_customize->add_section( 'myknowledgebase_knowledge_section' , array(
		'title' => __( 'Knowledge Base', 'myknowledgebase' ),
		'priority' => 33,
		'description' => __( 'Settings for the Knowledge Base page template.', 'myknowledgebase' ),
	) );
	$wp_customize->add_setting( 'myknowledgebase_page_title', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => 'no',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_page_title', array(
		'label' => __( 'Show page title', 'myknowledgebase' ),
		'section' => 'myknowledgebase_knowledge_section',
		'settings' => 'myknowledgebase_page_title',
		'type' => 'radio',
		'choices' => array(
			'yes' => __('Yes', 'myknowledgebase'),
			'no' => __('No', 'myknowledgebase'),
		),
	) ) );
	$wp_customize->add_setting( 'myknowledgebase_post_count', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => 'no',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_post_count', array(
		'label' => __( 'Show number of posts', 'myknowledgebase' ),
		'section' => 'myknowledgebase_knowledge_section',
		'settings' => 'myknowledgebase_post_count',
		'type' => 'radio',
		'choices' => array(
			'yes' => __('Yes', 'myknowledgebase'),
			'no' => __('No', 'myknowledgebase'),
		),
	) ) );
	$wp_customize->add_setting( 'myknowledgebase_post_meta', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => 'no',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_post_meta', array(
		'label' => __( 'Show post meta', 'myknowledgebase' ),
		'section' => 'myknowledgebase_knowledge_section',
		'settings' => 'myknowledgebase_post_meta',
		'type' => 'radio',
		'choices' => array(
			'yes' => __('Yes', 'myknowledgebase'),
			'no' => __('No', 'myknowledgebase'),
		),
	) ) );
	$wp_customize->add_setting( 'myknowledgebase_view_all', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => 'no',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_view_all', array(
		'label' => __( 'Show View All link', 'myknowledgebase' ),
		'section' => 'myknowledgebase_knowledge_section',
		'settings' => 'myknowledgebase_view_all',
		'type' => 'radio',
		'choices' => array(
			'yes' => __('Yes', 'myknowledgebase'),
			'no' => __('No', 'myknowledgebase'),
		),
	) ) );
	$wp_customize->add_setting( 'myknowledgebase_cat_description', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => 'no',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_cat_description', array(
		'label' => __( 'Show category description', 'myknowledgebase' ),
		'section' => 'myknowledgebase_knowledge_section',
		'settings' => 'myknowledgebase_cat_description',
		'type' => 'radio',
		'choices' => array(
			'yes' => __('Yes', 'myknowledgebase'),
			'no' => __('No', 'myknowledgebase'),
		),
	) ) );
	$wp_customize->add_setting( 'myknowledgebase_exclude', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_exclude', array(
		'label' => __( 'Exclude category by ID', 'myknowledgebase' ),
		'description' => __( 'Use a comma to separate multiple categories.', 'myknowledgebase' ),
		'section' => 'myknowledgebase_knowledge_section',
		'settings' => 'myknowledgebase_exclude',
	) ) );
	$wp_customize->add_setting( 'myknowledgebase_posts', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_posts', array(
		'label' => __( 'Posts per category', 'myknowledgebase' ),
		'description' => __( 'Only numeric characters allowed.', 'myknowledgebase' ),
		'section' => 'myknowledgebase_knowledge_section',
		'type' => 'number',
		'settings' => 'myknowledgebase_posts',
			'input_attrs' => array(
			'min' => 1,
			'max' => 100,
			'step' => 1,
		),
	) ) );
	$wp_customize->add_setting( 'myknowledgebase_order', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => 'date',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_order', array(
		'label' => __( 'Order posts', 'myknowledgebase' ),
		'section' => 'myknowledgebase_knowledge_section',
		'settings' => 'myknowledgebase_order',
		'type' => 'radio',
		'choices' => array(
			'date' => __('By date', 'myknowledgebase'),
			'name' => __('By name', 'myknowledgebase'),
		),
	) ) );
	$wp_customize->add_section( 'myknowledgebase_search_section' , array(
		'title' => __( 'Search Bar', 'myknowledgebase' ),
		'priority' => 34,
	) );
	$wp_customize->add_setting( 'myknowledgebase_show_search', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => 'yes',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_show_search', array(
		'label' => __( 'Show Search Bar', 'myknowledgebase' ),
		'section' => 'myknowledgebase_search_section',
		'settings' => 'myknowledgebase_show_search',
		'type' => 'radio',
		'choices' => array(
			'yes' => __('Yes', 'myknowledgebase'),
			'no' => __('No', 'myknowledgebase'),
		),
	) ) );
	$wp_customize->add_setting( 'myknowledgebase_search', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_search', array(
		'label' => __( 'Title', 'myknowledgebase' ),
		'description' => __( 'This will override the default title.', 'myknowledgebase' ),
		'section' => 'myknowledgebase_search_section',
		'settings' => 'myknowledgebase_search',
	) ) );
	$wp_customize->add_setting( 'myknowledgebase_search_field_placeholder', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_search_field_placeholder', array(
		'label' => __( 'Placeholder', 'myknowledgebase' ),
		'description' => __( 'This will override the default placeholder.', 'myknowledgebase' ),
		'section' => 'myknowledgebase_search_section',
		'settings' => 'myknowledgebase_search_field_placeholder',
	) ) );
	$wp_customize->add_section( 'myknowledgebase_footer_section' , array(
		'title' => __( 'Footer', 'myknowledgebase' ),
		'priority' => 35,
	) );
	$wp_customize->add_setting( 'myknowledgebase_footer_content', array(
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'wp_kses_post',
	) );
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'myknowledgebase_footer_content', array(
		'label' => __( 'Content', 'myknowledgebase' ),
		'type' => 'textarea',
		'section' => 'myknowledgebase_footer_section',
		'settings' => 'myknowledgebase_footer_content',
	) ) );
}
add_action('customize_register', 'myknowledgebase_theme_customizer');
