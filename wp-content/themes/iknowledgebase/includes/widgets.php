<?php
/**
 * Widgets
 *
 * @package iknowledgebase
 */

get_template_part( 'includes/widgets/class-widget-current-nav' );


if ( ! function_exists( 'iknowledgebase_widgets_include' ) ) {
	function iknowledgebase_widgets_include() {
		register_widget( 'iknowledgebase_Widget_Current_Nav' );

	}
}
add_action( 'widgets_init', 'iknowledgebase_widgets_include' );



function iknowledgebase_widget_block_dynamic_classname($classname, $block_name) {
	switch ( $block_name ) {
		case 'core/page-list':
		case 'core/archives':
		case 'core/latest-posts':
			$classname .= ' is-size-7 menu-list';
			break;
		case 'core/heading':
		case 'core/group':
		case 'core/list':
		case 'core/table':
		case 'core/quote':
			$classname .= ' content';
			break;
	}

	return $classname;
}


add_filter( 'widget_block_dynamic_classname', 'iknowledgebase_widget_block_dynamic_classname', 10, 2 );
