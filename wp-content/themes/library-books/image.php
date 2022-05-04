<?php
/**
 * The template for displaying image attachments.
 *
 * @package Library Books
 */
get_header(); ?>
<div class="container">
     <div class="page_content">
        <section class="site-main">
			<?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                        <div class="entry-meta">
                            <?php
                                $library_books_metadata = wp_get_attachment_metadata();
                                printf( wp_kses( 'Published <span class="entry-date"><time class="entry-date" datetime="%1$s">%2$s</time></span> at <a href="%3$s">%4$s &times; %5$s</a> in <a href="%6$s" rel="gallery">%7$s</a>', 'library-books' ),
                                    esc_attr( get_the_date( 'c' ) ),
                                    esc_html( get_the_date() ),
                                    esc_url( wp_get_attachment_url() ),
	                                esc_html($library_books_metadata['width']),
                                    esc_html($library_books_metadata['height']),
                                    esc_url( get_permalink( $post->post_parent ) ),
									esc_html(get_the_title( $post->post_parent )) 
                                );
                                edit_post_link( esc_html__( 'Edit', 'library-books' ), '<span class="edit-link">', '</span>' );
                            ?>
                        </div><!-- .entry-meta -->
                        <nav role="navigation" id="image-navigation" class="image-navigation">
                            <div class="nav-previous"><?php previous_image_link( false, wp_kses( '<span class="meta-nav">&larr;</span> Previous', 'library-books' ) ); ?></div>
                            <div class="nav-next"><?php next_image_link( false, wp_kses( 'Next <span class="meta-nav">&rarr;</span>', 'library-books' ) ); ?></div>
                        </nav><!-- #image-navigation -->
                    </header><!-- .entry-header -->
                    <div class="entry-content">
                        <div class="entry-attachment">
                            <div class="attachment">
                                <?php library_books_the_attached_image(); ?>
                            </div><!-- .attachment -->
                            <?php if ( has_excerpt() ) : ?>
                            <div class="entry-caption">
                                <?php the_excerpt(); ?>
                            </div><!-- .entry-caption -->
                            <?php endif; ?>
                        </div><!-- .entry-attachment -->
                        <?php
                            the_content();
                            wp_link_pages( array(
                                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'library-books' ),
                                'after'  => '</div>',
                            ) );
                        ?>
                    </div><!-- .entry-content -->
                    <?php edit_post_link( esc_html__( 'Edit', 'library-books' ), '<footer class="entry-meta"><span class="edit-link">', '</span></footer>' ); ?>
                </article><!-- #post-## -->
                <?php
                    // If comments are open or we have at least one comment, load up the comment template
                    if ( comments_open() || '0' != get_comments_number() )
                        comments_template();
                ?>
            <?php endwhile; // end of the loop. ?>
        </section>
        <?php get_sidebar();?>
        <div class="clear"></div>
    </div>
</div>
<?php get_footer(); ?>