<?php
/**
 * The template for displaying Category pages.
 *
 * @package iknowledgebase
 */

get_header();
$iknowledgebase_settings = get_option( 'iknowledgebase_settings', false );
?>
<?php if ( empty( $iknowledgebase_settings['archive_sidebar'] ) ) : ?>
    <section class="section">
        <div class="container">
            <div class="level">
                <div class="level-left"><?php iknowledgebase_breadcrumbs(); ?></div>
                <div class="level-right"><?php get_search_form(); ?></div>
            </div>
            <div class="columns is-multiline is-centered pt-5<?php iknowledgebase_sidebar_location(); ?>">

                <div class="column is-full-touch"><?php get_sidebar(); ?></div>

                <div class="column is-full-touch is-two-thirds-desktop">
                    <div class="box is-mobile">
						<?php iknowledgebase_posts_sorter(); ?>
                    </div>
					<?php if ( have_posts() ) : ?>
                        <div class="panel has-background-white">
							<?php
							iknowledgebase_get_sticky_posts_in_category();
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
<?php else: ?>

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
						iknowledgebase_get_sticky_posts_in_category();
						// Load posts loop.
						while ( have_posts() ) {
							the_post();
							if( is_sticky() ){
							    continue;
							}
							get_template_part( 'template-parts/content', 'list' );
						}
						?>
                    </div>
					<?php iknowledgebase_the_posts_pagination(); ?><?php endif; ?>
            </div>
        </div>

        </div>

    </section>

<?php endif; ?>


<?php get_footer(); ?>
