<?php

class CMODSAR_Replacement {

	protected static $filePath	 = '';
	protected static $cssPath	 = '';
	protected static $jsPath	 = '';

	/**
	 * Adds the hooks
	 */
	public static function init() {
		self::$filePath	 = plugin_dir_url( __FILE__ );
		self::$cssPath	 = self::$filePath . 'assets/css/';
		self::$jsPath	 = self::$filePath . 'assets/js/';

		add_filter( 'cmodsar-settings-tabs-array', array( __CLASS__, 'addSettingsTabs' ) );
		add_filter( 'cmodsar-custom-settings-tab-content-1', array( __CLASS__, 'addSearchAndReplaceReplacementTabContent' ) );

		add_action( 'cmodsar_save_options_after_on_save', array( __CLASS__, 'saveReplacement' ) );

		/*
		 * Search&replace in content
		 */
		add_action( 'the_content', array( __CLASS__, 'doCustomReplacement' ), 15000 );

		add_action( 'wp_ajax_cmodsar_add_replacement', array( __CLASS__, 'ajaxAddReplacement' ) );
		add_action( 'wp_ajax_cmodsar_delete_replacement', array( __CLASS__, 'ajaxDeleteReplacement' ) );
		add_action( 'wp_ajax_cmodsar_update_replacement', array( __CLASS__, 'ajaxUpdateReplacement' ) );
	}

	/**
	 * Add the new settings tabs
	 * @param array $settingsTabs
	 * @return type
	 */
	public static function addSettingsTabs( $settingsTabs ) {
		$settingsTabs[ '1' ] = 'Replacement Rules';
		return $settingsTabs;
	}

	/**
	 * @param array $content
	 * @return type
	 */
	public static function addSearchAndReplaceReplacementTabContent( $content ) {
		ob_start();
		?>
		<div class="block">
			<h3>Replacement Rules</h3>

			<?php
			$repl = get_option( 'cmodsar_replacements', array() );
			self::outputReplacements( $repl, TRUE );
			?>
		</div>
		<?php
		$content .= ob_get_clean();
		return $content;
	}

	/**
	 * Adds the replacements with AJAX
	 */
	public static function ajaxAddReplacement() {
		$post			 = filter_input_array( INPUT_POST );
		$replacements	 = get_option( 'cmodsar_replacements', array() );

		if ( empty( $replacements ) ) {
			$replacements = array();
		}
		// Ticket 56905
		$replace_from = trim($post[ 'replace_from' ]);

		$replace[ 'from' ]	 = !empty( $replace_from ) ? $replace_from : '';
		$replace[ 'to' ]	 = !empty( $post[ 'replace_to' ] ) ? $post[ 'replace_to' ] : '';
		$replace[ 'case' ]	 = !empty( $post[ 'replace_case' ] ) ? 1 : 0;

		$replacements[] = $replace;

		update_option( 'cmodsar_replacements', $replacements );
		self::outputReplacements( $replacements );
		die();
	}

	/**
	 * Updates the replacements with AJAX
	 */
	public static function ajaxUpdateReplacement() {
		$post			 = filter_input_array( INPUT_POST );
		$replacements	 = get_option( 'cmodsar_replacements', array() );

		if ( empty( $replacements ) ) {
			$replacements = array();
		}
		// Ticket 56905
		$replace_from = trim($post[ 'replace_from' ]);

		$id = $post[ 'replace_id' ];
		if ( isset( $replacements[ $id ] ) ) {
			$replace[ 'from' ]	 = isset( $replace_from ) ? $replace_from : '';
			$replace[ 'to' ]	 = isset( $post[ 'replace_to' ] ) ? $post[ 'replace_to' ] : '';
			$replace[ 'case' ]	 = !empty( $post[ 'replace_case' ] ) ? 1 : 0;

			$replacements[ $id ] = $replace;
		}

		update_option( 'cmodsar_replacements', $replacements );
		self::outputReplacements( $replacements );
		die();
	}

	/**
	 * Deletes the replacement with AJAX
	 */
	public static function ajaxDeleteReplacement() {
		$repl = get_option( 'cmodsar_replacements', array() );
		unset( $repl[ $_POST[ 'id' ] ] );
		update_option( 'cmodsar_replacements', $repl );
		self::outputReplacements( $repl );
		die();
	}

	/**
	 * Outputs the replacements header
	 */
	public static function outputReplacementsHeader() {
		?>
		<thead>
			<tr>
				<th class="cmodsar_from_input">From String</th>
				<th class="cmodsar_to_input">To String</th>
				<th class="cmodsar_case_input">Case<div class="cmodsar_field_help" title="Select if you like the &quot;From String&quot; to be case-sensitive."></div></th>
		<th class="cmodsar_options_input">Options</th>
		</tr>
		</thead>
		<?php
	}

	/**
	 * Outputs the replacements table
	 * @param type $repl
	 * @param bool $addRow
	 */
	public static function outputReplacements( $repl, $addRow = false ) {
		?>
		<div class="cmodsar-custom-replacement-wrapper">
			<div class="cmodsar-custom-replacement-list">
				<table class="form-table cmodsar_replacements_list">
					<?php
					self::outputReplacementsHeader();
					?>
					<tbody>
						<?php
						if ( !empty( $repl ) && is_array( $repl ) ) {
							foreach ( $repl as $k => $r ) {
								self::_outputReplacementRow( $r, $k );
							}
						} else {
							echo '<tr><td colspan="5">' . CMODSAR_Base::__( 'No replacements. Please add using the form below.' ) . '</td></tr>';
						}
						?>

					</tbody>
				</table>
			</div>
			<?php
			if ( $addRow ) :
				?>
				<div class="cmodsar-custom-replacement-add">
					<table class="form-table">
						<?php
						echo self::outputReplacementsHeader();
						echo self::_outputAddingRow();
						?>
					</table>
				</div>
				<?php
			endif;
			?>
		</div>
		<?php
	}

	/**
	 * Outputs the single replacement row
	 * @param type $replacementRow
	 * @param type $rowKey
	 */
	public static function _outputAddingRow() {
		?>
		<tr valign="top" class="cmodsar_new_replacement_row">
			<td class="cmodsar_from_input">
				<textarea rows="3" type="text" placeholder="From" name="cmodsar_custom_from_new" value=""></textarea>
			</td>
			<td class="cmodsar_to_input">
				<textarea rows="3" type="text" placeholder="To" name="cmodsar_custom_to_new" value=""></textarea>
			</td>
			<td class="cmodsar_case_input">
				<input type="hidden" name="cmodsar_custom_case_new" value="0" />
				<input type="checkbox" name="cmodsar_custom_case_new" value="1" />
			</td>
			<td class="cmodsar_options_input">
				<input type="button" class="button-primary" value="Add Rule" id="cmodsar-custom-add-replacement-btn">
			</td>
		</tr>
		<?php
	}

	/**
	 * Outputs the single replacement row
	 * @param type $replacementRow
	 * @param type $rowKey
	 */
	public static function _outputReplacementRow( $replacementRow = array(), $rowKey = '' ) {
		$from	 = (isset( $replacementRow[ 'from' ] )) ? $replacementRow[ 'from' ] : '';
		$to		 = (isset( $replacementRow[ 'to' ] )) ? $replacementRow[ 'to' ] : '';
		$case	 = (isset( $replacementRow[ 'case' ] ) && $replacementRow[ 'case' ] == 1) ? 1 : 0;
		?>
		<tr valign="top" class="cmodsar_new_replacement_row">
			<td class="cmodsar_from_input">
				<textarea rows="3" placeholder="From" name="cmodsar_custom_from[<?php echo $rowKey; ?>]"><?php echo $from; ?></textarea>
			</td>
			<td class="cmodsar_to_input">
				<textarea rows="3" placeholder="To" name="cmodsar_custom_to[<?php echo $rowKey; ?>]"><?php echo $to; ?></textarea>
			</td>
			<td class="cmodsar_case_input">
				<input type="hidden" name="cmodsar_custom_case[<?php echo $rowKey; ?>]" value="0" />
				<input type="checkbox" name="cmodsar_custom_case[<?php echo $rowKey; ?>]" value="1" <?php echo checked( 1, $case ) ?> />
			</td>
			<td>
				<input type="button" value="Update" class="button-primary cmodsar-custom-update-replacement" data-uid="<?php echo $rowKey ?>" />
				<input type="button" value="Delete" class="button-secondary cmodsar-custom-delete-replacement" data-rid="<?php echo $rowKey ?>" />
			</td>
		</tr>
		<?php
	}

	/**
	 * Save the info about replaced terms
	 */
	public static function saveReplacement( $post ) {
		/*
		 * Added code to update replacements while updating other options
		 */

		if ( isset( $post[ 'cmodsar_custom_from' ] ) && isset( $post[ 'cmodsar_custom_to' ] ) && isset( $post[ 'cmodsar_custom_case' ] ) ) {
			if ( is_array( $post[ 'cmodsar_custom_from' ] ) && is_array( $post[ 'cmodsar_custom_to' ] ) && is_array( $post[ 'cmodsar_custom_case' ] ) ) {
				$replacement_from	 = $post[ 'cmodsar_custom_from' ];
				$replacement_to		 = $post[ 'cmodsar_custom_to' ];
				$replacement_case	 = $post[ 'cmodsar_custom_case' ];

				$repl_array = array();
				foreach ( $replacement_from as $key => $value ) {
					if ( $replacement_from[ $key ] != '' && $replacement_to[ $key ] != '' ) {
						$repl_array[ $key ] = array(
							'from'	 => $replacement_from[ $key ],
							'to'	 => $replacement_to[ $key ],
							'case'	 => (isset( $replacement_case[ $key ] ) ? $replacement_case[ $key ] : 0)
						);
					}
				}

				// Ticket 56905 Adding "Add Rule" function
				$replace_from = trim($post[ 'cmodsar_custom_from_new' ]);

		 		$replace[ 'from' ]	 = !empty( $replace_from ) ? $replace_from : '';
		 		$replace[ 'to' ]	 = !empty( $post[ 'cmodsar_custom_to_new' ] ) ? $post[ 'cmodsar_custom_to_new' ] : '';
		 		$replace[ 'case' ]	 = !empty( $post[ 'cmodsar_custom_case_new' ] ) ? 1 : 0;

		 		$repl_array[] = $replace;

				update_option( 'cmodsar_replacements', $repl_array );
			}
		}
	}

	/**
	 * Replaces the words within the text
	 * @param type $content
	 * @return type
	 */
	public static function doCustomReplacement( $content ) {
		global $post, $wp_query;

		if ( $post === NULL ) {
			return $content;
		}

		if ( !is_object( $post ) ) {
			$post = $wp_query->post;
		}

		$repl = get_option( 'cmodsar_replacements', array() );
		if ( !empty( $repl ) && is_array( $repl ) ) {
			foreach ( $repl as $r ) {
				if ( !empty( $r[ 'from' ] ) ) {
					// Ticket 56905
					$r[ 'from' ] = preg_replace( '/"(.*?)"/', '&#8221;$1&#8221;', $r[ 'from' ] );
					$r[ 'from' ] = preg_replace( "/'(.*?)'/", '&#8217;$1&#8217;', $r[ 'from' ] );
					$r[ 'from' ] = preg_replace( '/(.*?)"/', '$1&#8221;', $r[ 'from' ] );
					$r[ 'from' ] = preg_replace( "/(.*?)'/", '$1&#8217;', $r[ 'from' ] );

					$content = ($r[ 'case' ] == 1) ? str_replace( $r[ 'from' ], $r[ 'to' ], $content ) : str_ireplace( $r[ 'from' ], $r[ 'to' ], $content );
				}
			}
		}
		return $content;
	}

}
