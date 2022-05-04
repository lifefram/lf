<?php
/**
 * Template Name: Constructor page
 *
 *
 * @package iknowledgebase
 */

get_header();
?>

	<section class="section">
		<div class="container">
			<div class="is-max-w-full mx-auto content pt-5">
				<?php while ( have_posts() ) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                        <div class="content">
							<?php the_content(); ?>
                        </div>

						<?php iknowledgebase_link_pages(); ?>
                    </article>
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

<?php get_footer(); ?>