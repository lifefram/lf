<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package KnowledgeCenter
 * @subpackage KnowledgeCenter
 * @since KnowledgeCenter 1.0
 */

?>

<footer id="colophon" class="footer">

    <div class="container is-max-widescreen">
        <div class="level">
            <div class="level-left">
                <div class="level-item"><a class="title is-4" href="<?php esc_url(home_url('/')); ?>"><?php knowledgecenter_footer_title(); ?></a></div>
            </div>

	        <?php
	        wp_nav_menu( array(
		        'theme_location'  => 'footer-menu',
		        'depth'           => '1',
		        'container'       => '',
		        'container_class' => '',
		        'container_id'    => '',
		        'menu_class'      => '',
		        'menu_id'         => '',
		        'items_wrap'      => '<ul id="%1$s" class="level-right %2$s">%3$s</ul>',
		        'fallback_cb'    => '__return_empty_string',
	        ) );

	        ?>
        </div>
        <hr>
        <div class="columns">
            <div class="column is-offset-6-tablet is-6-tablet has-text-centered has-text-right-tablet">
                <p class="subtitle is-6"><?php knowledgecenter_footer_copyrite_text(); ?></p>
            </div>
        </div>
    </div>

</footer>


<?php wp_footer(); ?>

</body></html>
