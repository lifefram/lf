<?php
/**
* Custom Theme info for This Theme
* Show in Dashboard, Link to Customizer Options, Show the latest features
* @subpackage Hgw_WhiteBoard
*/


function hgw_whiteboard_information () {
  wp_redirect(admin_url("themes.php?page=hgw-whiteboard"));
}
add_action('after_switch_theme', 'hgw_whiteboard_information');

function hgw_whiteboard_theme_page() {
    add_theme_page(
      'Hgw Whiteboard',
      'Hgw Whiteboard',
      'edit_theme_options',
      'hgw-whiteboard',
      'hgw_whiteboard_theme_option_page_info',
      1
    );
}
add_action( 'admin_menu', 'hgw_whiteboard_theme_page' );

function hgw_whiteboard_theme_option_page_info() { ?>
  <div class="wrap about__container">
   	<div class="about__section is-feature" style="background: #2271b1;color: white;">
   		<div class="column">
   			<h1 class="is-smaller-heading">Hgw Whiteboard - <?php $theme_version = wp_get_theme()->get( 'Version' ); echo esc_html($theme_version);?></h1>
   			<p>
          <?php esc_html_e( 'Hi, thank you for choosing Hgw Whiteboard theme. Help improve this theme with your suggestions.', 'hgw-whiteboard' ); ?>
        </p>
   		</div>
   	</div>
   	<hr>
   	<div class="about__section has-2-columns"  style="background-color: white;">
   		<div class="column">
          <?php
          echo sprintf(
            '<h3>%1$s</h3><p></p>%2$s<p><a href="%3$s" class="button button-primary" target="_self">%4$s</a></p>',
            esc_html__( 'Theme Options', 'hgw-whiteboard' ),
            esc_html__( 'Theme uses Customizer API for theme options. Using the Customizer you can easily customize different aspects of the theme.', 'hgw-whiteboard' ),
            esc_url( admin_url( 'customize.php?autofocus%5Bsection%5D=hgw-whiteboard-panel' ) ),
            esc_html__( 'Options', 'hgw-whiteboard' )
           );
          ?>
   		</div>
   		<div class="column">
          <?php
          echo sprintf(
            '<h3>%1$s</h3><p></p>%2$s<p><a href="%3$s" class="button button-primary" target="_self">%4$s</a></p>',
            esc_html__( 'Widgets', 'hgw-whiteboard' ),
            esc_html__( 'Theme uses Wedgets API for widget options. Using the Widgets you can easily customize different aspects of the theme.', 'hgw-whiteboard' ),
            esc_url( admin_url( 'widgets.php' ) ),
            esc_html__( 'Widgets', 'hgw-whiteboard' )
           );
          ?>
   		</div>
   	</div>
    <hr>
    <div class="about__section"  style="background-color: white;">
      <div class="column">
        <?php
        echo sprintf(
          '<h3>%1$s</h3><p>%2$s&nbsp;</p><p>%3$s&nbsp;<a href="%4$s" class="button button-primary" target="_blank">%5$s</a></p><p>%6$s&nbsp;<a href="%7$s" class="button button-primary" target="_self">%8$s</a></p>',
          esc_html__( 'Participate to get better', 'hgw-whiteboard' ),
          esc_html__( 'Any suggestion for improvement is welcome, if you think your suggestion to improve this script let me know', 'hgw-whiteboard' ),
          esc_html__( 'Have an idea for a better whiteboard theme?', 'hgw-whiteboard' ),
          esc_url( 'https://hamgamweb.com/themes/suggestion-or-criticism/' ),
          esc_html__( 'Tell me', 'hgw-whiteboard' ),
          esc_html__( 'Please rate this theme in WordPress', 'hgw-whiteboard' ),
          esc_url( 'https://wordpress.org/support/theme/hgw-whiteboard/reviews/#new-post' ),
          esc_html__( 'Review', 'hgw-whiteboard' )
         );
        ?>
   		</div>
    </div>
   	<hr class="is-small">
    <div class="about__section" style="background-color: white;border: 3px solid #2271b1;">
      <div class="column" style="direction: ltr;">
        <h3>Changelog 1.2</h3>
          <h4>1. Improved style and mobile friendly</h4>
            <p style="background:aliceblue;padding:20px;">
              In this version, the appearance of some parts has been improved,
              the logo display problems have been fixed and it is displayed well in mobile mode, Comments & ...
            </p>
          <h4>2. Display the logo and hide the site name and description</h4>
            <p style="background:aliceblue;padding:20px;margin-bottom: 0;">
              Now you can display the logo and hide the site name and description, Or use both<br />
              <b style="line-height: 3;">Activation :</b><br />
            </p>
              <ol style="background:aliceblue;margin: 0;padding: 5px 30px;">
                <li><b style="color: blue;">Upload the logo</b>
                  <ol>
                    <li>Customize</li>
                    <li>Site Identity</li>
                    <li>Select logo</li>
                    <li>Upload the logo and click the Select button( You can click the <b>skip cropping button</b> to select the full size )</li>
                    <li>Published</li>
                  </ol>
                </li>
                <li><b style="color: blue;">Hide sitename & description if you want</b>
                  <ol>
                    <li>Customize</li>
                    <li>Header</li>
                    <li>Enable Hide sitename & description in header</li>
                    <li>Published</li>
                  </ol>
                </li>
              </ol>
          <h4>3. Added 3 layouts for sidebars</h4>
            <p style="background:aliceblue;padding:20px;">
              Now you can use 1 sidebar or 2 sidebars on both sides,
              You can even remove the sidebar!<br />
              <b style="line-height: 3;">Activation :</b><br />
              1. Customize<br />
              2. Sidebars<br />
              3. Sidebar Settings<br />
              4. Choose in Select Box (Default, No sidebar, Two sidebars)<br />
              5. Published<br />
            </p>
      </div>
   	</div>
    <hr class="is-small">
   	<div class="about__section" style="background-color: white;">
      <div class="column" style="direction: ltr;">
        <h3>Changelog 1.1</h3>
          <h4>1. Improved style and mobile friendly</h4>
          <h4>2. Added short link</h4>
            <p style="background:aliceblue;padding:20px;">
              Thanks to "Mohsen bagheri" suggestion, a short link was added to the end of the content<br />
              <b style="line-height: 3;">Activation :</b><br />
              1. Go to any singular page<br />
              2. Customize<br />
              3. Singular<br />
              4. Click on "Show Short url"<br />
              5. Published<br />
            </p>
          <h4>3. Added Breadcrumbs</h4>
            <p style="background:aliceblue;padding:20px;">
              There are three types here & not displayed on the home page<br />
              <b style="line-height: 3;">Activation :</b><br />
              1. Customize<br />
              2. Breadcrumbs <br />
              3. Click on "Show Breadcrumb"<br />
              4. Published<br />
            </p>
          <h4>4. Added New Layouts for Archives</h4>
            <p style="background:aliceblue;padding:20px;">
              There are three types here<br />
              <b style="line-height: 3;">How to use?</b><br />
              1. Go to Home page<br />
              1. Customize<br />
              2. Archive posts layouts<br />
              3. Choose in Select Box<br />
              4. Published<br />
              <span style="color: green">More layouts in next version ;)</span>
            </p>
      </div>
   	</div>
  </div>
<?php
}
