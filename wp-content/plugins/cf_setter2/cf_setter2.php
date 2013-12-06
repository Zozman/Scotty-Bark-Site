<?php
/*
Plugin Name: CF Setter2
Plugin URI: http://hypertext.net/projects/cfsetter/
Description: Allows you to define a custom field value from within the body of a post.
Version: 0.1
Author: Justin Blanton
Author URI: http://hypertext.net
*/

/******************************************* 
This plugin is a modification of my Slugger+
plugin, which you can find at 
http://hypertext.net/projects/spluggerplus 
*******************************************/


/* customField_getValue2
* Reads in the post content, finds the custom field value you want to use and sets it as a global variable.
* @param STRING
* @return STRING
*/
function customField_getValue2($post_content) {
	
	$customFieldValue2 = customField_findValue2($post_content);
	
	if ($customFieldValue2) {
		$GLOBALS['customFieldValue2'] = $customFieldValue2;
	}   
	
	$temp = '/(' . customField_regExEscape2('[cf2]') . '(.*?)' . customField_regExEscape2('[/cf2]') . ')/i';
	$post_content = (preg_replace($temp, '', $post_content));
	
return $post_content;
}

/* customField_setValue2
* Sets the custom field value.
* @param STRING
*/
function customField_setValue2($post_id) {
	global $customFieldValue2;
	// Define the custom field you want this plugin to act on
	$customField = 'themolitor_address_two';
	
	// Insert the custom field value, if it isn't already inserted
	if ($customFieldValue2) {
		add_post_meta($post_id, $customField, $customFieldValue2, true);
	}
}

/* customField_findValue2
* Sifts through the post content, finds the custom field value and returns it
* @param STRING
* @return STRING
*/
function customField_findValue2($text) {
	
	$cfRegEX = '/(' . customField_regExEscape2('[cf2]') . '(.*?)' . customField_regExEscape2('[/cf2]') . ')/i';
	
	preg_match_all($cfRegEX, $text, $matches);
	
	if ($matches) {
		foreach ($matches[2] as $match) {
			if ($match) {
				return $match;
			}
		}
	} else {
		// Do nothing
		return false;
	}
}

/* customField_regExEscape2
* Escapes for the regular expression.
* @param STRING
* @return STRING
*/
function customField_regExEscape2($str) {
	$str = str_replace('\\', '\\\\', $str);
	$str = str_replace('/', '\\/', $str);
	$str = str_replace('[', '\\[', $str);
	$str = str_replace(']', '\\]', $str);

return $str;
}

// Grab the custom field value and save to a global
add_filter('content_save_pre', 'customField_getValue2'); 
// Insert the custom field value into the post's metadata
add_action('save_post', 'customField_setValue2');
?>