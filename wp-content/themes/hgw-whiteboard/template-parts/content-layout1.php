<?php
/**
 * The default template for displaying content
 *
 * Used for index.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @subpackage Hgw_WhiteBoard
 */

?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">


	<div class="post-inner type2">

		<div class="entry-content">

			<div class="content-header">
				<?php
				$ConThumbnail = has_post_thumbnail();

		    if ($ConThumbnail) {

		      echo '<div class="featured-media">';


		        the_post_thumbnail('medium');


		      echo '</div>';


		    }
				?>

				<div class="right-content<?php if ($ConThumbnail) { echo ' full-width';} ?>">

					<div class="inner-right-content">

						<?php

			              the_title( '<h4 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h4>' );

			      ?>

						<div class="entry-post-info">

			        <div class="entry-taxonomy">

			  				<?php hgw_whiteboard_categories() ?>

			  			</div>

			      </div>

					</div>

				</div>



			</div>
			<!-- Content-Header -->


			<div class="excerpt">

				<?php

					the_excerpt();

					?>

			</div>
			<!-- Excerpt-->

				<div class='bottom-content'>
						<?php
							echo sprintf(
								'<a class="read-more" href="%1$s">%2$s</a>',
								esc_url( get_permalink( get_the_ID() ) ),
								esc_html__( 'Read More', 'hgw-whiteboard' )
								) ;
						?>
						<div class="post-info">
							<span class="date">
								<?php hgw_whiteboard_posted_on(); ?>
							</span>
							<span class="comments">
								<i class="fa fa-comment" aria-hidden="true"></i>
								<?php comments_number(); ?>
							</span>
						</div>

				</div>
				<!-- bottom-content -->

		</div><!-- .entry-content -->

	</div><!-- .post-inner -->


</article><!-- .post -->
