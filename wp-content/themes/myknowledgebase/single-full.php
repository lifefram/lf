<?php
/*
 * Template Name: Full Width Template
 * Description: Template without sidebar
 * Template Post Type: post
 */
?>

<?php get_header(); ?>
<div id="content-full" role="main">
	<?php while (have_posts()) : the_post(); ?>

		<div id="post-<?php the_ID(); ?>" <?php post_class('post-single'); ?>>
			<h1 class="post-title-single entry-title"><?php the_title(); ?></h1>

			<?php get_template_part( 'content-postmeta' ); ?>

			<div class="entry-content">
				<?php the_content(); ?>

				<?php wp_link_pages( array(
					'before' => '<div class="pagelink">' . __( 'Pages:', 'myknowledgebase' ),
					'after'  => '</div>',
				) ); ?>
			</div>

			<?php get_template_part( 'content-postmeta-single' ); ?>
		</div>

		<?php comments_template(); ?>

	<?php endwhile; ?>

	<?php edit_post_link( __( 'Edit', 'myknowledgebase' ), '<div class="edit-link">', '</div>' ); ?>
</div>
<?php get_footer(); ?>
