<?php
/**
 * Custom template tags for this theme
 *
 * @subpackage Hgw_WhiteBoard
 */

if ( ! function_exists( 'hgw_whiteboard_posted_on' ) ) :
	/**
	 * meta information for the current post-date/time.
	 */
	function hgw_whiteboard_posted_on() {
		?>
		<span class="element">

			<i class="fa fa-user" aria-hidden="true"></i>

			<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) ?>">

				<?php echo esc_html( get_the_author() ) ?>

			</a>
			<span><?php echo esc_html_e( 'in', 'hgw-whiteboard' ); ?></span>
			<time class="entry-date published" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ) ?>">

				<?php echo esc_html( get_the_date('d M Y') ) ?>

			</time>

		</span>
		<?php
	}

endif;



if ( ! function_exists( 'hgw_whiteboard_categories' ) ) :
	/**
	 * Categories for the current post
	 */

    	function hgw_whiteboard_categories() {
        ?>
          <div class="entry-taxonomy-inner">

            <span class="screen-reader-text">

              <i class="fa fa-list" aria-hidden="true" title="<?php esc_attr_e( 'Categories', 'hgw-whiteboard' ); ?>"></i>

            </span>

        <?php

        $count_cat = 0;

        $getCats = get_the_category();

        $lastCat = array_key_last($getCats) + 1;

        foreach( $getCats as $getCat ){

          $count_cat++;

					$getCatURL = get_category_link($getCat->cat_ID);

					$getCatName = $getCat->cat_name;

          if ( $count_cat <= 3 ) {

            echo '<a href="'. esc_url( $getCatURL ).'">'.esc_html( $getCatName ).'</a>';

          }else {


            if ($count_cat < 3){

                  echo '<a href="'. esc_url( $getCatURL ).'">'.esc_html( $getCatName ).'</a>';

                }elseif ( $count_cat == 4 ) {

                  echo '<div class="more-tax"><span class="show-more-tax">More</span><div class="items">';
									echo '<a href="'. esc_url( $getCatURL ).'">'.esc_html( $getCatName ).'</a>';

								}elseif ( $count_cat == $lastCat ) {

                  echo '<a href="'. esc_url( $getCatURL ).'">'.esc_html( $getCatName ).'</a></div></div>';

								}else{

                echo '<a href="'. esc_url( $getCatURL ).'">'.esc_html( $getCatName ).'</a>';

								}

          }

        }

        ?>

        </div><!-- .entry-categories-inner -->

        <?php
	}
endif;



if ( ! function_exists( 'hgw_whiteboard_copyright' ) ) :

function hgw_whiteboard_copyright() {
	/**
	 * Add Copyright to footer
	 */
	global $wpdb;

	$copyright_dates = $wpdb->get_results("
			SELECT
			YEAR(min(post_date_gmt)) AS firstdate,
			YEAR(max(post_date_gmt)) AS lastdate
			FROM
			$wpdb->posts
			WHERE
			post_status = 'publish'
		");

	$output = '';

	if($copyright_dates) {

		$copyright = "&copy; " . $copyright_dates[0]->firstdate;

		if($copyright_dates[0]->firstdate != $copyright_dates[0]->lastdate) {

			$copyright .= '-' . $copyright_dates[0]->lastdate;

		}

		$output = $copyright;

	}

	return $output;

}

endif;


if ( ! function_exists( 'hgw_whiteboard_breadcrumbs_data' ) ) :

	function hgw_whiteboard_breadcrumbs_data(){

	  $delimiter = '/';
	  $home = esc_html__( 'Home', 'hgw-whiteboard' ); // text for the 'Home' link
	  $before = '<span>'; // tag before the current crumb
	  $after = '</span>'; // tag after the current crumb
	      if ( !is_home() && !is_front_page() || is_paged() ) {
	          global $post;
	          $homeLink = esc_url( home_url() );
	          echo '<a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';
	              if ( is_category() )
	                  {
	                      global $wp_query;
	                      $cat_obj = $wp_query->get_queried_object();
	                      $thisCat = $cat_obj->term_id;
	                      $thisCat = get_category($thisCat);
	                      $parentCat = get_category($thisCat->parent);
	                      if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
	                      echo $before . esc_html__( 'Category', 'hgw-whiteboard' ) . $delimiter . single_cat_title('', false) . '' . $after;
	                  }
	              elseif ( is_day() )
	                  {
	                      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
	                      echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
	                      echo $before . get_the_time('d') . $after;
	                  }
	              elseif ( is_month() )
	                  {
	                      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
	                      echo $before . get_the_time('F') . $after;
	                  }
	              elseif ( is_year() )
	                  {
	                      echo $before . get_the_time('Y') . $after;
	                  }
	              elseif ( is_single() && !is_attachment() )
	                  {
	                      if ( get_post_type() != 'post' )
	                          {
	                              $post_type = get_post_type_object(get_post_type());
	                              $slug = $post_type->rewrite;
	                              echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
	                              echo $before . get_the_title() . $after;
	                          }
	                      else
	                          {
	                              $cat = get_the_category(); $cat = $cat[0];
	                              echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
	                              echo $before . get_the_title() . $after;
	                          }
	                  }
	              elseif ( is_attachment() )
	                  {
	                      $parent = get_post($post->post_parent);
	                      $cat = get_the_category($parent->ID); $cat = $cat[0];
	                      echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
	                      echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
	                      echo $before . get_the_title() . $after;
	                  }
	              elseif ( is_page() && !$post->post_parent )
	                  {
	                      echo $before . get_the_title() . $after;
	                  }
	              elseif ( is_page() && $post->post_parent )
	                  {
	                      $parent_id  = $post->post_parent;
	                      $breadcrumbs = array();
	                      while ($parent_id)
	                          {
	                              $page = get_page($parent_id);
	                              $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
	                              $parent_id  = $page->post_parent;
	                          }
	                      $breadcrumbs = array_reverse($breadcrumbs);
	                      foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
	                      echo $before . get_the_title() . $after;
	                  }
	              elseif ( is_search() )
	                  {
	                      echo $before . esc_html__( 'Search results for', 'hgw-whiteboard' ) . ' "' . get_search_query() . '"' . $after;
	                  }
	              elseif ( is_tag() )
	                  {
	                      echo $before . esc_html__( 'Tag', 'hgw-whiteboard' ) .' "' . single_tag_title('', false) . '"' . $after;
	                  }
	              elseif ( is_author() )
	                  {
	                      global $author;
	                      $userdata = get_userdata($author);
	                      echo $before . esc_html__( 'Posts', 'hgw-whiteboard' ) .' ' . $userdata->display_name . $after;
	                  }
	              elseif ( is_404() )
	                  {
	                      echo $before . esc_html__( '404 Error', 'hgw-whiteboard' ) . $after;
	                  }
	              if ( get_query_var('paged') )
	                  {
	                      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
	                      echo esc_html__( 'Page', 'hgw-whiteboard' ) . ' ' . get_query_var('paged');
	                      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
	                  }
	      }


	}

endif;

if ( ! function_exists( 'hgw_whiteboard_breadcrumbs' ) ) :

	function hgw_whiteboard_breadcrumbs(){

		if ( get_theme_mod( 'hgw_show_breadcrumb', 0 ) == 1 && !is_front_page() && !is_home() ) {

			echo '<div class="hgw-breadcrumbs">';

				if ( get_theme_mod( 'hgw_type_breadcrumb' ) == 'hgwwhiteboard' ) {

					hgw_whiteboard_breadcrumbs_data();

				}

				if ( get_theme_mod( 'hgw_type_breadcrumb' ) == 'yoast' ){

					if ( function_exists('yoast_breadcrumb') ) {
						yoast_breadcrumb();
					}else{
						echo esc_html__( 'You must install the Yoast plugin', 'hgw-whiteboard' );
					}

				}

				if ( get_theme_mod( 'hgw_type_breadcrumb' ) == 'rankmath' ){

					if (function_exists('rank_math_the_breadcrumbs')) {
						rank_math_the_breadcrumbs();
					}else{
						echo esc_html__( 'You must install the Rank Math plugin, Check the plugin settings if Rank Math is installed', 'hgw-whiteboard' );
					}

				}


			echo '</div>';

		}

	}

endif;
