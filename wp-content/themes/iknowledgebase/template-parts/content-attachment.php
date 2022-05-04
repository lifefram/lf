<?php
/**
 *  Template part for displaying attachment.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package iknowledgebase
 */

?>
<header class="mb-5 pb-5 has-text-centered has-border-bottom">
    <h1 class="title"><?php the_title(); ?></h1>
	<?php if ( has_excerpt() ) : ?>
        <p class="subtitle"><?php the_excerpt(); ?></p>
	<?php endif; ?>
</header>
<div class="entry-attachment">
    <figure class="image mt-6">
		<?php $image_size = apply_filters( 'wporg_attachment_size', 'large' );
		echo wp_get_attachment_image( get_the_ID(), $image_size ); ?>
    </figure>
</div>