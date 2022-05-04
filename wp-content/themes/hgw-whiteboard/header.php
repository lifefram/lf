<?php
/**
 * Header file for the HGW Whiteboard theme.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @subpackage Hgw_WhiteBoard
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>

  <meta charset="<?php bloginfo( 'charset' ); ?>">

  <meta name="viewport" content="width=device-width, initial-scale=1.0" >

  <link rel="profile" href="https://gmpg.org/xfn/11">

  <?php wp_head(); ?>

</head>
<?php
$sidebarOptions = get_theme_mod( 'hgw_sidebar_types', 'default-sidebar' );
?>
<body <?php body_class(esc_html( $sidebarOptions )); ?>>

  <?php
  wp_body_open();
  ?>
  <header id="site-header">
    <div class="inner-width flex-sb">

      <a class="skip-link screen-reader-text" href="#site-content"><?php echo esc_attr_e( 'Skip to content', 'hgw-whiteboard' ) ?></a>
      <div class="brand<?php if ( has_custom_logo() ) { echo ' has_logo'; } ?>">

            <?php if ( has_custom_logo() ) { ?>
            <div class="col logo<?php if ( get_theme_mod( 'hgw_header_sitename', 0 ) == 1 ) { echo ' hide_sitename'; } ?>">
              <?php the_custom_logo(); ?>
            </div>
            <?php  } ?>


            <?php if ( get_theme_mod( 'hgw_header_sitename', 0 ) == 0 ) : ?>
              <div class="col brand-description">

                <div class="site-name">

                  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">

                    <h1><?php bloginfo( 'name' ) ?></h1>

                  </a>

                </div>

                <div class="site-description">


                    <p><?php bloginfo( 'description' ) ?></p>


                </div>

              </div>
            <?php endif; ?>



      </div>

      <div id="header-menu" class="header-menus">

        <button class="button open-menu"><i class="fa fa-bars" aria-hidden="true"></i></button>


        <div class="pas">

          <button class="button close-menu"><i class="fa fa-times" aria-hidden="true"></i></button>

          <div class="social-menu">

            <?php display_social_media(); ?>

          </div>

          <div class="site-menu primary-menu-wrapper">

              <?php
              if ( has_nav_menu( 'primary' ) ) {

                    wp_nav_menu(
                      array(
                        'theme_location' => 'primary',
                        'menu_class' => 'primary-menu',
                        'depth'             => 3,
                      )
                    );
                  }
                  else{

                      echo '<ul class="primary-menu">';

                         wp_list_categories(
                           array(
                                'title_li'   => '',
                                'orderby'    => 'name',
                                'show_count' => 0,
                                'number' => 5,
                                'orderby'    => 'count',
                                'order'      => 'DESC'
                            )
                          );

                        echo "</ul>";

                      }
                  ?>
          </div>

        </div>

      </div>

    </div>
  </header>
