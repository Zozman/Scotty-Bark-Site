<?php
// Field Array
$prefix = 'themolitor_';
 
$postPage = array( 'post', 'page' );
 
$themolitor_post_meta_box = array(
    'id' => 'themolitor-post-meta-box',
    'title' => __('Custom Post Options', 'themolitor'),
    'context' => 'normal',
    'priority' => 'high',
    'fields' => array(
        array(
            'name' => __('Zoom Level', 'themolitor'),
            'desc' => __('Enter a number between 1 and 20, where 1 is maximum zoomed out and 20 is maximum zoomed in.', 'themolitor'),
            'id' => $prefix.'zoom',
            'type' => 'text'
        ),
        array(
            'name' => __('Latitude - optional', 'themolitor'),
            'desc' => __("You can generate this info here: <a target='_blank' href='http://itouchmap.com/latlong.html'>http://itouchmap.com/latlong.html</a>", 'themolitor'),
            'id' => $prefix.'latitude',
            'type' => 'text'
        ),
        array(
            'name' => __('Longitude - optional', 'themolitor'),
            'desc' => __("You can generate this info here: <a target='_blank' href='http://itouchmap.com/latlong.html'>http://itouchmap.com/latlong.html</a>", 'themolitor'),
            'id' => $prefix.'longitude',
            'type' => 'text'
        ),
        array(
            'name' => __('Address Line 1', 'themolitor'),
            'desc' => __('Example: 112 Columbus Avenue', 'themolitor'),
            'id' => $prefix.'address_one',
            'type' => 'text'
        ),
        array(
            'name' => __('Address Line 2', 'themolitor'),
            'desc' => __('Example: Seattle, WA 98016', 'themolitor'),
            'id' => $prefix.'address_two',
            'type' => 'text'
        ),
        array(
            'name' => __('Custom Marker Image URL', 'themolitor'),
            'desc' => __('Enter full URL to customize the marker for this post.', 'themolitor'),
            'id' => $prefix.'pin',
            'type' => 'text'
        ),
        array(
            'name' => __('Background Image URL', 'themolitor'),
            'desc' => __('This is used when no address is provided above.', 'themolitor'),
            'id' => $prefix.'bg_img',
            'type' => 'text'
        )
    )
);

// Custom Meta Box
add_action( 'add_meta_boxes', 'themolitor_project_add_meta');
 
function themolitor_project_add_meta() {
    global $themolitor_post_meta_box;
 
    add_meta_box($themolitor_post_meta_box['id'], $themolitor_post_meta_box['title'], 'themolitor_display_post_meta', $postPage, $themolitor_post_meta_box['context'], $themolitor_post_meta_box['priority']);
 
} // END OF Function: themolitor_project_add_meta

function themolitor_display_post_meta() {
    global $themolitor_post_meta_box, $post;
 
    // Use nonce for verification
    echo '<input type="hidden" name="themolitor_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
 
    echo '<table class="form-table">';
 
    foreach ($themolitor_post_meta_box['fields'] as $field) {
 
        // get current post meta data
        $meta = get_post_meta($post->ID, $field['id'], true);
 
        switch($field['type']) {
 
            // If Text
            case 'text':
                echo '<tr style="border-top:1px solid #eeeeee;">',
                    '<th style="width:25%"><label for="', $field['id'], '"><strong>', $field['name'], '</strong><span style=" display:block; color:#999; line-height: 20px; margin:5px 0 0 0;">'. $field['desc'].'</span></label></th>',
                    '<td>';
                echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : stripslashes(htmlspecialchars(( $field['std']), ENT_QUOTES)), '" size="30" style="width:75%; margin-right: 20px; float:left;" />';
            break;
            
            // textarea  
			case 'textarea':  
    			echo '<textarea name="'.$field['id'].'" id="'.$field['id'].'" cols="60" rows="4">'.$meta.'</textarea> 
        			<br /><span class="description">'.$field['desc'].'</span>';  
			break; 
 			
 			// checkbox  
    		case 'checkbox':  
   			     echo '<tr style="border-top:1px solid #eeeeee;">',
   			     	'<th style="width:25%"><label for="', $field['id'], '"><strong>', $field['name'], '</strong><span style=" display:block; color:#999; line-height: 20px; margin:5px 0 0 0;">'. $field['desc'].'</span></label></th>',
                 	'<td>';
   			     echo '<input type="checkbox" name="'.$field['id'].'" id="'.$field['id'].'" ',$meta ? ' checked="checked"' : '','size="30" style="width:auto; margin-right: 20px; float:left;" />';
            	 
    		break;  
    			
    		// select  
			case 'select':  
    			echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';  
    			foreach ($field['options'] as $option) {  
        			echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';  
    			}  
    			echo '</select><br /><span class="description">'.$field['desc'].'</span>';  
			break; 
        }
 
    }
 
    echo '</table>';
 
} // END Of Function: themolitor_display_post_meta

// Save Meta Data
add_action('save_post', 'themolitor_post_save_data');
 
function themolitor_post_save_data($post_id) {
    global $themolitor_post_meta_box;
 
    // verify nonce
    if (!isset($_POST['themolitor_meta_box_nonce']) || !wp_verify_nonce($_POST['themolitor_meta_box_nonce'], basename(__FILE__))) {
        return $post_id;
    }
 
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
 
    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
 
    foreach ($themolitor_post_meta_box['fields'] as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
 
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    }
 
} // END Of Function: themolitor_post_save_data