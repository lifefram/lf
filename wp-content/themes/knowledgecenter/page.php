<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package KnowledgeCenter
 * @subpackage KnowledgeCenter
 * @since KnowledgeCenter 1.0
 */

get_header();
?>

<main id="primary" class="site-main">
    <section class="section">
        <div class="container is-max-widescreen py-4 ">
            <div class="columns is-multiline is-centered">
                <div class="column is-10-tablet is-9-desktop">
					<?php
					while ( have_posts() ) :
						the_post();

						get_template_part( 'template-parts/content', get_post_type() );

						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

					endwhile; // End of the loop.
					?>
                </div>
            </div>
        </div>
    </section>

</main>

<?php
get_footer(); ?>
