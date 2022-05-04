<?php
/**
 * Theme Info Settings
 *
 * Register Theme Info Settings
 *
 * @package iknowledgebase
 */

/**
 * Adds all Theme Info settings to the Customizer
 *
 * @param object $wp_customize / Customizer Object.
 */
function iknowledgebase_customize_register_theme_info_settings( $wp_customize ) {

	// Add Section for Theme Info.
	$wp_customize->add_section( 'iknowledgebase_section_theme_info', array(
		'title'    => esc_html__( 'Theme Info', 'iknowledgebase' ),
		'priority' => 200,
		'panel'    => 'iknowledgebase_settings',
	) );

	// Add Theme Links control.
	$wp_customize->add_control( new IKnowledgebase_Customize_Links_Control(
		$wp_customize, 'iknowledgebase_theme_options[theme_links]', array(
			'section'  => 'iknowledgebase_section_theme_info',
			'settings' => array(),
			'priority' => 10,
		)
	) );

	// Add Pro Version control.
	if ( ! class_exists( 'iknowledgebase_pro\\Wow_Plugin' ) ) {
		$wp_customize->add_control( new IKnowledgebase_Customize_Upgrade_Control(
			$wp_customize, 'iknowledgebase_theme_options[pro_version]', array(
				'section'  => 'iknowledgebase_section_theme_info',
				'settings' => array(),
				'priority' => 20,
			)
		) );
	}

}
add_action( 'customize_register', 'iknowledgebase_customize_register_theme_info_settings' );


if ( class_exists( 'WP_Customize_Control' ) ) :

	/**
	 * Displays the theme links in the Customizer.
	 */
	class IKnowledgebase_Customize_Links_Control extends WP_Customize_Control {
		/**
		 * Render Control
		 */
		public function render_content() {
			?>

			<div class="theme-links">

				<span class="customize-control-title"><?php esc_html_e( 'Theme Links', 'iknowledgebase' ); ?></span>

				<p>
					<a href="https://wow-company.com/iknowledgebase-theme/"
					   target="_blank"><?php esc_html_e( 'Theme Page', 'iknowledgebase' ); ?></a>
				</p>

				<p>
					<a href="https://themes.wow-company.com/iknowledgebase/"
					   target="_blank"><?php esc_html_e( 'Theme Demo', 'iknowledgebase' ); ?></a>
				</p>

				<p>
					<a href="https://themes.wow-company.com/iknowledgebasepro/"
					   target="_blank"><?php esc_html_e( 'Theme Demo PRO version', 'iknowledgebase' ); ?></a>
				</p>

				<p>
					<a href="https://docs.wow-company.com/category/themes/iknowledgebase/" target="_blank">
						<?php esc_html_e( 'Theme Documentation', 'iknowledgebase' ); ?>
					</a>
				</p>

				<p>
					<a href="<?php echo esc_url( __( 'https://wordpress.org/support/theme/iknowledgebase/reviews/', 'iknowledgebase' ) ); ?>"
					   target="_blank"><?php esc_html_e( 'Rate this theme', 'iknowledgebase' ); ?></a>
				</p>

			</div>

			<?php
		}
	}

	class IKnowledgebase_Customize_Upgrade_Control extends WP_Customize_Control {
		/**
		 * Render Control
		 */
		public function render_content() {
			?>

            <div class="upgrade-pro-version">

                <span class="customize-control-title"><?php esc_html_e( 'Pro Version Add-on', 'iknowledgebase' ); ?></span>

                <span class="textfield">
					<?php printf( esc_html__( 'Purchase the %s Pro Add-on to get additional features and advanced customization options.', 'iknowledgebase' ), 'IKnowledgeBase' ); ?>
				</span>

                <p>
                    <a href="https://wow-estore.com/item/iknowledgebase-pro/" target="_blank" class="button button-secondary">
						<?php printf( esc_html__( 'Learn more about %s Pro', 'iknowledgebase' ), 'IKnowledgeBase' ); ?>
                    </a>
                </p>

            </div>

			<?php
		}
	}

endif;