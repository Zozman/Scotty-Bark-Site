<script type="text/javascript">
//<![CDATA[
jQuery.noConflict(); jQuery(document).ready(function(){  
    //VAR SETUP
    <?php
	$mainZoom = get_theme_mod('themolitor_customizer_cat_zoom');
	$toggle = get_theme_mod('themolitor_customizer_mapstyle_onoff');
	$zoomOn = get_theme_mod('themolitor_customizer_mapzoom_onoff');
	$blogCat = get_option('themolitor_blog_category');
	$sitePin = get_theme_mod('themolitor_customizer_pin');
	
	if($zoomOn){$zoomSetting = 'true';}else{$zoomSetting = 'false';}
	?>
		
	jQuery('#footer').prepend('<div class="markerNav" title="<?php _e('Prev','themolitor');?>" id="prevMarker">&lsaquo;</div><div id="markers"></div><div class="markerNav" title="<?php _e('Next','themolitor');?>" id="nextMarker">&rsaquo;</div><?php if($toggle){ ?><div id="mapTypeContainer"><div id="mapStyleContainer"><div id="mapStyle" class="satellite"></div></div><div id="mapType" title="<?php _e('Map Type','themolitor');?>" class="satellite"></div></div><?php } ?>');
		
    jQuery('body').prepend("<div id='target'></div>");
        
        jQuery("#gMap").gmap3({ 
        	action: 'init',
            onces: {
              bounds_changed: function(){
              	var number = 0;
                jQuery(this).gmap3({
                  action:'getBounds', 
                  callback: function (){
                  	<?php 
                  	$customList = new WP_Query();
                  	if($blogCat){
                  		$customList->query('showposts=5000&cat=-'.$blogCat.'');
                  	}else{
                  		$customList->query('showposts=5000'); 
                  	}
                  	if ($customList->have_posts()) : while ($customList->have_posts()) : $customList->the_post(); 
                  	
    				//VAR SETUP
    				$latitude = get_post_meta( $post->ID, 'themolitor_latitude', TRUE );
					$longitude = get_post_meta( $post->ID, 'themolitor_longitude', TRUE );
					$addrOne = get_post_meta( $post->ID, 'themolitor_address_one', TRUE );
					$addrTwo = get_post_meta( $post->ID, 'themolitor_address_two', TRUE );
					$pin = get_post_meta( $post->ID, 'themolitor_pin', TRUE );

					//LEGACY SUPPORT
					$data = get_post_meta( $post->ID, 'key', true );
					$oldLatitude = $data['latitude'];
					$oldLongitude = $data['longitude'];
					$oldAddrOne = $data['address_one'];
					$oldAddrTwo = $data['address_two'];
					$oldPin = $data['pin'];
	
					//CHECK FOR LEGACY IF VARS EMPTY
					if($latitude){} elseif($oldLatitude){$latitude = $oldLatitude;}
					if($longitude){} elseif($oldLongitude){$longitude = $oldLongitude;}
					if($addrOne){} elseif($oldAddrOne){$addrOne = $oldAddrOne;}
					if($addrTwo){} elseif($oldAddrTwo){$addrTwo = $oldAddrTwo;}
					if($pin){} elseif($oldPin){$pin = $oldPin;} else {$pin = $sitePin;}
					
					//GET LAT/LONG FROM ADDRESS
					if (!$latitude && !$longitude && $addrOne && $addrTwo) {
						$addrOneFix = str_replace(" ", "+", $addrOne);
						$addrTwoFix = str_replace(" ", "+", $addrTwo);
						$address = $addrOneFix.'+'.$addrTwoFix;
						$geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false');
						$json = json_decode($geocode);
						$latitude = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
						$longitude = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
					}
                  	
                  	if($latitude && $longitude){
                  	?>
                  	add(jQuery(this), number += 1, "<?php the_title(); ?>", "<?php the_permalink() ?>","<?php if($addrOne && $addrTwo){ echo $addrOne.'<br />'.$addrTwo; } ?>","<?php echo $latitude; ?>","<?php echo $longitude; ?>", '<?php the_post_thumbnail(); ?>','<?php echo $pin;?>');<?php } endwhile; else:?>
                  		alert('<?php _e('You need to add posts to display on the map.','themolitor');?>');
                  	<?php endif; ?>
                  }
                });
              }
            }
          },{ 
			action: 'setOptions', args:[{
				zoom:<?php echo $mainZoom;?>,
				scrollwheel:true,
				disableDefaultUI:false,
				disableDoubleClickZoom:false,
				draggable:true,
				mapTypeControl:false,
				mapTypeId:'roadmap',
				panControl:false,
				scaleControl:false,
				streetViewControl:false,
				zoomControl:<?php echo $zoomSetting;?>,
				zoomControlOptions: {
        			style: google.maps.ZoomControlStyle.LARGE,
        			position: google.maps.ControlPosition.RIGHT_CENTER
    			}
			}]
		});
        function add(jQuerythis, i, title, link, excerpt, lati, longi, img, pin){
          jQuerythis.gmap3({
            action : 'addMarker',
            lat:lati,
            lng:longi,
            options: {icon: new google.maps.MarkerImage(pin)},
            events:{
       			mouseover: function(marker){
          			jQuerythis.css({cursor:'pointer'});
          			jQuery('#markerTitle'+i+'').fadeIn({ duration: 200, queue: false }).animate({bottom:"32px"},{duration:200,queue:false});
          			jQuery('.markerInfo').removeClass('activeInfo').hide();
          			jQuery('#markerInfo'+i+'').addClass('activeInfo').show();
          			jQuery('.marker').removeClass('activeMarker');
          			jQuery('#marker'+i+'').addClass('activeMarker');
      			},
       			mouseout: function(){
          			jQuerythis.css({cursor:'default'});
          			jQuery('#markerTitle'+i+'').stop(true,true).fadeOut(200,function(){jQuery(this).css({bottom:"0"})});
      			},
      			click: function(marker){window.location = link}
   			},
            callback: function(marker){
              var jQuerybutton = jQuery('<div id="marker'+i+'" class="marker"><div id="markerInfo'+i+'" class="markerInfo"><a href="'+link+'">'+img+'</a><h2><a href="'+link+'">'+title+'</a></h2><p>'+excerpt+'</p><a class="markerLink" href="'+link+'"><?php _e('View Details','themolitor');?> &rarr;</a><div class="markerTotal">'+i+' / <span></span></div></div></div>');
              jQuerybutton.mouseover(function(){
                  jQuerythis.gmap3({
                    action:'panTo', 
                    args:[marker.position]
                  });
                  jQuery("#target").stop(true,true).fadeIn(1200).delay(500).fadeOut(1200);
               });
              jQuery('#markers').append(jQuerybutton);
              var numbers = jQuery(".markerInfo").length;
              jQuery(".markerTotal span").html(numbers);
              if(i == 1){
              	jQuery('.marker:first-child').addClass('activeMarker').mouseover();
              }
              jQuerythis.gmap3({
              	action:'addOverlay',
              	content: '<div id="markerTitle'+i+'" class="markerTitle">'+title+'</div>',
              	latLng: marker.getPosition()
               });
            }    		
          });
        }
});
//]]>
</script>