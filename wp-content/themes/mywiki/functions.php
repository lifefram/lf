<?php 

use GuzzleHttp\Psr7;	//Needed by upload_to_ipfs function below

/** Mel: 24/11/21
 * This function modifies the main WordPress query to include an array of 
 * post types instead of the default 'post' post type.
 *
 * @param object $query The main WordPress query.
 */
function tg_include_custom_post_types_in_search_results( $query ) {
    if ( $query->is_main_query() && $query->is_search() && !is_admin() ) {
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

//Mel: 06/10/22. Commented to fix the bug with WP Social Login plugin redirection issue where they use wp-json slug instead
//Mel: 05/02/22. This will replace the 'wp-json' REST API prefix with 'api'. Go to Settings -> Permalinks in admin console and save to flush your rewrite rules for this change to work.
// add_filter( 'rest_url_prefix', function () {
	// return 'api';
// } );

//Mel: 05/02/22. Custom API endpoint to interact with NFT. 
add_action( 'rest_api_init', function() {
	
	//The URL is like https://example.com/api/v1/json/delete/?json_file=marie-e-cardamone-8.json&api_key=E7153EBFAA45C7EFE1706472039B3D21
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
	
	//The URL is like	https://example.com/api/v1/json/create/?title=johndoe&content=helloworld&api_key=E7153EBFAA45C7EFE1706472039B3D21
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


//add_action( 'publish_acadp_listing', 'post_published_notification', 10, 2 );

//Mel: 23/09/22
//To create a transient name for the use in upload_to_ipfs function
function get_ipfs_upload_transient_name() {
    return 'ipfs_upload_transient_name';
}

/** Mel: 22/09/22
 * This function uploads the biography's JSON file and profile photo to IPFS using nft.storage
 * It should be triggered by a cronjob and will find bios without content on IPFS and automatically upload it. IPFS should return a CID such as https://ipfs.io/ipfs/bafybeicgigday5ihxof2xpxvd35jqtwvxovrwc4p54qmqck3nl76a2lhnu which contains both the JSON file and image. 
 *
 */
function upload_to_ipfs() {

    // get the existing transient
    //
    // If the transient does not exist, does not have a value, or has expired, 
    // then the return value will be false.
    $process_running = get_site_transient( get_ipfs_upload_transient_name() );

    if ( $process_running ) {
        // bail out in case the transient exists and has not expired
        // this means the process is still running
        return; 
    }

    // set the transient to flag the process as started
    // 60 is the time until expiration, in seconds
    set_site_transient( get_ipfs_upload_transient_name(), 1, 120);

    // Run the upload process
    require_once( WP_PLUGIN_DIR . '/advanced-classifieds-and-directory-pro-premium/vendor/autoload.php' ); 

    define( 'UPLOAD_LIMIT', 5 );	//The number of biographies to be uploaded on each run
    define( 'IPFS_URL', 'https://api.nft.storage');
    define ( 'URL_PATH', 'upload' );
    define( 'API_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJkaWQ6ZXRocjoweGM5RTM5RDM4RDA0NjI0MTIzMTA2MzgyMjUzMjE2M0EwODM1ZjA5MUIiLCJpc3MiOiJuZnQtc3RvcmFnZSIsImlhdCI6MTY2Mzg5Mjg2MTk5OSwibmFtZSI6IkxpZmVmcmFtIn0.DEGrImdPqRuGX0IYlHZPzccutiGm644Tp5ocPZOslWg' );
    
    $args = array(
        'posts_per_page' => UPLOAD_LIMIT,	
        'post_type'	=> 'acadp_listings',
        'orderby' => 'date',
        'order' => 'desc',	//Uploading the latest bio first ensures that old bios that don't have any image or json file will not pile up and preventing new ones from being uploaded.
        'post_status'=>'publish',
        'meta_query' => array(
            'relation' => 'AND',
                array(
					'key' => 'ipfs_cid',
					'compare' => 'NOT EXISTS'
                ),
                array(
					'key' => '_json_file',
					'compare' => 'EXISTS'
                )
		));
        // 'meta_query' => array(
            // 'relation' => 'OR',
                // array(
					// 'key' => 'ipfs_cid',
					// 'compare' => 'NOT EXISTS'
                // )
		// ));   

    $listings = query_posts($args);

    //DEBUG
    //error_log("*******************************************");
    //error_log( print_r($listings, true) );

    $headers = [
        'Authorization' => 'Bearer ' . API_KEY,        
        'Accept'        => 'application/json',
    ];

    $client = new GuzzleHttp\Client([
        'base_uri' 	=> IPFS_URL,
        'headers'	=> $headers
    ]);

    $upload_dir = wp_upload_dir();
    $upload_basedir = $upload_dir[ 'basedir' ];

    foreach ( $listings as $listing ) {

        $json_file = get_post_meta( $listing->ID, '_json_file', true );
        $thumbnail_id = get_post_meta( $listing->ID, '_thumbnail_id', true );
        $image_file = get_post_meta( $thumbnail_id, '_wp_attached_file', true );

        //DEBUG
        error_log( "Image path: " .  $upload_basedir . '/' . $image_file );
        error_log( "JSON path: " . $upload_basedir . '/' . $json_file );
		
		if ( ( !empty( $json_file ) && file_exists( $upload_basedir . '/' . $json_file ) ) && ( !empty( $image_file ) && file_exists( $upload_basedir . '/' . $image_file ) ) ) {
        //if ( !empty( $json_file ) && !empty( $image_file ) && file_exists( $upload_basedir . '/' . $image_file ) ) {
            try {
                $response = $client->request('POST', '/' . URL_PATH, [
                    'multipart' => [
                        [
                            'name'     => 'file',
                            'contents' => Psr7\Utils::tryFopen( $upload_basedir . '/' . $image_file, 'r')
                        ],
                        [
                            'name'     => 'file',
                            'contents' => Psr7\Utils::tryFopen( $upload_basedir . '/' . $json_file, 'r')
                        ],
                    ]
                ]);

                $body = $response->getBody();
                $data = json_decode($body);
                update_post_meta( $listing->ID, 'ipfs_cid', $data->value->cid );

            } catch (Exception $e) {
                error_log( 'Exception when uploading to IPFS: ' . $e->getMessage() );
            }
        } elseif ( ( !empty( $json_file ) && file_exists( $upload_basedir . '/' . $json_file ) ) && ( empty( $image_file ) || !file_exists( $upload_basedir . '/' . $image_file ) ) ) {
		//} elseif ( !empty( $json_file ) && ( empty( $image_file ) || !file_exists( $upload_basedir . '/' . $image_file ) ) ) {
            try {
                $response = $client->request('POST', '/' . URL_PATH, [
                    'multipart' => [
                        [
                            'name'     => 'file',
                            'contents' => Psr7\Utils::tryFopen( $upload_basedir . '/' . $json_file, 'r')
                        ],
                    ]
                ]);

                $body = $response->getBody();
                $data = json_decode($body);
                update_post_meta( $listing->ID, 'ipfs_cid', $data->value->cid );

                //DEBUG
                //error_log( "CID: " . $data->value->cid );

            } catch (Exception $e) {
                error_log( 'Exception when uploading to IPFS: ' . $e->getMessage() );
            }
        }
    }
    // End of uploading process
    
    // delete the transient to remove the flag and allow the process to run again
    delete_site_transient( get_ipfs_upload_transient_name() ); 
    
}
//add_action( 'ipfs_upload', 'upload_to_ipfs' );	//Comment out if we are using the cron scheduler below. This line should be uncommented if using WP Crontrol plugin

//Mel: 24/09/22
//To run the WP Cron every 2 minutes
function cron_every_two_minutes( $schedules ) {

    $schedules['every_two_minutes'] = array(
            'interval'  => 60,
            'display'   => __( 'Every 1 Minute', 'textdomain' )
    );

    return $schedules;
}
add_filter( 'cron_schedules', 'cron_every_two_minutes' );

// Schedule an action if it's not already scheduled
if ( ! wp_next_scheduled( 'cron_every_two_minutes' ) ) {
    wp_schedule_event( time(), 'every_two_minutes', 'cron_every_two_minutes' );
}
// Hook into that action that'll fire the upload_to_ipfs function
add_action( 'cron_every_two_minutes', 'upload_to_ipfs' );


/** Mel: 22/09/22
 * This function creates the JSON file containing the person's biodata like name, date of birth, city and etc. The file is store under the latest "uploads" folder. If old JSON file exists, it doesn't delete the old file but recreates a new one especially when bio is updated.
 */
function create_biodata_json( $post_id, $post, $old_status ) {

	//To store the metadata of the deceased profile.
	$metadata = [];

	//To get the selected category (country) from acadp category list
	$country = wp_get_object_terms( $post_id, 'acadp_categories' );
	
	//Get the full name and biography from the post title and the content/body
	$metadata['fullName'] = $post->post_title;
	$metadata['bio'] = $post->post_content;
	
	$custom_fields = get_custom_fields();

	//Loop through all the custom fields such as city, state, date of birth, etc
	foreach ( $custom_fields as $custom_field ) {

		$field_data = get_post_meta( $post_id, $custom_field->ID, true);

		if ( !empty( $field_data ) ) {
			$metadata[ esc_html( format_field_name( $custom_field->post_title ) ) ] = esc_html( $field_data );	//Store custom field data in the array
		}
	}

	$metadata['country'] = $country[0]->name;

	$image = wp_get_attachment_metadata( get_post_meta( $post_id, '_thumbnail_id', true ), true );
	$image = esc_html( wp_basename( $image['file'] ) );
	$metadata['imageFilename'] = $image;

	//Convert the metadata array to JSON string.
	$json = json_encode($metadata, JSON_PRETTY_PRINT);

	//Save the metadata as JSON file where the full name is the filename such as john-doe.json
	$json_file = write_to_json_file( $metadata['fullName'], $json );

	$upload_dir = wp_upload_dir();
	$upload_path = $upload_dir['path'];
	$upload_subdir = $upload_dir['subdir'];
	
	//DEBUG
	// error_log("upload_dir: " . $upload_dir);
	// error_log("upload_path: " . $upload_path );
	// error_log("json file: ");
	// error_log( print_r( $json_file, true ));

	//If there's no error in creating JSON file
	if ( empty( $json_file['error'] ) ) {
		update_post_meta( $post_id, '_json_file', ltrim( $upload_subdir, '/') . '/' . wp_basename( $json_file['file'] ) );
		delete_post_meta ( $post_id, 'ipfs_cid' );	//When deleted, it allows the reupload of bio to IPFS  
		
	} else {
		error_log( "Error: Failed in creating JSON file for biography "  . $metadata['fullName'] );
	}

}
add_action( 'publish_acadp_listings', 'create_biodata_json', 10, 3 );

//Mel: 22/09/22
//Get all the available custom fields such as date of birth, city, spouse, etc.
function get_custom_fields() {

	$all_post_ids = get_posts(array(
		'posts_per_page' => '-1',	//-1 means unlimited posts per page
		'post_type' => 'acadp_fields',
		'post_status'=>'publish',
	));

	return $all_post_ids;	//return as array
}

//Mel: 22/09/22
//Convert string such as from Resting Place to restingPlace
function format_field_name( $field ) {

	$new_field_name = '';

	$parts = preg_split( '/\s+/', $field );	//Split the string into array based on the whitespaces

	foreach ( $parts as $key => $value ) {
		$value = trim( $value );
		$new_field_name .= ( $key == '0' ) ? strtolower( $value ) : ucfirst( $value );	// To convert word like Date Of Birth to dateOfBirth
	}

	return $new_field_name;
}

//Mel: 23/09/22
//To hide the "New" and "+" links on top left corner of user menu when logged in
function remove_admin_bar_links() {
    global $wp_admin_bar, $current_user;
    
    if ($current_user->ID != 1) {
        $wp_admin_bar->remove_menu('new-content');      // Remove the content link
    }
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );

//Mel: 30/09/22
//To remove certain menu items on backend admin depending on user role
function remove_menus() {
	
	if( is_user_logged_in() ) {
		$user = wp_get_current_user();
		$roles = ( array ) $user->roles;
		
		//If user has Editor role, remove Automatic plugin's menu items
		if ( $roles[0] == 'editor' ) {
			remove_menu_page( 'edit.php?post_type=wp_automatic' );
			remove_menu_page( 'post-new.php?post_type=wp_automatic' );
			remove_menu_page( 'edit.php?post_type=wp_automatic&page=gm_setting' );
		}	
	}
	
}
add_action( 'admin_init', 'remove_menus' );

//Mel: 03/10/22
//To know who's the one publishing a bio
function save_publisher( $post ) {
	
	if ( $post->post_type == 'acadp_listings' ) {
		// Set current user ID as a post meta
		update_post_meta( $post->ID, '_publisher_id', get_current_user_id() );
	}
}
add_action(  'pending_to_publish',  'save_publisher', 10, 1 );

//Mel: 04/10/22
//To formally add custom query variables to main $wp_query object. So that wp can read the query string.
//Src: https://wordpress.org/support/topic/how-and-why-does-wp-rewrite-certain-url-query-parameter/
function custom_query_vars_filter($vars) {
    $vars[] .= 'yr';	//Renamed from year to yr cos year someone is used by wp already
	$vars[] .= 'mth';
    return $vars;
}
add_filter( 'query_vars', 'custom_query_vars_filter' );

//Mel: 03/01/22
//To get the report on how many bio were published by an editor
function total_post_by_month() {
//function total_post_by_month( $atts, $content = null, $tag = '' ) {
	 //Uncomment the below when using shortcode attributes like [total_posts_by_editors month="9" year="2022"]
	 // $args = shortcode_atts( array(
	 // 'month' => 1,
	 // 'year' => 2022
	 // ), $atts );
	 
	$month = sanitize_text_field( get_query_var( 'mth' ) );
	$year = sanitize_text_field( get_query_var( 'yr' ) );
	
	//Uncomment the below when using shortcode attributes 
	//$month = sanitize_text_field( $args['month'] );
	//$year = sanitize_text_field( $args['year'] );
	$month = trim( $month ); //Remove leading and trailing whitespace
	$year = trim( $year );
	
	$current_user = wp_get_current_user();
	
	//If user is an "editor", show the report about this editor only
	if ( in_array( 'editor', (array) $current_user->roles ) ) {
		$arg = array( 'search' => $current_user->user_nicename );
		
	} else {
		$arg = array(
			'role'    => 'editor',
			'orderby' => 'user_nicename',
			'order'   => 'ASC'
		);
	}
	
	$users = get_users( $arg );
	
	$output = '';
	
	if ( !empty( $month ) && !empty( $year ) ) {
		$output .= '<p>Month: ' . $month . '<br>' . 'Year: ' . $year . '</p>';
	}

	foreach ( $users as $user ) {
		$output .= '<li>' . esc_html( $user->first_name ) . ' ' . esc_html( $user->last_name ) . ' (' . esc_html( $user->display_name ) . ') : ' . published_total_posts( $user->ID, $month, $year ) . '</li>';
		//$output .= '<li>' . esc_html( $user->display_name ) . '[' . esc_html( $user->user_email ) . '] : ' . published_total_posts( $user->ID, $month, $year ) . '</li>';
	}
	 
	 return $output;
}
add_shortcode( 'total_posts_by_editors', 'total_post_by_month' );

//Mel: 03/10/22
//To get total posts published by a user for the month and year
function published_total_posts( $user_id, $month, $year ) {
	
	if ( !empty( $month ) && !empty( $year ) ) {
		$args = array(
			'posts_per_page' => '-1',	//-1 means unlimited posts per page
			'post_type' => 'acadp_listings',
			'post_status'=>'publish',
			'meta_query' => array(
				array(
					'key' => '_publisher_id',
					'value' => $user_id,
					'compare' => '='
					),
				),
			'date_query' => array(
				array(
					'year'  => $year,
					'month' => $month,
					//'day'   => 30,
				),
			),
		);
		
	} else {
		$args = array(
			'posts_per_page' => '-1',	//-1 means unlimited posts per page
			'post_type' => 'acadp_listings',
			'post_status'=>'publish',
			'meta_query' => array(
				array(
					'key' => '_publisher_id',
					'value' => $user_id,
					'compare' => '='
					)
			)
		);	
	}

    $the_query = new WP_Query( $args );
	
    return $the_query->found_posts;
}

//Mel: 04/10/22
//To only allow access to publication report page for admin or editor.
function check_page_permission() {

  global $post;
  global $current_user;
  
  //Need to change to the correct page ID
  $page_id = 198153;	// page ID for publication report on production site.
  //$page_id = 3020;		// page ID for publication report on test site. 

  $post = is_singular() ? get_queried_object() : false;
  if ( !empty($post) && is_a($post, 'WP_Post') ) {
	  //If user who is not admin or editor wants to access page with $page_id
	  if ( ( $post->post_parent == $page_id || is_page( $page_id ) ) && ( !in_array( 'administrator', $current_user->roles) && !in_array( 'editor', $current_user->roles) ) ) {
		wp_redirect( get_site_url() ); 
		exit;
	  }
  }
  
}
add_action( 'template_redirect', 'check_page_permission' );

//Mel: 06/10/22
//To redirect user to a page after logged in
function redirect_after_login( $redirect_to, $requested_redirect_to, $user ) {

	if ( !empty( $requested_redirect_to ) ) {
        $redirect_to = $requested_redirect_to;
    } else {
		$redirect_to = get_site_url() . '/manage-submissions';
    }

    return $redirect_to;
}
add_filter( 'login_redirect', 'redirect_after_login', 10, 3 );

// Mel: 06/10/22
function auto_login_after_register( $user_id ) {
    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id );
    wp_redirect( get_site_url() );
    exit(); 
}
add_action( 'user_register', 'auto_login_after_register' );

// Mel: 29/10/22
// Function to change email address
add_filter( 'wp_mail_from', 'wpb_sender_email' );
function wpb_sender_email( $original_email_address ) {
    return 'hello@lifefram.org';
}
 
// Function to change sender name
add_filter( 'wp_mail_from_name', 'wpb_sender_name' );
function wpb_sender_name( $original_email_from ) {
    return 'Lifefram';
}

/*Customizer*/
require get_template_directory() . '/function/customizer.php';
/*theme-default-setup*/
require get_template_directory() . '/function/theme-default-setup.php';
// Implement Custom Header features.
require get_template_directory() . '/function/custom-header.php';