<?php
/**
 * The template used for displaying single post content in single.php
 *
 * @package iknowledgebase
 */

$iknowledgebase_settings = get_option( 'iknowledgebase_settings', false );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="box">
        <header class="entry-header mb-5">
            <h1 class="title is-2 is-spaced"><?php the_title(); ?></h1>
			<?php if ( empty( $iknowledgebase_settings['post_meta'] ) ) : ?>
                <div class="entry-meta level is-mobile has-border-top has-border-bottom py-3 is-size-7">
                    <div class="level-left">
                        <div class="level-item">
                        <span class="icon-text">
                          <span class="icon has-text-primary">
                            <span class="icon-clock"></span>
                          </span>
                          <span><?php iknowledgebase_posted_on(); ?></span>
                        </span>
                        </div>
                    </div>
                    <div class="level-right">
                        <div class="level-item is-hidden-touch">
                        <span class="icon-text">
                          <span class="icon has-text-primary">
                            <span class="icon-user-alt"></span>
                          </span>
                          <span><?php iknowledgebase_posted_by(); ?></span>
                        </span>
                        </div>
                        <div class="level-item">
                        <span class="icon-text">
                          <span class="icon has-text-primary">
                            <span class="icon-hourglass"></span>
                          </span>
                          <span><?php iknowledgebase_reading_time(); ?></span>
                        </span>
                        </div>
                    </div>
                </div>
	        <?php else: ?>
            <hr/>
			<?php endif; ?>
        </header>
		<?php if ( has_post_thumbnail() === true && ! empty( $iknowledgebase_settings['featured_image'] ) ) : ?>
            <figure class="image py-5">
				<?php the_post_thumbnail(); ?>
            </figure>
		<?php endif; ?>
        <div class="content">
			<?php the_content(); ?>
        </div>

        <?php do_action('iknowledgebase_post_footer');?>

        <div class="tags are-medium">
			<?php the_tags( '', ' ', '' ); ?>
        </div>
    </div>

	<?php iknowledgebase_link_pages(); ?>
</article>


