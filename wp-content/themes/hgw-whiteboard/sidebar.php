<?php
$sidebarOptions = get_theme_mod( 'hgw_sidebar_types', 'default-sidebar' );
if ( $sidebarOptions != 'no-sidebar') :
?>
<aside id="sidebar" class="sidebar">

  <div class="sticky">

      <?php

      if ( ! is_search() && get_theme_mod('hgw_show_sidebar_search_form', 1 ) == 1 ) { ?>

        <div class="widget top-search">

          <h4 class="widget-title"><?php esc_html_e('Search', 'hgw-whiteboard') ?></h4>

          <form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ) ?>">

            <div>

              <input type="text" id="searchtext" value="" name="s" placeholder="<?php echo esc_attr_e( 'Search ...', 'hgw-whiteboard' ); ?>" autocomplete="off"/>

              <button type="submit" id="searchsubmit" value="Search"><i class="fa fa-search"></i></button>

            </div>

          </form>

        </div>

      <?php

      }

    if ( is_active_sidebar( 'main-sidebar' ) ) {

          dynamic_sidebar('main-sidebar');

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
