<?php
/**
 * Template Name: Home Page
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
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

            <div class="columns is-multiline mb-6">
				<?php iknowledgebase_get_home_posts(); ?>
            </div>

        </div>
    </section>

<?php while ( have_posts() ) : the_post(); ?>
    <section class="section">
        <div class="container">
		<?php the_content(); ?>
        </div>
    </section>
<?php endwhile; // end of the loop. ?>


<?php get_footer(); ?>