<?php
/**
 * Correct Lite Theme Customizer
 *
 * @package Library Books
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function library_books_customize_register( $wp_customize ) {
	//Add a class for titles
    class library_books_Info extends WP_Customize_Control {
        public $type = 'info';
        public $label = '';
        public function render_content() {
        ?>
			<h3 style="text-decoration: underline; color: #DA4141; text-transform: uppercase;"><?php echo esc_html( $this->label ); ?></h3>
        <?php
        }
    }

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->add_setting('color_scheme',array(
			'default'	=> '#393939',
			'sanitize_callback'	=> 'sanitize_hex_color'
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'color_scheme',array(
			'label' => esc_html__('Color Scheme','library-books'),			
			 'description'	=> esc_html__('More color options in PRO Version','library-books'),	
			'section' => 'colors',
			'settings' => 'color_scheme'
		))
	);
	
	// Slider Section		
	$wp_customize->add_section( 'slider_section', array(
            'title' => esc_html__('Slider Settings', 'library-books'),
            'priority' => null,
            'description'	=> wp_kses_post('<strong>Slider Display When Frontpage Is Selected.</strong> <br/><br/>Featured Image Size Should be ( 1420 X 759 ) More slider settings available in PRO Version','library-books'),		
        )
    );
	
	$wp_customize->add_setting('page-setting10',array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting10',array(
			'type'	=> 'dropdown-pages',
			'label'	=> esc_html__('Select page for slide one:','library-books'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('page-setting11',array(
			'default' => '0',
			'capability' => 'edit_theme_options',			
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting11',array(
			'type'	=> 'dropdown-pages',
			'label'	=> esc_html__('Select page for slide two:','library-books'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('page-setting12',array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting12',array(
			'type'	=> 'dropdown-pages',
			'label'	=> esc_html__('Select page for slide three:','library-books'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('slide_button',array(
			'default'	=> null,
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('slide_button',array(
			'label'	=> esc_html__('Add Slide Button Title Here','library-books'),
			'section'	=> 'slider_section',
			'setting'	=> 'slide_button'
	));	
	
	//Slider hide
	$wp_customize->add_setting('hide_slides',array(
			'sanitize_callback' => 'wp_validate_boolean',
			'default' => true,
	));	 

	$wp_customize->add_control( 'hide_slides', array(
    	   'section'   => 'slider_section',    	 
		   'label'	=> esc_html__('Check To Hide Slider','library-books'),
    	   'type'      => 'checkbox'
     )); // Slider Section	
	 
// Home Section 1
	$wp_customize->add_section('section_one', array(
		'title'	=> esc_html__('Home Section One','library-books'),
		'description'	=> esc_html__('Select Page from the dropdown','library-books'),
		'priority'	=> null
	));	
	
	$wp_customize->add_setting('section1_title',array(
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('section1_title',array(
			'label'	=> __('Add section top title','library-books'),
			'section'	=> 'section_one',
			'setting'	=> 'section1_title'
	));		

	$wp_customize->add_setting('hmpage-column1',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'absint',
		));
	$wp_customize->add_control(	'hmpage-column1',array('type' => 'dropdown-pages',
			'section' => 'section_one',
	));	
	
	//Hide Section
	$wp_customize->add_setting('hide_sectionone',array(
			'sanitize_callback' => 'wp_validate_boolean',
			'default' => true,
	));	 
	$wp_customize->add_control( 'hide_sectionone', array(
    	   'section'   => 'section_one',    	 
		   'label'	=> esc_html__('Uncheck To Show This Section','library-books'),
    	   'type'      => 'checkbox'
     )); //Hide Section	 	 
	 
	// Home Section Two Eight Boxes 	
	$wp_customize->add_section('section_two', array(
		'title'	=> esc_html__('Home Section Two','library-books'),
		'description'	=> wp_kses_post('<strong>Section Display When Frontpage Is Selected.</strong> <br/><br/>Select pages from the dropdown for section boxes','library-books'),
		'priority'	=> null
	));	
	
	$wp_customize->add_setting('section2_title',array(
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('section2_title',array(
			'label'	=> __('Add section title','library-books'),
			'section'	=> 'section_two',
			'setting'	=> 'section2_title'
	));		
	
	$wp_customize->add_setting('sec2-page-column1',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'absint',
		));
 
	$wp_customize->add_control(	'sec2-page-column1',array('type' => 'dropdown-pages',
			'section' => 'section_two',
	));	
	
	$wp_customize->add_setting('sec2-page-column2',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'absint',
		));
 
	$wp_customize->add_control(	'sec2-page-column2',array('type' => 'dropdown-pages',
			'section' => 'section_two',
	));
	
	$wp_customize->add_setting('sec2-page-column3',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'absint',
		));
 
	$wp_customize->add_control(	'sec2-page-column3',array('type' => 'dropdown-pages',
			'section' => 'section_two',
	));
	
	$wp_customize->add_setting('sec2-page-column4',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'absint',
		));
 
	$wp_customize->add_control(	'sec2-page-column4',array('type' => 'dropdown-pages',
			'section' => 'section_two',
	));	
	
	$wp_customize->add_setting('sec2-page-column5',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'absint',
		));
 
	$wp_customize->add_control(	'sec2-page-column5',array('type' => 'dropdown-pages',
			'section' => 'section_two',
	));	
	
	$wp_customize->add_setting('sec2-page-column6',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'absint',
		));
 
	$wp_customize->add_control(	'sec2-page-column6',array('type' => 'dropdown-pages',
			'section' => 'section_two',
	));		
	
	$wp_customize->add_setting('sec2-page-column7',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'absint',
		));
 
	$wp_customize->add_control(	'sec2-page-column7',array('type' => 'dropdown-pages',
			'section' => 'section_two',
	));	
	
	$wp_customize->add_setting('sec2-page-column8',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'absint',
		));
 
	$wp_customize->add_control(	'sec2-page-column8',array('type' => 'dropdown-pages',
			'section' => 'section_two',
	));	
	
	//Hide Section
	$wp_customize->add_setting('hide_section_two',array(
			'sanitize_callback' => 'wp_validate_boolean',
			'default' => true,
	));	 

	$wp_customize->add_control( 'hide_section_two', array(
    	   'section'   => 'section_two',    	 
		   'label'	=> esc_html__('Check To Hide Section','library-books'),
    	   'type'      => 'checkbox'
     )); // Home Section Two Eight Boxes
	 
	// Home Section Three	
	$wp_customize->add_section('section_three', array(
		'title'	=> esc_html__('Home Section Three','library-books'),
		'description'	=> wp_kses_post('<strong>Section Display When Frontpage Is Selected.</strong> <br/><br/>Select page from the dropdown for section 3','library-books'),
		'priority'	=> null
	));	
	
	$wp_customize->add_setting('section3_title',array(
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('section3_title',array(
			'label'	=> __('Add section title','library-books'),
			'section'	=> 'section_three',
			'setting'	=> 'section3_title'
	));		
	
	$wp_customize->add_setting('sec3-page-column1',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'absint',
		));
 
	$wp_customize->add_control(	'sec3-page-column1',array('type' => 'dropdown-pages',
			'section' => 'section_three',
	));	
	
	$wp_customize->add_setting('sec3-page-column2',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'absint',
		));
 
	$wp_customize->add_control(	'sec3-page-column2',array('type' => 'dropdown-pages',
			'section' => 'section_three',
	));	
	
	$wp_customize->add_setting('sec3-page-column3',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'absint',
		));
 
	$wp_customize->add_control(	'sec3-page-column3',array('type' => 'dropdown-pages',
			'section' => 'section_three',
	));	
	
	//Hide Page Content Section
	$wp_customize->add_setting('hide_section_three',array(
			'sanitize_callback' => 'wp_validate_boolean',
			'default' => true,
	));	 

	$wp_customize->add_control('hide_section_three', array(
    	   'section'   => 'section_three',    	 
		   'label'	=> esc_html__('Check To Hide Section','library-books'),
    	   'type'      => 'checkbox'
     ));
	 // Hide Section	 
    $wp_customize->add_setting('library_books_options[layout-info]', array(
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control( new library_books_Info( $wp_customize, 'layout_section', array(
        'section' => 'theme_layout_sec',
        'settings' => 'library_books_options[layout-info]',
        'priority' => null
        ) )
    );
	  
    $wp_customize->add_setting('library_books_options[font-info]', array(
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control( new library_books_Info( $wp_customize, 'font_section', array(
        'section' => 'theme_font_sec',
        'settings' => 'library_books_options[font-info]',
        'priority' => null
        ) )
    );	
	  
    $wp_customize->add_setting('library_books_options[info]', array(
            'type' => 'info_control',
            'capability' => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field'
        )
    );

    $wp_customize->add_control( new library_books_Info( $wp_customize, 'doc_section', array(
        'section' => 'theme_doc_sec',
        'settings' => 'library_books_options[info]',
        'priority' => 10
        ) )
    );		
}
add_action( 'customize_register', 'library_books_customize_register' );
//setting inline css.
function library_books_custom_css() {
    wp_enqueue_style(
        'library-books-custom-style',
        get_template_directory_uri() . '/css/custom_script.css'
    );
        $color = esc_attr(get_theme_mod( 'color_scheme' )); //E.g. #FF0000
		$header_text_color = esc_attr(get_header_textcolor());
		
        $custom_css = "
                #sidebar ul li a:hover,
					.cols-3 ul li a:hover, .cols-3 ul li.current_page_item a,					
					.phone-no strong,					
					.left a:hover,
					.blog_lists h4 a:hover,
					.recent-post h6 a:hover,
					.postmeta a:hover,
					.recent-post .morebtn:hover{
                        color: {$color};
                }
				
                .pagination .nav-links span.current, .pagination .nav-links a:hover,
					#commentform input#submit:hover,
					.slide_info .slide_more:hover,													
					.wpcf7 input[type='submit'],					
					.social-icons a:hover,
					.benefitbox-4:hover .benefitbox-title,
					input.search-submit{
                        background-color: {$color};
                }
				.logo h2, .sitenav ul li a, .slide_info h2, .slide_info p{
					color: #$header_text_color;
				}
				
				.sitenav ul li.call-button a, .slide_info .slide_more{
					background-color: #$header_text_color;
				}
				";
        wp_add_inline_style( 'library-books-custom-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'library_books_custom_css' );         

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function library_books_customize_preview_js() {
	wp_enqueue_script( 'library_books_customizer', get_template_directory_uri() . '/js/customize-preview.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'library_books_customize_preview_js' );