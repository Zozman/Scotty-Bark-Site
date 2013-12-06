<?php   
/* 
Plugin Name: MaGeek Lab Google Maps  
Plugin URI: http://www.mageeklab.com 
Description: Plugin for creating Google Maps with different styles. Includes a generator
Author: MaGeek Lab 
Version: 1.0.1
Author URI: http://www.mageeklab.com  
*/ 

function mgl_gmaps_admin_actions() {  
    add_submenu_page('options-general.php', 'Google Maps', 'Google Maps', 'administrator', 'mgl-gmaps', 'mgl_gmaps_admin'); 
}  
  
add_action('admin_menu', 'mgl_gmaps_admin_actions');   

function mgl_gmaps_admin() {  
    include('mgl_gmaps_admin.php');  
}  

//Add JS scripts
function mgl_gmaps_admin_scripts() {

    if ( 'settings_page_mgl-gmaps' == get_current_screen() -> id) {

    	wp_enqueue_style("mgl_gmaps_admin", plugin_dir_url(__FILE__)."/css/mgl_gmaps_admin.css", false, "1.0", "all");

	}

    
}
add_action('admin_enqueue_scripts', 'mgl_gmaps_admin_scripts');

include('mgl_gmaps_functions.php');

/* Register Widgets */

require_once('widget-mgl_gmap.php');

add_action('widgets_init', 'mgl_gmaps_register_widgets');

function mgl_gmaps_register_widgets() {
    register_widget( 'mgl_GMaps' );
}

/* We need JQuery! */
function mgl_gmaps_scripts() {
	if(!is_admin()) {

		wp_enqueue_script("jquery"); 
	}
}

/* Only load if the checkbox is checked */

if(get_option('mgl_gmaps_jquery', true) == true) {
    add_action('wp_print_scripts', 'mgl_gmaps_scripts');
}

/* Include generator */

require_once('gmaps-generator/generator.php');

add_action('wp_ajax_mgl_gmap_generator', 'mgl_gmap_generator');
