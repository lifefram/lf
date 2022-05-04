<?php
/**
 * The template for displaying the 404 template in the Hgw Whiteboard theme.
 *
 * @subpackage Hgw_WhiteBoard
 */

get_header();
?>

<main id="site-content" class="error404">

	<div class="section-inner">

		<h1 class="entry-title"><?php __( 'Page Not Found', 'hgw-whiteboard' ); ?></h1>

		<div class="intro-text"><p><?php __( 'The page you were looking for could not be found. It might have been removed, renamed, or did not exist in the first place.', 'hgw-whiteboard' ); ?></p></div>

		<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">

		  <div>

		    <input type="text" id="searchtext" value="" name="s" placeholder="<?php echo esc_attr_e( 'Search ...', 'hgw-whiteboard' ); ?>" autocomplete="off"/>


				<button type="submit" id="searchsubmit" value="Search"><i class="fa fa-search"></i></button>

		  </div>

		</form>

	</div><!-- .section-inner -->

</main><!-- #site-content -->


<?php

get_footer();
