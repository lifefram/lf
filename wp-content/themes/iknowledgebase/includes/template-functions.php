<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package iknowledgebase
 */

function iknowledgebase_brand() {
	$option = get_option( 'iknowledgebase_settings', '' );

	if ( ! empty( $option['menu_hide_logo'] ) ) {
		return;
	}

	if ( has_custom_logo() ) {
		the_custom_logo();
	} else {
		?>
        <a class="navbar-item" href="<?php echo esc_url( home_url( '/' ) ); ?>"
           title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
            <span class="navbar-item brand-name">
                <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
            </span>
        </a>
		<?php
	}

}


// Custom page navigation
function iknowledgebase_the_posts_pagination() {
	$args = array(
		'show_all'           => false,
		'end_size'           => 1,
		'mid_size'           => 1,
		'prev_next'          => true,
		'prev_text'          => esc_attr__( 'Previous', 'iknowledgebase' ),
		'next_text'          => esc_attr__( 'Next page', 'iknowledgebase' ),
		'add_args'           => false,
		'add_fragment'       => '',
		'screen_reader_text' => esc_attr__( 'Posts navigation', 'iknowledgebase' ),
	);

	the_posts_pagination( $args );
}

function iknowledgebase_link_pages() {
	$args = array(
		'before'           => '<div class="nav-links">',
		'after'            => '</div>',
		'link_before'      => '',
		'link_after'       => '',
		'next_or_number'   => 'number',
		'nextpagelink'     => esc_attr__( 'Next page', 'iknowledgebase' ),
		'previouspagelink' => esc_attr__( 'Previous page', 'iknowledgebase' ),
		'pagelink'         => '%',
		'echo'             => 1,
	);


	wp_link_pages( $args );
}


function iknowledgebase_go_filter() {

	global $wp_query;

	$select = ! empty( $_GET['select'] ) ? sanitize_text_field( wp_unslash( $_GET['select'] ) ) : 'newest';
	switch ( $select ) {
		case 'title':
			$args['orderby'] = 'title';
			$args['order']   = 'ASC';
			break;
		case 'comments':
			$args['orderby'] = 'comment_count';
			break;
		default:
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
			break;
	}

	$posts_per_page = ! empty( $_GET['per_page'] ) ? absint( $_GET['per_page'] ) : 'default';

	if ( $posts_per_page === 'default' ) {
		$posts_per_page = get_option( 'posts_per_page ' );
	}

	$args['posts_per_page'] = $posts_per_page;

	$iknowledgebase_settings = get_option( 'iknowledgebase_settings', false );
	$sidebar                 = $iknowledgebase_settings['archive_sidebar'];

	$category = ! empty( $_GET['category'] ) ? absint( $_GET['category'] ) : 0;

	if ( $sidebar && is_category() && ! empty( $category ) ) {
		$args['cat'] = $category;
	}

	query_posts( array_merge( $args, $wp_query->query ) );
}

function iknowledgebase_posts_sorter() {
	if ( $_GET && ! empty( $_GET ) ) {
		iknowledgebase_go_filter();
	}

	$sorterby = ! empty( $_GET['select'] ) ? sanitize_text_field( wp_unslash( $_GET['select'] ) ) : '';


	$sorter_arr = array(
		'newest'   => esc_attr__( 'Newest', 'iknowledgebase' ),
		'title'    => esc_attr__( 'by Title', 'iknowledgebase' ),
		'comments' => esc_attr__( 'by Comments', 'iknowledgebase' ),
	);

	$posts = ! empty( $_GET['per_page'] ) ? absint( $_GET['per_page'] ) : 'default';

	$posts_arr = array(
		'default' => esc_attr__( 'Default', 'iknowledgebase' ),
		'20'      => '20 ' . esc_attr__( 'Per Page', 'iknowledgebase' ),
		'50'      => '50 ' . esc_attr__( 'Per Page', 'iknowledgebase' ),
		'100'     => '100 ' . esc_attr__( 'Per Page', 'iknowledgebase' ),

	);

	$iknowledgebase_settings = get_option( 'iknowledgebase_settings', false );
	$sidebar                 = $iknowledgebase_settings['archive_sidebar'] ?? '';

	if ( $sidebar && is_category() ) {
		$category = ! empty( $_GET['category'] ) ? absint( $_GET['category'] ) : 0;
		$term_id  = get_queried_object()->term_id;
		$children = get_term_children( $term_id, 'category' );
	}


	?>
    <form method="get" id="order" class="level is-mobile">
        <div class="level-left">
            <div class="level-item is-hidden-mobile">
                <p><?php esc_html_e( 'Sorted', 'iknowledgebase' ); ?>:</p>
            </div>
            <div class="level-item">
                <div class="field">
                    <div class="control">
                        <div class="select is-small is-primary">
                            <select name="select" class="" onchange="this.form.submit();">
								<?php
								foreach ( $sorter_arr as $key => $val ) {
									echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $sorterby, false ) . '>' . esc_html( $val ) . '</option>';
								}
								?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="level-item">
                <div class="field">
                    <div class="control">
                        <div class="select is-small is-primary">
                            <select name="per_page" class="" onchange="this.form.submit();">
								<?php
								foreach ( $posts_arr as $key => $val ) {
									echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $posts, false ) . '>' . esc_html( $val ) . '</option>';
								}
								?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

			<?php if ( $sidebar && is_category() && $children ) :

				array_unshift( $children, '0' );
				?>

                <div class="level-item">
                    <div class="field">
                        <div class="control">
                            <div class="select is-small is-primary">
                                <select name="category" class="" onchange="this.form.submit();">
									<?php
									foreach ( $children as $key => $val ) {
										$name = empty( $val ) ? esc_attr__( 'All', 'iknowledgebase' ) : get_cat_name( $val );
										echo '<option value="' . absint( $val ) . '" ' . selected( $val, $category, false ) . '>' . esc_html( $name ) . '</option>';
									}
									?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>


			<?php endif; ?>

        </div>
    </form>
	<?php
}

// Get Main image
function iknowledgebase_main_image() {
	$image = get_theme_mod( 'iknowledgebase_main_img', '' );
	if ( ! empty( $image ) ) {
		$image_id  = attachment_url_to_postid( $image );
		$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		echo '<img class="is-fullwidth mb-5 is-max-w-sm" src="' . esc_url( $image ) . '" alt="' . esc_attr( $image_alt ) . '">';
	}
}

function iknowledgebase_404_image() {
	$image = get_theme_mod( 'iknowledgebase_404_img', '' );
	if ( ! empty( $image ) ) {
		$image_id  = attachment_url_to_postid( $image );
		$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		echo '<img class="is-fullwidth mb-5 is-max-w-sm" src="' . esc_url( $image ) . '" alt="' . esc_attr( $image_alt ) . '">';
	}
}

// breadcrumbs
function iknowledgebase_breadcrumbs() {
	$separator        = get_theme_mod( 'iknowledgebase_breadcrumb_separators', '' );
	$separators_class = ! empty( $separator ) ? ' has-' . esc_attr( $separator ) . '-separator' : '';
	echo '<nav class="breadcrumb is-size-7 is-hidden-mobile' . esc_attr( $separators_class ) . '" aria-label="breadcrumbs"><ul>';
	echo ' <li><a href="' . esc_url( home_url() ) . '"><span>' . esc_html__( 'Home', 'iknowledgebase' ) . '</span></a></li>';
	if ( is_category() || is_tag() ) {
		$object = get_queried_object();
		if ( ! empty( $object->parent ) ) {
			$term_id   = $object->parent;
			$term_link = get_term_link( (int) $term_id, $object->taxonomy );
			echo '<li><a href="' . esc_url( $term_link ) . '">' . esc_html( get_the_category_by_ID( (int) $term_id ) ) . '</a></li>';
		}
		$term_id   = $object->term_id;
		$term_link = get_term_link( (int) $term_id, $object->taxonomy );
		echo '<li class="is-active"><a href="' . esc_url( $term_link ) . '"><span>' . esc_html( $object->name ) . '</span></a></li>';
	} elseif ( is_single() ) {
		echo '<li>' . get_the_category_list( '</li><li>', 'multiple' );
		echo '<li class="is-active"><a href="' . esc_url( get_permalink() ) . '" aria-current="page">' . esc_html( get_the_title() ) . '</a> </li>';
	} else {
		echo '<li class="is-active"><a aria-current="page">' . esc_html( wp_strip_all_tags( get_the_archive_title() ) ) . '</a></li>';
	}
	echo '</ul></nav>';
}


// Customizer Sidebar location
function iknowledgebase_sidebar_location() {
	$option = get_theme_mod( 'iknowledgebase_sidebar_location', 'left' );
	$class  = ( $option === 'right' ) ? ' is-flex-direction-row-reverse' : '';
	echo esc_attr( $class );
}

// Prints HTML with meta information for the current post-date/time.
function iknowledgebase_posted_on() {
	$time_string = sprintf( esc_html__( '%s ago', 'iknowledgebase' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );
	echo esc_attr( $time_string );

}

// Prints HTML with meta information for the current author.
function iknowledgebase_posted_by() {
	echo '<a class="" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>';

}

// Reading post time
function iknowledgebase_reading_time() {
	$content     = get_post_field( 'post_content', get_the_ID() );
	$word_count  = str_word_count( strip_tags( $content ) );
	$readingtime = ceil( $word_count / 200 );
	if ( $readingtime == 1 ) {
		$timer = esc_attr__( 'minute', 'iknowledgebase' );
	} else {
		$timer = esc_attr__( 'minutes', 'iknowledgebase' );
	}

	echo absint( $readingtime ) . ' ' . esc_attr( $timer );
}

// Get sticky post in category page
function iknowledgebase_get_sticky_posts_in_category() {
	$tax      = get_queried_object();
	$cat_id   = ! empty( $_GET['category'] ) ? absint( $_GET['category'] ) : $tax->term_id;
	$stickies = get_option( 'sticky_posts' );

	$post_icon         = apply_filters( 'iknowledgebase_post_icon', 'icon-book' );
	$sticky_icon_color = get_theme_mod( 'iknowledgebase_settings_sticky_icon_color', '' );
	$sticky_icon_color = ! empty( $sticky_icon_color ) ? ' ' . $sticky_icon_color : '';

	foreach ( $stickies as $sticky_id ) {
		if ( in_category( $cat_id, $sticky_id ) || iknowledgebase_post_is_in_descendant_category( $cat_id, $sticky_id ) ) {
			$title     = get_the_title( $sticky_id );
			$permalink = get_the_permalink( $sticky_id ); ?>
            <a class="panel-block is-borderless" href="<?php echo esc_url( $permalink ); ?>">
                <span class="panel-icon<?php echo esc_attr( $sticky_icon_color ); ?>">
                    <span class="<?php echo esc_attr( $post_icon ); ?>"></span>
                </span>
                <h4><?php echo esc_html( $title ); ?></h4>
            </a>
			<?php
		}
	}
}


// Checking whether a post belongs to the current or nested category
function iknowledgebase_post_is_in_descendant_category( $cats, $_post = null ) {
	foreach ( (array) $cats as $cat ) {
		// get_term_children() accepts integer ID only
		$descendants = get_term_children( (int) $cat, 'category' );
		if ( $descendants && in_category( $descendants, $_post ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Checks if AMP page is rendered.
 */
function iknowledgebase_is_amp() {
	return function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();
}


/**
 * Adds amp support for menu toggle.
 */
function iknowledgebase_amp_menu_toggle() {
	if ( iknowledgebase_is_amp() ) {
		echo "[aria-expanded]=\"mainMenuExpanded? 'true' : 'false'\" ";
		echo 'on="tap:AMP.setState({mainMenuExpanded: !mainMenuExpanded})" ';
		echo "[class]=\"'navbar-burger' + ( mainMenuExpanded ? ' is-active' : '' )\"";

	}
}


/**
 * Adds amp support for mobile dropdown navigation menu.
 */
function iknowledgebase_amp_menu_is_toggled() {
	if ( iknowledgebase_is_amp() ) {
		echo "[class]=\"'navbar-menu' + ( mainMenuExpanded ? ' is-active' : '' )\"";
	}
}

