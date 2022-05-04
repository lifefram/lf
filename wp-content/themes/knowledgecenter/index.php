<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package KnowledgeCenter
 * @subpackage KnowledgeCenter
 * @since KnowledgeCenter 1.0
 */

get_header();
?>
    <main id="primary" class="site-main">
        <section class="section">
            <div class="container is-max-widescreen py-4">
                <div class="columns is-multiline is-centered">
                    <div class="column is-10-tablet is-9-desktop">

						<?php
						if ( have_posts() ) :

							/* Start the Loop */
							while ( have_posts() ) :
								the_post();

								/*
								 * Include the Post-Type-specific template for the content.
								 * If you want to override this in a child theme, then include a file
								 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
								 */
								get_template_part( 'template-parts/content' );

							endwhile;

							knowledgecenter_the_posts_pagination();

						else :

							get_template_part( 'template-parts/content', 'none' );

						endif;
						?>
                    </div>

                </div>
            </div>
        </section>
    </main>

<?php
get_footer();
