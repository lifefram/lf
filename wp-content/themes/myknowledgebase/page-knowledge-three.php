<?php
/*
 * Template Name: Knowledge Base Sidebar
 * Description: Template for displaying Categories and Posts
 * Template Post Type: page
 */
?>

<?php get_header(); ?>
<div id="content" role="main">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php if ( get_theme_mod( 'myknowledgebase_page_title' ) == "yes" ) { ?>
			<h1 class="page-title"><?php the_title(); ?></h1>
		<?php } ?>

		<div class="entry-content-page">
			<?php if ( has_post_thumbnail() ) {
				the_post_thumbnail('post-thumbnail', array('class' => 'single-image'));
			} ?>

			<?php the_content(); ?>
		</div>
	<?php endwhile; ?>

	<ul id="categories-three">
		<?php get_template_part( 'content-knowledge' ); ?>
	</ul>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
