<?php
//VAR SETUP
$rss = get_theme_mod('themolitor_customizer_rss_onoff');
$skype = get_theme_mod('themolitor_customizer_skype');
$myspace = get_theme_mod('themolitor_customizer_myspace');
$flickr = get_theme_mod('themolitor_customizer_flickr');
$linkedin = get_theme_mod('themolitor_customizer_linkedin');
$youtube = get_theme_mod('themolitor_customizer_youtube');
$vimeo = get_theme_mod('themolitor_customizer_vimeo');
$facebook = get_theme_mod('themolitor_customizer_facebook');
$twitter = get_theme_mod('themolitor_customizer_twitter');
$widgets = get_theme_mod('themolitor_customizer_widgets_onoff');
$search = get_theme_mod('themolitor_customizer_search_onoff');
?>

<div class="clear"></div>
</div><!--end content-->
</div><!--end contentContainer-->

<div id="footer">
	
	<?php if(!empty($widgets)){ ?>
	<a href="#" id="widgetsOpen" title="<?php _e('More','themolitor');?>" class="widgetsToggle">+</a>
	<a href="#" id="widgetsClose" title="<?php _e('Close','themolitor');?>" class="widgetsToggle">&times;</a>	
	<?php }
	
	if(!empty($search)){ ?>
	<div id="footerSearch">
		<form method="get" action="<?php echo home_url(); ?>/">
			<input type="image" src="<?php echo get_template_directory_uri(); ?>/images/mag_glass.png" id="searchsubmit" alt="GO!" />
			<input type="text" value="" onfocus="this.value=''; this.onfocus=null;"  name="s" id="s" />
		</form>
	</div>
	<?php }
	
	if(is_single()){
		next_post_link('%link', '&lsaquo;', TRUE); 
		previous_post_link('%link', '&rsaquo;', TRUE);
	}?>

	<div class="pageContent">
		<?php if(is_single() || is_page()) { ?>
		<h2><?php the_title(); ?></h2>
		<?php } elseif(is_404()) { ?>
		<h2><?php _e('404 Error','themolitor');?></h2>
		<?php } elseif(is_search()) { ?>
		<h2><?php _e('Search Results','themolitor');?></h2>
		<?php } elseif(is_category()) { ?>
		<h2><?php single_cat_title(); ?></h2>
		<?php } elseif( is_tag() ) { ?>
		<h2><?php single_tag_title(); ?></h2>
		<?php } elseif (is_day()) { ?>
		<h2><?php _e('Archive for','themolitor');?> <?php the_time('F jS, Y'); ?></h2>
		<?php } elseif (is_month()) { ?>
		<h2><?php _e('Archive for','themolitor');?> <?php the_time('F, Y'); ?></h2>
		<?php } elseif (is_year()) { ?>
		<h2><?php _e('Archive for','themolitor');?> <?php the_time('Y'); ?></h2>
		<?php } elseif (is_author()) { ?>
		<h2><?php _e('Author Archive','themolitor');?></h2>
		<?php } ?>
	</div>
	
	<?php if (!empty($rss) || !empty($skype) || !empty($myspace) || !empty($flickr) || !empty($linkedin) || !empty($youtube) || !empty($vimeo) || !empty($facebook) || !empty($twitter)) { ?>
	<div id="socialStuff">
		<?php if (!empty($rss)) { ?>
			<a class="socialicon" id="rssIcon" href="<?php bloginfo('rss2_url'); ?>"  title="<?php _e('Subscribe via RSS','themolitor');?>" rel="nofollow"></a>
		<?php } if (!empty($skype)) { ?>
			<a class="socialicon" id="skypeIcon" href="<?php echo $skype; ?>"  title="Skype" rel="nofollow"></a>
		<?php } if (!empty($myspace)) { ?>
			<a class="socialicon" id="myspaceIcon" href="<?php echo $myspace; ?>"  title="MySpace" rel="nofollow"></a>
		<?php } if (!empty($flickr)) { ?>
			<a class="socialicon" id="flickrIcon" href="<?php echo $flickr; ?>"  title="Flickr" rel="nofollow"></a>
		<?php } if (!empty($linkedin)) { ?>
			<a class="socialicon" id="linkedinIcon" href="<?php echo $linkedin; ?>"  title="LinkedIn" rel="nofollow"></a>
		<?php } if (!empty($youtube)) { ?> 
			<a class="socialicon" id="youtubeIcon" href="<?php echo $youtube; ?>" title="YouTube Channel"  rel="nofollow"></a>
		<?php } if (!empty($vimeo)) { ?> 
			<a class="socialicon" id="vimeoIcon" href="<?php echo $vimeo; ?>"  title="Vimeo Profile" rel="nofollow"></a>
		<?php } if (!empty($facebook)) { ?> 
			<a class="socialicon" id="facebookIcon" href="<?php echo $facebook; ?>"  title="Facebook Profile" rel="nofollow"></a>
		<?php } if (!empty($twitter)) { ?> 
			<a class="socialicon" id="twitterIcon" href="<?php echo $twitter; ?>" title="Follow on Twitter"  rel="nofollow"></a>
		<?php } ?>
	</div>
	<?php } ?>
	
	<div id="copyright">
	<!--IMPORTANT! DO NOT REMOVE GOOGLE NOTICE-->
	&copy; <?php echo date('Y '); bloginfo('name'); ?>. <?php _e('Map by Google. Site by Team Scotty Bark');?>
	<!--IMPORTANT! DO NOT REMOVE GOOGLE NOTICE-->
	</div>	

</div><!--end footer-->

<?php wp_footer(); ?>

</body>
</html>