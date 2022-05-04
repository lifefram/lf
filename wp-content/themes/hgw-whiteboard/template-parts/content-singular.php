<?php
/**
 * The default template for displaying content
 *
 * Used for singular
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @subpackage Hgw_WhiteBoard
 */

?>

<article <?php post_class('singular-page'); ?> id="post-<?php the_ID(); ?>">


	<div class="post-inner">


		<div class="entry-content">

      <?php

					hgw_whiteboard_breadcrumbs();


					the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' );

					if (is_single( )) {


						if ( has_post_thumbnail() ) {

							echo '<div class="featured-media">';

								the_post_thumbnail();

							echo '</div>';


						}

					}


      if (is_single( )) {
				?>

				<div class="entry-post-info">

	        <div class="entry-taxonomy">

	  				<?php hgw_whiteboard_categories() ?>

	  			</div>

					<div class="date-publish">

						<?php hgw_whiteboard_posted_on(); ?>

					</div>

	      </div>

			<?php

				} // End is_single

				echo '<div class="innercontent">';

					the_content( );

				echo '</div>';

				echo '<div class="navigation single-navigation">';


			      wp_link_pages(
			  			array(
			  				'before'      => '<div class="pagination post-nav-links">',
			  				'after'       => '</div>',
			  				'link_before' => '',
			  				'link_after'  => '',
			  			)
			  		);


	      echo '</div>';

			?>

			</div><!-- .entry-content -->

			<div class="hw-footer-info">

				<?php if (get_theme_mod( 'hgw_singular_shortlink', 1 ) == 1 ): ?>

					<div class="shorturl">

						<div class="inner-shorturl">

							<span class="title"><?php esc_html_e( 'Short URL', 'hgw-whiteboard' ) ?></span>

							<span id="shorturl" class="surl"><?php echo esc_url( home_url() ) . '/?p='. get_the_id(); ?></span>
							<button id="copyshorturl"><?php esc_html_e( 'Copy', 'hgw-whiteboard' ) ?></button>
							<span id="copyresultshorturl"></span>

						</div>

					</div>
					<!-- .shorturl -->

				<?php endif; ?>

				<?php
				if (is_single( )) {
					?>

	      <div class="entry-post-info">


					<?php

						if ( has_tag() ) :

					?>

					<div class="entry-taxonomy tags">


						<div class="entry-taxonomy-inner">

							<?php the_tags( __( 'Tags', 'hgw-whiteboard' ), '','' ); ?>

						</div><!-- .entry-taxonomy-inner -->

					</div><!-- .entry-taxonomy -->

					<?php

					endif; // End has_tag()

					?>
				</div><!-- .entry-post-info -->

				<?php

					} // End is_single
				?>

			</div><!-- .hw-footer-info -->



		</div><!-- .post-inner -->



	<?php

	/**
	 *  Output comments wrapper if it's a post, or if comments are open,
	 * or if there's a comment number – and check for password.
	 * */
	if ( ( is_single() || is_page() ) && ( comments_open() || get_comments_number() ) && ! post_password_required() ) {
		?>

		<div class="comments-wrapper section-inner">

			<?php comments_template(); ?>

		</div><!-- .comments-wrapper -->

		<?php
	}

	if ( get_theme_mod( 'hgw_singular_next_prev_posts', 1 ) == 1 ) {

		get_template_part( 'template-parts/navigation' );

	}


	?>

</article><!-- .post -->
