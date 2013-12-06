<?php
$key = "key";

$meta_boxes = array(

"zoom" => array(
"name" => "zoom",
"title" => "Zoom Level",
"description" => "Enter a number between 0 and 20, where '0' is maximum zoomed out and '20' is maximum zoomed in."),

"latitude" => array(
"name" => "latitude",
"title" => "Latitude",
"description" => "You can generate this info here: <a target='_blank' href='http://itouchmap.com/latlong.html'>http://itouchmap.com/latlong.html</a>"),

"longitude" => array(
"name" => "longitude",
"title" => "Longitude",
"description" => "You can generate this info here: <a target='_blank' href='http://itouchmap.com/latlong.html'>http://itouchmap.com/latlong.html</a>"),

"address_one" => array(
"name" => "address_one",
"title" => "Address Line 1",
"description" => "Example: '100th ST NW'"),

"address_two" => array(
"name" => "address_two",
"title" => "Address Line 2",
"description" => "Example: 'Seattle, WA 98016'"),

"pin" => array(
"name" => "pin",
"title" => "Custom Marker Image URL",
"description" => "Enter full URL to customize the marker for this post."),

"bg_img" => array(
"name" => "bg_img",
"title" => "Background Image URL",
"description" => "This is used when no latitude and longitude is provided above."),

);
function create_meta_box() {
	global $key;
	if( function_exists( 'add_meta_box' ) ) {
		add_meta_box( 'new-meta-boxes', ' Custom Post Options', 'display_meta_box', 'post', 'normal', 'high' );
		add_meta_box( 'new-meta-boxes', ' Custom Post Options', 'display_meta_box', 'page', 'normal', 'high' );
	}
}
function display_meta_box() {
	global $post, $meta_boxes, $key;
?>
<div class="form-wrap">
<?php wp_nonce_field( plugin_basename( __FILE__ ), $key . '_wpnonce', false, true );
foreach($meta_boxes as $meta_box) {
	$data = get_post_meta($post->ID, $key, true);
?>
<div class="form-field form-required">
	<label for="<?php echo $meta_box[ 'name' ]; ?>"><?php echo $meta_box[ 'title' ]; ?></label>
	<input type="text" name="<?php echo $meta_box[ 'name' ]; ?>" value="<?php echo htmlspecialchars( $data[ $meta_box[ 'name' ] ] ); ?>" />
	<p><?php echo $meta_box[ 'description' ]; ?></p>
</div>
<?php } ?>
</div>
<?php
}
function save_meta_box( $post_id ) {
	global $post, $meta_boxes, $key;
	foreach( $meta_boxes as $meta_box ) {
		$data[ $meta_box[ 'name' ] ] = $_POST[ $meta_box[ 'name' ] ];
	}
	if ( !wp_verify_nonce( $_POST[ $key . '_wpnonce' ], plugin_basename(__FILE__) ) )
	return $post_id;
	if ( !current_user_can( 'edit_post', $post_id ))
	return $post_id;
	update_post_meta( $post_id, $key, $data );
}
add_action( 'admin_menu', 'create_meta_box' );
add_action( 'save_post', 'save_meta_box' );