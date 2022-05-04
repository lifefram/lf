<?php
/*
 * Nothing found used by files 404, archive, index and search.
 */
?>

<h1 class="page-title"><?php _e( 'Nothing Found', 'myknowledgebase' ); ?></h1>
<p><?php _e('Sorry, no posts matched your criteria.', 'myknowledgebase'); ?></p>
<?php if ( is_search() || is_404() ) : ?>
	<?php get_search_form(); ?>
<?php endif; ?>
