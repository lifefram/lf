<?php
/*
Widget Name: Search in Place Form
Description: Inserts a search form.
Documentation: https://searchinplace.dwbooster.com
*/

if ( ! class_exists( 'SiteOrigin_SearchInPlace' ) ) {
	class SiteOrigin_SearchInPlace extends SiteOrigin_Widget {

		public function __construct() {
			parent::__construct(
				'siteorigin-search-in-place',
				__( 'Search In Place Form', 'search-in-place' ),
				array(
					'description'   => esc_html__( 'Insert a search form', 'search-in-place' ),
					'panels_groups' => array( 'search-in-place' ),
					'help'          => 'https://searchinplace.dwbooster.com',
				),
				array(),
				array(
					'placeholder'       => array(
						'type'    => 'text',
						'label'   => esc_html__( 'Placeholder text', 'search-in-place' ),
						'default' => '',
					),
					'search_in_page'    => array(
						'type'    => 'checkbox',
						'label'   => esc_html__( 'Search in current page only', 'search-in-place' ),
						'default' => 0,
					),
					'disable_enter_key' => array(
						'type'    => 'checkbox',
						'label'   => esc_html__( 'Disable the enter key', 'search-in-place' ),
						'default' => 0,
					),
					'no_popup'          => array(
						'type'    => 'checkbox',
						'label'   => esc_html__( 'Hide results pop-up, affects the search in current page only', 'search-in-place' ),
						'default' => 0,
					),
					'exclude_hidden'    => array(
						'type'    => 'checkbox',
						'label'   => esc_html__( 'Exclude hidden terms on page, affects the search in current page only', 'search-in-place' ),
						'default' => 0,
					),
					'display_button'    => array(
						'type'    => 'checkbox',
						'label'   => esc_html__( 'Display the search button, affects the search in current page only', 'search-in-place' ),
						'default' => 0,
					),
				),
				plugin_dir_path( __FILE__ )
			);
		} // End __construct

		public function get_template_name( $instance ) {
			return 'siteorigin-search-in-place';
		} // End get_template_name

		public function get_style_name( $instance ) {
			return '';
		} // End get_style_name

	} // End Class SiteOrigin_SearchInPlace

	// Registering the widget
	siteorigin_widget_register( 'siteorigin-search-in-place', __FILE__, 'SiteOrigin_SearchInPlace' );
}
