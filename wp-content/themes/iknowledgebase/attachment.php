<?php
/**
 * The template for displaying all attachment.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package iknowledgebase
 */

get_header();
?>

<section class="section">
    <div class="container">
        <div class="is-max-w-full mx-auto content pt-5">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/content', get_post_type() ); ?>
				<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() ) :
					comments_template();
				endif;
				?>
			<?php endwhile; // end of the loop. ?>
        </div>
    </div>
</section>

<?php get_footer();