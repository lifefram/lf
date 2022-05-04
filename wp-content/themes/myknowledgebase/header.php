<?php
/*
 * The header for displaying logo, menu, header-image, homepage-widgets and search bar.
 */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
		<link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="container">
	<?php if ( is_page_template( 'page-full.php' ) || is_page_template( 'page-knowledge-four.php' ) || is_page_template( 'single-full.php' ) ) {
		$main_content = '#content-full';
	} else {
		$main_content = '#content';
	} ?>
	<a class="skip-link screen-reader-text" href="<?php echo $main_content; ?>"><?php _e( 'Skip to content', 'myknowledgebase' ); ?></a>
	<div id="header-first">
		<div class="logo">
			<?php if ( get_theme_mod( 'myknowledgebase_logo' ) ) : ?>
				<?php if ( get_theme_mod( 'myknowledgebase_logo_width' ) ) {
					$logo_width = 'style="width:'.get_theme_mod( 'myknowledgebase_logo_width' ).'px;"';
				} else {
					$logo_width = '';
				} ?>
				<a href='<?php echo esc_url( home_url( '/' ) ); ?>' title='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>' rel='home'>
				<img src='<?php echo esc_url( get_theme_mod( 'myknowledgebase_logo' ) ); ?>' <?php echo $logo_width; ?> alt='<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>'></a>
			<?php else : ?>
				<h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>
				<?php if ( get_bloginfo('description') ) : ?>
					<div class="site-tagline"><?php bloginfo('description'); ?></div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container_class' => 'nav-head' ) ); ?>
			<div class="mobile-nav-container">
				<button id="mobile-nav-toggle" class="mobile-nav-toggle"><?php _e( 'Menu', 'myknowledgebase' ); ?><?php _e( ' +', 'myknowledgebase' ); ?></button>
				<div id="mobile-nav" class="mobile-nav">
					<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php if ( is_front_page() ) { ?>
	<?php if ( get_header_image() ) { ?>
		<div id="header-second">
			<div class="image-homepage">
				<img src="<?php echo get_header_image(); ?>" class="header-img" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" />
			</div>
			<?php if ( is_active_sidebar( 'homepage' ) ) { ?>
				<div class="sidebar-homepage" role="complementary">
					<?php dynamic_sidebar( 'homepage' ); ?>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
	<?php } ?>
	<?php if ( get_theme_mod( 'myknowledgebase_show_search' ) != "no" ) : ?>
		<div id="header-third">
			<?php if ( get_theme_mod( 'myknowledgebase_search' ) ) {
				$search_title = esc_attr( get_theme_mod( 'myknowledgebase_search' ) );
			} else {
				$search_title = esc_attr__( 'Search Posts', 'myknowledgebase' );
			}
			if ( get_theme_mod( 'myknowledgebase_search_field_placeholder' ) ) {
				$search_field_placeholder = esc_attr( get_theme_mod( 'myknowledgebase_search_field_placeholder' ) );
			} else {
				$search_field_placeholder = esc_attr__( 'Search Posts', 'myknowledgebase' );
			} ?>
			<div class="searchbar-title"><?php echo $search_title; ?></div>
			<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label><span class="screen-reader-text"><?php _e( 'Search for:', 'myknowledgebase' ); ?></span></label>
				<input type="search" name="s" class="search-field" placeholder="<?php echo $search_field_placeholder; ?>" value="<?php echo get_search_query(); ?>" />
				<input type="hidden" name="post_type" value="post" />
				<input type="submit" class="search-submit" value="<?php _e( 'Search', 'myknowledgebase' ); ?>" />
			</form>
		</div>
	<?php endif; ?>
