<?php 
/*
Template Name: Submission Form
*/

//VAR SETUP
$siteBg = get_theme_mod('themolitor_customizer_background_url');
$crumbs = get_theme_mod('themolitor_customizer_bread_onoff');
$sendEmail = get_theme_mod('themolitor_send_email');
$altEmail = get_theme_mod('themolitor_alt_email');
$blogCat = get_option('themolitor_blog_category');

//ERROR VAR RESET
$postNameError = '';
$postEmailError = '';
$postTitleError = '';
$postAddrOneError = '';
$postAddrTwoError = '';
$postContentError = '';
$postTestError = '';
$confirmation = '';

if ( isset( $_POST['submitted'] ) && isset( $_POST['post_nonce_field'] ) && wp_verify_nonce( $_POST['post_nonce_field'], 'post_nonce' ) ) {

	//VAR SETUP
	$postName = $_POST['postName'];
	$postEmail = $_POST['postEmail'];
	$postTitle = $_POST['postFormTitle'];
	$postContent = $_POST['postContent'];
	$postCat = $_POST['cat'];
	$postTags = $_POST['postTags'];
	$postTest = $_POST['postTest'];
	$postAddrOne = $_POST['postAddrOne'];
	$postAddrTwo = $_POST['postAddrTwo'];
 
 	//REQUIRED CHECK
    if (trim($postName) == '') {$postNameError = 'Required';}
    if (trim($postEmail) == '') {$postEmailError = 'Required';}
    if (trim($postTitle) == '') {$postTitleError = 'Required';}
    if (trim($postContent) == '') {$postContentError = 'Required';}
    if (trim($postAddrOne) == '') {$postAddrOneError = 'Required';}
    if (trim($postAddrTwo) == '') {$postAddrTwoError = 'Required';}
    if (trim($postTest) != '102') {$postTestError = 'Required';}
 	
 	//WP INSERT POST SETTINGS
    $post_information = array(
        'post_title' => wp_strip_all_tags( $postTitle ),
        'post_content' => $postContent,
        'post_type' => 'post',
        'post_status' => 'pending',
        'tags_input'     => $postTags,
        'post_category'  => array($eventCat,$postCat)
    );
 
 	//GET POST ID
    $post_id = wp_insert_post($post_information);

	if($post_id) {
		$confirmation = '"'.$postTitle.'" '.__('has successfully been subitted for review.','themolitor');
	
    	//UPDATE CUSTOM META
    	if(isset($postAddrOne)){update_post_meta($post_id, 'themolitor_address_one', esc_attr(strip_tags($postAddrOne)));}
    	if(isset($postAddrTwo)){update_post_meta($post_id, 'themolitor_address_two', esc_attr(strip_tags($postAddrTwo)));}
    	
    	//IF EMAIL NOTIFICATION ON
    	if(isset($sendEmail)){
    		//EMAIL VAR SETUP
    		$pendingUrl = admin_url('edit.php?post_status=pending&post_type=post');
    		$postEdit = admin_url('post.php?post='.$post_id.'&action=edit');
    		$optionsUrl = admin_url('customize.php');
    		$blogName = get_option('blogname');
    		$message = $postName." (".$postEmail.") ".__('has submitted a new post on','themolitor')." ".$blogName." ".__('titled','themolitor')." '".$postTitle."'.\n\n";
    		$message .= __('Review','themolitor')." '".$postTitle."' ".__('here','themolitor').": ".$postEdit."\n\n";
    		$message .= __('Review all pending posts here','themolitor').": ".$pendingUrl."\n\n";
    		$message .= __('You can turn off notifications like this on the "Front-end Submission Form" tab here','themolitor').": ".$optionsUrl; 
		
			//SEND EMAIL NOTICE TO ADMIN
			wp_mail($altEmail, $blogName.' '.__('Post Pending Review','themolitor').': "'.$postTitle.'"', $message);
  		}  		
	}
}

get_header(); 

if (have_posts()) : while (have_posts()) : the_post(); 

//VAR SETUP
$bg = get_post_meta( $post->ID, 'themolitor_bg_img', TRUE );
if($bg){} elseif($siteBg) {$bg = $siteBg;}
?>

<div id="main">
	<div id="handle"></div>
	<div id="closeBox"></div>
	
	<h2 class="entrytitle"><?php the_title(); ?><?php edit_post_link(' <small>&#9997;</small>','',' '); ?></h2>
	
	<?php if ($crumbs && function_exists('dimox_breadcrumbs')) dimox_breadcrumbs(); ?>
	
	<div class="entry">
		<?php 
		the_content(); 
					
		if ( !post_password_required() ) { ?>
		
		<form id="primaryPostForm" method="POST">
					
       		<p><!--NAME-->
       		<label for="postName"><?php _e('Your Name', 'themolitor') ?><span class="red">*</span></label><?php if ($postNameError != '') { ?> <span class="error"><?php echo $postNameError; ?></span><?php } ?><br />
       		<input type="text" name="postName" id="postName" class="required" value="<?php if(isset($_POST['postName']) && $_SERVER['REQUEST_METHOD'] != "POST"){ echo $_POST['postName']; } ?>" /><br />
			</p>
			
       		<p><!--EMAIL-->
       		<label for="postEmail"><?php _e('Your Email', 'themolitor') ?><span class="red">*</span></label><?php if ($postEmailError != '') { ?> <span class="error"><?php echo $postEmailError; ?></span><?php } ?>&nbsp;&nbsp;<span class="formExample">- <?php _e('will not be published','themolitor');?></span><br />
       		<input type="email" name="postEmail" id="postEmail" class="required" value="<?php if(isset($_POST['postEmail']) && $_SERVER['REQUEST_METHOD'] != "POST"){ echo $_POST['postEmail']; } ?>" /><br />
			</p>
						
       		<p><!--TITLE-->
       		<label for="postFormTitle"><?php _e('Location Title', 'themolitor') ?><span class="red">*</span></label><?php if ($postTitleError != '') { ?> <span class="error"><?php echo $postTitleError; ?></span><?php } ?><br />
       		<input type="text" name="postFormTitle" id="postFormTitle" class="required" value="<?php if(isset($_POST['postTitle']) && $_SERVER['REQUEST_METHOD'] != "POST"){ echo $_POST['postTitle']; } ?>" /><br />
			</p>
						
			<p><!--ADDRESS ONE-->
			<label for="postAddrOne"><?php _e('Address Line 1', 'themolitor') ?><span class="red">*</span></label><?php if ($postAddrOneError != '') { ?> <span class="error"><?php echo $postAddrOneError; ?></span><?php } ?>&nbsp;&nbsp;<span class="formExample">- <?php _e('112 Columbus Avenue','themolitor');?></span><br />
			<input type="text" name="postAddrOne" id="postAddrOne" class="required" value="<?php if(isset($_POST['postAddrOne']) && $_SERVER['REQUEST_METHOD'] != "POST"){ echo $_POST['postAddrOne']; }?>" />
			</p>
			
			<p><!--ADDRESS TWO-->
			<label for="postAddrTwo"><?php _e('Address Line 2', 'themolitor') ?><span class="red">*</span></label><?php if ($postAddrTwoError != '') { ?> <span class="error"><?php echo $postAddrTwoError; ?></span><?php } ?>&nbsp;&nbsp;<span class="formExample">- <?php _e('Seattle, WA 98016','themolitor');?></span><br />
			<input type="text" name="postAddrTwo" id="postAddrTwo" class="required" value="<?php if(isset($_POST['postAddrTwo']) && $_SERVER['REQUEST_METHOD'] != "POST"){ echo $_POST['postAddrTwo']; }?>" />
			</p>
						
			<p><!--CATEGORY-->
			<label for="cat"><?php _e('Category', 'themolitor') ?></label><br />
			<?php wp_dropdown_categories( 'show_option_none='.__('Select','themolitor').'&taxonomy=category&exclude='.$blogCat); ?>
			</p>			
 			
 			<p><!--TAGS-->
 			<label for="postTags"><?php _e('Keyword Tags', 'themolitor') ?></label>&nbsp;&nbsp;<span class="formExample">- <?php _e('separated, by, comma','themolitor');?></span><br />
 			<input type="text" name="postTags" id="postTags" value="<?php if(isset($_POST['postTags']) && $_SERVER['REQUEST_METHOD'] != "POST"){ echo $_POST['postTags']; }?>" />
 			</p>
 			 			
 			<p><!--CONTENT-->
       		<label for="postContent"><?php _e('Details', 'themolitor') ?><span class="red">*</span></label><?php if ($postContentError != '') { ?> <span class="error"><?php echo $postContentError; ?></span><?php } ?><br />
 			<textarea name="postContent" id="postContent" rows="8" cols="30" class="required"><?php if(isset( $_POST['postContent']) && $_SERVER['REQUEST_METHOD'] != "POST"){ if(function_exists('stripslashes')){ echo stripslashes($_POST['postContent']); } else { echo $_POST['postContent'];} } ?></textarea>
 			</p>
 			 			 			
 			<p><!--TEST-->
 			<label for="postTest">100 + <?php _e('Two', 'themolitor') ?> = <span class="red">*</span></label>
 			<input type="text" name="postTest" id="postTest" value="<?php if(isset($_POST['postTest']) && $_SERVER['REQUEST_METHOD'] != "POST"){ echo $_POST['postTest']; }?>" class="required" /><?php if ($postTestError != '') { ?> <span class="error"><?php _e('Required','themolitor');?></span><?php } ?>
 			</p>
 			
 			<p><!--SUBMIT-->
       		<input type="hidden" name="submitted" id="submitted" value="true" />
       		<?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>
       		<input id="postSubmit" type="submit" value="<?php _e('Submit for Review', 'themolitor') ?>" /> 
       		</p>
       		
 		</form><!--end form-->
 		<?php } ?>
 		
		<div class="clear"></div>
    </div><!--end entry-->
       	
	<div class="clear"></div>
</div><!--end main-->
	
<?php if($bg) { ?>
<script>
jQuery.noConflict(); jQuery(document).ready(function(){
	jQuery.backstretch("<?php echo $bg; ?>", {speed: 150});
});
</script>
<?php }
endwhile; endif;
get_sidebar();
get_footer(); 
if($confirmation){echo "<script type='text/javascript'>alert('".$confirmation."');</script>";}
?>