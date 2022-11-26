<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Elementor_SearchInPlace_Widget extends Widget_Base {

	public function get_name() {
		return 'search-in-place';
	} // End get_name

	public function get_title() {
		return 'Search in Place';
	} // End get_title

	public function get_icon() {
		return 'eicon-search';
	} // End get_icon

	public function get_categories() {
		return array( 'search-in-place-cat' );
	} // End get_categories

	public function is_reload_preview_required() {
		return false;
	} // End is_reload_preview_required

	protected function register_controls() {
		$this->start_controls_section(
			'search_in_place_section',
			array(
				'label' => esc_html__( 'Search in Place', 'search-in-place' ),
			)
		);

		$this->add_control(
			'placeholder',
			array(
				'label'      => esc_html__( 'Placeholder text', 'search-in-place' ),
				'type'       => Controls_Manager::TEXT,
				'input_type' => 'text',
			)
		);

		$this->add_control(
			'search_in_page',
			array(
				'label'       => esc_html__( 'Search in current page only', 'search-in-place' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_block' => true,
			)
		);

		$this->add_control(
			'disable_enter_key',
			array(
				'label'       => esc_html__( 'Disable the enter key', 'search-in-place' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_block' => true,
			)
		);

		$this->add_control(
			'no_popup',
			array(
				'label'       => esc_html__( 'Hide pop-up, affects the search in current page only', 'search-in-place' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_block' => true,
			)
		);

		$this->add_control(
			'exclude_hidden',
			array(
				'label'       => esc_html__( 'Exclude hidden terms on page, affects the search in current page only', 'search-in-place' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_block' => true,
			)
		);

		$this->add_control(
			'display_button',
			array(
				'label'       => esc_html__( 'Display the search button, affects the search in current page only', 'search-in-place' ),
				'type'        => Controls_Manager::SWITCHER,
				'label_block' => true,
			)
		);

		$this->end_controls_section();
	} // End register_controls

	private function _get_shortcode() {
		 $attrs   = '';
		$settings = $this->get_settings_for_display();

		$attrs .= ' placeholder="' . ( isset( $settings['placeholder'] ) ? esc_attr( $settings['placeholder'] ) : '' ) . '"';
		if ( 'yes' == $settings['search_in_page'] ) {
			$attrs .= ' in_current_page="1"';
		}
		if ( 'yes' == $settings['disable_enter_key'] ) {
			$attrs .= ' disable_enter_key="1"';
		}
		if ( 'yes' == $settings['no_popup'] ) {
			$attrs .= ' no_popup="1"';
		}
		if ( 'yes' == $settings['exclude_hidden'] ) {
			$attrs .= ' exclude_hidden_terms="1"';
		}
		if ( 'yes' == $settings['display_button'] ) {
			$attrs .= ' display_button="1"';
		}

		return '[search-in-place-form' . $attrs . ']';
	} // End _get_shortcode

	protected function render() {
		print do_shortcode( shortcode_unautop( $this->_get_shortcode() ) );
	} // End render

	public function render_plain_content() {
		echo esc_html( $this->_get_shortcode() );
	} // End render_plain_content

} // End Elementor_SearchInPlace_Widget

// Register the widgets
Plugin::instance()->widgets_manager->register_widget_type( new Elementor_SearchInPlace_Widget() );
