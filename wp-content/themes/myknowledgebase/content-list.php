<?php
/*
 * The content used by files archive, index and search.
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('post-list'); ?>>
	<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
		<p class="sticky-title"><?php _e( 'Featured post', 'myknowledgebase' ); ?></p>
	<?php endif; ?>

	<h2 class="post-title entry-title">
		<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf(__('Permalink to %s', 'myknowledgebase'), the_title_attribute('echo=0')); ?>"> <?php the_title(); ?></a>
	</h2>

	<?php get_template_part( 'content-postmeta' ); ?>

	<div class="entry-content">
		<?php if ( has_post_thumbnail() ) {
			the_post_thumbnail('post-thumbnail', array('class' => 'list-image'));
		} ?>
		<?php if ( get_theme_mod( 'myknowledgebase_content_type' ) == "no" ) { ?>
			<?php the_content(); ?>
		<?php } else { ?>
			<?php the_excerpt(); ?>
		<?php } ?>
	</div>

	<?php if ( get_theme_mod( 'myknowledgebase_read_more' ) != "no" ) { ?>
		<div class="more">
			<a class="readmore" href="<?php the_permalink() ?>" rel="bookmark"><?php _e( 'Read More &raquo;', 'myknowledgebase' ); ?><span class="screen-reader-text"> <?php the_title(); ?></span></a>
		</div>
	<?php } ?>
</article>
