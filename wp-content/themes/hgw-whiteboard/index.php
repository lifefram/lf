<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @subpackage Hgw_WhiteBoard
 */

get_header();
?>

<main id="site-content" class="sitecontent">
	<div class="inner-width flex-sb">
		<div class="inner-site-content flex-sb">
			<?php
			$twosidebarOptions = get_theme_mod( 'hgw_sidebar_types', 'default-sidebar' );
			if ( $twosidebarOptions == 'two-sidebars') :

				get_sidebar('secondary');
			?>
			<div class="content-two-sidebar flex-sb">
			<?php endif; ?>

			<div class="main-content">

				<?php

				if ( !is_singular() ) {
					hgw_whiteboard_breadcrumbs();
				}


				if (is_search()) {
					?>

					<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ) ?>">

						<div>

							<input type="text" id="searchtext" value="<?php echo get_search_query(); ?>" name="s" placeholder="<?php esc_attr__( 'Search ...', 'hgw-whiteboard' ); ?>" autocomplete="off"/>

							<button type="submit" id="searchsubmit" value="Search"><i class="fa fa-search"></i></button>

						</div>

					</form>

					<?php
				}

					if (have_posts()) :


							while (have_posts()) :	the_post();


							if ( is_singular( ) ) {

								get_template_part( 'template-parts/content', 'singular' );

							}else{

								if ( get_theme_mod( 'hgw_archive_posts_show_types', 'layout1' ) == 'layout3' ) {
									get_template_part( 'template-parts/content', 'layout3' );
								}
								elseif ( get_theme_mod( 'hgw_archive_posts_show_types', 'layout1' ) == 'layout2' ) {
									get_template_part( 'template-parts/content', 'layout2' );
								}else{
									get_template_part( 'template-parts/content', 'layout1' );
								}

							}

						endwhile;

								if ( is_search() || ! is_singular() ) :

									the_posts_pagination();

								endif;

					else :

						if ( is_search() ) {
							echo esc_html_e( 'Does not match any results!', 'hgw-whiteboard' );
						}
						else {
							echo esc_html_e( 'Not Found', 'hgw-whiteboard' );
						}



					endif;


					?>

			</div>

			<?php

			get_sidebar();


			if ( $twosidebarOptions == 'two-sidebars') :
				//<div class="content-two-sidebar">
			?>
		</div>
			<?php endif; ?>




		</div>
	</div>
</main><!-- #site-content -->

<?php

	get_footer();
