<?php
/**
 * The template for displaying archive pages
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
                    <div class="column is-9-tablet">

						<?php if ( have_posts() ) : ?>

                            <header class="page-header block has-text-centered mb-6">
								<?php
								the_archive_title( '<h1 class="title">', '</h1>' );
								the_archive_description( '<p class="subtitle">', '</p>' );
								?>
                            </header><!-- .page-header -->

							<?php
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
