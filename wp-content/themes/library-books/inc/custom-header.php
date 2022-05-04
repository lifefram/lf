<?php
/**
 * Filter Library Books custom-header support arguments.
 *
 * @package Library Books
 *
 * @param array $args {
 *     An array of custom-header support arguments.
 *
 *     @type string $default_text_color      Default color of the header text.
 *     @type int    $width                   Width in pixels of the custom header image. Default 954.
 *     @type int    $height                  Height in pixels of the custom header image. Default 1300.
 *     @type string $wp-head-callback        Callback function used to styles the header image and text
 *                                           displayed on the blog.
 *	   @type string $admin-head-callback     Call on custom background administration screen.
 *	   @type string $admin-preview-callback  Output a custom background image div on the custom background administration                                                 screen. Optional.
 * }

 */

function library_books_custom_header_setup()
{
	add_theme_support('custom-header', apply_filters('library_books_custom_header_args', array(
		'default-text-color' => 'ffffff',
		'width' => 1600,
		'height' => 200,
		'wp-head-callback' => 'library_books_header_style',
		'admin-head-callback' => 'library_books_admin_header_style',
		'admin-preview-callback' => 'library_books_admin_header_image',
	)));
}

add_action('after_setup_theme', 'library_books_custom_header_setup');

if (!function_exists('library_books_header_style')):
	/**
	 * Styles the header image and text displayed on the blog
	 *
	 * @see library_books_custom_header_setup().
	 */
	function library_books_header_style()
	{
		$header_text_color = get_header_textcolor();
?>
	<style type="text/css">
	<?php
		// Check if user has defined any header image.
		if (get_header_image()):
	?>
		.header, .inrheader{
			background: url(<?php
			echo esc_url(get_header_image()); ?>) no-repeat;
			background-position: center top;
		}
	<?php
		endif; ?>	
	</style>
	<?php
	}
endif; // library_books_header_style

if (!function_exists('library_books_admin_header_style')):
	/**
	 * Styles the header image displayed on the Appearance > Header admin panel.
	 *
	 * @see library_books_custom_header_setup().
	 */
	function library_books_admin_header_style()
	{ ?>
	<style type="text/css">
	.appearance_page_custom-header #headimg { border: none; }
	</style><?php
	}

endif; // library_books_admin_header_style

if (!function_exists('library_books_admin_header_image')):
	/**
	 * Custom header image markup displayed on the Appearance > Header admin panel.
	 *
	 * @see library_books_custom_header_setup().
	 */
	function library_books_admin_header_image()
	{
?>
	<div id="headimg">
		<?php
		if (get_header_image()): ?>
		<img src="<?php
			header_image(); ?>" alt="">
		<?php
		endif; ?>
	</div>
<?php
	}
endif; // library_books_admin_header_image