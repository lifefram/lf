<?php
/* Prevent direct access */
defined( 'ABSPATH' ) || die( "You can't access this file directly." );

class CodePeopleSearchInPlace {

	private $autocomplete;
	private $text_domain = 'search-in-place';
	private $javascriptVariable;
	private $id_list = array();

	public function __construct() {
		require_once dirname( __FILE__ ) . '/autocomplete.clss.php';
		require_once dirname( __FILE__ ) . '/../pagebuilders/pagebuilders.php';
		$this->autocomplete = new CPSPAutocomplete();
		CPSP_PAGEBUILDERS::run();
	} // End __construct

	private function _process_summary( &$text, $terms, $length, $post_id ) {
		$l = strlen( $text );
		$i = ( ! empty( $text ) && ! empty( $terms ) ) ? strpos( strtolower( $text ), strtolower( $terms ) ) : false;

		$p = ( false !== $i && $i - $length / 2 > 0 ) ? $i - $length / 2 : 0;

		// Start the summary from the begining of word
		if ( $p > 0 ) {
			if ( ' ' == $text[ $p ] ) {
				$p++;
			} elseif ( ' ' !== $text[ $p - 1 ] ) {
				$k       = strrpos( $text, ' ', -1 * ( $l - $p ) );
				$k       = ( $k < 0 ) ? 0 : $k + 1;
				$length += $p - $k;
				$p       = $k;
			}
		}
		$text = substr( $text, $p, $length );
		$text = apply_filters( 'search-in-page-summary', $text, $post_id );
		return ( false !== $i );
	} // End _process_summary

	/*
		Load the language file and initialize the javascript object to pass to the client side
	*/
	public function init() {
		// I18n
		load_plugin_textdomain( 'search-in-place', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages/' );

		$root = trim( get_admin_url( get_current_blog_id() ), '/' ) . '/';

		$this->javascriptVariables = array(
			'own_only'       => get_option( 'search_in_place_own_only', 0 ),
			'result_number'  => get_option( 'search_in_place_number_of_posts', 10 ),
			'more'           => esc_html__( get_option( 'search_in_place_more_results_text', __( 'More Results', 'search-in-place' ) ), 'search-in-place' ),
			'empty'          => esc_html__( '0 results', 'search-in-place' ),
			'char_number'    => get_option( 'search_in_place_minimum_char_number', 3 ),
			'root'           => base64_encode( substr( $root, strpos( $root, '//' ) ) ),
			'home'           => get_home_url( get_current_blog_id() ),
			'summary_length' => get_option( 'search_in_place_summary_char_number', 20 ),
			'operator'       => get_option( 'search_in_place_connection_operator', 'or' ),
		);

		$locale       = get_locale();
		$locale_parts = explode( '_', $locale );
		if ( count( $locale_parts ) ) {
			$this->javascriptVariables['lang'] = $locale_parts[0];
		}

		$highlight_colors = get_option( 'search_in_place_highlight_colors', array( '#B5DCE1', '#F4E0E9', '#D7E0B1', '#F4D9D0', '#D6CDC8', '#F4E3C9', '#CFDAF0', '#F4EFEC' ) );

		if ( ! empty( $highlight_colors ) ) {
			$this->javascriptVariables['highlight_colors'] = $highlight_colors;
		}

		$search_in_page_selectors = get_option( 'search_in_place_selectors', array( 'div.hentry', '#content', '#main', 'div.content', '#middle', '#container', '#wrapper', 'article', '.elementor', 'body' ) );

		if ( ! empty( $search_in_page_selectors ) ) {
			$this->javascriptVariables['areas'] = $search_in_page_selectors;
		}

		// Fake variables to allow the translation for Poedit application
		$a = __( 'post', 'search-in-place' );
		$a = __( 'page', 'search-in-place' );
	} // End init

	/*
		Returns the get search form
	*/
	public function get_search_form( $atts = array() ) {
		$args = shortcode_atts(
			array( 'in_current_page' => 0 ),
			$atts
		);

		$in_current_page = @intval( $args['in_current_page'] );

		$search = get_search_form( false );
		$search = str_replace(
			'name="s"',
			'name="s" data-search-in-place="1"' . ( $in_current_page ? ' data-search-in-page="1"' : '' ),
			$search
		);

		if ( ! $in_current_page && isset( $atts['disable_enter_key'] ) && @intval( $atts['disable_enter_key'] ) ) {
			$search = str_replace( 'name="s"', 'name="s" data-disable-enter-key="1"', $search );
		}

		if ( isset( $atts['exclude_hidden_terms'] ) && @intval( $atts['exclude_hidden_terms'] ) ) {
			$search = str_replace( 'name="s"', 'name="s" data-exclude-hidden="1"', $search );
		}

		if ( isset( $atts['placeholder'] ) ) {
			if ( preg_match( '/placeholder="[^"]*"/i', $search ) ) {
				$search = preg_replace( '/placeholder="[^"]*"/i', 'placeholder="' . esc_attr__( $atts['placeholder'], 'search-in-place' ) . '"', $search );
			} else {
				$search = preg_replace( '/name="s"/i', 'name="s" placeholder="' . esc_attr__( $atts['placeholder'], 'search-in-place' ) . '"', $search );
			}
		}

		if ( isset( $atts['no_popup'] ) && @intval( $atts['no_popup'] ) ) {
			$search = str_replace( 'name="s"', 'name="s" data-no-popup="1"', $search );
		}

		$search = preg_replace( '/<\/form>/i', '<input type="hidden" name="search_in_place_form" value="1"></form>', $search );
		$search = '<div class="search-in-place-box-container ' . ( isset( $atts['display_button'] ) && @intval( $atts['display_button'] ) ? '' : 'hide-search-button' ) . ' ">' . apply_filters( 'search-in-page-form', $search ) . '</div>';
		return $search;
	} // End get_search_form

	public function javascriptVariables() {
		return $this->javascriptVariables;
	} // End javascritpVariables

	/*
		The most important method for search process, populate the list of results.
	*/
	public function modifySearchQuery( $query, $query_obj ) {
		global $cp_search_in_place;

		if ( ( ! is_admin() && $query_obj->is_main_query() && is_search() && isset( $_GET['s'] ) ) || ! empty( $cp_search_in_place ) ) {
			$connection_operator = get_option( 'search_in_place_connection_operator', 'or' );
			$connection_operator = ( ( empty( $connection_operator ) ) ? 'OR' : $connection_operator );
			$query               = preg_replace( '/\)\)\s*AND\s*\(\(/i', ')) ' . $connection_operator . ' ((', $query );
		}

		$query = apply_filters( 'search-in-page-query', $query );
		return $query;

	} // End modifySearchQuery

	public function populate() {
		global $wp_query, $wpdb, $cp_search_in_place;

		$cp_search_in_place = true;

		add_filter( 'posts_request', array( &$this, 'modifySearchQuery' ), 10, 2 );

		$limit     = get_option( 'search_in_place_number_of_posts', 10 ); // Number of results to display
		$post_list = array();

		$wp_query = new WP_Query();

		// Get the posts and pages with the search terms
		$s = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

		$params = array(
			's'           => $s,
			'showposts'   => $limit,
			'post_type'   => 'any',
			'post_status' => 'publish',
		);

		$wp_query->query( $params );
		$posts = $wp_query->posts;

		foreach ( $posts as $result ) {
			if ( in_array( $result->ID, $this->id_list ) ) {
				continue;
			} else {
				array_push( $this->id_list, $result->ID );
			}
			$obj = new stdClass();
			// Include the author in search results
			if ( get_option( 'search_in_place_display_author', 1 ) == 1 ) {
				$author      = get_userdata( $result->post_author );
				$obj->author = $author->display_name;
			}

			// The link to the item is required
			$obj->link = get_permalink( $result->ID );

			// Include the thumbnail in search results
			if ( get_option( 'search_in_place_display_thumbnail', 1 ) ) {
				if ( 'attachment' == $result->post_type ) {
					if ( strpos( $result->post_mime_type, 'image' ) !== false ) {
						$obj->thumbnail = wp_get_attachment_thumb_url( $result->ID );
					}
				} else {

					if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $result->ID ) ) {
						// If post thumbnail is used
						$obj->thumbnail = wp_get_attachment_thumb_url( get_post_thumbnail_id( $result->ID, 'thumbnail' ) );
					} elseif ( function_exists( 'get_post_image_id' ) ) {
						// Support for WP 2.9 post thumbnails
						$imgID          = get_post_image_id( $result->ID );
						$img            = wp_get_attachment_image_src( $imgID, apply_filters( 'post_image_size', 'thumbnail' ) );
						$obj->thumbnail = $img[0];
					} else {
						// If not post thumbnail, grab the first image from the post
						// Get images for this post
						$imgArr = @get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $result->ID );

						// If images exist for this page
						if ( ! empty( $imgArr ) ) {
							$flag = PHP_INT_MAX;

							foreach ( $imgArr as $img ) {
								if ( $img->menu_order < $flag ) {
									$flag         = $img->menu_order;
									$img_selected = $img;
								}
							}
							$obj->thumbnail = wp_get_attachment_thumb_url( $img_selected->ID );
						}
					}
				}
			}

			// Include a post summary in search results, the summary is limited to the number of letters declared in configuration
			if ( get_option( 'search_in_place_display_summary', 1 ) ) {

				$length     = get_option( 'search_in_place_summary_char_number', 20 );
				$summary    = '';
				$in_excerpt = false;
				$in_content = false;

				if ( ! empty( $result->post_excerpt ) ) {
					$excerpt    = preg_replace( '/\[[^\]]*\]/', '', $result->post_excerpt );
					$excerpt    = apply_filters( 'get_the_excerpt', $excerpt );
					$excerpt    = apply_filters( 'localization', $excerpt );
					$excerpt    = strip_tags( $excerpt );
					$in_excerpt = $this->_process_summary( $excerpt, $s, $length, $result->ID );
				}

				if ( ! $in_excerpt ) {
					$content    = preg_replace( '/\[[^\]]*\]/', '', $result->post_content );
					$content    = apply_filters( 'the_content', $content );
					$content    = apply_filters( 'localization', $content );
					$content    = strip_tags( $content );
					$in_content = $this->_process_summary( $content, $s, $length, $result->ID );
					$summary    = ( ! $in_content && ! empty( $excerpt ) ) ? $excerpt : $content;
				} else {
					$summary = $excerpt;
				}

				// Set the search terms in bold
				$obj->resume = preg_replace( '/(' . preg_quote( $s ) . ')/i', '<strong>$1</strong>', $summary ) . '<span class="ellipsis">[...]</span>';
			}

			// Include the publication date in search results
			if ( get_option( 'search_in_place_display_date', 1 ) ) {
				$obj->date = date_i18n( get_option( 'search_in_place_date_format' ), strtotime( $result->post_date ) );
			}

			// The post title is a required field
			$obj->title = apply_filters( 'localization', apply_filters( 'the_title', $result->post_title, $result->ID ) );

			$type = esc_html__( $result->post_type, 'search-in-place' );
			if ( ! isset( $post_list[ $type ] ) ) {
				$post_list[ $type ] = array();
			}
			$obj                  = apply_filters( 'search-in-page-item', $obj, $result->ID );
			$post_list[ $type ][] = $obj;

		}

		$post_list = apply_filters( 'search-in-page-results', $post_list );
		$output    = array(
			'result' => $post_list,
		);

		// Load the autocomplete terms if enabled
		if ( $this->autocomplete->getAttr( 'enabled' ) && ! empty( $_GET['s'] ) ) {
			$s                      = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
			$autocomplete           = $this->autocomplete->autocomplete( $s );
			$autocomplete           = apply_filters( 'search-in-page-autocomplete', $autocomplete );
			$output['autocomplete'] = $autocomplete;
		}
		if ( defined( 'JSON_PARTIAL_OUTPUT_ON_ERROR' ) ) {
			print json_encode( $output, JSON_PARTIAL_OUTPUT_ON_ERROR );
		} else {
			print json_encode( $output );
		}
		die;

	} // End populate

	/*
		Allow for search in posts, pages and attachments
	*/
	public function modifySearch( $query ) {
		if ( ! is_admin() && $query->is_search && isset( $_GET['s'] ) ) {
			$query->set( 'post_type', array( 'post', 'page' ) );
			$query->set( 'post_status', array( 'publish' ) );
		}
	} // End modifySearch

	/*
		Set a link to plugin settings
	*/
	public function settingsLink( $links ) {
		$settings_link = '<a href="options-general.php?page=codepeople_search_in_place.php">' . __( 'Settings' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	} // End settingsLink

	/*
		Set a link to contact page
	*/
	public function customizationLink( $links ) {
		array_unshift(
			$links,
			'<a href="https://searchinplace.dwbooster.com/contact-us" target="_blank">' . __( 'Request custom changes' ) . '</a>',
			'<a href="https://wordpress.org/support/plugin/search-in-place#new-post" target="_blank">' . __( 'Help' ) . '</a>'
		);
		return $links;
	} // End settingsLink

	public function clearSettings() {
		delete_option( 'search_in_place_number_of_posts' );
		delete_option( 'search_in_place_minimum_char_number' );
		delete_option( 'search_in_place_summary_char_number' );
		delete_option( 'search_in_place_display_thumbnail' );
		delete_option( 'search_in_place_display_date' );
		delete_option( 'search_in_place_display_summary' );
		delete_option( 'search_in_place_display_author' );
		delete_option( 'search_in_place_box_background_color' );
		delete_option( 'search_in_place_box_border_color' );
		delete_option( 'search_in_place_label_text_color' );
		delete_option( 'search_in_place_label_text_shadow' );
		delete_option( 'search_in_place_label_background_start_color' );
		delete_option( 'search_in_place_label_background_end_color' );
		delete_option( 'search_in_place_active_item_background_color' );
		delete_option( 'search_in_place_own_only' );
		delete_option( 'search_in_place_date_format' );
		delete_option( 'search_in_place_connection_operator' );
		delete_option( 'search_in_place_selectors' );
		delete_option( 'search_in_place_highlight_colors' );

		$this->autocomplete->clearSettings();
	} // End clearSettings

	/**
		Print out the admin page
	 */
	public function printAdminPage() {
		if ( isset( $_GET['page'] ) ) {
			if ( 'search_in_place_help' == sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {
				echo( "Redirecting to documentation...<script type='text/javascript'>document.location='https://searchinplace.dwbooster.com?acode=19735';</script>" );
				exit;
			}
			if ( 'search_in_place_upgrade' == sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {
				echo( "Upgrade...<script type='text/javascript'>document.location='https://searchinplace.dwbooster.com/download';</script>" );
				exit;
			}
		}

		wp_enqueue_style( 'codepeople-search-in-place-admin', plugin_dir_url( __FILE__ ) . '../css/codepeople_shearch_in_place_admin.css', array(), SEARCH_IN_PLACE_VERSION );

		// Load the picker color resources
		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );

		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'search-in-place-settings' ) && ! isset( $_POST['search_in_place_delete_settings'] ) ) {
			echo '<div class="updated"><p><strong>' . esc_html__( 'Settings Updated' ) . '</strong></div>';

			$_POST['number_of_posts']     = isset( $_POST['number_of_posts'] ) && is_numeric( $_POST['number_of_posts'] ) ? intval( $_POST['number_of_posts'] ) : 0;
			$_POST['minimum_char_number'] = isset( $_POST['minimum_char_number'] ) && is_numeric( $_POST['minimum_char_number'] ) ? intval( $_POST['minimum_char_number'] ) : 0;
			$_POST['summary_char_number'] = isset( $_POST['summary_char_number'] ) && is_numeric( $_POST['summary_char_number'] ) ? intval( $_POST['summary_char_number'] ) : 0;

			$search_in_place_number_of_posts     = ( ! empty( $_POST['number_of_posts'] ) && is_numeric( $_POST['number_of_posts'] ) && $_POST['number_of_posts'] > 0 ) ? intval( $_POST['number_of_posts'] ) : 10;
			$search_in_place_own_only            = ( ! empty( $_POST['own_only'] ) ) ? 1 : 0;
			$search_in_place_minimum_char_number = ( ! empty( $_POST['minimum_char_number'] ) && is_numeric( $_POST['minimum_char_number'] ) && $_POST['minimum_char_number'] > 0 ) ? intval( $_POST['minimum_char_number'] ) : 3;
			$search_in_place_summary_char_number = ( ! empty( $_POST['summary_char_number'] ) && is_numeric( $_POST['summary_char_number'] ) && $_POST['summary_char_number'] >= 0 ) ? intval( $_POST['summary_char_number'] ) : 20;
			$search_in_place_date_format         = isset( $_POST['date_format'] ) ? sanitize_text_field( wp_unslash( $_POST['date_format'] ) ) : 'Y-m-d';
			$search_in_place_display_thumbnail   = ( ! empty( $_POST['thumbnail'] ) ) ? sanitize_text_field( wp_unslash( $_POST['thumbnail'] ) ) : 0;
			$search_in_place_display_date        = ( ! empty( $_POST['date'] ) ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : 0;
			$search_in_place_display_summary     = ( ! empty( $_POST['summary'] ) ) ? sanitize_text_field( wp_unslash( $_POST['summary'] ) ) : 0;
			$search_in_place_display_author      = ( ! empty( $_POST['author'] ) ) ? sanitize_text_field( wp_unslash( $_POST['author'] ) ) : 0;
			$search_in_place_connection_operator = ( ! empty( $_POST['connection_operator'] ) && in_array( $_POST['connection_operator'], array( 'and', 'or' ) ) ) ? sanitize_text_field( wp_unslash( $_POST['connection_operator'] ) ) : 'or';

			$box_background_color         = ( isset( $_POST['box_background_color'] ) ) ? sanitize_text_field( wp_unslash( $_POST['box_background_color'] ) ) : '';
			$box_border_color             = ( isset( $_POST['box_border_color'] ) ) ? sanitize_text_field( wp_unslash( $_POST['box_border_color'] ) ) : '';
			$label_text_color             = ( isset( $_POST['label_text_color'] ) ) ? sanitize_text_field( wp_unslash( $_POST['label_text_color'] ) ) : '';
			$label_text_shadow            = ( isset( $_POST['label_text_shadow'] ) ) ? sanitize_text_field( wp_unslash( $_POST['label_text_shadow'] ) ) : '';
			$label_background_start_color = ( isset( $_POST['label_background_start_color'] ) ) ? sanitize_text_field( wp_unslash( $_POST['label_background_start_color'] ) ) : '';
			$label_background_end_color   = ( isset( $_POST['label_background_end_color'] ) ) ? sanitize_text_field( wp_unslash( $_POST['label_background_end_color'] ) ) : '';
			$more_results_text            = ( isset( $_POST['more_results_text'] ) ) ? sanitize_text_field( wp_unslash( $_POST['more_results_text'] ) ) : 'More Results';
			$active_item_background_color = ( isset( $_POST['active_item_background_color'] ) ) ? sanitize_text_field( wp_unslash( $_POST['active_item_background_color'] ) ) : '';

			$highlight_colors = ( isset( $_POST['highlight_colors'] ) ) ? sanitize_textarea_field( wp_unslash( $_POST['highlight_colors'] ) ) : '';
			$highlight_colors = explode( "\n", $highlight_colors );
			foreach ( $highlight_colors as $key => $value ) {
				if ( trim( $value ) == '' ) {
					unset( $highlight_colors[ $key ] );
				}
			}

			$search_in_page_selectors = ( isset( $_POST['search_in_page_selectors'] ) ) ? sanitize_text_field( wp_unslash( $_POST['search_in_page_selectors'] ) ) : '';
			$search_in_page_selectors = explode( ',', $search_in_page_selectors );
			foreach ( $search_in_page_selectors as $key => $value ) {
				if ( trim( $value ) == '' ) {
					unset( $search_in_page_selectors[ $key ] );
				}
			}

			update_option( 'search_in_place_box_background_color', $box_background_color );
			update_option( 'search_in_place_box_border_color', $box_border_color );
			update_option( 'search_in_place_label_text_color', $label_text_color );
			update_option( 'search_in_place_label_text_shadow', $label_text_shadow );
			update_option( 'search_in_place_label_background_start_color', $label_background_start_color );
			update_option( 'search_in_place_label_background_end_color', $label_background_end_color );
			update_option( 'search_in_place_more_results_text', $more_results_text );
			update_option( 'search_in_place_active_item_background_color', $active_item_background_color );

			update_option( 'search_in_place_number_of_posts', $search_in_place_number_of_posts );
			update_option( 'search_in_place_own_only', $search_in_place_own_only );
			update_option( 'search_in_place_minimum_char_number', $search_in_place_minimum_char_number );
			update_option( 'search_in_place_summary_char_number', $search_in_place_summary_char_number );
			update_option( 'search_in_place_date_format', $search_in_place_date_format );
			update_option( 'search_in_place_display_thumbnail', $search_in_place_display_thumbnail );
			update_option( 'search_in_place_display_date', $search_in_place_display_date );
			update_option( 'search_in_place_display_summary', $search_in_place_display_summary );
			update_option( 'search_in_place_display_author', $search_in_place_display_author );
			update_option( 'search_in_place_connection_operator', $search_in_place_connection_operator );

			update_option( 'search_in_place_highlight_colors', $highlight_colors );
			update_option( 'search_in_place_selectors', $search_in_page_selectors );

			// Update the autocomplete settings
			$this->autocomplete->updateSettings( $_POST );

		} elseif ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'search-in-place-settings' ) && isset( $_POST['search_in_place_delete_settings'] ) ) {
			if ( function_exists( 'is_multisite' ) && is_multisite() ) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
				foreach ( $blogids as $blog_id ) {
					switch_to_blog( $blog_id );
					$this->clearSettings();
				}
				switch_to_blog( $old_blog );
			} else {
				$this->clearSettings();
			}
		}

		$search_in_place_number_of_posts     = get_option( 'search_in_place_number_of_posts', 10 );
		$search_in_place_own_only            = get_option( 'search_in_place_own_only' );
		$search_in_place_minimum_char_number = get_option( 'search_in_place_minimum_char_number', 3 );
		$search_in_place_summary_char_number = get_option( 'search_in_place_summary_char_number', 20 );
		$search_in_place_date_format         = get_option( 'search_in_place_date_format' );
		$search_in_place_display_thumbnail   = get_option( 'search_in_place_display_thumbnail', 1 );
		$search_in_place_display_date        = get_option( 'search_in_place_display_date', 1 );
		$search_in_place_display_summary     = get_option( 'search_in_place_display_summary', 1 );
		$search_in_place_display_author      = get_option( 'search_in_place_display_author', 1 );
		$search_in_place_connection_operator = get_option( 'search_in_place_connection_operator', 'or' );
		if ( empty( $search_in_place_connection_operator ) ) {
			$search_in_place_connection_operator = 'or';
		}

		$box_background_color         = get_option( 'search_in_place_box_background_color', '#F9F9F9' );
		$box_border_color             = get_option( 'search_in_place_box_border_color', '#DDDDDD' );
		$label_text_color             = get_option( 'search_in_place_label_text_color', '#333333' );
		$label_text_shadow            = get_option( 'search_in_place_label_text_shadow', '#FFFFFF' );
		$label_background_start_color = get_option( 'search_in_place_label_background_start_color', '#F9F9F9' );
		$label_background_end_color   = get_option( 'search_in_place_label_background_end_color', '#ECECEC' );
		$more_results_text            = get_option( 'search_in_place_more_results_text', 'More Results' );
		$active_item_background_color = get_option( 'search_in_place_active_item_background_color', '#FFFFFF' );
		$highlight_colors             = get_option( 'search_in_place_highlight_colors', array( '#F4EFEC', '#B5DCE1', '#F4E0E9', '#D7E0B1', '#F4D9D0', '#D6CDC8', '#F4E3C9', '#CFDAF0' ) );
		$search_in_page_selectors     = get_option( 'search_in_place_selectors', array( 'div.hentry', '#content', '#main', 'div.content', '#middle', '#container', '#wrapper', 'article', '.elementor', 'body' ) );

		echo '
			<div class="wrap">
				<form method="post" action="' . esc_attr( sanitize_text_field( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '' ) ) . '">' . wp_nonce_field( 'search-in-place-settings', '_wpnonce' ) . '
					<h1>Search In Place</h1>
					<p  style="border:1px solid #E6DB55;margin-bottom:10px;padding:5px;background-color: #FFFFE0;">' . __( 'For more information go to the <a href="https://searchinplace.dwbooster.com" target="_blank">Search in Place</a> plugin page.', 'search-in-place' ) . ' <br />' . __( 'For any issues with Search in Place, do not hesitate in <a href="https://wordpress.org/support/plugin/search-in-place#new-post" target="_blank">contact us</a>.', 'search-in-place' ) . '
					<br/><br />' . __( 'If you want test the premium version of Search in Place go to the following links:<br/> <a href="https://demos.dwbooster.com/search-in-place/wp-login.php" target="_blank">Administration area: Click to access the administration area demo</a><br/> <a href="https://demos.dwbooster.com/search-in-place/" target="_blank">Public page: Click to access the Search in Place</a>', 'search-in-place' ) . '</p>
					<p  style="border:1px solid #4caf50;margin-bottom:10px;padding:5px;background-color: #e6f5e6;">' .
					esc_html__( "The plugin converts all search box in the website into search in place components, however, for inserting a search box in the page's content use the shortcode:", 'search-in-place' ) .
					' <b>[search-in-place-form]</b><br><b>' .
					__( "For search in page only, highlighting the search terms, insert the shortcode with the <span style='color:red;' class='search_in_place_blink_me'>in_current_page</span> attribute:", 'search-in-place' ) .
					'</b> <b style="font-size:1.3em;color:red;">[search-in-place-form in_current_page="1"]</b>
					</p>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="own_only">' . esc_html__( 'Apply to the search box inserted as shortcode only', 'search-in-place' ) . '</label>
								</th>
								<td>
									<input aria-label="Apply to the search box inserted as shortcode only" type="checkbox" id="own_only" name="own_only" ' . ( ( $search_in_place_own_only ) ? 'CHECKED' : '' ) . ' />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="number_of_posts">' . esc_html__( 'Enter the number of posts to display', 'search-in-place' ) . '</label>
								</th>
								<td>
									<input aria-label="Enter the number of posts to display" type="text" id="number_of_posts" name="number_of_posts" value="' . esc_attr( $search_in_place_number_of_posts ) . '" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="minimum_char_number">' . esc_html__( 'Enter the minimum of characters number for start the search', 'search-in-place' ) . '</label>
								</th>
								<td>
									<input aria-label="Enter the minimum of characters number for start the search" type="text" id="minimum_char_number" name="minimum_char_number" value="' . esc_attr( $search_in_place_minimum_char_number ) . '" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="operator">' . esc_html__( 'Connection operator', 'search-in-place' ) . '</label>
								</th>
								<td>
									<input aria-label="Connection operator - or" type="radio" name="connection_operator" value="or" ' . ( ( 'or' == $search_in_place_connection_operator ) ? 'CHECKED' : '' ) . ' /> OR&nbsp;&nbsp;&nbsp;&nbsp;
									<input aria-label="Connection operator - and" type="radio" name="connection_operator" value="and" ' . ( ( 'and' == $search_in_place_connection_operator ) ? 'CHECKED' : '' ) . ' /> AND <br />
									' . esc_html__( 'Get results with any or all of words in the search box.', 'search-in-place' ) . '
								</td>
							</tr>
						</tbody>
					</table>

					<h3>' . esc_html__( 'Elements to display', 'search-in-place' ) . '</h3>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<td>
									<input aria-label="Post title" type="checkbox" checked disabled name="title" id="title"> ' . esc_html__( 'Post title', 'search-in-place' ) . ' <input aria-label="Post thumbnail" type="checkbox" name="thumbnail" id="thumbnail" value="1" ' . ( ( 1 == $search_in_place_display_thumbnail ) ? 'checked' : '' ) . ' /> ' . esc_html__( 'Post thumbnail', 'search-in-place' ) . ' <input aria-label="Post author" type="checkbox" name="author" value="1" id="author" ' . ( ( 1 == $search_in_place_display_author ) ? 'checked' : '' ) . ' /> ' . esc_html__( 'Post author', 'search-in-place' ) . ' <input aria-label="Post date" type="checkbox" name="date" id="date" value="1" ' . ( ( 1 == $search_in_place_display_date ) ? 'checked' : '' ) . ' /> ' . esc_html__( 'Post date', 'search-in-place' ) . ' <input aria-label="Post summary" type="checkbox" name="summary" id="summary" value="1" ' . ( ( 1 == $search_in_place_display_summary ) ? 'checked' : '' ) . ' /> ' . esc_html__( 'Post summary', 'search-in-place' ) . '
								</td>
							</tr>
						</tbody>
					</table>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="date_format">' . esc_html__( 'Select the date format', 'search-in-place' ) . '</label>
								</th>
								<td>
									<select aria-label="Select the date format" name="date_format" id="date_format" style="width:135px;">
										<option value="Y-m-d" ' . ( ( 'Y-m-d' == $search_in_place_date_format ) ? 'selected' : '' ) . '>yyyy-mm-dd</option>
										<option value="Y-d-m" ' . ( ( 'Y-d-m' == $search_in_place_date_format ) ? 'selected' : '' ) . '>yyyy-dd-mm</option>
										<option value="m-d-Y" ' . ( ( 'm-d-Y' == $search_in_place_date_format ) ? 'selected' : '' ) . '>mm-dd-yyyy</option>
										<option value="d-m-Y" ' . ( ( 'd-m-Y' == $search_in_place_date_format ) ? 'selected' : '' ) . '>dd-mm-yyyy</option>
									</select>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="summary_char_number">' . esc_html__( "Enter the number of characters for posts' summaries", 'search-in-place' ) . '</label>
								</th>
								<td>
									<input aria-label="Enter the number of characters for posts\' summaries" type="text" id="summary_char_number" name="summary_char_number" value="' . esc_attr( $search_in_place_summary_char_number ) . '" />
								</td>
							</tr>
						</tbody>
					</table>
					<h3>' . esc_html__( 'Search box design', 'search-in-place' ) . '</h3>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="box_background_color">' . esc_html__( 'Background color', 'search-in-place' ) . '</label>
								</th>
								<td>
									<input aria-label="Background color" type="text" name="box_background_color" id="box_background_color" value="' . esc_attr( $box_background_color ) . '" />
									<div id="box_background_color_picker"></div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="box_border_color">' . esc_html__( 'Border color', 'search-in-place' ) . '</label>
								</th>
								<td>
									<input aria-label="Border color" type="text" name="box_border_color" id="box_border_color" value="' . esc_attr( $box_border_color ) . '" />
									<div id="box_border_color_picker"></div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="label_text_color">' . esc_html__( 'Label text color', 'search-in-place' ) . '</label>
								</th>
								<td>
									<input aria-label="Label text color" type="text" name="label_text_color" id="label_text_color" value="' . esc_attr( $label_text_color ) . '" />
									<div id="label_text_color_picker"></div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="label_text_shadow">' . esc_html__( 'Label text shadow', 'search-in-place' ) . '</label>
								</th>
								<td>
									<input aria-label="Label text shadow" type="text" name="label_text_shadow" id="label_text_shadow" value="' . esc_attr( $label_text_shadow ) . '" />
									<div id="label_text_shadow_picker"></div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label>' . esc_html__( 'Label background color', 'search-in-place' ) . '</label>
								</th>
								<td>
									Gradient start color:
									<input aria-label="Gradient start color" type="text" name="label_background_start_color" id="label_background_start_color" value="' . esc_attr( $label_background_start_color ) . '" />
									<div id="label_background_start_color_picker"></div>
									Gradient end color:
									<input aria-label="Gradient end color" type="text" name="label_background_end_color" id="label_background_end_color" value="' . esc_attr( $label_background_end_color ) . '" />
									<div id="label_background_end_color_picker"></div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label>' . esc_html__( 'More results text', 'search-in-place' ) . '</label>
								</th>
								<td>
									<input aria-label="More results text" type="text" name="more_results_text" id="more_results_text" value="' . esc_attr( $more_results_text ) . '" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="active_item_background_color">' . esc_html__( 'Background color of active item', 'search-in-place' ) . '</label>
								</th>
								<td>
									<input aria-label="Background color of active item" type="text" name="active_item_background_color" id="active_item_background_color" value="' . esc_attr( $active_item_background_color ) . '" />
									<div id="active_item_background_color_picker"></div>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="padding:0;"><p style="border:1px solid #FFCC66;background-color:#FFFFCC;padding:10px;margin:0;">' . esc_html__( 'The next options are available only for the advanced version of Search in Place', 'search-in-place' ) . '. <a href="https://searchinplace.dwbooster.com" target="_blank">' . esc_html__( 'CLICK HERE for more information', 'search-in-place' ) . '</a></p>
								</td>
							</tr>
							<tr>
								<th>
									<label for="box_background_color" style="color:#AAA;">' . esc_html__( 'Exclude posts/pages (Ids separated by comma)', 'search-in-place' ) . '</label>
								</th>
								<td>
									<input aria-label="Exclude posts/pages (Ids separated by comma)" type="text" style="width:100%;" disabled readonly />
								</td>
							</tr>

						</tbody>
					</table>

					<h3  style="color:#AAA;">' . esc_html__( 'Search in', 'search-in-place' ) . '</h3>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th  style="color:#AAA;">
									' . esc_html__( 'Posts/Pages common data (title, content):', 'search-in-place' ) . '
								</th>
								<td>
									<input aria-label="Posts/Pages common data (title, content)" type="checkbox" name="post_data" id="post_data" checked disabled readonly />
								</td>
							</tr>
							<tr valign="top">
								<th  style="color:#AAA;">
									' . esc_html__( 'Posts/Pages metadata (additional data of articles):', 'search-in-place' ) . '
								</th>
								<td>
									<input aria-label="Posts/Pages metadata (additional data of articles)" type="checkbox" name="post_metadata" id="post_metadata" onclick="forbiddenOption(this);" readonly disabled />
								</td>
							</tr>
							<tr valign="top">
								<th  style="color:#AAA;">
									' . esc_html__( 'Posts/Pages author (display name):', 'search-in-place' ) . '
								</th>
								<td>
									<input aria-label="Posts/Pages author (display name)" type="checkbox" name="post_author" id="post_author" onclick="forbiddenOption(this);" readonly disabled />
								</td>
							</tr>
							<tr>
								<th colspan="2"  style="color:#AAA;">
								' . esc_html__( 'If you are using in your website some of plugins listed below, press the related button for searching in its custom post-types and taxonomies.', 'search-in-place' ) . '
								</th>
							</tr>
							<tr>
								<th colspan="2">
								<input type="button" class="button-secondary" value="WooCommerce" onclick="window.alert(\'This feature is available only for the advanced version of Search in Place\');" disabled />
								<input type="button" class="button-secondary" value="WP e-Commerce" onclick="window.alert(\'This feature is available only for the advanced version of Search in Place\');" disabled />
								<input type="button" class="button-secondary" value="Jigoshop" onclick="window.alert(\'This feature is available only for the advanced version of Search in Place\');" disabled />
								<input type="button" class="button-secondary" value="Ready! Ecommerce Shopping Cart" onclick="window.alert(\'This feature is available only for the advanced version of Search in Place\');" disabled />
								</th>
							</tr>
							<tr valign="top">
								<th  style="color:#AAA;">
									' . esc_html__( 'Posts Type:', 'search-in-place' ) . '
								</th>
								<td  style="color:#AAA;">

									<input aria-label="Post type" type="text" value="post" disabled style="color:#999999;" class="post-type" readonly />  enabled by default <br />
									<input aria-label="Post type" type="text" value="page" disabled style="color:#999999;" class="post-type" readonly />  <br />
							        <input type="button" value="Add new type" class="button-primary" onclick="window.alert(\'This feature is available only in the commercial version of plugin\');" disabled />
								</td>
							</tr>

							<tr>
								<th  style="color:#AAA;">
									' . esc_html__( 'Taxonomy:', 'search-in-place' ) . '
								</th>
								<td>
									<input type="button" id="add_taxonomy" value="Add new taxonomy" class="button-primary" onclick="window.alert(\'The searching in taxonomies is possible only in the commercial version of plugin\');" disabled />
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<p  style="border:1px solid #23282d;margin-bottom:10px;padding:5px;background:rgb(236, 236, 236);">' .
										__( 'In the <b>Professional version of the plugin</b> it is possible to insert a search box to search for specific post types, regardless of the post types entered above, by adding the post_types attribute in the shortcode, and separating the post types by comma:', 'search-in-place' ) .
										'</b> <b style="font-size:1.3em;">[search-in-place-form post_types="product,download"]</b>
									</p>
								</td>
							</tr>
						</tbody>
					</table>
					<h3  style="color:#AAA;">' . esc_html__( 'In Search Page', 'search-in-place' ) . '</h3>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="highlight"  style="color:#AAA;">' . esc_html__( 'Highlight the terms in result', 'search-in-place' ) . '</label>
								</th>
								<td>
									<input aria-label="Highlight the terms in result" type="checkbox" name="highlight" id="highlight" onclick="forbiddenOption(this);" disabled readonly />
								</td>
							</tr>
							<tr><td colspan="2" style="font-style:italic;color:#AAA;" >
							' . esc_html__( 'Highlights the search terms on search page.', 'search-in-place' ) . '
							</td></tr>
							<tr valign="top">
								<th scope="row">
									<label for="mark_post_type"  style="color:#AAA;">' . esc_html__( 'Identify the posts type in search result', 'search-in-place' ) . '</label>
								</th>
								<td>
									<input aria-label="Identify the posts type in search result" type="checkbox" name="mark_post_type" id="mark_post_type" onclick="forbiddenOption(this);" disabled readonly />
								</td>
								<tr><td colspan="2" style="font-style:italic;color:#AAA;" >
								' . esc_html__( 'Indicates the type of document (article or page)', 'search-in-place' ) . '
								</td></tr>
							</tr>
						</tbody>
					</table>
					<h3  style="color:#AAA;">' . esc_html__( 'In Resulting Pages', 'search-in-place' ) . '</h3>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th scope="row">
									<label for="highlight"  style="color:#AAA;">' . esc_html__( 'Highlight the terms in resulting pages', 'search-in-place' ) . '</label>
								</th>
								<td>
									<input aria-label="Highlight the terms in resulting pages" type="checkbox" name="highlight_resulting_page" id="highlight_resulting_page" onclick="forbiddenOption(this);" readonly disabled />
								</td>
							</tr>
							<tr><td colspan="2" style="font-style:italic;color:#AAA;" >
							' . esc_html__( 'Highlights the search terms on resulting page.', 'search-in-place' ) . '
							</td></tr>
							<tr valign="top">
								<th scope="row">
									<label for="highlight">' . esc_html__( 'Terms colors in resulting pages and search in page', 'search-in-place' ) . '</label>
								</th>
								<td>
									<textarea aria-label="Terms colors in resulting pages and search in page" name="highlight_colors" rows="5" cols="40">' . esc_textarea( implode( "\n", $highlight_colors ) ) . '</textarea>
									<div><i>' . esc_html__( 'Enter a color code per line. Ex. #FF0000', 'search-in-place' ) . '</i></div>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row">
									<label for="highlight">' . esc_html__( 'Search in page selectors', 'search-in-place' ) . '</label>
								</th>
								<td>
									<input aria-label="Search in page selectors" type="text" name="search_in_page_selectors" style="width:100%;" value="' . esc_attr( implode( ',', $search_in_page_selectors ) ) . '">
									<div><i>' . __( 'Enter the selector where to search and highlight the search terms separated by comma.<br>Ex. <b>div.hentry,#content,#main,div.content,#middle,#container,#wrapper,article,.elementor,body</b>', 'search-in-place' ) . '</i></div>
								</td>
							</tr>
						</tbody>
					</table>';

			// Display the autocomplete settings
			$this->autocomplete->getSettings();
			echo '
					<div style="width:100%; border: 1px solid #FF0000; padding: 20px; margin-top:20px; box-sizing: border-box;">
						<h3 style="color:#FF0000;">' . esc_html__( 'Recommended before uninstalling', 'search-in-place' ) . '</h3>
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="search_in_place_delete_settings" style="color:#FF0000;">' . esc_html__( 'Delete all settings?', 'search-in-place' ) . '</label>
									</th>
									<td>
										<input aria-label="Delete all settings" type="checkbox" name="search_in_place_delete_settings" id="search_in_place_delete_settings" />
										<span>' . esc_html__( 'Deletes the settings from the database. If you are in a multisite WordPress installation, deletes the settings from all blogs.', 'search-in-place' ) . '</span>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					';

			echo '<p style="border:1px solid #FFCC66;background-color:#FFFFCC;padding:10px;">' . esc_html__( 'If you require some of features listed above, don\'t doubt to upgrade to the advanced version of Search in Place', 'search-in-place' ) . '. <a href="https://searchinplace.dwbooster.com" target="_blank">' . esc_html__( 'CLICK HERE for more information', 'search-in-place' ) . '</a></p>
					<input type="hidden" name="search_in_place_submit" value="ok" />
					<div class="submit"><input type="submit" class="button-primary" value="' . esc_attr__( 'Update Settings', 'search-in-place' ) . '" /></div>
				</form>
			</div>
			<script>
				function forbiddenOption(e){
					e.checked = false;
					window.alert("' . esc_html__( 'The option selected is available only in the advanced version, please go to the product\'s webpage  through the previous link', 'search-in-place' ) . '");
				}
			</script>
		';
		?>
		<script>
		// Set the picker colors
		jQuery(function(){
			jQuery('#box_background_color_picker').hide();
			jQuery('#box_background_color_picker').farbtastic("#box_background_color");
			jQuery("#box_background_color").click(function(){jQuery('#box_background_color_picker').slideToggle()});

			jQuery('#box_border_color_picker').hide();
			jQuery('#box_border_color_picker').farbtastic("#box_border_color");
			jQuery("#box_border_color").click(function(){jQuery('#box_border_color_picker').slideToggle()});

			jQuery('#label_text_color_picker').hide();
			jQuery('#label_text_color_picker').farbtastic("#label_text_color");
			jQuery("#label_text_color").click(function(){jQuery('#label_text_color_picker').slideToggle()});

			jQuery('#label_text_shadow_picker').hide();
			jQuery('#label_text_shadow_picker').farbtastic("#label_text_shadow");
			jQuery("#label_text_shadow").click(function(){jQuery('#label_text_shadow_picker').slideToggle()});

			jQuery('#label_background_start_color_picker').hide();
			jQuery('#label_background_start_color_picker').farbtastic("#label_background_start_color");
			jQuery("#label_background_start_color").click(function(){jQuery('#label_background_start_color_picker').slideToggle()});

			jQuery('#label_background_end_color_picker').hide();
			jQuery('#label_background_end_color_picker').farbtastic("#label_background_end_color");
			jQuery("#label_background_end_color").click(function(){jQuery('#label_background_end_color_picker').slideToggle()});

			jQuery('#active_item_background_color_picker').hide();
			jQuery('#active_item_background_color_picker').farbtastic("#active_item_background_color");
			jQuery("#active_item_background_color").click(function(){jQuery('#active_item_background_color_picker').slideToggle()});
		});
		</script>
		<?php
	} // End printAdminPage

	public function setStyles() {
		$box_background_color         = get_option( 'search_in_place_box_background_color', '#F9F9F9' );
		$box_border_color             = get_option( 'search_in_place_box_border_color', '#DDDDDD' );
		$label_text_color             = get_option( 'search_in_place_label_text_color', '#333333' );
		$label_text_shadow            = get_option( 'search_in_place_label_text_shadow', '#FFFFFF' );
		$label_background_start_color = get_option( 'search_in_place_label_background_start_color', '#F9F9F9' );
		$label_background_end_color   = get_option( 'search_in_place_label_background_end_color', '#ECECEC' );
		$active_item_background_color = get_option( 'search_in_place_active_item_background_color', '#FFFFFF' );

		echo "<style>\n";
		if ( ! empty( $box_background_color ) ) {
			echo ".search-in-place {background-color: $box_background_color;}\n";
		}
		if ( ! empty( $box_border_color ) ) {
			echo ".search-in-place {border: 1px solid $box_border_color;}\n";
			echo ".search-in-place .item{border-bottom: 1px solid $box_border_color;}";
		}
		if ( ! empty( $label_text_color ) ) {
			echo ".search-in-place .label{color:$label_text_color;}\n";
		}
		if ( ! empty( $label_text_shadow ) ) {
			echo ".search-in-place .label{text-shadow: 0 1px 0 $label_text_shadow;}\n";
		}
		if ( ! empty( $label_background_start_color ) && ! empty( $label_background_end_color ) ) {
			echo ".search-in-place .label{
				background: $label_background_end_color;
				background: -moz-linear-gradient(top,  $label_background_start_color 0%, $label_background_end_color 100%);
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,$label_background_start_color), color-stop(100%,$label_background_end_color));
				background: -webkit-linear-gradient(top,  $label_background_start_color 0%,$label_background_end_color 100%);
				background: -o-linear-gradient(top,  $label_background_start_color 0%,$label_background_end_color 100%);
				background: -ms-linear-gradient(top,  $label_background_start_color 0%,$label_background_end_color 100%);
				background: linear-gradient(to bottom,  $label_background_start_color 0%,$label_background_end_color 100%);
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='$label_background_start_color', endColorstr='$label_background_end_color',GradientType=0 );
			}\n";
		}
		if ( ! empty( $active_item_background_color ) ) {
			echo ".search-in-place .item.active{background-color:$active_item_background_color;}\n";
		}

		echo '</style>';

	}

} // End SearchInPlace
