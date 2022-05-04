<?php 
/** Mel: 24/11/21
 * This function modifies the main WordPress query to include an array of 
 * post types instead of the default 'post' post type.
 *
 * @param object $query The main WordPress query.
 */
function tg_include_custom_post_types_in_search_results( $query ) {
    if ( $query->is_main_query() && $query->is_search() ) {
        $query->set( 'post_type', array( 'post', 'acadp_listings' ) );
    }
}
add_action( 'pre_get_posts', 'tg_include_custom_post_types_in_search_results' );

add_action( 'wp_enqueue_scripts', 'mywiki_theme_setup' );
function mywiki_theme_setup(){
  
 wp_enqueue_style( 'google-fonts-lato', '//fonts.googleapis.com/css?family=Lato', array(), false,null );
 wp_enqueue_style( 'google-fonts-cabin', '//fonts.googleapis.com/css?family=Cabin', array(), false,null );

  wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.css', array(), false,null );

  wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/css/font-awesome.css', array(), false, 'all' );
  wp_enqueue_style( 'mywiki-style', get_stylesheet_uri());
  wp_enqueue_script( 'bootstrap',  get_template_directory_uri() . '/js/bootstrap.js', array('jquery'), '3.0.1'); 
  wp_enqueue_script( 'mywiki-general',  get_template_directory_uri() . '/js/general.js');
  wp_localize_script( 'mywiki-general', 'my_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
  if ( is_singular() ): wp_enqueue_script( 'comment-reply' ); endif;
}

/* mywiki theme starts */
if ( ! function_exists( 'mywiki_setup' ) ) :
  function mywiki_setup() {
    	/* content width */
    	global $content_width;
    	if ( ! isset( $content_width ) ) {
    		$content_width = 900;
    	}
    	/*
    	 * Make mywiki theme available for translation.
    	 *
    	 */
    	load_theme_textdomain( 'mywiki', get_template_directory() . '/languages' );

      register_nav_menus(
        array(
          'primary' => __( 'The Main Menu', 'mywiki' ),  // main nav in header
          'footer-links' => __( 'Footer Links', 'mywiki' ) // secondary nav in footer
        )
      );
    	// This theme styles the visual editor to resemble the theme style.
    	add_editor_style( 'css/editor-style.css' );
    	// Add RSS feed links to <head> for posts and comments.
    	add_theme_support( 'automatic-feed-links' );
      add_theme_support( 'title-tag' );
      add_theme_support( 'custom-logo', array(
                'height'      => 160,
                'width'       => 45,
                'flex-height' => true,
                'flex-width'  => true,
                'priority'    => 11,
                'header-text' => array( 'site-title', 'site-description' ), 
            ) );
    	/*
    	 * Enable support for Post Formats.
    	 */
    	// This theme allows users to set a custom background.
    	add_theme_support( 'custom-background', apply_filters( 'mywiki_custom_background_args', array(
    		'default-color' => '048eb0',
    	) ) );
    	// Add support for featured content.
    	add_theme_support( 'featured-content', array(
    		'featured_content_filter' => 'mywiki_get_featured_posts',
    		'max_posts' => 6,
    	) );
    	// This theme uses its own gallery styles.
    	add_filter( 'use_default_gallery_style', '__return_false' );


      add_theme_support( 'post-thumbnails' );
      set_post_thumbnail_size( 150, 150 ); // default Post Thumbnail dimensions
     
      
      add_image_size( 'category-thumb', 300, 9999 ); //300 pixels wide (and unlimited height)
      add_image_size( 'homepage-thumb', 220, 180, true ); //(cropped)
      
      add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form' ) );
  }
endif; // mywiki_setup
add_action( 'after_setup_theme', 'mywiki_setup' );

add_filter('get_custom_logo','mywiki_change_logo_class');
function mywiki_change_logo_class($html)
{
  //$html = str_replace('class="custom-logo"', 'class="img-responsive logo-fixed"', $html);
  $html = str_replace('width=', 'original-width=', $html);
  $html = str_replace('height=', 'original-height=', $html);
  $html = str_replace('class="custom-logo-link"', 'class="navbar-brand logo"', $html);
  return $html;
}

if ( ! function_exists( 'mywiki_entry_meta' ) ) :
/**
 * Set up post entry meta.
 *
 * Meta information for current post: categories, tags, permalink, author, and date.
 **/
function mywiki_entry_meta() {
	$mywiki_category_list = get_the_category_list(', '); 
  $mywiki_tags_list = get_the_tags(', ');  ?>
  <i class="fa fa-calendar-check-o"></i>&nbsp;&nbsp;
  <a href="<?php echo esc_url(get_permalink()); ?>" title="<?php echo esc_attr(get_the_time()); ?>" ><time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time></a>
  &nbsp;  
  <?php if ( $mywiki_category_list ) { ?>
   <i class="fa fa-folder-open"></i>
  <?php echo wp_kses_post(get_the_category_list(', '));    }  
 }
endif;
/**
 * Add default menu style if menu is not set from the backend.
 */
function mywiki_add_menuclass ($page_markup) {
  preg_match('/^<div class=\"([a-z0-9-_]+)\">/i', $page_markup, $mywiki_matches);
  $mywiki_toreplace = array('<div class="navbar-collapse collapse top-gutter">', '</div>');
  $mywiki_replace = array('<div class="navbar-collapse collapse top-gutter">', '</div>');
  $mywiki_new_markup = str_replace($mywiki_toreplace,$mywiki_replace, $page_markup);
  $mywiki_new_markup= preg_replace('/<ul/', '<ul class="nav navbar-nav navbar-right mywiki-header-menu"', $mywiki_new_markup);
  return $mywiki_new_markup; 
} //}
add_filter('wp_page_menu', 'mywiki_add_menuclass');

/**
 * Wiki search
 */
function mywiki_search() {
	global $wpdb;
	$mywiki_title=(isset($_POST['queryString']))?trim(sanitize_text_field(wp_unslash($_POST['queryString']))):'';
  if(strpos($mywiki_title,"#")>-1):
    $tags = strtolower(str_replace(array(' ','#'),array( '-',''),$mywiki_title));
    
	//Mel: 24/11/21
	$mywiki_args = array('posts_per_page' => -1, 'order'=> 'ASC', "orderby"=> "title", "post_type" => "acadp_listings",'post_status'=>'publish',"tag" => $tags);
	//$mywiki_args = array('posts_per_page' => -1, 'order'=> 'ASC', "orderby"=> "title", "post_type" => "post",'post_status'=>'publish',"tag" => $tags);
  else:
    
	//Mel: 24/11/21
	$mywiki_args = array('posts_per_page' => -1, 'order'=> 'ASC', "orderby"=> "title", "post_type" => "acadp_listings",'post_status'=>'publish', "s" => $mywiki_title);
	//$mywiki_args = array('posts_per_page' => -1, 'order'=> 'ASC', "orderby"=> "title", "post_type" => "post",'post_status'=>'publish', "s" => $mywiki_title);
  endif;	
	
	//Mel: 11/04/22. To search only the full name of bios
	$mywiki_posts = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_status='publish' AND post_type='acadp_listings' AND post_title LIKE '%s'", '%'. $wpdb->esc_like( $mywiki_title ) .'%') );
	//$mywiki_posts = get_posts( $mywiki_args );
	
	$mywiki_output='';
	if($mywiki_posts):
		 $mywiki_h=0; ?>
		 <ul id="search-result">
  		 <?php foreach ( $mywiki_posts as $mywiki_post ) { //setup_postdata( $mywiki_post ); //Mel: 11/04/22?>
  			 <li class="que-icn">
            <a href="<?php echo esc_url(get_the_permalink($mywiki_post->ID))?>"> <i class="fa fa-angle-right"></i><?php echo esc_html($mywiki_posts[$mywiki_h]->post_title);?> </a>
          </li>  			 
  		 <?php $mywiki_h++; } ?>
  	 </ul>
	<?php  //wp_reset_postdata(); //Mel: 11/04/22	
  else: ?>
      <ul id="search-result">
		<li class="que-icn">
			<?php esc_html_e('No','mywiki'); ?>
		</li>
	  </ul>
	<?php endif;
	die();
}
add_action('wp_ajax_mywiki_search', 'mywiki_search');
add_action('wp_ajax_nopriv_mywiki_search', 'mywiki_search' );

if ( ! function_exists( 'mywiki_comment' ) ) :
  /**
   * Template for comments and pingbacks.
   *
   * To override this walker in a child theme without modifying the comments template
   * simply create your own mywiki_comment(), and that function will be used instead.
   *
   * Used as a callback by wp_list_comments() for displaying the comments.
   *
   * @since Twenty Twelve 1.0
   */
  function mywiki_comment( $comment, $args, $depth ) {
  	//$GLOBALS['comment'] = $comment;
  		// Proceed with normal comments.
 		global $post; ?>
  		<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?> >
      	<article class="div-comment-<?php comment_ID(); ?>" id="div-comment-1">
  				<footer class="comment-meta">
  					<div class="comment-author vcard">
  						<?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
  					<b class="fn">	<?php printf( /* translators: 1 is author link */ esc_html__( '%s says:','mywiki' ), get_comment_author_link()  ); ?></b>
  					</div><!-- .comment-author -->
  					<div class="comment-metadata">
  						<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
  							<time datetime="<?php comment_time( 'c' ); ?>">
  								<?php printf( /* translators: 1 is post date , 2 is post time */ esc_html__( '%1$s at %2$s', 'mywiki' ), get_comment_date(), get_comment_time() ); ?>
  							</time>
  						</a>
  						<?php edit_comment_link( __( 'Edit','mywiki' ), '<span class="edit-link">', '</span>' ); ?>
            </div><!-- .comment-metadata -->
  				</footer><!-- .comment-meta -->
  				<div class="comment-content">
  					<?php comment_text(); ?>
  				</div><!-- .comment-content -->
  				<div class="reply">
  					<?php comment_reply_link( array_merge( $args, array( 'add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
                 </div><!-- .reply -->
  			</article>
  	<?php
  }
endif;
add_action('wp_ajax_mywiki_header', 'mywiki_header_image_function');
add_action('wp_ajax_nopriv_mywiki_header', 'mywiki_header_image_function' );
function mywiki_header_image_function(){
	$mywiki_return['header'] = get_header_image();
	echo json_encode($mywiki_return);
	die;
}


function mywiki_search_form($html) {   
    $html= '<form class="asholder search-main col-md-12 col-sm-12 col-xs-12" role="search" method="get" id="searchformtop" action="'.esc_url(home_url()).'">        
          <div class="input-group" id="suggest">
            <input name="s" id="s" type="text" onKeyUp="suggest(this.value);" onBlur="fill();" class="search-query form-control pull-right" autocomplete="off" placeholder="'.esc_attr__('Have a Question? Write here and press enter','mywiki').'" data-provide="typeahead" data-items="4" data-source="">
            <div class="suggestionsbox" id="suggestions" style="display: none;"> <img src="'.esc_url(get_template_directory_uri().'/img/arrow1.png').'" height="18" width="27" class="upArrow" alt="upArrow" />
              <div class="suggestionlist" id="suggestionslist"></div>
            </div>        
        </div>
      </form>';
   
 return $html;
}


add_action( 'admin_menu', 'mywiki_admin_menu');
function mywiki_admin_menu( ) {
    add_theme_page( __('Pro Feature','mywiki'), __('MyWiki Pro','mywiki'), 'manage_options', 'mywiki-pro-buynow', 'mywiki_buy_now', 300 );   
}
function mywiki_buy_now(){ ?>
<div class="mywiki_pro_version">
  <a href="<?php echo esc_url('https://fasterthemes.com/wordpress-themes/mywikipro/'); ?>" target="_blank">
    
    <img src ="<?php echo esc_url(get_template_directory_uri()); ?>/img/mywiki_pro_features.png" width="100%" height="auto" />

  </a>
</div>
<?php
}

//Mel: 25/03/22. To allow JSON files to be uploaded
add_filter( 'upload_mimes', 'my_custom_mime_types' );
function my_custom_mime_types( $mimes ) {
 
	// New allowed mime types.
	$mimes['json'] = 'application/json';
	 
	// Optional. Remove a mime type.
	//unset( $mimes['exe'] );
	 
	return $mimes;
}

//Mel: 26/03/22. To ensure each listing will never expire
add_action( 'acadp_listing_form_after_save', 'listing_never_expires' );
function listing_never_expires( $post_id ) {
    if ( $post_id  > 0 ) {
        update_post_meta( $post_id, 'never_expires', 1 );
    }
}

//Mel: 05/02/22. This will replace the 'wp-json' REST API prefix with 'api'. Go to Settings -> Permalinks in admin console and save to flush your rewrite rules for this change to work.
add_filter( 'rest_url_prefix', function () {
	return 'api';
} );

//Mel: 05/02/22. Custom API endpoint to interact with NFT. 
add_action( 'rest_api_init', function() {
	
	//The URL is like https://liframpro.test/api/v1/json/delete/?json_file=marie-e-cardamone-8.json&api_key=E7153EBFAA45C7EFE1706472039B3D21
	register_rest_route( 'v1', '/json/delete/', [
		'methods'   => WP_REST_Server::READABLE,
		'callback' => 'rest_route_delete_json',
		'permission_callback' => '__return_true',
		'args'     => [
			'jsonfile' => [
				'required' => true,
				'type'     => 'string',
			],
			'apikey' => [
				'required' => true,
				'type'     => 'string',
			],
		],
	] );
	
	//The URL is like	https://liframpro.test/api/v1/json/create/?title=johndoe&content=helloworld&api_key=E7153EBFAA45C7EFE1706472039B3D21
	//To create the json file of the deceased
	register_rest_route( 'v1', '/json/create/', [
		'methods'   => WP_REST_Server::CREATABLE,	//To set method to be POST
		'callback' => 'rest_route_create_json',
		'permission_callback' => '__return_true',
		'args'     => [
			'fullname' => [
				'required' => true,
				'type'     => 'string',
			],
			'bio' => [
				'required' => true,
				'type'     => 'string',
			],
			'dateofbirth' => [
				'required' => false,
				'type'     => 'string',
			],
			'dateofdeath' => [
				'required' => true,
				'type'     => 'string',
			],
			'spouse' => [
				'required' => false,
				'type'     => 'string',
			],
			'siblings' => [
				'required' => false,
				'type'     => 'string',
			],
			'children' => [
				'required' => false,
				'type'     => 'string',
			],
			'restingplace' => [
				'required' => false,
				'type'     => 'string',
			],
			'occupation' => [
				'required' => false,
				'type'     => 'string',
			],
			'city' => [
				'required' => false,
				'type'     => 'string',
			],
			'state' => [
				'required' => false,
				'type'     => 'string',
			],
			'apikey' => [
				'required' => true,
				'type'     => 'string',
			],
		],
	] );
	
} );

//Mel: 26/03/22. Delete the unused JSON file of bio.
function rest_route_delete_json( $request ) {

	//We use the same API key for now
	define("API_KEY", "E7153EBFAA45C7EFE1706472039B3D21");

	$json_file = $request->get_param( 'jsonfile' );

	$api_key = $request->get_param( 'apikey' );

	$upload_dir = wp_upload_dir();
	$upload_path = $upload_dir['path'];
	
	$file = $upload_path . '/' . $json_file;
	
	if ( $api_key == API_KEY ) { 
	
		if ( file_exists( $file ) ) {
			
			wp_delete_file( $file );

			$return = esc_html__( 'JSON file is successfully deleted from server', 'advanced-classifieds-and-directory-pro' ); 
			
			//Response with JSON format
			wp_send_json($return, 200);

		} else {
			$return = esc_html__( 'JSON file not found', 'advanced-classifieds-and-directory-pro' );
			wp_send_json($return, 400);
		}

	} else {
		$return = esc_html__( 'Wrong API key', 'advanced-classifieds-and-directory-pro' );
		wp_send_json($return, 400);
	}
}

//Mel: 06/04/22
function rest_route_create_json( $request ) {
	
	//We use the same API key for now
	define("API_KEY", "E7153EBFAA45C7EFE1706472039B3D21");

	//To store the metadata of the deceased profile.
	$metadata = [];
	
	$api_key = $request->get_param( 'apikey' );
	
	//Get the full name and biography from the post title and the content/body
	$metadata['fullName'] = clean_fullname( $request->get_param( 'fullname' ) );
	$metadata['bio'] = $request->get_param( 'bio' ); 
	$metadata['dateOfBirth'] = $request->get_param( 'dateofbirth' ); 
	$metadata['dateOfDeath'] = $request->get_param( 'dateofdeath' ); 
	$metadata['spouse'] = $request->get_param( 'spouse' ); 
	$metadata['siblings'] = $request->get_param( 'siblings' ); 
	$metadata['children'] = $request->get_param( 'children' ); 
	$metadata['restingPlace'] = $request->get_param( 'restingplace' ); 
	$metadata['occupation'] = $request->get_param( 'occupation' ); 
	$metadata['city'] = $request->get_param( 'city' ); 
	$metadata['state'] = $request->get_param( 'state' ); 
	$metadata['country'] = $request->get_param( 'country' );
	$metadata['imageFilename'] = $request->get_param( 'imagefilename' ); 	

	if ( $api_key == API_KEY ) {

		//Convert the metadata array to JSON string.
		$json = json_encode($metadata, JSON_PRETTY_PRINT);

		//Save the metadata as JSON file where the full name is the filename such as john-doe.json
		$json_file = write_to_json_file( $metadata['fullName'], $json );
		
		if ( empty( $json_file['error'] ) ) {
			
			//Response with path to JSON file
			wp_send_json($json_file, 200);
			
		} else {
			$return = esc_html__( 'Error creating JSON file. Check log file', 'advanced-classifieds-and-directory-pro' );
			wp_send_json($return, 400);
		}

	} else {
		$return = esc_html__( 'Wrong API key', 'advanced-classifieds-and-directory-pro' );
		wp_send_json($return, 400);
	}
	
}

//Mel: 06/04/22
//To clean up the formatting of the full name, to ensure it looks like Rick J. Reed
function clean_fullname( $title ) {
	
	if ( !empty( $title ) ) {
		
		$order = array("\r\n", "\n", "\r");	
	
		$title = str_replace( $order, '', trim($title) );	//Remove line breaks
		
		$title = preg_replace('/\s+/', ' ', $title);	//Remove double or more white spaces
		
		$title = esc_html( $title );	//Escape html elements
		
		return $title;
	
	} else {
		
		$error_message = esc_html__( 'Ensure there is a full name in the title field. Enter the full name and click save before uploading to IPFS.', 'advanced-classifieds-and-directory-pro' );
		add_settings_error('title_empty', '', $error_message, 'error');
		settings_errors( 'title_empty' );
	
	}			
	
}

//Mel: 06/04/22
/**
 * Write the json file to the upload directory.
 * Unique file will be created if it does not exist.
 * 
 * @param string $json The JSON formatted string to write into the file.
 * @param string $filename The filename to be saved such as john-doe.json where $filename is "john doe"
 * 
 * @return void
 */
function write_to_json_file( $filename, $json ) {
	
	$upload_dir = wp_upload_dir();
	
	$order = array(".");
	
	$filename = str_replace( $order, '', strtolower($filename) );	//Remove dots (periods)
	
	$filename = sanitize_file_name( $filename . ".json" );
	
	$uploaded_file = wp_upload_bits( $filename, null, $json );
	
	return $uploaded_file;
}

//Mel: 12/04/22. To fix the issue where <p class=sr-only> does not have empty space between paragraphs
function wpb_hook_javascript() {
    ?>
		<script>
		(function($) {
			$(document).ready(function() {
				$( "<br><br>" ).insertAfter( ".sr-only" );
			})
		})(jQuery);
          
        </script>
    <?php
}
add_action('wp_head', 'wpb_hook_javascript');

/*Customizer*/
require get_template_directory() . '/function/customizer.php';
/*theme-default-setup*/
require get_template_directory() . '/function/theme-default-setup.php';
// Implement Custom Header features.
require get_template_directory() . '/function/custom-header.php';