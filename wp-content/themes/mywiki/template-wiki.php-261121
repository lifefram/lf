<?php
/*
Template name: Wiki template
*/ 
get_header(); ?>
<div id="content" class="row">
  <div id="main" class="col-sm-12 clearfix " role="main">
    <div id="home-main" class="home-main home">
      <header>
        <div class="page-catheader">
          <h2 class="page-title"><i class="fa fa-folder"></i>
          <?php 
            echo esc_html(get_theme_mod('mywiki_category_title',esc_html__('Knowledgebase Categories','mywiki')));  ?></h2>
        </div>
      </header>
      <?php $mywiki_category_selected = get_theme_mod('mywiki_category_list',array());
      if(empty($mywiki_category_selected)||$mywiki_category_selected[0]==0):$mywiki_category_selected=array();endif;
      $mywiki_category_perpage = get_theme_mod('mywiki_category_count',3);     
		$cat = array('child_of'=> 0,'hide_empty'=> 0,'hierarchical'=> 1,'taxonomy'=> 'category','pad_counts'=> false);	 
		$cat = get_categories( $cat ); 
		$i=0;
		foreach ($cat as $categories) { 
			if(empty($mywiki_category_selected) || in_array($categories->term_id, $mywiki_category_selected)){
				$i++;
				if($i<5):$cat_id="cat-id"; else: $cat_id=''; endif;	?>
	      		<div class="cat-main-section col-md-4 col-sm-6 col-xs-12 <?php echo esc_attr($cat_id); ?>"> 
			        <a href="<?php echo esc_url(get_category_link( $categories->term_id ));?>">
			 	       <h4><i class="fa <?php echo esc_attr(get_theme_mod('mywiki_category_icon','fa-list-alt')); ?>"></i> <?php echo esc_html($categories->name) ;?> <span>(<?php echo esc_html($categories->count); ?>)</span>
			 	       </h4>
			        </a> 
			        <?php
					 $args = array(
							'posts_per_page' => $mywiki_category_perpage,				
							'tax_query' => array(
							'relation' => 'AND',
							array(
								'taxonomy' => 'category',
								'field' => 'id',
								'terms' => array($categories->term_id),
							),
						)
					); 
					$cat_post = new WP_Query( $args );
					if ( $cat_post->have_posts() ) :?>
			        <div class="content-according">
			          <ul>
			            <?php while ( $cat_post->have_posts() ):$cat_post->the_post(); ?>
			            <li><i class="fa <?php echo esc_attr(get_theme_mod('mywiki_category_post_icon','fa-file-text-o')); ?>"></i><a href="<?php the_permalink(); ?>">
			              <?php the_title();?>
			              </a></li>
			            <?php endwhile;?>
			          </ul>
			        </div>
		        <?php endif;?>
		    </div>
		<?php 					
			}
		}?>
    </div>
    <!-- end #main --> 
  </div>
</div>
</div>
<!-- end #content -->
<?php get_footer();?>
