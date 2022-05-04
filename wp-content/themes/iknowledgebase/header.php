<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
        <link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>">
	<?php endif; ?>
	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="header">
    <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'iknowledgebase' ); ?></a>
	<?php $iknowledgebase_menu_classes = apply_filters( 'iknowledgebase_menu_nav_classes', '' ); ?>
	<?php do_action( 'iknowledgebase_header_bar' ); ?>
    <nav class="navbar <?php echo esc_attr( $iknowledgebase_menu_classes ); ?>" role="navigation"
         aria-label="<?php esc_attr_e( 'Main Navigation', 'iknowledgebase' ); ?>">
        <div class="container">
            <div class="navbar-brand">
				<?php iknowledgebase_brand(); ?>
                <a href="#" role="button" class="navbar-burger burger" id="navigation-burger"
                   aria-label="<?php esc_attr_e( 'Menu', 'iknowledgebase' ); ?>" aria-expanded="false"
                   data-target="main-menu" <?php iknowledgebase_amp_menu_toggle(); ?>>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
            </div>
            <div id="main-menu" class="navbar-menu" <?php iknowledgebase_amp_menu_is_toggled(); ?>>
                <div class="navbar-start">
					<?php
					wp_nav_menu( array(
						'theme_location'  => 'start-nav',
						'depth'           => '2',
						'container'       => '',
						'container_class' => '',
						'container_id'    => '',
						'menu_class'      => '',
						'menu_id'         => '',
						'items_wrap'      => '%3$s',
						'walker'          => new IKnowledgebaseBase_Walker_Nav_Menu(),
						'fallback_cb'     => '',
					) );
					?>
                </div>

                <div class="navbar-end">
					<?php
					wp_nav_menu( array(
						'theme_location'  => 'end-nav',
						'depth'           => '2',
						'container'       => '',
						'container_class' => '',
						'container_id'    => '',
						'menu_class'      => '',
						'menu_id'         => '',
						'items_wrap'      => '%3$s',
						'walker'          => new IKnowledgebaseBase_Walker_Nav_Menu(),
						'fallback_cb'     => '',
					) );
					?>
                </div>
            </div>
        </div>
    </nav>
</header>
<main class="main is-relative is-flex-shrink-0" id="content">
	<?php $iknowledgebase_settings = get_option( 'iknowledgebase_settings', false ); ?>
	<?php if ( !empty( $iknowledgebase_settings['body_svg'] ) ) : ?>
        <svg class="intersect" viewBox="0 0 1441 279" xmlns="http://www.w3.org/2000/svg"
             xmlns:xlink="http://www.w3.org/1999/xlink">
            <g id="intersect" transform="translate(0.178955, 0.820312)" fill-rule="nonzero">
                <path d="M0,177.850479 L0,0 L1440.00104,0 L1440.00104,177.850479 C1268.57105,239.085479 1021.55925,277.43899 731.888245,277.43899 C442.215245,277.43899 171.433045,239.085479 0,177.850479 Z"
                      id="Path"></path>
            </g>
        </svg>
	<?php endif; ?>

