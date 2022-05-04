<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package KnowledgeCenter
 * @subpackage KnowledgeCenter
 * @since KnowledgeCenter 1.0
 */


/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function knowledgecenter_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}

add_action( 'wp_head', 'knowledgecenter_pingback_header' );

function knowledgecenter_logo_class( $html ) {
	$html = str_replace( 'custom-logo-link', 'navbar-item', $html );

	return $html;
}

add_filter( 'get_custom_logo', 'knowledgecenter_logo_class' );

function knowledgecenter_show_comments_cookies_opt_in( $fields ) {
	$check_cookie = get_option( 'show_comments_cookies_opt_in' );
	if ( ! $check_cookie ) {
		unset ( $fields['cookies'] );
	}

	return $fields;
}

add_filter( 'knowledgecenter_comment_form_default_fields', 'knowledgecenter_show_comments_cookies_opt_in' );


function knowledgecenter_filter_archive_title( $title ) {
	$title = preg_replace( '~^[^:]+: ~', '', $title );

	return $title;
}

add_filter( 'get_the_archive_title', 'knowledgecenter_filter_archive_title' );


function knowledgecenter_change_tag_cloud_font_sizes( array $args ) {
	$args['smallest'] = '8';
	$args['largest']  = '8';

	return $args;
}

add_filter( 'widget_tag_cloud_args', 'knowledgecenter_change_tag_cloud_font_sizes' );


function knowledgecenter_add_classes_tags_cloud( $tags_data ) {

	foreach ( $tags_data as $key => $tag_data ) {
		foreach ( $tag_data as $data => $value ) {
			if ( $data === 'class' ) {
				$tags_data[ $key ][ $data ] = 'tag is-link is-normal ' . esc_attr( $value );
			}
		}
	}

	return $tags_data;
}

add_filter( 'wp_generate_tag_cloud_data', 'knowledgecenter_add_classes_tags_cloud' );

// Filter Pagination page link
function knowledgecenter_link_pages_link( $link, $i ) {

	$new_link = str_replace( 'post-page-numbers', 'button is-link is-small', $link );
	$new_link = str_replace( 'current', 'is-outlined', $new_link );
	$link     = $new_link;

	return $link;

}

add_filter( 'wp_link_pages_link', 'knowledgecenter_link_pages_link', 10, 2 );

/**
 * Add a divs elements for body background.
 */
function knowledgecenter_body_background() {
	?>
    <div class="circle is-light circle-1"></div>
    <div class="circle is-light circle-2"></div>
    <div class="circle is-primary circle-3"></div>
    <div class="circle is-primary circle-4"></div>
    <div class="circle is-primary circle-5"></div>
    <div class="circle is-primary circle-6"></div>
    <div class="circle is-light circle-7"></div>
	<?php
}


// Pagination
function knowledgecenter_the_posts_pagination() {
	$args = array(
		'show_all'           => false,
		'end_size'           => 1,
		'mid_size'           => 1,
		'prev_next'          => true,
		'prev_text'          => esc_attr__( 'Previous', 'knowledgecenter' ),
		'next_text'          => esc_attr__( 'Next', 'knowledgecenter' ),
		'add_args'           => false,
		'add_fragment'       => '',
		'screen_reader_text' => esc_attr__( 'Posts navigation', 'knowledgecenter' ),
	);
	echo '<div class="block mt-6">';
	the_posts_pagination( $args );
	echo '</div>';
}

// Filter the tags link
function knowledgecenter_filter_tags_link( $links ) {

	$links = str_replace( 'rel="tag">', 'class="tag is-primary" rel="tag">', $links );

	return $links;
}

add_filter( 'term_links-post_tag', 'knowledgecenter_filter_tags_link', 10, 5 );

// Add class to comment reply link
function knowledgecenter_filter_replay_comment_link( $link ) {

	$link = str_replace( 'comment-reply-link', 'comment-reply-link is-size-7', $link );

	return $link;
}

add_filter( 'comment_reply_link', 'knowledgecenter_filter_replay_comment_link' );

// Add class to the comment reply link
function knowledgecenter_filter_cancel_comment_reply_link( $formatted_link ) {

	$formatted_link = str_replace( 'id="cancel-comment-reply-link"', 'id="cancel-comment-reply-link" class="is-size-7 has-text-danger"', $formatted_link );

	return $formatted_link;

}

add_filter( 'cancel_comment_reply_link', 'knowledgecenter_filter_cancel_comment_reply_link' );

// Filter for footer menu links
function knowledgecenter_filter_footer_menu_links( $links ) {

	$links = str_replace( 'menu-item>', 'level-item menu-item', $links );

	return $links;
}

// Single post sidebar menu
function knowledgecenter_single_side_menu() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	$categories = get_the_category();
	$category   = $categories[0];
	$menu_data  = knowledgecenter_get_side_menu_data( $category );
	$cat_name   = $menu_data[0];
	get_search_form(); ?>
    <aside class="menu my-5">
        <p class="menu-label burger" data-target="sideMenuNavigation">
            <a href="#" class="navbar-burger is-inline ml-2 mr-4 is-hidden-desktop" role="button" aria-label="<?php esc_attr_e( 'Menu', 'knowledgecenter' ); ?>"
               aria-expanded="false" >
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
			<?php echo esc_html( $cat_name ); ?> </p>
		<?php if ( $menu_data[1] ) {
			echo '<ul class="menu-list" id="sideMenuNavigation">';
			knowledgecenter_get_child_category( $category, $menu_data[1] );
			echo '</ul>';
		} else {
			echo '<ul class="menu-list categories-list" id="sideMenuNavigation">';
			echo wp_kses_post( knowledgecenter_get_side_menu_posts( $category->term_id ) );
			echo '</ul>';
		} ?>
    </aside>
	<?php

}

// Category page side menu
function knowledgecenter_category_side_menu() {
	$cat_object = get_queried_object();
	$menu_data  = knowledgecenter_get_side_menu_data( $cat_object );
	$cat_name   = $menu_data[0];
	get_search_form();
	?>
    <aside class="menu my-5">
        <p class="menu-label burger" data-target="sideMenuNavigation">
            <a href="#" class="navbar-burger is-inline ml-2 mr-4 is-hidden-desktop" role="button" aria-label="<?php esc_attr_e( 'Menu', 'knowledgecenter' ); ?>"
               aria-expanded="false" >
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
			<?php echo esc_html( $cat_name ); ?> </p>
		<?php if ( $menu_data[1] ) {
			echo '<ul class="menu-list categories-list" id="sideMenuNavigation">';
			knowledgecenter_get_child_category( $cat_object, $menu_data[1] );
			echo '</ul>';
		} ?>
    </aside>
	<?php
}

function knowledgecenter_get_side_menu_posts( $cat_id ) {
	$posts_arg       = array(
		'numberposts' => 0,
		'category'    => $cat_id,
	);
	$posts           = get_posts( $posts_arg );
	$current_post_id = get_the_ID();
	$menu_posts      = '';
	foreach ( $posts as $single ) {
		setup_postdata( $single );
		$link  = get_permalink( $single->ID );
		$title = $single->post_title;

		if ( $current_post_id === $single->ID ) {
			$menu_posts .= '<li><a href="' . esc_url( $link ) . '" class="is-active">' . esc_html( $title ) . '</a></li>';
		} else {
			$menu_posts .= '<li><a href="' . esc_url( $link ) . '">' . esc_html( $title ) . '</a></li>';
		}
	}

	return $menu_posts;
}


function knowledgecenter_get_child_category( $cat_object, $child_cats ) {
	$cats = '';
	foreach ( $child_cats as $cat ) {
		$cat_link = get_category_link( $cat->cat_ID );
		if ( $cat_object->term_id === $cat->cat_ID ) {
			$cats .= '<li><a href="' . esc_url( $cat_link ) . '" class="is-active">' . esc_html( $cat->cat_name ) . '</a>';
			if ( is_singular( 'post' ) ) {
				$cats .= '<ul>';
				$cats .= knowledgecenter_get_side_menu_posts( $cat->cat_ID );
				$cats .= '</ul>';
			}
			$cats .= '</li>';
		} else {
			$cats .= '<li><a href="' . esc_url( $cat_link ) . '">' . esc_html( $cat->cat_name ) . '</a></li>';
		}

	}
	echo wp_kses_post( $cats );
}

// Get the data for category side menu
function knowledgecenter_get_side_menu_data( $cat ) {
	$parent = $cat->parent;
	$cat_id = $cat->term_id;
	$data   = array();

	if ( empty( $parent ) ) {
		$child_cats = get_categories( array(
			'parent' => $cat_id,
		) );
		$data       = [ $cat->name, $child_cats ];
	} else {
		$parent_obj = get_category( $parent );
		$child_cats = get_categories( array(
			'parent' => $parent_obj->term_id,
		) );
		$data       = [ $parent_obj->name, $child_cats ];
	}

	return $data;
}

// Add the new classes to images gallery
function knowledgecenter_gallery_output( $output, $attr, $instance ) {

	global $post, $wp_locale;

	$html5 = current_theme_supports( 'html5', 'gallery' );
	$atts  = shortcode_atts( array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post ? $post->ID : 0,
		'itemtag'    => $html5 ? 'div' : 'dl',
		'icontag'    => $html5 ? 'figure' : 'dt',
		'captiontag' => $html5 ? 'figcaption' : 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => '',
		'link'       => ''
	), $attr, 'gallery' );

	$id = intval( $atts['id'] );

	if ( ! empty( $atts['include'] ) ) {
		$_attachments = get_posts( array(
			'include'        => $atts['include'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['order'],
			'orderby'        => $atts['orderby']
		) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[ $val->ID ] = $_attachments[ $key ];
		}
	} elseif ( ! empty( $atts['exclude'] ) ) {
		$attachments = get_children( array(
			'post_parent'    => $id,
			'exclude'        => $atts['exclude'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['order'],
			'orderby'        => $atts['orderby']
		) );
	} else {
		$attachments = get_children( array(
			'post_parent'    => $id,
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['order'],
			'orderby'        => $atts['orderby']
		) );
	}

	if ( empty( $attachments ) ) {
		return '';
	}

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment ) {
			$output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
		}

		return $output;
	}

	$itemtag    = tag_escape( $atts['itemtag'] );
	$captiontag = tag_escape( $atts['captiontag'] );
	$icontag    = tag_escape( $atts['icontag'] );
	$valid_tags = wp_kses_allowed_html( 'post' );
	if ( ! isset( $valid_tags[ $itemtag ] ) ) {
		$itemtag = 'dl';
	}
	if ( ! isset( $valid_tags[ $captiontag ] ) ) {
		$captiontag = 'dd';
	}
	if ( ! isset( $valid_tags[ $icontag ] ) ) {
		$icontag = 'dt';
	}

	$columns   = intval( $atts['columns'] );
	$itemwidth = $columns > 0 ? floor( 100 / $columns ) : 100;
	$float     = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = '';

	/**
	 * Filter whether to print default gallery styles.
	 *
	 * @param bool $print Whether to print default gallery styles.
	 *                    Defaults to false if the theme supports HTML5 galleries.
	 *                    Otherwise, defaults to true.
	 *
	 * @since 3.1.0
	 *
	 */
	if ( apply_filters( 'knowledgecenter_use_default_gallery_style', ! $html5 ) ) {
		$gallery_style = "
	<style type='text/css'>
		#{$selector} {
			margin: auto;
		}
		#{$selector} .gallery-item {
			float: {$float};
			margin-top: 10px;
			text-align: center;
			width: {$itemwidth}%;
		}
		#{$selector} img {
			border: 2px solid #cfcfcf;
		}
		#{$selector} .gallery-caption {
			margin-left: 0;
		}
		/* see gallery_shortcode() in wp-includes/media.php */
	</style>\n\t\t";
	}

	switch ( $columns ) {
		case '1':
			$column = ' is-full';
			break;
		case '2':
			$column = ' is-half';
			break;
		case '3':
			$column = ' is-one-third';
			break;
		case '4':
			$column = ' is-one-quarter';
			break;
		default:
			$column = '';
			break;
	}


	$size_class  = sanitize_html_class( $atts['size'] );
	$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} columns is-multiline gallery-size-{$size_class}'>";

	/**
	 * Filter the default gallery shortcode CSS styles.
	 *
	 * @param string $gallery_style Default CSS styles and opening HTML div container
	 *                              for the gallery shortcode output.
	 *
	 * @since 2.5.0
	 *
	 */
	$output = apply_filters( 'knowledgecenter_gallery_style', $gallery_style . $gallery_div );

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {

		$attr = ( trim( $attachment->post_excerpt ) ) ? array( 'aria-describedby' => "$selector-$id" ) : '';
		if ( ! empty( $atts['link'] ) && 'file' === $atts['link'] ) {
			$image_output = wp_get_attachment_link( $id, $atts['size'], false, false, false, $attr );
		} elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {
			$image_output = wp_get_attachment_image( $id, $atts['size'], false, $attr );
		} else {
			$image_output = wp_get_attachment_link( $id, $atts['size'], true, false, false, $attr );
		}
		$image_meta = wp_get_attachment_metadata( $id );

		$orientation = '';
		if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
			$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
		}
		$output .= "<{$itemtag} class='gallery-item column{$column}'>";
		$output .= "
		<{$icontag} class='gallery-icon is-marginless image {$orientation}'>
			$image_output
		";
		if ( $captiontag && trim( $attachment->post_excerpt ) ) {
			$output .= "
			<{$captiontag} class='wp-caption-text gallery-caption is-italic is-size-7 has-text-dark' id='$selector-$id'>
			" . wptexturize( $attachment->post_excerpt ) . "
			</{$captiontag}>";
		}
		$output .= "</{$icontag}></{$itemtag}>";
		if ( ! $html5 && $columns > 0 && ++ $i % $columns == 0 ) {
			$output .= '<br style="clear: both" />';
		}
	}

	if ( ! $html5 && $columns > 0 && $i % $columns !== 0 ) {
		$output .= "
		<br style='clear: both' />";
	}

	$output .= "
	</div>\n";

	return $output;
}

add_filter( 'post_gallery', 'knowledgecenter_gallery_output', 10, 3 );


