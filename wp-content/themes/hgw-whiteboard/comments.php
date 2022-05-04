<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @subpackage Hgw_WhiteBoard
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password,
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}

$hgw_whiteboard_comment_count = get_comments_number();
?>

<div id="comments" class="comments-area default-max-width <?php echo get_option( 'show_avatars' ) ? 'show-avatars' : ''; ?>">

	<?php
	if ( have_comments() ) :
		?>
		<h2 class="comments-title">
			<?php
			if ( '1' === $hgw_whiteboard_comment_count ) :
				 esc_html_e( '1 Comment', 'hgw-whiteboard' );
			 else :
					echo esc_html( $hgw_whiteboard_comment_count ) . ' ' .esc_html__( 'Comments', 'hgw-whiteboard' );
			endif;
			?>
		</h2><!-- .comments-title -->

		<ol class="commentlist">
			<?php
			wp_list_comments();
			?>
		</ol><!-- .comment-list -->

		<?php
		the_comments_pagination();
		?>

		<?php if ( ! comments_open() ) : ?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'hgw-whiteboard' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	comment_form();
	?>

</div><!-- #comments -->
