<?php
/**
 * Customizer settings for this theme.
 *
 * @subpackage Hgw_WhiteBoard
 */

//Do we want our template to be boxed or wide?
function hgw_live_social_customize_register($wp_customize){
  //  =============================
  //  = Add section               =
  //  =============================
  $wp_customize->add_section(
    'hgw_section_header_settings',
    array(
      'title'    => esc_html__('Header', 'hgw-whiteboard'),
      'description' => esc_html__('Header Settings', 'hgw-whiteboard'),
      'priority' => 10,
  )
);
  //  =============================
  //  = Add setting               =
  //  =============================

  $wp_customize->add_setting(
  'hgw_header_sitename',
    array(
        'default' => 0,
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'hgw_whiteboard_sanitize_checkbox',
    )
  );

  $wp_customize->add_control(
  'hgw_header_sitename',
    array(
        'label' => esc_html__('Hide sitename & description in header', 'hgw-whiteboard'),
        'section' => 'hgw_section_header_settings',
        'type' => 'checkbox',
    )
  );
  //  =============================
  //  = Add section               =
  //  =============================
    $wp_customize->add_section(
    'hgw_singular_section',
        array(
          'title'    => esc_html__('Singular', 'hgw-whiteboard'),
          'priority' => 10,
      )
    );
    // Show url ============
    $wp_customize->add_setting(
    'hgw_singular_shortlink',
      array(
          'default' => 1,
          'capability' => 'edit_theme_options',
          'sanitize_callback' => 'hgw_whiteboard_sanitize_checkbox',
      )
    );

    $wp_customize->add_control(
    'hgw_singular_shortlink',
      array(
          'label' => esc_html__('Show Short url', 'hgw-whiteboard'),
          'section' => 'hgw_singular_section',
          'type' => 'checkbox',
          'priority' => 10,
      )
    );
    // Show url ============
    $wp_customize->add_setting(
    'hgw_singular_next_prev_posts',
      array(
          'default' => 1,
          'capability' => 'edit_theme_options',
          'sanitize_callback' => 'hgw_whiteboard_sanitize_checkbox',
      )
    );

    $wp_customize->add_control(
    'hgw_singular_next_prev_posts',
      array(
          'label' => esc_html__('Show Prev & Next posts', 'hgw-whiteboard'),
          'section' => 'hgw_singular_section',
          'type' => 'checkbox',
          'priority' => 10,
      )
    );
  //  =============================
  //  = Add section               =
  //  =============================
    $wp_customize->add_section(
    'hgw_archive_posts',
        array(
          'title'    => esc_html__('Archive posts layouts', 'hgw-whiteboard'),
          'priority' => 10,
      )
    );
    // Breadcrumb ============
    $wp_customize->add_setting(
    'hgw_archive_posts_show_types',
      array(
         'default' => 'layout1',
         'capability' => 'edit_theme_options',
         'sanitize_callback' => 'hgw_sanitize_radio_and_select',
      )
    );

    $wp_customize->add_control(
    'hgw_archive_posts_show_types',
      array(
         'label' => esc_html__('Archive posts layouts', 'hgw-whiteboard'),
         'section' => 'hgw_archive_posts',
         'type' => 'select',
         'choices' => array(
            'layout1' => 'Type 1',
            'layout2' => 'Type 2',
            'layout3' => 'Type 3',
          )
      )
    );
  //  =============================
  //  = Add section               =
  //  =============================
    $wp_customize->add_section(
    'hgw_breadcrumb',
        array(
          'title'    => esc_html__('Breadcrumbs', 'hgw-whiteboard'),
          'priority' => 10,
      )
    );
    // Breadcrumb ============
    $wp_customize->add_setting(
    'hgw_show_breadcrumb',
      array(
          'default' => 0,
          'capability' => 'edit_theme_options',
          'sanitize_callback' => 'hgw_whiteboard_sanitize_checkbox',
      )
    );

    $wp_customize->add_control(
    'hgw_show_breadcrumb',
      array(
          'label' => esc_html__('Show Breadcrumb', 'hgw-whiteboard'),
          'section' => 'hgw_breadcrumb',
          'type' => 'checkbox',
          'priority' => 10,
      )
    );
    $wp_customize->add_setting(
    'hgw_type_breadcrumb',
      array(
         'default' => 'hgwwhiteboard',
         'capability' => 'edit_theme_options',
         'sanitize_callback' => 'hgw_sanitize_radio_and_select',
      )
    );

    $wp_customize->add_control(
    'hgw_type_breadcrumb',
      array(
         'label' => esc_html__('Breadcrumbs Type', 'hgw-whiteboard'),
         'section' => 'hgw_breadcrumb',
         'type' => 'select',
         'choices' => array(
            'hgwwhiteboard' => 'Whiteboard Theme',
            'yoast' => 'Yoast Plugin',
            'rankmath' => 'Rank Math Plugin',
          ),
         'active_callback'  =>  'hgw_breadcrumb_status',
      )
    );
  //  =============================
  //  = Add section               =
  //  =============================
    $wp_customize->add_section(
    'hgw_sidebar',
        array(
          'title'    => esc_html__('Sidebar', 'hgw-whiteboard'),
          'description' => esc_html__('Customize sidebar', 'hgw-whiteboard'),
          'priority' => 10,
      )
    );
    // Sidebars
    $wp_customize->add_setting(
    'hgw_sidebar_types',
      array(
         'default' => 'default-sidebar',
         'capability' => 'edit_theme_options',
         'sanitize_callback' => 'hgw_sanitize_radio_and_select',
      )
    );

    $wp_customize->add_control(
    'hgw_sidebar_types',
      array(
         'label' => esc_html__('Sidebar Settings', 'hgw-whiteboard'),
         'section' => 'hgw_sidebar',
         'type' => 'select',
         'choices' => array(
            'default-sidebar' => 'Default',
            'no-sidebar' => 'No sidebar',
            'two-sidebars' => 'Two sidebars',
          )
      )
    );
    // Sidebar Search Form ============
    $wp_customize->add_setting(
    'hgw_show_sidebar_search_form',
      array(
          'default' => 1,
          'capability' => 'edit_theme_options',
          'sanitize_callback' => 'hgw_whiteboard_sanitize_checkbox',
      )
    );

    $wp_customize->add_control(
    'hgw_show_sidebar_search_form',
      array(
          'label' => esc_html__('Show Search Form', 'hgw-whiteboard'),
          'section' => 'hgw_sidebar',
          'type' => 'checkbox',
          'priority' => 10,
          'active_callback'  =>  'hgw_sidebar_searchform_status'
      )
    );
    //  =============================
    //  = Add section               =
    //  =============================
    $wp_customize->add_section(
      'hgw-social-section',
      array(
        'title'    => esc_html__('Social Media Settings', 'hgw-whiteboard'),
        'description' => esc_html__('Add Your Page ( Full URL )', 'hgw-whiteboard'),
        'priority' => 10,
    )
  );
    //  =============================
    //  = Add setting               =
    //  =============================

    $wp_customize->add_setting(
    'hgw_show_social_media_checkbox',
      array(
          'default' => 1,
          'capability' => 'edit_theme_options',
          'sanitize_callback' => 'hgw_whiteboard_sanitize_checkbox',
      )
    );

    $wp_customize->add_control(
    'hgw_show_social_media_checkbox',
      array(
          'label' => esc_html__('Show Social Media', 'hgw-whiteboard'),
          'section' => 'hgw-social-section',
          'type' => 'checkbox',
      )
    );

    // ===
    // Twitter
    $wp_customize->add_setting(
        'twitter_url',
        array(
          'capability'     => 'edit_theme_options',
          'type'           => 'option',
          'sanitize_callback' => 'hgw_whiteboard_sanitize_url',
        )
    );

    $wp_customize->add_control(
        'twitter_url',
        array(
            'label' => 'Twitter URL',
            'section'    => 'hgw-social-section',
            'type' => 'url',
            'input_attrs' => array( 'placeholder' => 'https://twitter.com/name', ),
            'active_callback'  =>  'hgw_social_media_status',
        )
    );

    // ===
    // Facebook
    $wp_customize->add_setting(
        'facebook_url',
        array(
          'capability'     => 'edit_theme_options',
          'type'           => 'option',
          'sanitize_callback' => 'hgw_whiteboard_sanitize_url'
        )
    );

    $wp_customize->add_control(
        'facebook_url',
        array(
            'label' => 'Facebook URL',
            'section'    => 'hgw-social-section',
            'type' => 'url',
            'input_attrs' => array( 'placeholder' => 'https://facebook.com/name', ),
            'active_callback'  =>  'hgw_social_media_status',
        )
    );

    // ===
    // Google+
    $wp_customize->add_setting(
        'google_plus_url',
        array(
          'capability'     => 'edit_theme_options',
          'type'           => 'option',
          'sanitize_callback' => 'hgw_whiteboard_sanitize_url'
        )
    );

    $wp_customize->add_control(
        'google_plus_url',
        array(
            'label' => 'Google Plus URL',
            'section'    => 'hgw-social-section',
            'type' => 'url',
            'input_attrs' => array( 'placeholder' => 'https://plus.google.com/name', ),
            'active_callback'  =>  'hgw_social_media_status',
        )
    );

    // ===
    // Youtube URL
    $wp_customize->add_setting(
        'youtube_url',
        array(
          'capability'     => 'edit_theme_options',
          'type'           => 'option',
          'sanitize_callback' => 'hgw_whiteboard_sanitize_url'
        )
    );

    $wp_customize->add_control(
        'youtube_url',
        array(
            'label' => 'Youtube URL',
            'section'    => 'hgw-social-section',
            'type' => 'url',
            'input_attrs' => array( 'placeholder' => 'https://www.youtube.com/channel/name', ),
            'active_callback'  =>  'hgw_social_media_status',
        )
    );

    // ===
    // Instagram URL
    $wp_customize->add_setting(
        'instagram_url',
        array(
          'capability'     => 'edit_theme_options',
          'type'           => 'option',
          'sanitize_callback' => 'hgw_whiteboard_sanitize_url'
        )
    );

    $wp_customize->add_control(
        'instagram_url',
        array(
            'label' => 'Instagram URL',
            'section'    => 'hgw-social-section',
            'type' => 'url',
            'input_attrs' => array( 'placeholder' => 'https://www.instagram.com/name', ),
            'active_callback'  =>  'hgw_social_media_status',
        )
    );

    // ===
    // Linkedin URL
    $wp_customize->add_setting(
        'linkedin_url',
        array(
          'capability'     => 'edit_theme_options',
          'type'           => 'option',
          'sanitize_callback' => 'hgw_whiteboard_sanitize_url'
        )
    );

    $wp_customize->add_control(
        'linkedin_url',
        array(
            'label' => 'Linkedin URL',
            'section'    => 'hgw-social-section',
            'type' => 'url',
            'input_attrs' => array( 'placeholder' => 'https://www.linkedin.com/company/name', ),
            'active_callback'  =>  'hgw_social_media_status',
        )
    );

    // ===
    // Pinterest URL
    $wp_customize->add_setting(
        'pinterest_url',
        array(
          'capability'     => 'edit_theme_options',
          'type'           => 'option',
          'sanitize_callback' => 'hgw_whiteboard_sanitize_url'
        )
    );

    $wp_customize->add_control(
        'pinterest_url',
        array(
            'label' => 'Pinterest URL',
            'section'    => 'hgw-social-section',
            'type' => 'url',
            'input_attrs' => array( 'placeholder' => 'http://pinterest.com/name', ),
            'active_callback'  =>  'hgw_social_media_status',
        )
    );

    // ===
    // Telegram Channel URL
    $wp_customize->add_setting(
        'telegram_url',
        array(
          'capability'     => 'edit_theme_options',
          'type'           => 'option',
          'sanitize_callback' => 'hgw_whiteboard_sanitize_url'
        )
    );

    $wp_customize->add_control(
        'telegram_url',
        array(
            'label' => 'Telegram URL',
            'section'    => 'hgw-social-section',
            'type' => 'url',
            'input_attrs' => array( 'placeholder' => 'https://t.me/name', ),
            'active_callback'  =>  'hgw_social_media_status',
        )
    );

    // ===
    // Whatsapp URL
    $wp_customize->add_setting(
        'whatsapp_url',
        array(
          'capability'     => 'edit_theme_options',
          'type'           => 'option',
          'sanitize_callback' => 'hgw_whiteboard_sanitize_url'
        )
    );

    $wp_customize->add_control(
        'whatsapp_url',
        array(
            'label' => 'Whatsapp URL',
            'section'    => 'hgw-social-section',
            'type' => 'url',
            'input_attrs' => array( 'placeholder' => 'https://api.whatsapp.com/send?phone=989123456789', ),
            'active_callback'  =>  'hgw_social_media_status',
        )
    );

    // ===
    // Sanitize URL
    function hgw_whiteboard_sanitize_url( $url ) {
      return esc_url_raw( $url );
    }
    // ===
    // Sanitize Checkbox
    function hgw_whiteboard_sanitize_checkbox( $input ) {
       return ( ( isset( $input ) && true === $input ) ? true : false );
    }
    // ===
    // Sanitize Radio & Select
    function hgw_sanitize_radio_and_select( $input, $setting ) {
      $input = sanitize_key( $input );
      $choices = $setting->manager->get_control( $setting->id )->choices;
      return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
    }

    // ===
    // Social Media Status
    function hgw_social_media_status($control){
        if (true == $control->manager->get_setting('hgw_show_social_media_checkbox')->value()) {
            return true;
        } else {
            return false;
        }
    }
    // ===
    // Breadcrumbs Status
    function hgw_breadcrumb_status($control){
        if (true == $control->manager->get_setting('hgw_show_breadcrumb')->value()) {
            return true;
        } else {
            return false;
        }
    }
    // ===
    // Sidebar Search form Status
    function hgw_sidebar_searchform_status($control){
        if ( 'no-sidebar' != $control->manager->get_setting('hgw_sidebar_types')->value()) {
            return true;
        } else {
            return false;
        }
    }

}
add_action( 'customize_register', 'hgw_live_social_customize_register' );


function display_social_media(){


  if ( get_theme_mod('hgw_show_social_media_checkbox', 1 ) == 1 ) :

    $facebook_url = get_option('facebook_url');

    $twitter_url = get_option('twitter_url');

    $google_plus_url = get_option('google_plus_url');

    $youtube_url = get_option('youtube_url');

    $instagram_url = get_option('instagram_url');

    $linkedin_url = get_option('linkedin_url');

    $pinterest_url = get_option('pinterest_url');

    $telegram_url = get_option('telegram_url');

    $whatsapp_url = get_option('whatsapp_url');

    echo '<ul>';

      if ( $twitter_url ){

        echo '<li class="twitter"><a href="'.esc_url($twitter_url).'" target="_blank"><i class="fa fa-twitter-square" aria-hidden="true"></i></a></li>';
      }

      if ( $facebook_url ){

        echo '<li class="facebook"><a href="'.esc_url($facebook_url).'" target="_blank"><i class="fa fa-facebook-square" aria-hidden="true"></i></a></li>';
      }

      if ( $instagram_url ){

        echo '<li class="instagram"><a href="'.esc_url($instagram_url).'" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>';
      }

      if ( $google_plus_url ){

        echo '<li class="google-plus"><a href="'.esc_url($google_plus_url).'" target="_blank"><i class="fa fa-google-plus-square" aria-hidden="true"></i></a></li>';
      }

      if ( $youtube_url ){

        echo '<li class="youtube"><a href="'.esc_url($youtube_url).'" target="_blank"><i class="fa fa-youtube" aria-hidden="true"></i></a></li>';
      }

      if ( $linkedin_url ){

        echo '<li class="linkedin"><a href="'.esc_url($linkedin_url).'" target="_blank"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a></li>';
      }

      if ( $pinterest_url ){

        echo '<li class="pinterest"><a href="'.esc_url($pinterest_url).'" target="_blank"><i class="fa fa-pinterest-square" aria-hidden="true"></i></a></li>';
      }

      if ( $telegram_url ){

        echo '<li class="telegram"><a href="'.esc_url($telegram_url).'" target="_blank"><i class="fa fa-telegram" aria-hidden="true"></i></a></li>';
      }

      if ( $whatsapp_url ){

        echo '<li class="whatsapp"><a href="'.esc_url($whatsapp_url).'" target="_blank"><i class="fa fa-whatsapp" aria-hidden="true"></i></a></li>';
      }

    echo '</ul>';

  endif;


}
