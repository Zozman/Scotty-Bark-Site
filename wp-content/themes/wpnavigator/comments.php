<?php
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');
	if ( post_password_required() ) { ?>
		<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.','themolitor');?></p>
<?php return; }?>

<!--IF THERE ARE COMMENTS-->
<?php if ( have_comments() ) : ?>
	<!--<h3 id="comments">Comments</h3>-->

	<ol class="commentlist">
	<?php wp_list_comments('avatar_size=32'); ?>
	</ol>

	<div class="navigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>
<?php endif; ?>

<!--IF COMMENTS ARE OPEN-->
<?php if ('open' == $post->comment_status) : ?>
	<div id="respond">
	
	<h3><?php comment_form_title( __('Leave a Reply','themolitor'), __('Leave a Reply to %s','themolitor') ); ?></h3>

	<div class="cancel-comment-reply">
		<small><?php cancel_comment_reply_link(); ?></small>
	</div>

	<!--IF USER MUST BE LOGGED IN-->
	<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
	<p><?php _e('You must be','themolitor');?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>"><?php _e('logged in','themolitor');?></a> <?php _e('to post a comment.','themolitor');?></p>

	<?php else : ?>

	<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

	<!--IF USER IS LOGGED IN-->
	<?php if ( $user_ID ) : ?>
	<p><?php _e('Logged in as','themolitor');?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out of this account','themolitor');?>"><?php _e('Log out','themolitor');?> &raquo;</a></p>

	<!--IF USER DOESN'T HAVE TO BE LOGGED IN-->
	<?php else : ?>
	<p>
	<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" <?php if ($req) echo "aria-required='true'"; ?> />
	<label for="author"><small><?php _e('Name','themolitor');?> <?php if ($req) _e('(required)','themolitor'); ?></small></label>
	</p>

	<p>
	<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> />
	<label for="email"><small><?php _e('Email','themolitor');?> <?php if ($req) echo  _e('(required)','themolitor'); ?></small></label>
	</p>

	<p>
	<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
	<label for="url"><small><?php _e('Website','themolitor');?></small></label>
	</p>

	<?php endif; ?>

	<p>
	<textarea name="comment" id="comment" tabindex="4"></textarea>
	</p>

	<p>
	<input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit Comment','themolitor');?>" />
	<?php comment_id_fields(); ?>
	</p>

	<?php do_action('comment_form', $post->ID); ?>

	</form>

<?php endif;?>
	</div><!--end respond-->
<?php endif; ?>