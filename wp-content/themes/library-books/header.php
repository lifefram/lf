<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div class="container">
 *
 * @package Library Books
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php endif; ?>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php do_action( 'wp_body_open' ); ?>
<div class="<?php if (!is_home() && !is_front_page()) { ?>inrheader<?php } else{?>header<?php } ?>" aria-label="<?php esc_attr_e( 'header', 'library-books' ); ?>">
  <div class="container">
    <div class="logo">
		<?php the_custom_logo(); ?>
        <div class="clear"></div>
        <?php if (display_header_text()==true){ ?>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
        <h2><?php bloginfo('name'); ?></h2>
        <p><?php bloginfo( 'description' ); ?></p>                          
        </a>
        <?php } ?>
    </div>
         <div class="toggle"><button class="toggleMenu" href="#" style="display:none;"><?php esc_html_e('Menu','library-books'); ?></button></div> 
        <div class="sitenav">
          <?php wp_nav_menu( array('theme_location' => 'primary') ); ?>         
        </div><!-- .sitenav--> 
        <div class="clear"></div> 
  </div> <!-- container -->
</div><!--.header -->