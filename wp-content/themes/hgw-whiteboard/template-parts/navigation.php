<?php
/**
 * Displays the next and previous post navigation in single posts.
 *
 * @subpackage Hgw_WhiteBoard
 */

$next_post = get_next_post();
$prev_post = get_previous_post();

if ( $next_post || $prev_post ) {

	$pagination_classes = '';

	if ( ! $next_post ) {

		$pagination_classes = ' only-one only-prev';

	} elseif ( ! $prev_post ) {

		$pagination_classes = ' only-one only-next';

	}

	?>

	<nav class="pagination-single section-inner<?php echo esc_attr( $pagination_classes ); ?>">


		<div class="pagination-single-inner">

			<?php
			if ( $prev_post ) {
				?>

				<a class="previous-post" href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>">

					<span class="arrow" aria-hidden="true">&larr;</span>

					<span class="title">

						<span class="title-inner" title="<?php echo wp_kses_post( get_the_title( $prev_post->ID ) ); ?>"><?php esc_html_e('Previous Post', 'hgw-whiteboard') ?></span>

					</span>

				</a>

				<?php
			}



			if ( $next_post ) {
				?>

				<a class="next-post" href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>">

						<span class="title">

							<span class="title-inner" title="<?php echo wp_kses_post( get_the_title( $next_post->ID ) ); ?>"><?php esc_html_e('Next Post', 'hgw-whiteboard') ?></span>

						</span>

						<span class="arrow" aria-hidden="true">&rarr;</span>

				</a>

				<?php
			}
			?>

		</div><!-- .pagination-single-inner -->


	</nav><!-- .pagination-single -->

	<?php
}
