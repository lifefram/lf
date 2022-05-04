<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package KnowledgeCenter
 * @subpackage KnowledgeCenter
 * @since KnowledgeCenter 1.0
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
} else {
	do_action( 'wp_body_open' );
} ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'knowledgecenter' ); ?></a>

<header id="masthead" class="header">

    <nav class="navbar is-spaced has-shadow">
        <div class="container is-max-widescreen">
            <div class="navbar-brand">
				<?php if ( has_custom_logo() ) :
					the_custom_logo();
					?><?php else : ?>
                    <a class="navbar-item" href="<?php echo esc_url( home_url( '/' ) ); ?>"
                       title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                        <span class="navbar-item title is-5 has-text-black-bis has-text-orbitron site-name"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
                    </a>
				<?php endif; ?>
                <a href="#" class="navbar-burger burger" role="button" aria-label="<?php esc_attr_e( 'Menu', 'knowledgecenter' ); ?>" aria-expanded="false"
                   data-target="mainMenuNavigation" aria-label="<?php esc_attr_e( 'Menu', 'knowledgecenter' ); ?>">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
            </div>
            <div class="navbar-menu" id="mainMenuNavigation">
                <div class="navbar-start">
					<?php
					wp_nav_menu( array(
						'theme_location'  => 'first-menu',
						'depth'           => '2',
						'container'       => '',
						'container_class' => '',
						'container_id'    => '',
						'menu_class'      => '',
						'menu_id'         => '',
						'items_wrap'      => '%3$s',
						'walker'          => new KnowledgeCenter_Navbar(),
						'fallback_cb'     => 'KnowledgeCenter_Navbar::fallback',
					) );
					?>
                </div>

                <div class="navbar-end">
					<?php
					wp_nav_menu( array(
						'theme_location'  => 'second-menu',
						'depth'           => '2',
						'container'       => '',
						'container_class' => '',
						'container_id'    => '',
						'menu_class'      => '',
						'menu_id'         => '',
						'items_wrap'      => '%3$s',
						'walker'          => new KnowledgeCenter_Navbar(),
						'fallback_cb'     => 'KnowledgeCenter_Navbar::fallback',
					) );
					?>
                </div>
            </div>
        </div>
    </nav>

</header>
