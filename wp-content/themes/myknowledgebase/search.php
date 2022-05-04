<?php
/*
 * The template for displaying search results.
 */
?>

<?php get_header(); ?>
<div id="content" role="main">
	<?php if ( have_posts() ) : ?>

		<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'myknowledgebase' ), get_search_query() ); ?></h1>

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
