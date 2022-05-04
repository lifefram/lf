<?php
/**
 * Widget API: Iknow widget for category and single posts
 *
 * @package iknowledgebase
 */

class iknowledgebase_Widget_Current_Nav extends WP_Widget {

	/**
	 * Sets up a new Category_Post widget instance.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'iknowledgebase_widget_current_nav menu',
			'description'                 => esc_attr__( 'Navigation for current category and post.', 'iknowledgebase' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'iknowledgebase_widget_current_nav', esc_attr__( 'Iknowledgebase Current Nav', 'iknowledgebase' ), $widget_ops );
	}

	public function widget( $args, $instance ) {

		if ( ! is_category() && ! is_singular( 'post' ) ) {
			return;
		}

		$title        = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$post_number  = ! empty( $instance['post_number'] ) ? $instance['post_number'] : 0;
		$post_orderby = ! empty( $instance['post_orderby'] ) ? $instance['post_orderby'] : 'date';
		$post_order   = ! empty( $instance['post_order'] ) ? $instance['post_order'] : 'DESC';
		$post_arg     = array(
			'numberposts' => $post_number,
			'orderby'     => $post_orderby,
			'order'       => $post_order,
		);

		$current_background = get_theme_mod( 'iknowledgebase_widget_main_color', '' );
		$current_color      = get_theme_mod( 'iknowledgebase_widget_current_color', '' );

		$display_posts = ! empty( $instance['display_posts'] ) ? '1' : '0';

		$out = $args['before_widget'];

		if ( $title ) {
			$out .= $args['before_title'] . esc_attr( $title ) . $args['after_title'];
		}

		if ( is_category() ) {
			$cat        = get_queried_object();
			$cat_parent = ( $cat->category_parent === 0 ) ? $cat->cat_ID : $cat->category_parent;

			$out .= '<p class="menu-label">' . esc_attr( get_the_category_by_ID( $cat_parent ) ) . '</p>';

			$child_cats = get_categories(
				array( 'parent' => $cat_parent )
			);

			if ( $child_cats ) {
				$out .= '<ul class="menu-list">';
				foreach ( $child_cats as $child ) {
					$cat_link     = get_category_link( $child->cat_ID );
					$cat_icon     = apply_filters( 'iknowledgebase_category_icon', 'icon-folder', $child->cat_ID );
					$current      = ( $cat->cat_ID === $child->cat_ID ) ? 'is-active' : '';
					$current_icon = ( $cat->cat_ID === $child->cat_ID ) ? ' has-text-white' : ' has-text-primary';
					$current_tag  = ( $cat->cat_ID === $child->cat_ID ) ? ' has-text-primary has-text-weight-bold' : '';
					$out          .= '<li><a href="' . esc_url( $cat_link ) . '" class="is-flex ' . esc_attr( $current ) . '">';
					$out          .= '<span class="mr-2 icon' . esc_attr( $current_icon ) . ' ' . esc_attr( $cat_icon ) . '"></span>';
					$out          .= '<span>' . esc_attr( $child->name ) . '</span><span class="tag ml-auto' . esc_attr( $current_tag ) . '">' . absint( $child->count ) . '</span></a></li>';
				}
				$out .= '</ul>';
			}

		} elseif ( is_single() ) {
			$cat = get_the_category()[0];

			$cat_parent = ( $cat->category_parent === 0 ) ? $cat->cat_ID : $cat->category_parent;

			$out .= '<p class="menu-label">' . esc_attr( get_the_category_by_ID( $cat_parent ) ) . '</p>';

			$child_cats = get_categories(
				array( 'parent' => $cat_parent )
			);

			$out      .= '<ul class="menu-list">';
			$cat_icon = apply_filters( 'iknowledgebase_category_icon', 'icon-folder-open', $cat->cat_ID );
			$url      = get_category_link( $cat->cat_ID );
			$out      .= '<li>';
			if ( $child_cats ) {
				$out .= '<a class="is-active is-flex ' . esc_attr( $current_background ) . '" href="' . esc_url( $url ) . '">';
				$out .= '<span class="mr-2 icon has-text-white ' . esc_attr( $cat_icon ) . '"></span>';
				$out .= '<span>' . esc_attr( $cat->cat_name ) . '</span><span class="tag is-light has-text-primary has-text-weight-bold ml-auto">' . absint( $cat->count ) . '</span></a>';
			}
			if ( empty( $display_posts ) ) {
				$out .= $this->get_cat_posts( $cat->cat_ID, $post_arg );
			}
			$out .= '</li>';

			if ( $child_cats ) {
				foreach ( $child_cats as $child ) {
					if ( $child->cat_ID === $cat->cat_ID ) {
						continue;
					}
					$child_icon = apply_filters( 'iknowledgebase_category_icon', 'icon-folder', $child->cat_ID );
					$cat_link   = get_category_link( $child->cat_ID );
					$out        .= '<li><a href="' . esc_url( $cat_link ) . '" class="is-flex">';
					$out        .= '<span class="mr-2 icon has-text-primary  ' . esc_attr( $child_icon ) . '"></span>';
					$out        .= '<span>' . esc_attr( $child->name ) . '</span><span class="tag ml-auto">' . absint( $child->count ) . '</span></a></li>';
				}
			}
			$out .= '</ul>';
		}

		$out .= $args['after_widget'];

		echo wp_kses_post( $out );


	}

	/**
	 * Handles updating settings for the current Categories widget instance.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Updated settings to save.
	 * @since 2.8.0
	 *
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                  = $old_instance;
		$instance['title']         = sanitize_text_field( $new_instance['title'] );
		$instance['display_posts'] = ! empty( $new_instance['display_posts'] ) ? 1 : 0;
		$instance['post_number']   = isset( $new_instance['post_number'] ) ? absint( $new_instance['post_number'] ) : 0;
		$instance['post_orderby']  = isset( $new_instance['post_orderby'] ) ? $this->sanitize_post_orderby( $new_instance['post_orderby'] ) : 'date';
		$instance['post_order']    = isset( $new_instance['post_order'] ) ? $this->sanitize_post_order( $new_instance['post_order'] ) : 'DESC';


		return $instance;
	}

	/**
	 * Outputs the settings form for the Categories widget.
	 *
	 * @param array $instance Current settings.
	 *
	 * @since 2.8.0
	 *
	 */
	public function form( $instance ) {
		//Defaults
		$instance      = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$display_posts = isset( $instance['display_posts'] ) ? (bool) $instance['display_posts'] : false;
		$post_number   = isset( $instance['post_number'] ) ? $instance['post_number'] : 0;
		$post_orderby  = isset( $instance['post_orderby'] ) ? $instance['post_orderby'] : 'date';
		$post_order    = isset( $instance['post_order'] ) ? $instance['post_order'] : 'DESC';

		?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'iknowledgebase' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
                   value="<?php echo esc_attr( $instance['title'] ); ?>"/></p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'post_number' ) ); ?>"><?php esc_html_e( 'Number of posts:', 'iknowledgebase' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_number' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'post_number' ) ); ?>" type="number"
                   value="<?php echo esc_attr( $post_number ); ?>"/></p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'post_orderby' ) ); ?>"><?php esc_html_e( 'Posts orderby:', 'iknowledgebase' ); ?></label><br/>
            <select id="<?php echo esc_attr( $this->get_field_id( 'post_orderby' ) ); ?>"
                    name="<?php echo esc_attr( $this->get_field_name( 'post_orderby' ) ); ?>">
                <option value="date" <?php selected( $post_orderby, 'date' ); ?>><?php esc_html_e( 'Date', 'iknowledgebase' ); ?></option>
                <option value="title" <?php selected( $post_orderby, 'title' ); ?>><?php esc_html_e( 'Title', 'iknowledgebase' ); ?></option>
                <option value="comment_count" <?php selected( $post_orderby, 'comment_count' ); ?>><?php esc_html_e( 'Comment count', 'iknowledgebase' ); ?></option>
                <option value="ID" <?php selected( $post_orderby, 'ID' ); ?>><?php esc_html_e( 'ID', 'iknowledgebase' ); ?></option>
            </select></p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'post_order' ) ); ?>"><?php esc_html_e( 'Posts order:', 'iknowledgebase' ); ?></label><br/>
            <select id="<?php echo esc_attr( $this->get_field_id( 'post_order' ) ); ?>"
                    name="<?php echo esc_attr( $this->get_field_name( 'post_order' ) ); ?>">
                <option value="DESC" <?php selected( $post_order, 'DESC' ); ?>><?php esc_html_e( 'DESC ', 'iknowledgebase' ); ?></option>
                <option value="ASC" <?php selected( $post_order, 'ASC' ); ?>><?php esc_html_e( 'ASC', 'iknowledgebase' ); ?></option>
            </select></p>

        <input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'display_posts' ) ); ?>"
               name="<?php echo esc_attr( $this->get_field_name( 'display_posts' ) ); ?>"<?php checked( $display_posts ); ?> />
        <label for="<?php echo esc_attr( $this->get_field_id( 'display_posts' ) ); ?>"><?php esc_html_e( 'Hide Posts', 'iknowledgebase' ); ?></label>
        <br/>

		<?php
	}

	private function get_cat_posts( $cat_ID, $post_arg ) {
		$defaults = array(
			'numberposts' => 0,
			'category'    => $cat_ID,
			'orderby'     => 'date',
			'order'       => 'DESC',
		);

		$args              = wp_parse_args( $post_arg, $defaults );
		$posts             = get_posts( $args );
		$sticky_icon_color = get_theme_mod( 'iknowledgebase_settings_sticky_icon_color', '' );
		$sticky_icon_color = ! empty( $sticky_icon_color ) ? ' ' . $sticky_icon_color : '';
		$out               = '';
		if ( $posts ) {
			$post_icon       = apply_filters( 'iknowledgebase_post_icon', 'icon-book' );
			$current_post_id = get_the_ID();
			$out             .= '<ul class="mx-0 my-2 px-0">';
			foreach ( $posts as $post ) {
				setup_postdata( $post );
				if ( $current_post_id === $post->ID ) {
					$out .= '<li><a href="' . esc_url( get_permalink( $post->ID ) ) . '" class="is-radiusless is-size-7 has-background-light">';
				} else {
					$out .= '<li><a href="' . esc_url( get_permalink( $post->ID ) ) . '" class="is-radiusless is-size-7">';
				}
				if ( is_sticky( $post->ID ) ) {
					$out .= '<span class="icon ' . esc_attr( $post_icon . $sticky_icon_color ) . '"></span>';
				} else {
					$out .= '<span class="icon ' . esc_attr( $post_icon ) . '"></span>';
				}

				$out .= esc_attr( get_the_title( $post->ID ) ) . '</a></li>';
			}
			wp_reset_postdata();
			$out .= '</ul>';
		}

		return $out;
	}

	private function sanitize_post_orderby( $input ) {
		$valid = array(
			''              => esc_attr__( 'Date', 'iknowledgebase' ),
			'title'         => esc_attr__( 'Title', 'iknowledgebase' ),
			'comment_count' => esc_attr__( 'Comment count', 'iknowledgebase' ),
			'ID'            => esc_attr__( 'ID', 'iknowledgebase' ),
		);

		if ( array_key_exists( $input, $valid ) ) {
			return $input;
		} else {
			return 'date';
		}
	}

	private function sanitize_post_order( $input ) {
		$valid = array(
			'ASC'  => esc_attr__( 'ASC', 'iknowledgebase' ),
			'DESC' => esc_attr__( 'DESC', 'iknowledgebase' ),
		);

		if ( array_key_exists( $input, $valid ) ) {
			return $input;
		} else {
			return esc_attr__( 'DESC', 'iknowledgebase' );
		}
	}
}