<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package iknowledgebase
 */

$post_icon = apply_filters( 'iknowledgebase_post_icon', 'icon-book' );
?>

<a class="panel-block is-borderless" href="<?php the_permalink(); ?>">
    <span class="panel-icon">
        <span class="<?php echo esc_attr( $post_icon ); ?>"></span>
    </span>
    <h4><?php the_title(); ?></h4>
</a>
