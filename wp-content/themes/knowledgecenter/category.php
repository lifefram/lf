<?php
/**
 * The template for displaying category pages
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
				<div class="columns is-multiline">
					<div class="column is-offset-1-tablet is-offset-0-desktop is-10-tablet is-3-desktop">
						<?php knowledgecenter_category_side_menu(); ?>
						<?php get_sidebar(); ?>
					</div>
					<div class="column is-offset-1-tablet is-offset-0-desktop is-10-tablet is-9-desktop">

						<?php if ( have_posts() ) : ?>

							<header class="page-header block has-text-centered mb-6">
								<h1 class="title"><?php single_cat_title();?></h1>
                                <?php the_archive_description( '<p class="subtitle">', '</p>' ); ?>
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

