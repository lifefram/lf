<?php get_header(); ?>
<div id="content" class="clearfix">
  <div id="main" class="col-sm-8 clearfix" role="main">
    <div id="home-main" class="home-main home">
      <header>
        <div class="page-catheader cat-catheader">
            <h4 class="cat-title">
				<span><?php esc_html_e('Search','mywiki'); echo " : "?></span>
				<?php echo get_search_query(); ?>
            </h4>
         </div>
      </header>
	  <div style="padding-top: 30px;"></div>
	  <div class="row">
		<?php
			add_filter('get_search_form', 'mywiki_search_form');
            get_search_form('mywiki_search_form');
            remove_filter('get_search_form', 'mywiki_search_form');
		?>
	  </div>
	  <div style="padding-bottom: 30px;"></div>
	  
      <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<hr class="hr hr-blurry" />
		<div style="padding-top: 15px;"></div>
		<div class="row">
			<div class="col-md-2">
			  <?php the_post_thumbnail( 'thumbnail' ); ?>
			</div>
				<h3 style="margin-top: 0px;" >
					<a style="padding-left: 15px; -webkit-box-decoration-break: clone;" href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
				</h3>
			<div class="col-md-10" >
				<?php the_excerpt(); ?>
			</div>
		</div>
      <?php endwhile; ?>
	  <?php endif; ?>
		<!--Pagination Start-->
    <?php if(get_option('posts_per_page ') < $wp_query->found_posts) { ?>
    <nav class="mywiki-nav">
            <span class="mywiki-nav-previous"><?php previous_posts_link(); ?></span>
            <span class="mywiki-nav-next"><?php next_posts_link(); ?></span>
        </nav>
    <?php } ?>
    <!--Pagination End-->
    </div>
  </div>
</div>
<!-- end #content -->
<?php get_footer(); ?>