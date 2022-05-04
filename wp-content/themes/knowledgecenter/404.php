<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package KnowledgeCenter
 * @subpackage KnowledgeCenter
 * @since KnowledgeCenter 1.0
 */

get_header();

?>

<main class="section is-large">
    <div class="container has-text-centered">
        <div class="columns is-centered">
            <div class="column is-7">
                <h1 class="title is-1"><?php esc_html_e( '404', 'knowledgecenter' ); ?></h1>
                <p class="subtitle is-3"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'knowledgecenter' ); ?></p>
            </div>
        </div>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button is-primary">
		    <?php esc_html_e( 'Back to Home', 'knowledgecenter' ); ?>
        </a>
    </div>
</main>

<?php get_footer(); ?>
