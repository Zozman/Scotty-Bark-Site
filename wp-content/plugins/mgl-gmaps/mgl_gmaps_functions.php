<?php

class Mgl_shortcode_gmaps {
	static $add_script;
	static $scripts = array();
	static $markers = array();

	static function init() {
		add_shortcode('mgl_gmap', array(__CLASS__, 'handle_shortcode'));
		add_shortcode('mgl_marker', array(__CLASS__, 'handle_marker_shortcode'));
		add_action('init', array(__CLASS__, 'register_script'));
		add_action('wp_footer', array(__CLASS__, 'print_script'));
		add_action('init', array(__CLASS__, 'mgl_add_button_mageeklab_gmaps')); 
	}

	static function handle_shortcode($atts, $cont = null) {
		self::$add_script = true;

		$mgl_gmaps_settings = get_option('mgl_gmaps', array('address' => 'Barcelona', 'zoom' => 13));

		extract(shortcode_atts(array(
			'mapid'		=> 'mgl_gmap',
	        'address'	=> '',
		 	'lat'		=> false,
			'long'		=> false,
			'zoom'		=> 13,
			'width' 	=> '100%',
			'height'	=> '300px',
			'skin'		=> '',
			'color'		=> ''
		), $atts));

		if(!$mapid){
			return __('Shortcode error: MAP ID is needed. Set the attribute mapID in the shortcode.', 'mgl_gmaps');
		}else{
				// If we don't have address or lat, load the default one
				if(!$address && !$lat){
					$mgl_gmaps_settings = get_option('mgl_gmaps', array('address' => 'Barcelona', 'zoom' => 13));
					$address = $mgl_gmaps_settings['address'];

				}

				//Addres or coordenates are a valid position.
				$location = '';

				if($address){
					$location = 'address: "' . $address . '"';
				}else{
					$location = 'latitude: ' . $lat . ',longitude: ' . $long;
				}

				//Marker
				//Looks for shortcodes inside 
				$markerCode = '';
				$pattern = get_shortcode_regex();

			    if (   preg_match_all( '/'. $pattern .'/s', $cont, $matches )
			        && array_key_exists( 2, $matches )
			        && in_array( 'mgl_marker', $matches[2] ) )
			    {
			    	//Execute shortcodes
			        do_shortcode( $cont );
			        $markerCode = ', markers:[';

			        //print_r(self::$markers);
			        
			        //Write javascript code for each Marker
			        $count_markers = 0;
			        foreach (self::$markers as $marker) {
			        	if($count_markers != 0) { $markerCode .= ', '; }

			        	//If marker doesn't have coordenates or address, we get it from map
			        	if(isset($lat)) { $map_lat = $lat; } else { $map_lat = ''; }
			        	if(isset($long)) { $map_long = $long; } else { $map_long = ''; }
			        	if(isset($address)) { $map_address = $address; } else { $map_address = ''; }

			        	$markerCode .= self::create_marker($marker,$map_lat, $map_long, $map_address);

			        	$count_markers++;
			        }

			        $markerCode .= ']';

			    } else {
			    	// Not shortcode detected, there are no markers
			    	$markerCode = '';
			    }

				//Return code
				$html = '
					<style>.mgl_gmaps img {max-width:inherit !important;}</style>
					<div id="'. $mapid . '" style="width:' . $width . ';height:' . $height . '" class="mgl_gmaps"></div>
					<script type="text/javascript">
						jQuery(document).ready(function($){
							$("#' . $mapid . '").gMap({
								controls: { mapTypeControl: false, zoomControl: true, panControl: true, streetViewControl: true },
								zoom: ' . $zoom . ',
								' . $location . '
								' . $markerCode . '

							});
							
							  

						     
							
						});
					</script>
				';

				if($skin != '' && $color == '') {
					// Load skin
					switch($skin) {
				        case 'satellite':
				        case 'roadmap':
				        	 $html .= '
							<script type="text/javascript">
							jQuery(document).ready(function($){
						        $("#' . $mapid . '").data("gMap.reference").setMapTypeId("'.$skin.'");
						        });
							</script>';
				        break;
				        case 'hybrid':
				        	$html .= '
							<script type="text/javascript">
							jQuery(document).ready(function($){
						        $("#' . $mapid . '").data("gMap.reference").setMapTypeId("google.maps.MapTypeId.HYBRID");
						        });
							</script>';
				        break;
				         case 'terrain':
				         	$html .= '
							<script type="text/javascript">
							jQuery(document).ready(function($){
						        $("#' . $mapid . '").data("gMap.reference").setMapTypeId("google.maps.MapTypeId.TERRAIN");
						        });
							</script>';
				        break;
				        default:
				            $html .= '
							<script type="text/javascript">
							jQuery(document).ready(function($){'.
								self::select_skin($skin).' 
								var '.$skin.'styledMap = new google.maps.StyledMapType('.$skin.'_styles, {name: "'.$skin.'"});
						        // Setup skin for the map
						        $("#' . $mapid . '").data("gMap.reference").mapTypes.set("'.$skin.'_style", '.$skin.'styledMap);
						        $("#' . $mapid . '").data("gMap.reference").setMapTypeId("'.$skin.'_style");
						        });
							</script>';
				        break;
				    }
					

				} elseif($color != '') {
					$color_id = substr($color, 1);
					$html .= '
					<script type="text/javascript">
					jQuery(document).ready(function($){'.
						self::setup_color_skin($color_id).' 
						var one_color_'.$color_id.'styledMap = new google.maps.StyledMapType(one_color_'.$color_id.'_styles, {name: "one_color_'.$color_id.'"});
				        // Setup skin for the map
				        $("#' . $mapid . '").data("gMap.reference").mapTypes.set("one_color_'.$color_id.'_style", one_color_'.$color_id.'styledMap);
				        $("#' . $mapid . '").data("gMap.reference").setMapTypeId("one_color_'.$color_id.'_style");
				        });
					</script>';
				}
				// Reset the markers array
				self::$markers = array();

				// Return map
				return $html;
			}
	}

	static function handle_marker_shortcode($atts, $cont = null) {
		extract(shortcode_atts(array(
			
	        'address'	=> false,
		 	'lat'		=> false,
			'long'		=> false,
			'icon'		=> false,
			'iconsize'	=> false,
			'iconanchor'=> false
		), $atts));

		self::$markers[] = array(
			'address' => $address,
			'lat'		=> $lat,
			'long'		=> $long,
			'cont'		=> $cont,
			'icon'		=> $icon,
			'iconsize'  => $iconsize,
			'iconanchor'=> $iconanchor
			);
	}

	//Generate the markers javascript code
	static function create_marker($marker, $lat, $long, $address) {
		
		//Set the marker's location
		if($marker['address'] != '' || $marker['lat'] != '' ){

			// Get marker coords

			if($marker['address'] != ''){
				$location = 'address: "' . $marker['address'] . '"';
			}else{
				$location = 'latitude: ' . $marker['lat'] . ',longitude: ' . $marker['long'];
			}
		} else {

			// Get map coords
			if($address != ''){
				$location = 'address: "' . $address . '"';
			}else{
				$location = 'latitude: ' . $lat . ',longitude: ' . $long;
			}
		}

		$html = '';
		if($marker['cont'] != '') {
			$html = ",html:'<div class=\"mgl_infowindow_content\">" . addslashes($marker['cont']) . "</div>'" ;
		}

		if($marker['icon']) { 
			
			$markers_skins = array('blue','pink','orange','black','green','purple','flag_blue','flag_pink','flag_orange','flag_black','flag_green','flag_purple');
			
			if(in_array($marker['icon'], $markers_skins)) { $marker_icon = plugin_dir_url(__FILE__).'/images/marker_'.$marker['icon'].'.png'; } else { $marker_icon = $marker['icon']; }
			
			$iconCode =  ',icon: {';

				$iconCode .= "image: '" . $marker_icon . "'";
				
				list($marker_width, $marker_height, $type, $attr) = getimagesize($marker_icon);

				if($marker['iconsize']) { $iconCode .= ", iconsize: [" . $marker['iconsize'] . "]"; } else { $iconCode .= ", iconsize: [" . $marker_width .",". $marker_height . "]"; }
				if($marker['iconanchor']) { $iconCode .= ", iconanchor: [" . $marker['iconanchor'] . "]"; } else { $iconCode .= ", iconanchor: [" . $marker_width / 2 . ", ".$marker_height." ]"; }

			$iconCode .= '}';
		}

		

		$result = '{';
		$result .= $location;

		if($html != '' ) $result .= $html;
		if(isset($marker_icon)) $result .= $iconCode;
		$result .= '}';
		return $result;
	}

	static function select_skin($skinname) {
		switch ($skinname) {
			case 'cartoon':

				$skin =  '// Cartoon
						      var cartoon_styles = [ 
						          { "featureType": "landscape", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "transit", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "poi.park", "elementType": "labels", "stylers": [ { "visibility": "off" }] 
						        },{ "featureType": "poi.park", "elementType": "geometry.fill", "stylers": [ { "color": "#d3d3d3" }, { "visibility": "on" } ]
						        },{ "featureType": "road", "elementType": "geometry.stroke", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "landscape", "stylers": [ { "visibility": "on" }, { "color": "#b1bc39" } ]
						        },{ "featureType": "landscape.man_made", "stylers": [ { "visibility": "on" }, { "color": "#ebad02" } ] 
						        },{ "featureType": "water", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#416d9f" } ] 
						        },{ "featureType": "road", "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" }, { "color": "#000000" } ]
						        },{ "featureType": "road", "elementType": "labels.text.stroke", "stylers": [ { "visibility": "off" }, { "color": "#ffffff" } ]
						        },{ "featureType": "administrative", "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" }, { "color": "#000000" } ]
						        },{ "featureType": "road", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#ffffff" } ] 
						        },{ "featureType": "road", "elementType": "labels.icon", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "water", "elementType": "labels", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "poi", "elementType": "geometry.fill", "stylers": [ { "color": "#ebad02" } ]
						        },{ "featureType": "poi.park", "elementType": "geometry.fill", "stylers": [ { "color": "#8ca83c" } ]
						        } 
						      ];';
				break;

			case 'grey':

				$skin = '// Grey Scale
						      var grey_styles = [ 
						          { "featureType": "road.highway", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "landscape", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "transit", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "poi", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "poi.park", "stylers": [ { "visibility": "on" } ] 
						        },{ "featureType": "poi.park", "elementType": "labels", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "poi.park", "elementType": "geometry.fill", "stylers": [ { "color": "#d3d3d3" }, { "visibility": "on" } ]
						        },{ "featureType": "poi.medical", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "poi.medical", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "road", "elementType": "geometry.stroke", "stylers": [ { "color": "#cccccc" } ] 
						        },{ "featureType": "water", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#cecece" } ] 
						        },{ "featureType": "road.local", "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" }, { "color": "#808080" } ]
						        },{ "featureType": "administrative", "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" }, { "color": "#808080" } ]
						        },{ "featureType": "road", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#fdfdfd" } ] 
						        },{ "featureType": "road", "elementType": "labels.icon", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "water", "elementType": "labels", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "poi", "elementType": "geometry.fill", "stylers": [ { "color": "#d2d2d2" } ]
						        } 
						      ];';

				break;
			
			case 'bw':

				$skin = '// Black & White	
							var bw_styles = [ 
						          { "featureType": "road.highway", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "landscape", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "transit", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "poi.park", "elementType": "labels", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "poi.park", "elementType": "geometry.fill",  "stylers": [ { "color": "#d3d3d3" }, { "visibility": "on" } ]
						        },{ "featureType": "road", "elementType": "geometry.stroke", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "landscape", "stylers": [ { "visibility": "on" }, { "color": "#ffffff" } ] 
						        },{ "featureType": "water", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#cecece" } ] 
						        },{ "featureType": "road", "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" }, { "color": "#000000" } ]
						        },{ "featureType": "road", "elementType": "labels.text.stroke", "stylers": [ { "visibility": "on" }, { "color": "#ffffff" } ]
						        },{ "featureType": "administrative", "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" }, { "color": "#000000" } ]
						        },{ "featureType": "road", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#000000" } ] 
						        },{ "featureType": "road", "elementType": "labels.icon", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "water", "elementType": "labels", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "poi", "elementType": "geometry.fill", "stylers": [ { "visibility": "off" } ]
						        } 
						      ];';

				break;

			case 'night':

				$skin = '// Night	
							var night_styles = [ 
						        { "featureType": "landscape", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "transit", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "poi.park", "elementType": "labels", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "poi.park", "elementType": "geometry.fill", "stylers": [ { "color": "#d3d3d3" }, { "visibility": "on" } ]
						        },{ "featureType": "road", "elementType": "geometry.stroke", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "landscape", "stylers": [ { "visibility": "on" }, {  "hue": "#0008ff" }, { "lightness": -75 }, { "saturation": 10 } ]
						        },{ "elementType": "geometry.stroke", "stylers": [ { "color": "#1f1d45" } ]
						        },{ "featureType": "landscape.natural", "stylers": [ { "color": "#1f1d45" } ]
						        },{ "featureType": "water", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#01001f" } ] 
						        },{ "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" }, { "color": "#e7e8ec" } ]
						        },{ "elementType": "labels.text.stroke", "stylers": [ { "visibility": "on" }, { "color": "#151348" } ]
						        },{ "featureType": "administrative", "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" }, { "color": "#f7fdd9" } ]
						        },{ "featureType": "administrative", "elementType": "labels.text.stroke", "stylers": [ { "visibility": "on" }, { "color": "#01001f" } ]
						        },{ "featureType": "road", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#316694" } ] 
						        },{ "featureType": "road", "elementType": "labels.icon", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "water", "elementType": "labels", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "poi", "elementType": "geometry.fill", "stylers": [ { "color": "#1a153d" } ]
						        
						        } 
						      ];';

				break;
			
			case 'night_light':

				$skin = '// Night Light
						      var night_light_styles = [ 
						          {"elementType": "geometry", "stylers": [ { "visibility": "on" }, { "hue": "#232a57" } ]
						        },{ "featureType": "road.highway", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "landscape", "elementType": "geometry.fill", "stylers": [ { "hue": "#0033ff" }, { "saturation": 13 }, { "lightness":-77 } ]
						        },{ "featureType": "landscape", "elementType": "geometry.stroke", "stylers": [ { "color": "#4657ab" } ] 
						        },{ "featureType": "transit", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "road", "elementType": "geometry.stroke", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "water", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#0d0a1f" } ] 
						        },{ "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" }, { "color": "#d2cfe3" } ]
						        },{ "elementType": "labels.text.stroke", "stylers": [ { "visibility": "on" }, { "color": "#0d0a1f" } ]
						        },{ "featureType": "administrative", "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" }, { "color": "#ffffff" } ]
						        },{ "featureType": "administrative", "elementType": "labels.text.stroke", "stylers": [ { "visibility": "on" }, { "color": "#0d0a1f" } ]
						        },{ "featureType": "road", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#ff9910" } ] 
						        },{ "featureType": "road.local", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#4657ab" } ] 
						        },{ "featureType": "road", "elementType": "labels.icon", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "water", "elementType": "labels", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "poi", "elementType": "geometry.fill", "stylers": [ { "color": "#232a57" } ]
						        },{ "featureType": "poi.park", "elementType": "geometry.fill", "stylers": [ { "color": "#232a57" } ]
						        },{ "featureType": "poi", "elementType": "labels", "stylers": [ { "visibility": "off" } ]
						        } 
						      ];';

				break;

			case 'retro':

				$skin = '// Retro
						       var retro_styles = [ 
						        { "featureType": "transit", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "poi.park", "elementType": "geometry.fill", "stylers": [ { "color": "#d3d3d3" }, { "visibility": "on" } ]
						        },{ "featureType": "road", "elementType": "geometry.stroke", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "landscape", "stylers": [ { "visibility": "on" }, { "color": "#eee8ce" } ] 
						        },{ "featureType": "water", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#b8cec9" } ] 
						        },{ "featureType": "road", "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" }, { "color": "#000000" } ]
						        },{ "featureType": "road", "elementType": "labels.text.stroke", "stylers": [ { "visibility": "off" }, { "color": "#ffffff" } ]
						        },{ "featureType": "administrative", "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" }, { "color": "#000000" } ]
						        },{ "featureType": "road", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#ffffff" } ] 
						        },{ "featureType": "road", "elementType": "geometry.stroke", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "road", "elementType": "labels.icon", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "water", "elementType": "labels", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "poi", "elementType": "geometry.fill", "stylers": [ { "color": "#d3cdab" } ]
						        },{ "featureType": "poi.park", "elementType": "geometry.fill", "stylers": [ { "color": "#ced09d" } ]
						        },{ "featureType": "poi", "elementType": "labels", "stylers": [ { "visibility": "off" } ]
						        } 
						      ];';

				break;
				
			case 'papiro':

				$skin = '// Papiro
						      var papiro_styles = [ 
						          {"elementType": "geometry", "stylers": [ { "visibility": "on" }, { "color": "#f2e48c" } ]
						        },{ "featureType": "road.highway", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "transit", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "poi.park", "elementType": "labels", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "poi.park", "elementType": "geometry.fill",  "stylers": [ { "color": "#d3d3d3" }, { "visibility": "on" } ]
						        },{ "featureType": "road", "elementType": "geometry.stroke", "stylers": [ { "visibility": "off" } ] 
						        },{ "featureType": "landscape", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#f2e48c" } ] 
						        },{ "featureType": "landscape", "elementType": "geometry.stroke", "stylers": [ { "visibility": "on" }, { "color": "#592c00" } ] 
						        },{ "featureType": "water", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#a77637" } ] 
						        },{ "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" }, { "color": "#592c00" } ]
						        },{ "elementType": "labels.text.stroke", "stylers": [ { "visibility": "on" }, { "color": "#f2e48c" } ]
						        },{ "featureType": "administrative", "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" }, { "color": "#592c00" } ]
						        },{ "featureType": "administrative", "elementType": "labels.text.stroke", "stylers": [ { "visibility": "on" }, { "color": "#f2e48c" } ]
						        },{ "featureType": "road", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#a5630f" } ] 
						        },{ "featureType": "road.highway", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" }, { "color": "#592c00" } ] 
						        },{ "featureType": "road", "elementType": "labels.icon", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "water", "elementType": "labels", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "poi", "elementType": "geometry.fill", "stylers": [ { "visibility": "off" } ]
						        },{ "featureType": "poi", "elementType": "labels", "stylers": [ { "visibility": "off" } ] 
						        } 
						      ];';

				break;

			default:
				return ;
				break;
		}

		return $skin;
	}

	static function setup_color_skin($color) {
		$skin = '
		var one_color_'.$color.'_styles = [ 
	        { "stylers": [ { "hue": "#'.$color.'" }, { "saturation": 1 }, { "lightness": 1 } ] }
	      ]; ';

	    return $skin;
	}

	static function register_script() {
		wp_register_script('mgl_gmap_api', 'http://maps.google.com/maps/api/js?sensor=true', array('jquery'), '1.0', true);
		self::$scripts[] = 'mgl_gmap_api';

		wp_register_script('mgl_gmap', plugin_dir_url(__FILE__) . 'js/jquery.gmap.min.js', array('jquery','mgl_gmap_api'), '1.0', true);
		self::$scripts[] = 'mgl_gmap';


	}

	static function print_script() {
		if ( ! self::$add_script )
			return;

		foreach(self::$scripts as $script){

			if(!(wp_script_is($script, 'printed') && wp_script_is( $script, 'queue' ))){

				//Print the script
				wp_print_scripts($script);
			}
		}
		
	}

	static function mgl_add_button_mageeklab_gmaps() {
   		if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )
		   {
		     add_filter('mce_external_plugins', array(__CLASS__, 'mgl_add_mageeklab_gmaps_plugin'));
		     add_filter('mce_buttons', array(__CLASS__, 'mgl_register_mageeklab_gmaps_button'));

		     

		     wp_enqueue_script('mgl_gmap_api', 'http://maps.google.com/maps/api/js?sensor=true', array('jquery'), '1.0', true);

		     wp_enqueue_script('mgl_gmap', plugin_dir_url(__FILE__) . 'js/jquery.gmap.min.js', array('jquery','mgl_gmap_api'), '1.0', true);

		     wp_enqueue_style("mgl_gmaps_styles", plugin_dir_url(__FILE__)."css/mgl_gmaps_styles.css", false, "1.0", "all");

		     if(is_admin()) {
			     wp_enqueue_style("mgl_gmaps_admin", plugin_dir_url(__FILE__)."css/mgl_gmaps_admin.css", false, "1.0", "all");
			     wp_enqueue_script('generator', plugin_dir_url(__FILE__) . 'gmaps-generator/generator.js', array('jquery','mgl_gmap_api'), '1.0', true);

			     $mgl_gmap_values = array( 'plugin_url' => plugin_dir_url(__FILE__) );
	    		 // Load default settings
	    		 $mgl_gmaps_settings = get_option('mgl_gmaps', array('address' => 'Barcelona', 'zoom' => 13));

	    		 wp_localize_script( 'generator', 'mgl_gmap_values', $mgl_gmap_values );
	    		 wp_localize_script( 'generator', 'mgl_map_defaults', $mgl_gmaps_settings );
	    		 wp_localize_script( 'generator', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
    		}

		   }
	}

	static function mgl_register_mageeklab_gmaps_button($buttons) {
	   array_push($buttons, "MageekLab_Colorfull_GoogleMaps");
	   return $buttons;
	}

	static function mgl_add_mageeklab_gmaps_plugin($plugin_array) {
	   $plugin_array['MageekLab_Colorfull_GoogleMaps'] = plugin_dir_url(__FILE__).'gmaps-generator/generator.js';
	   return $plugin_array;
	}
}

Mgl_shortcode_gmaps::init();