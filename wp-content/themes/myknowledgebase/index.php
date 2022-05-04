<?php
/*
 * The template for main blog page.
 */
?>

<?php get_header(); ?>
<div id="content" role="main">
	<?php if ( is_home() ) { ?>
		<?php if ( get_theme_mod( 'myknowledgebase_blog_title' ) ) : ?>
			<h1 class="page-title">
				<?php echo esc_attr( get_theme_mod('myknowledgebase_blog_title') ); ?>
			</h1>
		<?php endif; ?>
		<?php if ( get_theme_mod( 'myknowledgebase_blog_content' ) ) : ?>
			<div class="blog-page-text">
				<?php echo wpautop(wp_kses_post( get_theme_mod('myknowledgebase_blog_content') ) ); ?>
			</div>
		<?php endif; ?>
	<?php } ?>
	<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'content-list' ); ?>
		<?php endwhile; ?>

		<div class="post-nav">
			<?php next_posts_link(); ?>
			<?php previous_posts_link(); ?>
		</div>

	<?php else: ?>
		<?php get_template_part( 'content-none' ); ?>

	<?php endif; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
