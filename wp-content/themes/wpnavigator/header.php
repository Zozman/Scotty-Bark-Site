<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php
//VAR SETUP
$customcss = get_theme_mod('themolitor_customizer_css');
$logo = get_theme_mod('themolitor_customizer_logo');
$favicon = get_theme_mod('themolitor_customizer_favicon_url');
$tagline = get_theme_mod('themolitor_customizer_tagline_onoff');
$color = get_theme_mod('themolitor_customizer_link_color');
$googleKeyword = get_theme_mod('themolitor_customizer_google_key');

if(!empty($favicon)) { echo '<link rel="icon" href="'.$favicon.'" type="image/x-icon" />'; }
if(!empty($googleKeyword)) { $googleKeyEdited = str_replace(" ", "+", $googleKeyword); echo '<link href="http://fonts.googleapis.com/css?family='.$googleKeyEdited.'" rel="stylesheet" type="text/css" />'; } 
?>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/scripts/prettyPhoto.css" type="text/css" media="screen" />

<style type="text/css">
a {color: <?php echo $color;?>;}

#commentform input[type="submit"], 
input[type="submit"],
.toggleButton:hover,
.widget_tag_cloud a {background: <?php echo $color;?>;}

<?php 
if(!empty($logo)){ echo "#loading {background-image: url(".$logo.");}\n";}
if(!empty($googleKeyword)){ echo "h1,h2,h3, h4, h5, h6 {font-family: '".$googleKeyword."', sans-serif;}\n";}
if(!empty($customcss)){echo $customcss;}
?>
</style>

<?php 
wp_enqueue_script('jquery');
wp_head(); 
if ( is_singular() ) wp_enqueue_script('comment-reply');
?>

<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/scripts/jquery-ui-1.9.2.custom.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/scripts/prettyphoto.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/scripts/activity.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?v=3.13&sensor=false"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/scripts/gmap3.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/scripts/jquery.backstretch.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/scripts/jquery.animate-colors-min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/scripts/custom.js"></script>
<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
<script type="text/javascript">
jQuery.noConflict(); jQuery(document).ready(function(){
//ACCORDION TOGGLES	
	jQuery('.toggleButton').hover(function(){
		jQuery(this).stop(true,true).animate({paddingLeft:"10px",backgroundColor:'<?php echo $color;?>', color:'#000'},300);
	},function(){
		jQuery(this).stop(true,true).animate({paddingLeft:"8px",backgroundColor:'#333',color:'#fff'},300);
	});
});
</script>
</head>

<body <?php body_class();?>>

<div id="gMap"></div>

<div id="header">
	<?php if(!empty($logo)){?><a id="logo" href="<?php echo home_url()?>"><img src="<?php echo $logo;?>" alt="<?php bloginfo('name'); ?>" /></a><?php } ?> 
	<?php if(!empty($tagline)){?><h2 id="description"><?php bloginfo('description')?></h2><?php } ?>
	<?php if (has_nav_menu( 'main' ) ) { wp_nav_menu(array('theme_location' => 'main', 'container_id' => 'navigation', 'menu_id' => 'dropmenu')); }?>
</div><!--end header-->	

<div id="loading"></div>

<div id="contentContainer">
	<div id="content">