<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package iknowledgebase
 */

get_header();
?>

    <section class="section">
        <div class="container">
            <div class="has-text-centered mb-6">
				<?php iknowledgebase_main_image(); ?>
                    <div class="is-max-w-2xl mx-auto">
						<?php get_search_form(); ?>
                    </div>
            </div>
            <div class="is-max-w-2xl mx-auto pt-5">
                <div class="box is-mobile">
					<?php iknowledgebase_posts_sorter(); ?>
                </div>

				<?php if ( have_posts() ) : ?>
                    <div class="panel has-background-white">
						<?php

						// Load posts loop.
						while ( have_posts() ) {
							the_post();
							get_template_part( 'template-parts/content', 'list' );
						}

						?>
                    </div>
					<?php iknowledgebase_the_posts_pagination();
				else :

					get_template_part( 'template-parts/content', 'none' );

				endif;

				?>
            </div>
        </div>
    </section>

<?php get_footer(); ?>