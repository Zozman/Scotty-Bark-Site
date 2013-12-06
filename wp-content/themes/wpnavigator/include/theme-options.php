<?php
////////////////////////////
//THEME CUSTOMIZER MENU ITEM
////////////////////////////
function themolitor_customizer_admin() {
    add_theme_page( __('Theme Options','themolitor'),  __('Theme Options','themolitor'), 'edit_theme_options', 'customize.php' ); 
}
add_action ('admin_menu', 'themolitor_customizer_admin');

////////////////////////////
//THEME CUSTOMIZER SETTINGS
////////////////////////////
add_action( 'customize_register', 'themolitor_customizer_register' );

function themolitor_customizer_register($wp_customize) {

	//CREATE TEXTAREA OPTION
	class Example_Customize_Textarea_Control extends WP_Customize_Control {
    	public $type = 'textarea';
 
    	public function render_content() { ?>
        	<label>
        	<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
        	<textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
        	</label>
        <?php }
	}
	
	//CREATE CATEGORY DROP DOWN OPTION
	$options_categories = array();  
	$options_categories_obj = get_categories();
	$options_categories[''] = 'Select a Category';
	foreach ($options_categories_obj as $category) {
		$options_categories[$category->cat_ID] = $category->cat_name;
	}
	
	//-------------------------------
	//TITLE & TAGLINE SECTION
	//-------------------------------
	//LOGO
	$wp_customize->add_setting( 'themolitor_customizer_logo');
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'themolitor_customizer_logo', array(
    	'label'    => __('Logo', 'themolitor'),
    	'section'  => 'title_tagline',
    	'settings' => 'themolitor_customizer_logo',
    	'priority' => 1
	)));
	
	//DISPLAY TAGLINE
	$wp_customize->add_setting( 'themolitor_customizer_tagline_onoff', array(
    	'default' => 1
	));
	$wp_customize->add_control( 'themolitor_customizer_tagline_onoff', array(
    	'label' => 'Display Tagline',
    	'type' => 'checkbox',
    	'section' => 'title_tagline',
    	'settings' => 'themolitor_customizer_tagline_onoff',
    	'priority' => 10
	));
					
	//-------------------------------
	//COLORS SECTION
	//-------------------------------
	
	//LINK COLOR
	$wp_customize->add_setting( 'themolitor_customizer_link_color', array(
		'default' => '#99b3cc'
	));
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'themolitor_customizer_link_color', array(
		'label'   => __( 'Link Color', 'themolitor'),
		'section' => 'colors',
		'settings'   => 'themolitor_customizer_link_color'
	)));		
	
	//-------------------------------
	//GENERAL SECTION
	//-------------------------------
	
	//ADD GENERAL SECTION
	$wp_customize->add_section( 'themolitor_customizer_general_section', array(
		'title' => __( 'General', 'themolitor' ),
		'priority' => 197
	));
	
	//DEFAULT BACKGROUND IMAGE URL
    $wp_customize->add_setting( 'themolitor_customizer_background_url');
	$wp_customize->add_control('themolitor_customizer_background_url', array(
   		'label'   => __( 'Default Background Image URL', 'themolitor'),
    	'section' => 'themolitor_customizer_general_section',
    	'settings'   => 'themolitor_customizer_background_url',
    	'type' => 'text',
    	'priority' => 3
	));
	
	//FAVICON URL
    $wp_customize->add_setting( 'themolitor_customizer_favicon_url');
	$wp_customize->add_control('themolitor_customizer_favicon_url', array(
   		'label'   => __( 'Favicon URL (optional)', 'themolitor'),
    	'section' => 'themolitor_customizer_general_section',
    	'settings'   => 'themolitor_customizer_favicon_url',
    	'type' => 'text',
    	'priority' => 4
	));	

	//DISPLAY BREADCRUMBS
	$wp_customize->add_setting( 'themolitor_customizer_bread_onoff', array(
    	'default' => 1
	));
	$wp_customize->add_control( 'themolitor_customizer_bread_onoff', array(
    	'label' => 'Display Breadcrumbs',
    	'type' => 'checkbox',
    	'section' => 'themolitor_customizer_general_section',
    	'settings' => 'themolitor_customizer_bread_onoff',
    	'priority' => 5
	));

	//BLOG CATEGORY
	$wp_customize->add_setting('themolitor_blog_category', array(
	    'capability'     => 'edit_theme_options',
	    'type'           => 'option'
	));
	$wp_customize->add_control( 'themolitor_blog_category', array(
 	   'settings' => 'themolitor_blog_category',
 	   'label'   => __('Blog Category','themolitor'),
   	 	'section' => 'themolitor_customizer_general_section',
   	 	'type'    => 'select',
   	 	'choices' => $options_categories,
   	 	'priority' => 6
	));
	
	//-------------------------------
	//MAP SETTINGS SECTION
	//-------------------------------
	
	//ADD MAP SETTINGS SECTION
	$wp_customize->add_section( 'themolitor_customizer_map_section', array(
		'title' => __( 'Map Settings', 'themolitor' ),
		'priority' => 198
	));
	
	//DISPLAY BUTTON FOR MAP STYLE
	$wp_customize->add_setting( 'themolitor_customizer_mapstyle_onoff', array(
    	'default' => 1
	));
	$wp_customize->add_control( 'themolitor_customizer_mapstyle_onoff', array(
    	'label' => 'Display Button for Map Style',
    	'type' => 'checkbox',
    	'section' => 'themolitor_customizer_map_section',
    	'settings' => 'themolitor_customizer_mapstyle_onoff',
    	'priority' => 1
	));
	
	//DISPLAY MAP ZOOM CONTROLS
	$wp_customize->add_setting( 'themolitor_customizer_mapzoom_onoff', array(
    	'default' => 1
	));
	$wp_customize->add_control( 'themolitor_customizer_mapzoom_onoff', array(
    	'label' => 'Display Map Zoom Controls',
    	'type' => 'checkbox',
    	'section' => 'themolitor_customizer_map_section',
    	'settings' => 'themolitor_customizer_mapzoom_onoff',
    	'priority' => 2
	));
	
	//CATEGORY ZOOM LEVEL
    $wp_customize->add_setting( 'themolitor_customizer_cat_zoom', array(
    	'default' => '3'
	));
	$wp_customize->add_control('themolitor_customizer_cat_zoom', array(
   		'label'   => __( 'Category Zoom Level (1-20)', 'themolitor'),
    	'section' => 'themolitor_customizer_map_section',
    	'settings'   => 'themolitor_customizer_cat_zoom',
    	'type' => 'text',
    	'priority' => 3
	));
	
	//POST/PAGE ZOOM LEVEL
    $wp_customize->add_setting( 'themolitor_customizer_post_zoom', array(
    	'default' => '17'
	));
	$wp_customize->add_control('themolitor_customizer_post_zoom', array(
   		'label'   => __( 'Default Post/Page Zoom Level (1-20)', 'themolitor'),
    	'section' => 'themolitor_customizer_map_section',
    	'settings'   => 'themolitor_customizer_post_zoom',
    	'type' => 'text',
    	'priority' => 4
	));
	
	//DEFAULT MARKER IMAGE URL
    $wp_customize->add_setting( 'themolitor_customizer_pin', array(
    	'default' => ''. get_template_directory_uri() .'/images/pin.png'
	));
	$wp_customize->add_control('themolitor_customizer_pin', array(
   		'label'   => __( 'Default Marker Image URL', 'themolitor'),
    	'section' => 'themolitor_customizer_map_section',
    	'settings'   => 'themolitor_customizer_pin',
    	'type' => 'text',
    	'priority' => 5
	));

		
	//-------------------------------
	//FOOTER SECTION
	//-------------------------------

	//ADD FOOTER SECTION
	$wp_customize->add_section( 'themolitor_customizer_footer_section', array(
		'title' => __( 'Footer', 'themolitor' ),
		'priority' => 199
	));
	
	//DISPLAY WIDGETS
	$wp_customize->add_setting( 'themolitor_customizer_widgets_onoff', array(
    	'default' => 1
	));
	$wp_customize->add_control( 'themolitor_customizer_widgets_onoff', array(
    	'label' => 'Display Widgets',
    	'type' => 'checkbox',
    	'section' => 'themolitor_customizer_footer_section',
    	'settings' => 'themolitor_customizer_widgets_onoff',
    	'priority' => 1
	));
	
	//DISPLAY SEARCH
	$wp_customize->add_setting( 'themolitor_customizer_search_onoff', array(
    	'default' => 1
	));
	$wp_customize->add_control( 'themolitor_customizer_search_onoff', array(
    	'label' => 'Display Search Bar',
    	'type' => 'checkbox',
    	'section' => 'themolitor_customizer_footer_section',
    	'settings' => 'themolitor_customizer_search_onoff',
    	'priority' => 2
	));
		
	//DISPLAY RSS BUTTON
	$wp_customize->add_setting( 'themolitor_customizer_rss_onoff', array(
    	'default' => 1
	));
	$wp_customize->add_control( 'themolitor_customizer_rss_onoff', array(
    	'label' => 'Display RSS Button',
    	'type' => 'checkbox',
    	'section' => 'themolitor_customizer_footer_section',
    	'settings' => 'themolitor_customizer_rss_onoff',
    	'priority' => 5
	));
	
	//TWITTER
    $wp_customize->add_setting( 'themolitor_customizer_twitter');
	$wp_customize->add_control('themolitor_customizer_twitter', array(
   		'label'   => __( 'Twitter URL', 'themolitor'),
    	'section' => 'themolitor_customizer_footer_section',
    	'settings'   => 'themolitor_customizer_twitter',
    	'type' => 'text',
    	'priority' => 6
	));
	
	//FACEBOOK
    $wp_customize->add_setting( 'themolitor_customizer_facebook');
	$wp_customize->add_control('themolitor_customizer_facebook', array(
   		'label'   => __( 'Facebook URL', 'themolitor'),
    	'section' => 'themolitor_customizer_footer_section',
    	'settings'   => 'themolitor_customizer_facebook',
    	'type' => 'text',
    	'priority' => 7
	));
	
	//FLICKr
    $wp_customize->add_setting( 'themolitor_customizer_flickr');
	$wp_customize->add_control('themolitor_customizer_flickr', array(
   		'label'   => __( 'Flickr URL', 'themolitor'),
    	'section' => 'themolitor_customizer_footer_section',
    	'settings'   => 'themolitor_customizer_flickr',
    	'type' => 'text',
    	'priority' => 8
	));
	
	//MYSPACE
    $wp_customize->add_setting( 'themolitor_customizer_myspace');
	$wp_customize->add_control('themolitor_customizer_myspace', array(
   		'label'   => __( 'MySpace URL', 'themolitor'),
    	'section' => 'themolitor_customizer_footer_section',
    	'settings'   => 'themolitor_customizer_myspace',
    	'type' => 'text',
    	'priority' => 9
	));
	
	//LINKEDIN
    $wp_customize->add_setting( 'themolitor_customizer_linkedin');
	$wp_customize->add_control('themolitor_customizer_linkedin', array(
   		'label'   => __( 'LinkedIn URL', 'themolitor'),
    	'section' => 'themolitor_customizer_footer_section',
    	'settings'   => 'themolitor_customizer_linkedin',
    	'type' => 'text',
    	'priority' => 10
	));
	
	//YOUTUBE
    $wp_customize->add_setting( 'themolitor_customizer_youtube');
	$wp_customize->add_control('themolitor_customizer_youtube', array(
   		'label'   => __( 'YouTube URL', 'themolitor'),
    	'section' => 'themolitor_customizer_footer_section',
    	'settings'   => 'themolitor_customizer_youtube',
    	'type' => 'text',
    	'priority' => 11
	));
	
	//SKYPE
    $wp_customize->add_setting( 'themolitor_customizer_skype');
	$wp_customize->add_control('themolitor_customizer_skype', array(
   		'label'   => __( 'Skype URL', 'themolitor'),
    	'section' => 'themolitor_customizer_footer_section',
    	'settings'   => 'themolitor_customizer_skype',
    	'type' => 'text',
    	'priority' => 12
	));
	
	//VIMEO
    $wp_customize->add_setting( 'themolitor_customizer_vimeo');
	$wp_customize->add_control('themolitor_customizer_vimeo', array(
   		'label'   => __( 'Vimeo URL', 'themolitor'),
    	'section' => 'themolitor_customizer_footer_section',
    	'settings'   => 'themolitor_customizer_vimeo',
    	'type' => 'text',
    	'priority' => 13
	));
	
	//-------------------------------
	//GOOGLE FONT SECTION
	//-------------------------------

	//ADD GOOGLE FONT SECTION
	$wp_customize->add_section( 'themolitor_customizer_googlefont_section', array(
		'title' => __( 'Google Custom Font', 'themolitor' ),
		'description' => 'Visit <a target="_blank" href="http://google.com/fonts">google.com/fonts</a> to view fonts available.',
		'priority' => 200
	));
	
	/*
	//GOOGLE API
    $wp_customize->add_setting( 'themolitor_customizer_google_api', array(
    	'default' => '<link href="http://fonts.googleapis.com/css?family=Gruppo" rel="stylesheet" type="text/css" />'
	));
	$wp_customize->add_control('themolitor_customizer_google_api', array(
   		'label'   => __( 'Google Font API Link', 'themolitor'),
    	'section' => 'themolitor_customizer_googlefont_section',
    	'settings'   => 'themolitor_customizer_google_api',
    	'type' => 'text',
    	'priority' => 1
	));
	*/
	
	//GOOGLE KEYWORD
    $wp_customize->add_setting( 'themolitor_customizer_google_key', array(
    	'default' => 'Gruppo'
	));
	$wp_customize->add_control('themolitor_customizer_google_key', array(
   		'label'   => __( 'Google Font Name', 'themolitor'),
    	'section' => 'themolitor_customizer_googlefont_section',
    	'settings'   => 'themolitor_customizer_google_key',
    	'type' => 'text',
    	'priority' => 2
	));
		
	//-------------------------------
	//CUSTOM CSS SECTION
	//-------------------------------
	
	//ADD CSS SECTION
	$wp_customize->add_section( 'themolitor_customizer_custom_css', array(
		'title' => __( 'CSS', 'themolitor' ),
		'priority' => 201
	));
			
	//CUSTOM CSS
    $wp_customize->add_setting( 'themolitor_customizer_css');
	$wp_customize->add_control( new Example_Customize_Textarea_Control( $wp_customize, 'themolitor_customizer_css', array(
   		'label'   => __( 'Custom CSS', 'themolitor'),
    	'section' => 'themolitor_customizer_custom_css',
    	'settings'   => 'themolitor_customizer_css'
	)));
	
	//-------------------------------
	//POST FORM SECTION
	//-------------------------------
	
	//ADD POST FORM SECTION
	$wp_customize->add_section( 'themolitor_customizer_form_section', array(
		'title' => __( 'Front-end Submission Form', 'themolitor' ),
		'priority' => 201
	));
	
	//SEND ADMIN EMAIL NOTICE FOR NEW SUBMISSION
	$wp_customize->add_setting( 'themolitor_send_email', array(
    	'default' => 1
	));
	$wp_customize->add_control( 'themolitor_send_email', array(
    	'label' => 'Send email notice',
    	'type' => 'checkbox',
    	'section' => 'themolitor_customizer_form_section',
    	'settings' => 'themolitor_send_email',
    	'priority' => 1
	));
	
	//EMAIL TO USE
	$adminEmail = get_option('admin_email');
	$wp_customize->add_setting( 'themolitor_alt_email',array(
		'default' => $adminEmail
	));
	$wp_customize->add_control( 'themolitor_alt_email', array(
    	'label' => 'Email notice goes to:',
    	'type' => 'text',
    	'section' => 'themolitor_customizer_form_section',
    	'settings' => 'themolitor_alt_email',
    	'priority' => 2
	));
}