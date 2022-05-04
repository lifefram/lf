<?php
$twosidebarOptions = get_theme_mod( 'hgw_sidebar_types', 'default-sidebar' );
if ( $twosidebarOptions != 'no-sidebar') :
?>
<aside id="sidebar" class="sidebar secondary">

  <div class="sticky">

    <?php
    if ( is_active_sidebar( 'secondary-sidebar' ) ) {

          dynamic_sidebar('secondary-sidebar');

    }else{
      echo '<div class="widget">';
        echo '<h4 class="widget-title">'.esc_html__( 'Recent Posts', 'hgw-whiteboard' ).'</h4>';
        $postwloop = new WP_Query( array( 'posts_per_page' => 5, ) );
        if ( $postwloop->have_posts() ) {
          echo '<nav role="navigation"><ul class="hgw-posts-widget">';
          while ( $postwloop->have_posts() ) { $postwloop->the_post();
            ?>
            <li class="post>">
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

                      <div class="info">
                        <?php
                        $copyright_format = "<i class='fa fa-clock-o' aria-hidden='true'></i>&nbsp;<time datetime='%1\$s'>%2\$s</time>";
                        echo sprintf(
                          $copyright_format,// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                          esc_attr( get_the_date('c') ),
                          esc_html( get_the_date() )
                        );
                        ?>
                          <span class="comments">
                            <i class="fa fa-comment" aria-hidden="true"></i>
                            <?php comments_number(); ?>
                          </span>

                      </div>
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
        echo '</div>';
      ?>
      <div class="widget">

        <h4 class="widget-title"><?php echo esc_html__( 'Categories', 'hgw-whiteboard' ) ?></h4>

        <nav>
          <ul>
            <?php
            wp_list_categories(
              array(
                   'title_li'   => '',
                   'orderby'    => 'name',
                   'show_count' => 0,
                   'orderby'    => 'count',
                   'order'      => 'DESC'
               )
             );
             ?>
          </ul>

        </nav>

      </div>

      <?php

    }

  ?>

  </div>

</aside>
<?php endif; ?>
