<?php

/**  
 * Copyright 2013-2022 Epsiloncool
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 ******************************************************************************
 *  I am thank you for the help by buying PRO version of this plugin 
 *  at https://fulltextsearch.org/ 
 *  It will keep me working further on this useful product.
 ******************************************************************************
 * 
 *  @copyright 2013-2022
 *  @license GPLv3
 *  @package Wordpress Fulltext Search
 *  @author Epsiloncool <info@e-wm.org>
 */

class WPFTS_Custom_Widget extends WP_Widget
{
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct()
	{
		parent::__construct(
			'wpfts_custom_widget', // Base ID
			__('WPFTS :: Live Search', 'fulltext-search'), // Name
			array(
				'classname' => 'widget_search',
				'description' => __('The highly configurable widget for WPFTS search', 'fulltext-search'),
				'customize_selective_refresh' => true,
			) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance)
	{
		$instance = apply_filters('wpfts_widget_instance', $instance, $this->id_base);

		$title = !empty($instance['title']) ? $instance['title'] : '';
		$preset_id = !empty($instance['wpfts_wdgt']) ? $instance['wpfts_wdgt'] : '';
		$placeholder = isset($instance['placeholder']) ? $instance['placeholder'] : '';
		$button_text = isset($instance['button_text']) ? $instance['button_text'] : '';
		$hide_button = isset($instance['hide_button']) ? $instance['hide_button'] : '';
		$class = isset($instance['class']) ? $instance['class'] : '';

		$preset_id2 = $preset_id;
		if (strlen($preset_id) < 1) {
			$preset_id2 = 'default';
		}

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters('widget_title', $title, $instance, $this->id_base);

		// get_search_form();	// We replaced this form

		$preset = array(
			'results_url' => home_url('/'),
			'autocomplete_mode' => 1,
		);
		if ((strlen($preset_id) > 0) && (function_exists('WPFTS_Get_Widget_List'))) {
			$prs = WPFTS_Get_Widget_List();
			if (isset($prs[$preset_id])) {
				$preset = $prs[$preset_id];
			}
		}

		$preset = apply_filters('wpfts_preset_data', $preset, $preset_id, 'widget');

		ob_start();

		echo isset($args['before_widget']) ? $args['before_widget'] : '';

		?><div class="wpfts_widget wpfts_search_widget presetid-<?php echo $preset_id2; ?><?php echo strlen($class) > 0 ? ' '.$class : ''; ?>">
				<?php
					if ($title) {
						echo (isset($args['before_title']) ? $args['before_title'] : '') . $title . (isset($args['after_title']) ? $args['after_title'] : '');
					}
				?>
		<form role="search" class="wpfts_search_form-<?php echo htmlspecialchars($preset_id2); ?> search-form <?php echo $preset['autocomplete_mode'] ? ' wpfts_autocomplete': ''; ?>" action="<?php echo htmlspecialchars($preset['results_url']); ?>" method="get">
			<?php echo (strlen($preset_id) > 0) ? '<input type="hidden" name="wpfts_wdgt" value="'.htmlspecialchars($preset_id).'">' : ''; ?>
			<label>
				<span class="screen-reader-text"><?php echo __('Search for:', 'fulltext-search'); ?></span>
				<input type="search" class="search-field" placeholder="<?php echo $placeholder; ?>" value="<?php echo get_search_query(); ?>" name="s">
			</label>
			<?php if ($hide_button != 1) {
				?><input type="submit" class="search-submit" value="<?php echo $button_text; ?>"><?php
			}
			?>
		</form>
		</div>
		<?php
		$out = ob_get_clean();

		echo apply_filters('wpfts_widget_html', $out, $preset, $preset_id, 'widget');

		echo (isset($args['after_widget']) ? $args['after_widget'] : '');
	}

	/*
	* Outputs the settings form for the Search widget.
	*
	* @since 2.8.0
	*
	* @param array $instance Current settings.
	*/
	public function form($instance)
	{
		$instance = wp_parse_args(
			(array) $instance, 
			array(
				'title' => '', 
				'wpfts_wdgt' => '',
				'placeholder' => __('Search &hellip;', 'fulltext-search'),
				'button_text' => __('Search', 'fulltext-search'),
				'hide_button' => 0,
				'class' => '',
			)
		);
		$title = $instance['title'];
		$wpfts_wdgt = $instance['wpfts_wdgt'];
		$placeholder = $instance['placeholder'];
		$button_text = $instance['button_text'];
		$hide_button = $instance['hide_button'];
		$class = $instance['class'];

		$widget_list = array('' => __('-- No Preset --', 'fulltext-search'));
		if (function_exists('WPFTS_Get_Widget_List')) {
			$prs = WPFTS_Get_Widget_List();
			foreach ($prs as $k => $d) {
				$widget_list[$k] = $d['title'];
			}
		}

		$wpfts_wdgt_id = $this->get_field_id('wpfts_wdgt');
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<?php echo __('Title:', 'fulltext-search'); ?> 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $wpfts_wdgt_id; ?>">
				<?php echo __('Preset:', 'fulltext-search'); ?>
					<a href="#" id="<?php echo $wpfts_wdgt_id.'_edtpst'; ?>" target="_blank"><?php echo __('Edit Presets', 'fulltext-search'); ?></a> 
					<select class="widefat" id="<?php echo $wpfts_wdgt_id; ?>" name="<?php echo $this->get_field_name('wpfts_wdgt'); ?>">
					<?php
						foreach ($widget_list as $k => $d) {
							echo '<option value="'.esc_attr($k).'"'.($k == $wpfts_wdgt ? ' selected="selected"' : '').'>'.htmlspecialchars($d).'</option>';
						}
					?>
				</select>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('placeholder'); ?>">
				<?php echo __('Placeholder Text:', 'fulltext-search'); ?> 
				<input class="widefat" id="<?php echo $this->get_field_id('placeholder'); ?>" name="<?php echo $this->get_field_name('placeholder'); ?>" type="text" value="<?php echo esc_attr($placeholder); ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('button_text'); ?>">
				<?php echo __('Button Text:', 'fulltext-search'); ?> 
				<input class="widefat" id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" type="text" value="<?php echo esc_attr($button_text); ?>" />
			</label>
			<label for="<?php echo $this->get_field_id('hide_button'); ?>">
				<input type="checkbox" value="1" id="<?php echo $this->get_field_id('hide_button'); ?>" name="<?php echo $this->get_field_name('hide_button'); ?>" <?php echo ($hide_button == 1) ? ' checked="checked"' : ''; ?> />&nbsp;
				<?php echo __('Hide Button', 'fulltext-search'); ?> 
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('class'); ?>">
				<?php echo __('CSS Class:', 'fulltext-search'); ?> 
				<input class="widefat" id="<?php echo $this->get_field_id('class'); ?>" name="<?php echo $this->get_field_name('class'); ?>" type="text" value="<?php echo esc_attr($class); ?>" />
			</label>
		</p>

		<style type="text/css">
		<?php echo '#'.$wpfts_wdgt_id.'_edtpst'; ?> {
			display: inline-block;
    		position: absolute;
    		right: 20px;
		}
		</style>
		<?php
	}

	/*
	* Handles updating settings for the current Search widget instance.
	*
	* @since 2.8.0
	*
	* @param array $new_instance New settings for this instance as input by the user via
	*                            WP_Widget::form().
	* @param array $old_instance Old settings for this instance.
	* @return array Updated settings.
	*/
	public function update($new_instance, $old_instance)
	{
		$instance          = $old_instance;
		$new_instance      = wp_parse_args((array) $new_instance, array('title' => '', 'wpfts_wdgt' => ''));
		$instance['title'] = sanitize_text_field($new_instance['title']);
		$instance['wpfts_wdgt'] = sanitize_text_field($new_instance['wpfts_wdgt']);
		$instance['placeholder'] = sanitize_text_field($new_instance['placeholder']);
		$instance['button_text'] = sanitize_text_field($new_instance['button_text']);
		$instance['hide_button'] = sanitize_text_field($new_instance['hide_button']);
		$instance['class'] = sanitize_text_field($new_instance['class']);
		return $instance;
	}
}
