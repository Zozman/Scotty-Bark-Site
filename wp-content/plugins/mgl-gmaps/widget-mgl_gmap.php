<?php

/* Widget */
class mgl_GMaps extends WP_Widget {


/* Widget Setup */

	function mgl_GMaps() {

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'mgl_GMaps', 'description' => __('Display a Google Map in your sidebar.', 'mgl_gmaps') );

		/* Widget control settings. */
		$control_ops = array( 'id_base' => 'mgl_gmaps' );

		/* Create the widget. */
		$this->WP_Widget( 'mgl_gmaps', 'MaGeek Lab - Google Maps', $widget_ops, $control_ops );
	}


/* Display Widget */

	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
        
        $map = $instance['map'];

		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		echo do_shortcode( $map );

		/* After widget (defined by themes). */
		echo $after_widget;
	}



/* Update Widget */

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] 		= strip_tags( $new_instance['title'] );
		$instance['map'] 		= html_entity_decode($new_instance['map']);        

		
		return $instance;
	}



/*  Widget Settings */

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */

	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
		'title' => '',
        'map' => '',

		);
		$instance = wp_parse_args( (array) $instance, $defaults ); 

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title', 'mgl_gmaps') ?>:</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<a href="#" class="mgl_widget_generator">Open map generator</a>

		<p>
			<input type="hidden" class="widefat mgl_widget_map_hidden" id="<?php echo $this->get_field_id( 'map' ); ?>" name="<?php echo $this->get_field_name( 'map' ); ?>" value="<?php echo  htmlspecialchars($instance['map']); ?>" />
			<em class="mgl_text_center">Always remember to save your widget after closeing the Generator</em>
		</p>

	<?php
	}
}

//Add JS scripts
function mgl_widget_gmap_enqueue_scripts() {
    $file_dir = plugin_dir_url(__FILE__);
    if ('widgets' == get_current_screen() -> id ) {
		
		wp_enqueue_script('generator', plugin_dir_url(__FILE__) . 'gmaps-generator/generator.js', array('jquery','mgl_gmap_api'), '1.0', true);

	    $mgl_gmap_values = array( 'plugin_url' => plugin_dir_url(__FILE__) );
		 // Load default settings
		$mgl_gmaps_settings = get_option('mgl_gmaps', array('address' => 'Barcelona', 'zoom' => 13));

		wp_localize_script( 'generator', 'mgl_gmap_values', $mgl_gmap_values );
		wp_localize_script( 'generator', 'mgl_map_defaults', $mgl_gmaps_settings );
		wp_localize_script( 'generator', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );

		wp_enqueue_style( 'media-views' );
		wp_enqueue_style("mgl_gmaps_admin", plugin_dir_url(__FILE__)."css/mgl_gmaps_admin.css", false, "1.0", "all");
    }
}
add_action('admin_enqueue_scripts', 'mgl_widget_gmap_enqueue_scripts');

?>