<?php
/**
 * The template used for displaying single page content in page.php
 *
 * @package iknowledgebase
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('box'); ?>>
    <header class="mb-5 pb-5 has-text-centered has-border-bottom">
        <h1 class="title"><?php the_title(); ?></h1>
		<?php if ( has_excerpt() ) : ?>
            <p class="subtitle"><?php the_excerpt(); ?></p>
		<?php endif; ?>
    </header>
    <div class="content">
		<?php the_content(); ?>
    </div>

	<?php iknowledgebase_link_pages(); ?>
</article>
