<?php 
get_header();
	
//VAR SETUP
$crumbs = get_theme_mod('themolitor_customizer_bread_onoff');
$blogCat = get_option('themolitor_blog_category');
$bg = get_theme_mod('themolitor_customizer_background_url');
?>

<div id="main" <?php if($blogCat && is_category($blogCat)){ echo 'class="blog"'; } ?>>
	<div id="handle"></div>
	<div id="closeBox"></div>
	
	<?php if($blogCat && !is_category($blogCat)) { //IF NOT BLOG CATEGORY...?>
		<h2 class="entrytitle"><?php single_cat_title(); ?></h2>	
		<?php if ($crumbs && function_exists('dimox_breadcrumbs')) dimox_breadcrumbs(); ?>
		<div class="entry"><?php echo category_description(); ?></div>
	<?php } ?>
	
	<div class="listing">
	
	<?php 
	if($blogCat && is_category($blogCat)){  //IF BLOG CATEGORY...
	
		if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div <?php post_class();?>>
				<h2 class="blogTitle"><a href="<?php the_permalink();?>"><?php the_title(); ?></a></h2>
    			<a class="blogThumb" href="<?php the_permalink();?>"><?php the_post_thumbnail('blog');	?></a>
    			<p class="blogMeta"><?php _e('Posted','themolitor');?> <?php the_date();?>&nbsp; / &nbsp; <?php _e('By','themolitor');?> <?php the_author();?>&nbsp; / &nbsp;<?php comments_number(__('0 Comments','themolitor'), __('1 Comment','themolitor'), __('% Comments','themolitor')); ?></p>
    			<?php the_excerpt();?>
    			<p class="readMore"><a href="<?php the_permalink();?>"><?php _e('Read More','themolitor');?> &rarr;</a></p>
    			<div class="clear"></div>
    		</div>
        <?php endwhile; endif;
        
   		get_template_part('navigation');
	
	} else { //IF NOT BLOG CATEGORY...
		if (have_posts()) {
			get_template_part('navigation');
		} else { ?>
		<h2><?php _e('Not Found','themolitor');?></h2>
		<p><?php _e('The page you were looking for does not exist.','themolitor');?></p>
		<?php }} ?>
	
	</div><!--end listing-->
</div><!--end main-->

<?php if($blogCat && $bg && is_category($blogCat)){ //IF BLOG CATEGORY...?>  
<script type="text/javascript">
jQuery.noConflict(); jQuery(document).ready(function(){
	//FULL SCREEN IMAGE
	jQuery.backstretch("<?php echo $bg; ?>", {speed: 150});
});
</script>
<?php } else { //IF NOT BLOG CATEGORY...
	get_template_part('script_list'); 
}

get_sidebar();
get_footer(); 
?>