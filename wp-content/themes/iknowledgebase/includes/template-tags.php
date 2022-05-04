<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package iknowledgebase
 */

// Change logo class
function iknowledgebase_logo_class( $html ) {
	$html = str_replace( 'custom-logo-link', 'navbar-item brand-logo', $html );

	return $html;
}

add_filter( 'get_custom_logo', 'iknowledgebase_logo_class' );

// Post Icon filter
function iknowledgebase_filter_post_icon( $icon ) {
	$iknowledgebase_option = get_option( 'iknowledgebase_settings', '' );
	$post_icon             = ! empty( $iknowledgebase_option['post_icon'] ) ? $iknowledgebase_option['post_icon'] : '';
	if ( ! empty( $post_icon ) ) {
		$icon = $post_icon;
	}

	return $icon;

}

add_filter( 'iknowledgebase_post_icon', 'iknowledgebase_filter_post_icon' );

// Filter the tags link
function iknowledgebase_filter_tags_link( $links ) {

	$links = str_replace( 'rel="tag">', 'class="tag is-info is-light is-normal" rel="tag">#', $links );

	return $links;
}

add_filter( 'term_links-post_tag', 'iknowledgebase_filter_tags_link', 10, 5 );

// Add checkbox Cookie to comments
function iknowledgebase_show_comments_cookies_opt_in( $fields ) {
	$check_cookie = get_option( 'show_comments_cookies_opt_in' );
	if ( ! $check_cookie ) {
		unset ( $fields['cookies'] );
	}

	return $fields;
}

add_filter( 'iknowledgebase_comment_form_default_fields', 'iknowledgebase_show_comments_cookies_opt_in' );


// Add class to comment reply link
function iknowledgebase_filter_replay_comment_link( $link ) {

	$link = str_replace( 'comment-reply-link', 'comment-reply-link is-size-7', $link );

	return $link;
}

add_filter( 'comment_reply_link', 'iknowledgebase_filter_replay_comment_link' );


// Add class to the comment reply link
function iknowledgebase_filter_cancel_comment_reply_link( $formatted_link ) {

	$formatted_link = str_replace( 'id="cancel-comment-reply-link"', 'id="cancel-comment-reply-link" class="is-size-7 has-text-danger "', $formatted_link );

	return $formatted_link;
}

add_filter( 'cancel_comment_reply_link', 'iknowledgebase_filter_cancel_comment_reply_link' );

// Change tag cloud size
function iknowledgebase_change_tag_cloud_font_sizes( array $args ) {
	$args['smallest'] = '12';
	$args['largest']  = '12';

	return $args;
}

add_filter( 'widget_tag_cloud_args', 'iknowledgebase_change_tag_cloud_font_sizes' );

// Change style of the tag in cloud
function iknowledgebase_add_hash_tags_cloud( $tags_data ) {

	foreach ( $tags_data as $key => $tag_data ) {
		foreach ( $tag_data as $data => $value ) {
			if ( $data === 'class' ) {
				$tags_data[ $key ][ $data ] = 'tag is-info is-light ' . esc_attr( $value );
			}
		}
	}

	return $tags_data;
}

add_filter( 'wp_generate_tag_cloud_data', 'iknowledgebase_add_hash_tags_cloud' );

// Gallery
function iknowledgebase_gallery_output( $output, $attr, $instance ) {

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
	if ( apply_filters( 'iknow_use_default_gallery_style', ! $html5 ) ) {
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
	$output = apply_filters( 'iknow_gallery_style', $gallery_style . $gallery_div );

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

add_filter( 'post_gallery', 'iknowledgebase_gallery_output', 10, 3 );

// Filter for Home page category
function iknowledgebase_filter_categories_homepage( $arg ) {
	$options = get_theme_mod( 'iknowledgebase_home', '' );

	$orderby = ! empty( $options['cat_orderby'] ) ? $options['cat_orderby'] : 'name';
	$order   = ! empty( $options['cat_order'] ) ? $options['cat_order'] : 'ASC';
	$exclude = ! empty( $options['cat_exclude'] ) ? $options['cat_exclude'] : '';
	$include = ! empty( $options['cat_include'] ) ? $options['cat_include'] : '';

	$new_args = array(
		'orderby'    => $orderby,
		'order'      => $order,
		'hide_empty' => 1,
		'exclude'    => $exclude,
		'include'    => $include,
	);

	$arg = wp_parse_args( $new_args, $arg );

	return $arg;
}

add_filter( 'iknowledgebase_category_home_args', 'iknowledgebase_filter_categories_homepage' );

// Customizer Navigation menu
function iknowledgebase_menu_nav_classes( $classes ) {
	$option      = get_option( 'iknowledgebase_settings', '' );
	$space       = ! empty( $option['menu_space'] ) ? '' : ' is-spaced';
	$shadow      = ! empty( $option['menu_shadow'] ) ? ' has-shadow' : '';
	$fixed       = ! empty( $option['menu_fixed'] ) ? ' is-fixed-top' : '';
	$transparent = ! empty( $option['menu_transparent'] ) ? ' is-transparent' : '';

	$classes = $space . $shadow . $fixed . $transparent;

	return $classes;
}

add_filter( 'iknowledgebase_menu_nav_classes', 'iknowledgebase_menu_nav_classes' );

// Insert custom field for menu item

function iknowledgebase_menu_item_custom_fields( $item_id, $item ) {
	wp_nonce_field( 'iknowledgebase_menu_meta_nonce', '_iknowledgebase_menu_meta_nonce' );
	$menu_meta            = get_post_meta( $item_id, '_iknowledgebase_menu_meta', true );
	$item_type            = isset( $menu_meta['item-type'] ) ? $menu_meta['item-type'] : '';
	$button_type          = isset( $menu_meta['button-type'] ) ? $menu_meta['button-type'] : '';
	$button_type_outlined = isset( $menu_meta['button-type-outlined'] ) ? $menu_meta['button-type-outlined'] : '';
	do_action( 'iknowledgebase_menu_item_fields_before', $item_id, $item );
	?>
    <p class="field-item-type description description-thin field-custom-extension-menu">
        <input type="hidden" class="nav-menu-id" value="<?php echo esc_attr( $item_id ); ?>"/>
        <label for="edit-menu-item-type-<?php echo esc_attr( $item_id ); ?>">
			<?php esc_attr_e( "Item Appearance", 'iknowledgebase' ); ?><br>
            <select name="iknowledgebase_menu_meta[<?php echo esc_attr( $item_id ); ?>][item-type]"
                    id="iknowledgebase-menu-meta-for-<?php echo esc_attr( $item_id ); ?>">
                <option value="link" <?php selected( $item_type, 'link' ); ?>><?php esc_html_e( "Default", 'iknowledgebase' ); ?></option>
                <option value="button" <?php selected( $item_type, 'button' ); ?>><?php esc_html_e( "Button", 'iknowledgebase' ); ?></option>
                <option value="divider" <?php selected( $item_type, 'divider' ); ?>><?php esc_html_e( "Divider", 'iknowledgebase' ); ?></option>
                <option value="dropdown" <?php selected( $item_type, 'dropdown' ); ?>><?php esc_html_e( "Dropdown right", 'iknowledgebase' ); ?></option>
            </select>
        </label>

    </p>

    <p class="field-item-type-button description description-thin field-custom-extension-menu" style="display: none;">
        <label for="edit-menu-item-type-button-<?php echo esc_attr( $item_id ); ?>">
			<?php esc_attr_e( "Button Color", 'iknowledgebase' ); ?><br>
            <select name="iknowledgebase_menu_meta[<?php echo esc_attr( $item_id ); ?>][button-type]"
                    id="iknowledgebase-menu-type-button-for-<?php echo esc_attr( $item_id ); ?>">
                <option value="" <?php selected( $button_type, '' ); ?>><?php esc_html_e( "Default", 'iknowledgebase' ); ?></option>
                <option value="is-primary" <?php selected( $button_type, 'is-primary' ); ?>><?php esc_html_e( "Primary", 'iknowledgebase' ); ?></option>
                <option value="is-info" <?php selected( $button_type, 'is-info' ); ?>><?php esc_html_e( "Blue", 'iknowledgebase' ); ?></option>
                <option value="is-success" <?php selected( $button_type, 'is-success' ); ?>><?php esc_html_e( "Green", 'iknowledgebase' ); ?></option>
                <option value="is-warning" <?php selected( $button_type, 'is-warning' ); ?>><?php esc_html_e( "Yellow", 'iknowledgebase' ); ?></option>
                <option value="is-danger" <?php selected( $button_type, 'is-danger' ); ?>><?php esc_html_e( "Red", 'iknowledgebase' ); ?></option>
                <option value="is-white" <?php selected( $button_type, 'is-white' ); ?>><?php esc_html_e( "White", 'iknowledgebase' ); ?></option>
                <option value="is-light" <?php selected( $button_type, 'is-light' ); ?>><?php esc_html_e( "Light", 'iknowledgebase' ); ?></option>
                <option value="is-dark" <?php selected( $button_type, 'is-dark' ); ?>><?php esc_html_e( "Dark", 'iknowledgebase' ); ?></option>
                <option value="is-black" <?php selected( $button_type, 'is-black' ); ?>><?php esc_html_e( "Black", 'iknowledgebase' ); ?></option>
            </select>
        </label>
    </p>

    <p class="field-item-type-button description field-custom-extension-menu" style="display: none;">
        <label for="edit-menu-item-type-button-outlined-<?php echo esc_attr( $item_id ); ?>">
            <input type="checkbox" id="edit-menu-item-type-button-outlined-<?php echo esc_attr( $item_id ); ?>"
                   value="1"
                   name="iknowledgebase_menu_meta[<?php echo esc_attr( $item_id ); ?>][button-type-outlined]"<?php checked( $button_type_outlined ); ?>>
			<?php esc_attr_e( "Outlined", 'iknowledgebase' ); ?>
        </label>
    </p>
	<?php
	do_action( 'iknowledgebase_menu_item_fields_after', $item_id, $item );
}

add_action( 'wp_nav_menu_item_custom_fields', 'iknowledgebase_menu_item_custom_fields', 10, 2 );


// Saved custom field for menu item
function iknowledgebase_menu_items_update( $menu_id, $menu_item_db_id ) {

	// Verify this came from our screen and with proper authorization.
	if ( ! isset( $_POST['_iknowledgebase_menu_meta_nonce'] ) || ! wp_verify_nonce( $_POST['_iknowledgebase_menu_meta_nonce'], 'iknowledgebase_menu_meta_nonce' ) ) {
		return $menu_id;
	}

	if ( isset( $_POST['iknowledgebase_menu_meta'][ $menu_item_db_id ] ) ) {
		$sanitized_data = map_deep( $_POST['iknowledgebase_menu_meta'][ $menu_item_db_id ], 'sanitize_text_field' );
		update_post_meta( $menu_item_db_id, '_iknowledgebase_menu_meta', $sanitized_data );
	} else {
		delete_post_meta( $menu_item_db_id, '_iknowledgebase_menu_meta' );
	}
}

add_action( 'wp_update_nav_menu_item', 'iknowledgebase_menu_items_update', 10, 2 );

// Filter for right dropdown in navigation menu
function iknowledgebase_menu_item_dropdown_right( $output, $item_id ) {
	$menu_meta = get_post_meta( $item_id, '_iknowledgebase_menu_meta', true );
	$item_type = isset( $menu_meta['item-type'] ) ? $menu_meta['item-type'] : '';
	if ( $item_type === 'dropdown' ) {
		$output .= ' is-right ';
	}

	return $output;

}

add_filter( 'iknowledgebase_menu_dropdown_right', 'iknowledgebase_menu_item_dropdown_right', 10, 2 );

function iknowledgebase_filter_archive_title( $title ) {
	$iknowledgebase_settings = get_option( 'iknowledgebase_settings', false );

	if ( empty( $iknowledgebase_settings['archive_title'] ) ) {
		return $title;
	}

	return preg_replace( '~^[^:]+: ~', '', $title );

}

add_filter( 'get_the_archive_title', 'iknowledgebase_filter_archive_title' );



