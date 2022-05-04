<?php
/**
 * The template for displaying the footer
 *
 * Contains the opening of the #site-footer div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @subpackage Hgw_WhiteBoard
 */

?>
			<footer id="site-footer" class="header-footer-group">

				<div class="inner-width">

					<div class="section-inner">


							<p class="footer-copyright">

								<?php echo esc_html( hgw_whiteboard_copyright() ); ?>

								<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>

							</p><!-- .footer-copyright -->

							<p class="wordpress">

								<?php esc_html_e( 'Powered by WordPress', 'hgw-whiteboard' ); ?>

								<a href="https://hamgamweb.com/themes/hgw-whiteboard/"  target="_blank">

									<?php esc_html_e( 'Whiteboard Theme', 'hgw-whiteboard' ); ?>

								</a>

							</p>

							<a class="to-the-top" href="#site-header">&uarr;</a><!-- .to-the-top -->


					</div><!-- .section-inner -->

				</div>

			</footer><!-- #site-footer -->

		<?php wp_footer(); ?>

	</body>
</html>
