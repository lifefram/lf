<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package iknowledgebase
 */

get_header();
?>

<section class="section">
    <div class="container">
        <div class="is-max-w-md mx-auto has-text-centered pt-5">
            <?php iknowledgebase_404_image();?>
            <span class="title has-text-primary"><?php esc_html_e('Whoops!', 'iknowledgebase');?></span>
            <h2 class="title is-spaced"><?php esc_html_e('Something went wrong!', 'iknowledgebase');?></h2>
            <p class="subtitle"><?php esc_html_e('Sorry, but we are unable to open this page.', 'iknowledgebase');?></p>
            <div class="buttons is-centered"><a class="button is-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e('Go back to Homepage', 'iknowledgebase');?></a></div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
