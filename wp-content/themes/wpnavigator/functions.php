<?php
///////////////////////
// Localization Support
///////////////////////
load_theme_textdomain( 'themolitor', get_template_directory().'/languages' );
$locale = get_locale();
$locale_file = get_template_directory().'/languages/$locale.php';
if ( is_readable($locale_file) )
    require_once($locale_file);

///////////////////////
//FEED LINKS
///////////////////////
add_theme_support('automatic-feed-links' );

///////////////////////
//CONTENT WIDTH
///////////////////////
if ( !isset( $content_width ) ) $content_width = 300;

///////////////////////
//EXCERPT STUFF
///////////////////////
function new_excerpt_more($more) {
	return ' ...';
}
add_filter('excerpt_more', 'new_excerpt_more');

///////////////////////
//IMAGE ATTACHMENTS TOOLBOX
///////////////////////
function attachment_toolbox($size = thumbnail) {

	if($images = get_children(array(
		'post_parent'    => get_the_ID(),
		'post_type'      => 'attachment',
		'numberposts'    => -1, // show all
		'post_status'    => null,
		'post_mime_type' => 'image',
		'orderby' => 'menu_order'
	))) {
		foreach($images as $image) {
			$attimg   = wp_get_attachment_image($image->ID,$size);
			$atturl   = wp_get_attachment_url($image->ID);
			$attlink  = get_attachment_link($image->ID);
			$postlink = get_permalink($image->post_parent);
			$atttitle = apply_filters('the_title',$image->post_title);
			/*
			echo '<p><strong>wp_get_attachment_image()</strong><br />'.$attimg.'</p>';
			echo '<p><strong>wp_get_attachment_url()</strong><br />'.$atturl.'</p>';
			echo '<p><strong>get_attachment_link()</strong><br />'.$attlink.'</p>';
			echo '<p><strong>get_permalink()</strong><br />'.$postlink.'</p>';
			echo '<p><strong>Title of attachment</strong><br />'.$atttitle.'</p>';
			echo '<p><strong>Image link to attachment page</strong><br /><a href="'.$attlink.'">'.$attimg.'</a></p>';
			echo '<p><strong>Image link to attachment post</strong><br /><a href="'.$postlink.'">'.$attimg.'</a></p>';
			echo '<p><strong>Image link to attachment file</strong><br /><a href="'.$atturl.'">'.$attimg.'</a></p>';
			*/ 
			echo'<li class="wrapperli"><a title="'.$atttitle.'" href="'.$atturl.'">'.$attimg.'</a></li>';
		}
	}
}

///////////////////////
//EXCLUDE PAGES FROM SEARCH
///////////////////////
function SearchFilter($query) {
	if ($query->is_search) {
    	$query->set('post_type', 'post');
    }
    return $query;
}
add_filter('pre_get_posts','SearchFilter');

///////////////////////
//FEATURED IMAGE SUPPORT
///////////////////////
add_theme_support( 'post-thumbnails', array( 'post' ) );
set_post_thumbnail_size( 95, 95, true );
add_image_size( 'slider',300 ,200, true );
add_image_size( 'blog',500 ,200, true );
add_image_size( 'small',53 ,53, true );

///////////////////////
//CATEGORY ID FROM NAME FOR PAGE TEMPLATES
///////////////////////
function get_category_id($cat_name){
	$term = get_term_by('name', $cat_name, 'category');
	return $term->term_id;
}

///////////////////////
//ADD MENU SUPPORT
///////////////////////
add_theme_support( 'menus' );
register_nav_menu('main', 'Main Navigation Menu');

///////////////////////
//SIDEBAR GENERATOR (FOR SIDEBAR AND FOOTER)
///////////////////////
if ( function_exists('register_sidebar') )
register_sidebar(array('name'=>'Live Widgets',
	'description' => 'It is recommended that you not exceed 3 widgets here.',
	'before_widget' => '<li id="%1$s" class="widget %2$s">',
	'after_widget' => '</li>',
	'before_title' => '<h2 class="widgettitle">',
	'after_title' => '</h2>',
));

///////////////////////
//BREADCRUMBS
///////////////////////
include(TEMPLATEPATH . '/include/breadcrumbs.php');

////////////////////////
//CUSOTM POST OPTIONS
////////////////////////
include(TEMPLATEPATH . '/include/post-meta.php');

////////////////////////
//LEGACY CUSTOM POST OPTIONS
////////////////////////
//include(TEMPLATEPATH . '/include/old-post-meta.php');

////////////////////////
//THEME OPTIONS
////////////////////////
include(TEMPLATEPATH . '/include/theme-options.php');