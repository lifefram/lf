<?php
/**
 * The template for displaying all attachment.
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
					endwhile; // End of the loop.
					?>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer(); ?>
