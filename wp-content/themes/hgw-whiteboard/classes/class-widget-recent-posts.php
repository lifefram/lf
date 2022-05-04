<?php
/**
 * Add Custom Recent Posts widget.
 */
class Hgw_Whiteboard_Recent_Posts extends WP_Widget {
  /**
  * Register widget with WordPress.
  */
  function __construct() {

      parent::__construct(
          'hgw-recent-post-widget',// Base ID
          __( 'Custom Posts widget', 'hgw-whiteboard' ), // Name
          array(
            'description' => __( 'Custom recent posts with image and by categories', 'hgw-whiteboard' ),// description
            )
      );

      add_action( 'widgets_init', function() {
          register_widget( 'Hgw_Whiteboard_Recent_Posts' );
      });

  }

  public $args = array(
      'before_title'  => '<h4 class="widget-title">',
      'after_title'   => '</h4>',
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget'  => '</div>'
  );
  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
    public function widget( $args, $instance ) {
      // Class
      $BTClass  = $args['before_title'];
      $ATClass  = $args['after_title'];
      $BWClass  = $args['before_widget'];
      $AWClass  = $args['after_widget'];
      // Widget Fields
      $WTitle = ( ! empty( $instance['hgw_recent_post_title'] ) ) ? $instance['hgw_recent_post_title'] : '';
      $WPostsNumber = ( ! empty( $instance['hgw_recent_post_postsnum'] ) ) ? $instance['hgw_recent_post_postsnum'] : '5';
      $WCategory = ( !empty( $instance['hgw_recent_post_caregory'] ) ) ? $instance['hgw_recent_post_caregory'] : '';
      $WDate = isset( $instance['hgw_recent_post_date'] ) ? $instance['hgw_recent_post_date'] : false;
      $WComments = isset( $instance['hgw_recent_post_comments'] ) ? $instance['hgw_recent_post_comments'] : false;

        // Start
        echo $BWClass;  // Before Widget Class
        if ($WTitle) {
          echo $BTClass . $WTitle . $ATClass;
        }

      		$postloop = new WP_Query(
            array(
              'posts_per_page' => $WPostsNumber,
              'cat'            => $WCategory
            )
          );
      		if ( $postloop->have_posts() ) {
            echo '<nav role="navigation"><ul class="hgw-posts-widget">';
      			while ( $postloop->have_posts() ) { $postloop->the_post();
              ?>
              <li class="post<?php if ( ! has_post_thumbnail() ) { echo ' noimg'; } ?>">
                <div class="post-inner">
                  <div class="post-thumbnail">
                    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
                    <?php
                    if ( has_post_thumbnail() ) {
                      the_post_thumbnail( 'thumbnail');
                    }
                    else {
                      echo '<div class="thumbnailbg wp-post-image"><img src="'.esc_url( get_template_directory_uri() . '/assets/img/default-thumbnail.jpg' ).'" alt="'.esc_attr( get_the_title() ).'" width="150" height="150"/></div>';
                    }
                    ?>
                    </a>
                  </div>
                  <div class="inner-post-content">
                      <div class="title">
                        <?php the_title( '<a href="' . esc_url( get_permalink() ) . '" class="entry-title">', '</a>' ) ?>
                      </div>

                      <?php if ( $WDate || $WComments ): ?>

                        <div class="info">
                          <?php
                          if ($WDate):
                          $copyright_format = "<i class='fa fa-clock-o' aria-hidden='true'></i>&nbsp;<time datetime='%1\$s'>%2\$s</time>";
                          echo sprintf(
                            $copyright_format,// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            esc_attr( get_the_date('c') ),
                            esc_html( get_the_date() )
                          );
                          endif;
                          ?>
                          <?php if ($WComments): ?>
                            <span class="comments">
                              <i class="fa fa-comment" aria-hidden="true"></i>
                              <?php comments_number(); ?>
                            </span>
                          <?php endif; ?>

                        </div>

                      <?php endif; ?>

                  </div>
                </div>
              </li><!-- Post Class -->
              <?php
            }
            echo "</ul></nav>";
      		}
          else {
            echo '<nav role="navigation"><ul><li class="post">'.esc_html__( 'Not post yet', 'hgw-whiteboard' ).'</li></ul></nav>';
           }
          wp_reset_postdata();
        echo $AWClass;  // After Widget Class
        // END
    }


    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
    		$instance['hgw_recent_post_title'] = sanitize_text_field( $new_instance['hgw_recent_post_title'] );
        $instance['hgw_recent_post_caregory'] = ( !empty( $new_instance['hgw_recent_post_caregory'] ) ) ? $new_instance['hgw_recent_post_caregory'] : '';
    		$instance['hgw_recent_post_postsnum']  = (int) $new_instance['hgw_recent_post_postsnum'];
        $instance['hgw_recent_post_date'] = isset( $new_instance['hgw_recent_post_date'] ) ? (bool) $new_instance['hgw_recent_post_date'] : false;
        $instance['hgw_recent_post_comments'] = isset( $new_instance['hgw_recent_post_comments'] ) ? (bool) $new_instance['hgw_recent_post_comments'] : false;
    		return $instance;
    }


    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {

        // Title Box
        $title = isset( $instance['hgw_recent_post_title'] ) ? esc_attr( $instance['hgw_recent_post_title'] ) : '';
        ?>
        <p>
          <label for="<?php echo esc_attr( $this->get_field_id( 'hgw_recent_post_title' ) ); ?>">
            <?php echo esc_html__( 'Title', 'hgw-whiteboard' ); ?>
          </label>
        <p>
        </p>
          <input
            class="widefat"
            id="<?php echo esc_attr( $this->get_field_id( 'hgw_recent_post_title' ) ); ?>"
            name="<?php echo esc_attr( $this->get_field_name( 'hgw_recent_post_title' ) ); ?>"
            type="text"
            value="<?php echo esc_attr( $title ); ?>"
          >
        </p>
        <?php
        // Category Box
        $frmCategory = ! empty( $instance['hgw_recent_post_caregory'] ) ? $instance['hgw_recent_post_caregory'] : '';
        ?>
        <p>
          <label for="<?php echo $this->get_field_id('hgw_recent_post_caregory'); ?>">
            <?php echo esc_html__( 'Category', 'hgw-whiteboard' ); ?>
          </label>
        </p>
        <p>
          <select name="<?php echo $this->get_field_name('hgw_recent_post_caregory')?>" id="<?php echo $this->get_field_id('hgw_recent_post_caregory') ?>" style="width: 100%;">
            <option value=""><?php esc_html__( 'Recent Posts', 'hgw-whiteboard' ) ?></option>
            <?php
            // Get Categories
            $hgw_rpw_Categories = get_categories(array('type'=>'post','orderby'=> 'name','order'=> 'ASC'));
            foreach( $hgw_rpw_Categories as $hgw_rpw_Cat) :
            ?>
      				<option value="<?php echo $hgw_rpw_Cat->cat_ID ?>" <?php echo selected( $hgw_rpw_Cat->cat_ID, $frmCategory, false) ?>>
                <?php echo $hgw_rpw_Cat->name . ' (' . $hgw_rpw_Cat->count . ')' ?>
              </option>
      			<?php endforeach; ?>
      		</select>
        </p>
        <p>
          <?php
          // Posts Number
          $PostsNumber = ! empty( $instance['hgw_recent_post_postsnum'] ) ? $instance['hgw_recent_post_postsnum'] : '5';
          ?>
          <label for="<?php echo $this->get_field_id( 'hgw_recent_post_postsnum' ); ?>"><?php _e( 'Number of posts', 'hgw-whiteboard' ); ?></label>
          <input class="tiny-text" id="<?php echo $this->get_field_id( 'hgw_recent_post_postsnum' ) ?>"
          name="<?php echo $this->get_field_name( 'hgw_recent_post_postsnum' ) ?>"
          type="number"
          value="<?php echo $PostsNumber ?>" step="1" min="1" size="3" />
        </p>
        <p>
          <?php
          $show_date = isset( $instance['hgw_recent_post_date'] ) ? (bool) $instance['hgw_recent_post_date'] : false;
          ?>
    			<input class="checkbox" type="checkbox"<?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'hgw_recent_post_date' ); ?>" name="<?php echo $this->get_field_name( 'hgw_recent_post_date' ); ?>" />
    			<label for="<?php echo $this->get_field_id( 'hgw_recent_post_date' ); ?>"><?php _e( 'Display post date', 'hgw-whiteboard' ); ?></label>
    		</p>
        <p>
          <?php
          $show_comment = isset( $instance['hgw_recent_post_comments'] ) ? (bool) $instance['hgw_recent_post_comments'] : false;
          ?>
    			<input class="checkbox" type="checkbox"<?php checked( $show_comment ); ?> id="<?php echo $this->get_field_id( 'hgw_recent_post_comments' ); ?>" name="<?php echo $this->get_field_name( 'hgw_recent_post_comments' ); ?>" />
    			<label for="<?php echo $this->get_field_id( 'hgw_recent_post_comments' ); ?>"><?php _e( 'Display post comments', 'hgw-whiteboard' ); ?></label>
    		</p>
        <?php
    }
}
$hgw_whiteboard_recent_posts = new Hgw_Whiteboard_Recent_Posts();
