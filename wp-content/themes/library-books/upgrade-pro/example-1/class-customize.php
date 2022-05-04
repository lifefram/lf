<?php
/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class library_books_Example_1_Customize {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function library_books_get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->library_books_setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function library_books_setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'library_books_sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'library_books_enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	 */
	public function library_books_sections( $manager ) {

		// Load custom sections.
		get_template_part( 'upgrade-pro/example-1/section', 'pro' );

		// Register custom section types.
		$manager->register_section_type( 'library_books_Example_1_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new library_books_Example_1_Customize_Section_Pro(
				$manager,
				'example_1',
				array(
					'title'    => esc_html__( 'Upgrade to Pro', 'library-books' ),
					'pro_text' => esc_html__( 'Upgrade Now', 'library-books' ),
					'pro_url'  => 'https://www.pinnaclethemes.net/product/library-wordpress-theme/',
					'priority'   => 1,
				)
			)
		);
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function library_books_enqueue_control_scripts() {

		wp_enqueue_script( 'example-1-customize-controls', trailingslashit( get_template_directory_uri() ) . 'upgrade-pro/example-1/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'example-1-customize-controls', trailingslashit( get_template_directory_uri() ) . 'upgrade-pro/example-1/customize-controls.css' );
	}
}

// Doing this customizer thang!
library_books_Example_1_Customize::library_books_get_instance();