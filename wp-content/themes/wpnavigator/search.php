<?php get_header();?>

<div id="main">
	<div id="handle"></div>
	<div id="closeBox"></div>
	
	<h2 class="entrytitle"><?php _e('Search Results','themolitor');?></h2>
	
	<div class="listing">
	
	<?php if (have_posts()) { ?>
	
		<p id="results"><?php _e('Search returned','themolitor');?> <span></span> <?php _e('listing(s)','themolitor');?>...</p>
	
		<?php get_template_part("navigation");
	
		 } else {?>
		<p><?php _e("Sorry, your search didn't return any listings. Try again?",'themolitor');?></p>
		
		<script>
		jQuery.noConflict(); jQuery(document).ready(function(){
			jQuery.backstretch("<?php echo get_template_directory_uri();?>/images/Yield_Sign.jpg", {speed: 150});
		});
		</script>
	<?php } ?>
	
	</div><!--end listing-->
	
</div><!--end main-->

<?php 
get_template_part('script_list');
get_sidebar();
get_footer(); 
?>