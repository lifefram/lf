<?php
/**
 * The template for displaying Archive pages.
 *
 * @package iknowledgebase
 */

get_header();
?>

    <section class="section">
        <div class="container">
            <div class="level">
                <div class="level-left"><?php iknowledgebase_breadcrumbs(); ?></div>
                <div class="level-right"><?php get_search_form(); ?></div>
            </div>
            <div class="is-max-w-2xl mx-auto pt-5">
                    <div class="box is-mobile">
						<?php iknowledgebase_posts_sorter(); ?>
                    </div>
					<?php if ( have_posts() ) : ?>
                        <div class="panel has-background-white">
	                        <h2 class="panel-heading"><?php the_archive_title(); ?></h2>
							<?php

							// Load posts loop.
							while ( have_posts() ) {
								the_post();
								get_template_part( 'template-parts/content', 'list' );
							}
							?>
                        </div>
						<?php iknowledgebase_the_posts_pagination(); ?><?php endif; ?>
                </div>
            </div>

        </div>

    </section>

<?php get_footer(); ?>