<?php
/**
 * The template for displaying home page.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Library Books
 */
get_header();
/**
 * Show the slider in front page section
 */
$library_books_hideslide = get_theme_mod('hide_slides', 1);
if (!is_home() && is_front_page())
	{
	if ($library_books_hideslide == '')
		{
		$library_books_slidepages = array();
		for ($library_books_sld = 10; $library_books_sld < 13; $library_books_sld++)
			{
			$library_books_mod = absint(get_theme_mod('page-setting' . $library_books_sld));
			if ('page-none-selected' != $library_books_mod)
				{
				$library_books_slidepages[] = $library_books_mod;
				}
			}
		if (!empty($library_books_slidepages)):
			$library_books_args = array(
				'posts_per_page' => 3,
				'post_type' => 'page',
				'post__in' => $library_books_slidepages,
				'orderby' => 'post__in'
			);
			$library_books_query = new WP_Query($library_books_args);
			if ($library_books_query->have_posts()):
				$library_books_sld = 10;
?>
<section id="home_slider">
  <div class="slider-wrapper theme-default">
    <div id="slider" class="nivoSlider">
      <?php
				$library_books_i = 0;
				while ($library_books_query->have_posts()):
					$library_books_query->the_post();
					$library_books_i++;
					$library_books_slideno[] = $library_books_i;
					$library_books_slidetitle[] = get_the_title();
					$library_books_slidedesc[] = get_the_excerpt();
					$library_books_slidelink[] = esc_url(get_permalink());
					if (has_post_thumbnail()) { ?>
	          <img src="<?php the_post_thumbnail_url('full'); ?>" title="#slidecaption<?php echo esc_attr($library_books_i); ?>" />
          	  <?php } else {  ?>
          	  <img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/no_slide.jpg" title="#slidecaption<?php echo esc_attr($library_books_i); ?>" /><?php } $library_books_sld++; endwhile; ?>
    		</div>
	  		<?php
				$library_books_k = 0;
				foreach($library_books_slideno as $library_books_sln)
				{ ?>
    		<div id="slidecaption<?php echo esc_attr($library_books_sln); ?>" class="nivo-html-caption">
      		<div class="slide_info">
        	<h2><?php echo esc_html($library_books_slidetitle[$library_books_k]); ?></h2>
        	<p><?php echo esc_html($library_books_slidedesc[$library_books_k]); ?></p>
        	<div class="clear"></div>
        	<?php $library_books_slide_button = get_theme_mod('slide_button');
				  if (!empty($library_books_slide_button)) { ?>
       			 <a class="slide_more" href="<?php echo esc_url($library_books_slidelink[$library_books_k]); ?>"><?php echo esc_html($library_books_slide_button); ?> </a>
        	<?php } ?>
      </div>
    </div>
	    <?php
		$library_books_k++;
		wp_reset_postdata();
		} 
		endif;
		endif; ?>
  </div>
  <div class="clear"></div>
</section>
<?php
		}
	} 
wp_reset_postdata(); ?>
<div class="clear"></div>

<?php
if (!is_home() && is_front_page())
	{
	/**
	 * Section One
	 */
	$library_books_hidesectionone = get_theme_mod('hide_sectionone', 1);
	if ($library_books_hidesectionone == '')
	{
?>
<section class="homeone_section_area">
    <div class="center">
        <div class="homeone_section_content">
          <?php 
        if( get_theme_mod('hmpage-column1',false)) {
        $library_books_homeonequery = new WP_query('page_id='.get_theme_mod('hmpage-column1',true)); 
        while( $library_books_homeonequery->have_posts() ) : $library_books_homeonequery->the_post(); ?>
        <div class="section-leftarea">
          <h2>
            <?php
            $library_books_section1_title = get_theme_mod('section1_title');
            if(!empty($library_books_section1_title)){?>
            <small><?php echo esc_html($library_books_section1_title); ?></small>
            <?php } ?>
            <?php the_title(); ?>
          </h2>
          <?php the_content(); ?>
        </div>
        <?php if( has_post_thumbnail() ) { ?>
        <div class="section-rightarea">
          <?php the_post_thumbnail('full'); ?>
        </div>
        <?php } ?>
        <?php endwhile;
              wp_reset_postdata(); 
        } ?>
        <div class="clear"></div>
        </div>
    </div>    
</section>
<?php }  } ?>
<?php
if (!is_home() && is_front_page())
	{
	/**
	 * Section Two 
	 */
	$library_books_hidesectiontwo = get_theme_mod('hide_section_two', 1);
	if ($library_books_hidesectiontwo == '')
	{
?>
<section class="hometwo_section_area">
    <div class="center">
        <div class="hometwo_section_content">
        	<?php $library_books_section2_title = get_theme_mod('section2_title'); ?>
            <?php if(!empty($library_books_section2_title)){?>
            	<h2><?php echo esc_html($library_books_section2_title); ?></h2>
            <?php } ?>
            <div class="hometwo-columns-row">
  				<?php 
				for ($library_books_q = 1; $library_books_q < 9; $library_books_q++)
				{ 
				if (get_theme_mod('sec2-page-column' . $library_books_q, false))
				{
				$library_books_section2query = new WP_query('page_id=' . get_theme_mod('sec2-page-column' . $library_books_q, true));
				while ($library_books_section2query->have_posts()): $library_books_section2query->the_post(); ?>            
            	<div class="hometwo-columns">
                <div class="hometwo-columnn-inner">
				<?php if (has_post_thumbnail()) { the_post_thumbnail(); } ?>
				<a href="<?php echo esc_url(get_permalink()); ?>" class="hometwo-columnn-overlay"><h3 class="hometwo-column-overlay-title"><?php the_title(); ?></h3></a>
                <h3 class="hometwo-column-title"><?php the_title(); ?></h3>
                </div>
                </div>
				<?php
                endwhile;
                wp_reset_postdata();
                }
                } 
                ?>
            </div>
            <div class="clear"></div>
        </div>
     </div>
</section>        
<?php
		}
	}
if (!is_home() && is_front_page()){
	$library_books_hidesectionthree = get_theme_mod('hide_section_three', 1);
	if ($library_books_hidesectionthree == '')
	{ ?>
<section class="homethree_section_area">
    <div class="center">
        <div class="homethree_section_content">
        	<?php $library_books_section3_title = get_theme_mod('section3_title'); ?>
            <?php if(!empty($library_books_section3_title)){?>
            	<h2><?php echo esc_html($library_books_section3_title); ?></h2>
            <?php } ?>  
            <div class="homethree-columns-row">
  				<?php 
				for ($library_books_z = 1; $library_books_z < 4; $library_books_z++)
				{ 
				if (get_theme_mod('sec3-page-column' . $library_books_z, false))
				{
				$library_books_section3query = new WP_query('page_id=' . get_theme_mod('sec3-page-column' . $library_books_z, true));
				while ($library_books_section3query->have_posts()): $library_books_section3query->the_post(); ?>             
            	<div class="homethree_columns">
                	<div class="homethree-column-inner">
					<?php if (has_post_thumbnail()) {?><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_post_thumbnail();?></a><?php } ?>
                    <div class="homethree-column-content">
                    	<div class="numberbg"><?php echo esc_html($library_books_z); ?></div>
                        <h3><a href="<?php echo esc_url(get_permalink()); ?>"><?php the_title(); ?></a></h3>
                        <?php the_excerpt(); ?>
                     </div>
                     </div>
                </div>
                <?php
                endwhile;
                wp_reset_postdata();
                }
                } 
                ?>
            </div>    
            <div class="clear"></div> 
        </div>
    </div>
</section>        
<?php
		}
	}
wp_reset_postdata(); ?>
<div class="container">
  <div class="page_content">
    <?php if ('posts' == get_option('show_on_front')) { ?>
    <section class="site-main">
      <div class="blog-post">
        <?php
		if (have_posts()):
		// Start the Loop.
		while (have_posts()):
			the_post();
			/*
			* Include the post format-specific template for the content. If you want to
			* use this in a child theme, then include a file called called content-___.php
			* (where ___ is the post format) and that will be used instead.
			*/
			get_template_part('content', get_post_format());
		endwhile;
		// Previous/next post navigation.
		the_posts_pagination(array(
			'mid_size' => 2,
			'prev_text' => esc_html__('Back', 'library-books') ,
			'next_text' => esc_html__('Next', 'library-books') ,
		));
	else:
		// If no content, include the "No posts found" template.
		get_template_part('no-results', 'index');
	endif;
?>
      </div>
      <!-- blog-post --> 
    </section>
    <?php
	}
  else
	{
?>
    <section class="site-main">
      <div class="blog-post">
        <?php
	if (have_posts()):
		// Start the Loop.
		while (have_posts()):
			the_post();
			/*
			* Include the post format-specific template for the content. If you want to
			* use this in a child theme, then include a file called called content-___.php
			* (where ___ is the post format) and that will be used instead.
			*/
?>
        <header class="entry-header">
          <h1>
            <?php
			the_title(); ?>
          </h1>
        </header>
        <?php
			the_content();
			wp_link_pages(array(
				'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'library-books') . '</span>',
				'after' => '</div>',
				'link_before' => '<span>',
				'link_after' => '</span>',
				'pagelink' => '<span class="screen-reader-text">' . esc_html__('Page', 'library-books') . ' </span>%',
				'separator' => '<span class="screen-reader-text">, </span>',
			));
?>
        <div class="clear"></div>
        <?php
			// If comments are open or we have at least one comment, load up the comment template.
			if (comments_open() || get_comments_number()):
				comments_template();
			endif;
		endwhile;
		// Previous/next post navigation.
		the_posts_pagination(array(
			'mid_size' => 2,
			'prev_text' => esc_html__('Back', 'library-books') ,
			'next_text' => esc_html__('Next', 'library-books') ,
		));
	else:
		// If no content, include the "No posts found" template.
		get_template_part('no-results', 'index');
	endif;
?>
      </div>
      <!-- blog-post --> 
    </section>
    <?php
	}
	get_sidebar();
?>
   <div class="clear"></div>
  </div>
  <!-- site-aligner --> 
</div>
<!-- content -->
<?php get_footer(); ?>