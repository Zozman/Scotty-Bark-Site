<?php

function mgl_gmap_generator() {
   $shortcode = stripslashes($_POST['previous_shortcode']);
   $skins = array(
      'roadmap' => 'Default',
      'satellite' => 'Satellite',
      'hybrid' => 'Hybrid',
      'terrain' => 'Terrain',
      'cartoon' => 'Cartoon', 
      'grey' => 'Grey', 
      'bw' => 'Black & White', 
      'night' => 'Night', 
      'night_light' => 'Night light', 
      'retro' => 'Retro', 
      'papiro' => 'Papiro',
      'one_color' => 'One Color'
      );

   $markers_skins = array(
      
      'blue'    => 'Blue',
      'pink'    => 'Pink',
      'orange'  => 'Orange',
      'black'   => 'Black',
      'green'   => 'Green',
      'purple'  => 'Purple',
      'flag_blue'    => 'Blue flag',
      'flag_pink'    => 'Pink flag',
      'flag_orange'  => 'Orange flag',
      'flag_black'   => 'Black flag',
      'flag_green'   => 'Green flag',
      'flag_purple'  => 'Purple flag',
      'default' => 'Default',
    );

   // Get params

   $shortcode_attr = str_replace('[mgl_gmap ', '', $shortcode);
   $shortcode_attr = substr($shortcode_attr, 0, strpos($shortcode_attr, ']'));

   $map_params     = shortcode_parse_atts( $shortcode_attr );
   $markers        = preg_replace('%\[mgl\_gmap[^\]]*\]%s', '', $shortcode);
   $markers        = str_replace('[/mgl_gmap]', '', $markers);

   $countMarkers   = preg_match_all('[/mgl_marker]', $markers, $matches);
   $markers        = explode('[/mgl_marker]', $markers);

   echo '<pre>';
   //print_r($countMarkers);
   echo '</pre>';

   $map_settings = array(
         'mapid'  => '',
         'address'=> '',
         'lat'    => '',
         'long'   => '',
         'width'  => '',
         'height' => '',
         'zoom'   => '',
         'skin'   => '',
         'color'  => ''
      );
   if(is_array($map_params)) {
      $map_settings = array_merge($map_settings, $map_params); 
   }
  
?>
<a href="#" class="media-modal-close"><span class="media-modal-icon"></span></a>
<div class="media-frame-menu">
   <div class="media-menu">
    <span class="mageeklab_brand">MaGeek Lab</span>
         <ul id="mgl_map_generator_menu">
            <li><a class="menu_active" href="#mgl_map_settings">Map</a></li>
            <li><a href="#markersForm">Markers</a></li>
         </ul>
      <div class="sideContent">
         <form action="#" id="mgl_map_settings">
            <div class="field-group">
               <label for="address">Map ID</label>
               <input type="text" name="mapid" placeholder="Map ID" value="<?php echo $map_settings['mapid']; ?>" />
            </div>
            <div class="field-group">
               <label for="address">Address</label>
               <input type="text" id="mgl_address" name="address" placeholder="Address" value="<?php echo $map_settings['address']; ?>" />
            </div>
            <div class="field-group">
               <label for="lat">Lat / Long</label>
               <input type="text" id="mgl_lat" name="lat" placeholder="Lattitude" value="<?php echo $map_settings['lat']; ?>" />
               <input type="text" id="mgl_long" name="long" placeholder="Longitude" value="<?php echo $map_settings['long']; ?>" />
            </div>
            <div class="field-group">
               <label for="zoom">Zoom</label>
               <input type="text" id="mgl_zoom" name="zoom" placeholder="Zoom" value="<?php echo $map_settings['zoom']; ?>" />
            </div>
            <div class="field-group">
               <label for="width">Width</label>
               <input type="text" id="mgl_width" name="width" placeholder="Width" value="<?php echo $map_settings['width']; ?>" />
            </div>
            <div class="field-group">
               <label for="height">Height</label>
               <input type="text" id="mgl_height" name="height" placeholder="Height" value="<?php echo $map_settings['height']; ?>" />
            </div>
            <div class="field-group">
               <label for="skin">Skin</label>
               <select name="skin" id="mgl_skin_switcher">
                  <?php foreach ($skins as $key => $skin): ?>
                     <option value="<?php echo $key; ?>" <?if($map_settings['skin'] == $key) { echo 'selected="selected"'; } ?> ><?php echo $skin; ?></option>
                  <?php endforeach ?>
               </select>
            </div>
            <div class="field-group">
               <label for="height">Color</label>
               <input type="text" id="mgl_skin_one_color" name="color" placeholder="Color" value="<?php echo $map_settings['color']; ?>" />
            </div>
         </form>
         <div id="markersForm">
            <a id="addNewFormMarker" class="button button-large" href="#">Add New Marker</a>
            <div id="markersCont">
                  <?php for($i = ($countMarkers-1); $i >= 0; $i-- ) { 
                     $marker = explode(']', $markers[$i]);
                     $marker_params = str_replace('[mgl_marker', '', $marker[0]);     
                     $marker_params = shortcode_parse_atts( $marker_params );

                     $marker_settings = array(
                           'address'=> '',
                           'lat'    => '',
                           'long'   => '',
                           'icon'   => ''
                        );
                     
                     if(is_array($marker_params)) {
                        $marker_settings = array_merge($marker_settings, $marker_params);
                     }

                     if($marker_settings['icon'] == '') {

                         $selected_icon = plugin_dir_url(__FILE__).'../images/marker_default.png';
                         
                     } else if( array_key_exists($marker_settings['icon'], $markers_skins)) {
                        $selected_icon = plugin_dir_url(__FILE__).'../images/marker_'.$marker_settings['icon'].'.png';
                     } else {
                        $selected_icon = $marker_settings['icon'];
                     }
                     
                  ?>
                  <div id="markerForm_<?php echo $i ?>" class="markerForm">
                       <div class="marker_icon_selector">
                           <div class="marker_skin_container">
                              <a href="" class="marker_skin"><img class="marker_selected" src="<?php echo $selected_icon; ?>" /></a>
                           </div>
                        </div>
                       <strong class="marker_title">Marker <?php echo $i+1 ?></strong>
                       <a class="delete_marker" href="#">X</a>
                       <div class="marker_fields">
                          <input type="hidden" name="marker[<?php echo $i ?>][icon]" class="marker_icon" value="<?php echo $marker_settings['icon']; ?>" />
                          <input type="text" name="marker[<?php echo $i ?>][address]" class="marker_address" placeholder="Address" value="<?php echo $marker_settings['address']; ?>" />
                          <input type="text" name="marker[<?php echo $i ?>][lat]" class="marker_lat" placeholder="Latitude" value="<?php echo $marker_settings['lat']; ?>" />
                          <input type="text" name="marker[<?php echo $i ?>][long]" class="marker_long" placeholder="Longitude" value="<?php echo $marker_settings['long']; ?>" />
                          <textarea class="marker_content" placeholder="Your text here" ><?php echo $marker[1] ?></textarea>
                       </div>
                    </div>
                  <?php } ?>
            </div>
            <div id="markers_skins" class="marker_skins">
              <?php foreach ($markers_skins as $key => $marker_skin) { ?>
                <a class="marker_skin_selector" href="<?php echo $key ?>" title="<?php echo $marker_skin; ?>"><img src="<?php echo plugin_dir_url(__FILE__); ?>../images/marker_<?php echo $key ?>.png" /></a>
             <?php } ?>
             <input type="text" class="marker_skin_custom" placeholder="Custom icon url" />
            </div>
         </div>
      </div>
   </div>
</div>
<div class="media-frame-title">
   <h1>Google Maps generator</h1>
</div>
<div class="media-frame-router">
  <em>Creating awesome Google Maps is easy</em>
</div>
<div class="media-frame-content">
  <?php
   $map_styles = ''; 
   if($map_settings['width'] != '') { $map_styles .= ' width:'.$map_settings['width'].';'; }
   if($map_settings['height'] != '') { $map_styles .= ' height:'.$map_settings['height'].';'; }
  ?> 
   <div id="mgl_map_generator" class="mgl_map" style="<?php echo $map_styles; ?>"></div>
</div>
<div class="media-frame-toolbar">
   <div class="media-toolbar">
      <div class="media-toolbar-primary">
         <a href="#" class="button media-button button-primary button-large media-button-insert mgl-insert-map">Insert map</a>
      </div>
   </div>
</div>

<?php
die();
}
?>