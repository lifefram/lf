<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
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

						<?php if ( have_posts() ) : ?>

                            <header class="mb-6">
                                <h1 class="title is-spaced has-text-centered">
									<?php
									/* translators: %s: search query. */
									printf( esc_html__( 'Search Results for: %s', 'knowledgecenter' ), '<span class="has-text-success">' . get_search_query() . '</span>' );
									?>
                                </h1>
                            </header>

							<?php
							/* Start the Loop */
							while ( have_posts() ) :
								the_post();

								/**
								 * Run the loop for the search to output the results.
								 * If you want to overload this in a child theme then include a file
								 * called content-search.php and that will be used instead.
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
