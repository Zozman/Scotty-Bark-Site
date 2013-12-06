<?php
get_header();

//VAR SETUP
$siteBg = get_theme_mod('themolitor_customizer_background_url');
$toggle = get_theme_mod('themolitor_customizer_mapstyle_onoff');
$zoomOn = get_theme_mod('themolitor_customizer_mapzoom_onoff');
$crumbs = get_theme_mod('themolitor_customizer_bread_onoff');
$postZoom = get_theme_mod('themolitor_customizer_post_zoom');
$sitePin = get_theme_mod('themolitor_customizer_pin');

if($zoomOn){$zoomSetting = 'true';}else{$zoomSetting = 'false';}

if (have_posts()) : while (have_posts()) : the_post(); 
?>

<div id="main">
	<div id="handle"></div>
	<div id="closeBox"></div>
	
	<h2 class="entrytitle"><?php the_title(); ?><?php edit_post_link(' <small>&#9997;</small>','',' '); ?></h2>
	
	<?php if ($crumbs && function_exists('dimox_breadcrumbs')) dimox_breadcrumbs(); ?>
	
	<div class="entry">
		<?php the_content(); wp_link_pages();?>												
	</div>
		
    <?php if ('open' == $post->comment_status) : ?>     
	<div id="commentToggle" class="toggleButton"><?php comments_number( __('Comments','themolitor'), __('1 Comment','themolitor'), __('% Comments','themolitor') ); ?> <span>+</span></div>	       
    <div class="clear" id="commentsection">
		<?php comments_template(); ?>
    </div>
    <?php endif; ?>
</div><!--end main-->

<?php
//VAR SETUP
$zoom = get_post_meta( $post->ID, 'themolitor_zoom', TRUE );
$latitude = get_post_meta( $post->ID, 'themolitor_latitude', TRUE );
$longitude = get_post_meta( $post->ID, 'themolitor_longitude', TRUE );
$addrOne = get_post_meta( $post->ID, 'themolitor_address_one', TRUE );
$addrTwo = get_post_meta( $post->ID, 'themolitor_address_two', TRUE );
$pin = get_post_meta( $post->ID, 'themolitor_pin', TRUE );
$bg = get_post_meta( $post->ID, 'themolitor_bg_img', TRUE );

//LEGACY SUPPORT
$data = get_post_meta( $post->ID, 'key', true );
$oldZoom = $data['zoom'];
$oldLatitude = $data['latitude'];
$oldLongitude = $data['longitude'];
$oldAddrOne = $data['address_one'];
$oldAddrTwo = $data['address_two'];
$oldPin = $data['pin'];
$oldBg = $data['bg_img'];

//CHECK FOR LEGACY IF VARS EMPTY
if($zoom){} elseif($oldZoom){$zoom = $oldZoom;} else {$zoom = $postZoom;}
if($latitude){} elseif($oldLatitude){$latitude = $oldLatitude;}
if($longitude){} elseif($oldLongitude){$longitude = $oldLongitude;}
if($addrOne){} elseif($oldAddrOne){$addrOne = $oldAddrOne;}
if($addrTwo){} elseif($oldAddrTwo){$addrTwo = $oldAddrTwo;}
if($pin){} elseif($oldPin){$pin = $oldPin;} else {$pin = $sitePin;}
if($bg){} elseif($oldBg){$bg = $oldBg;} else {$bg = $siteBg;}

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
	
if($latitude && $longitude){ //START MAP?>
<script>
jQuery.noConflict(); jQuery(document).ready(function(){
	
	<?php if($toggle){ ?>jQuery("#footer").append('<div id="mapTypeContainer"><div id="mapStyleContainer" class="gradientBorder"><div id="mapStyle"></div></div><div id="mapType" class="roadmap"></div></div>');<?php } ?>
    	
	jQuery("#gMap").gmap3({
    	action: 'addMarker',
    	lat:<?php echo $latitude; ?>,
    	lng:<?php echo $longitude; ?>,
    	marker:{
      		options:{
        		icon: new google.maps.MarkerImage('<?php echo $pin;?>')
      		}
    	},
    	map:{
     	 center: true,
     	 zoom: <?php echo $zoom;?>
   		}
	},{
		action: 'setOptions', args:[{			
			scrollwheel:true,
			disableDefaultUI:false,
			disableDoubleClickZoom:false,
			draggable:true,
			mapTypeControl:false,
			mapTypeId:'satellite',
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
});
</script>
<?php } elseif($bg) { ?>
<script>
jQuery.noConflict(); jQuery(document).ready(function(){
	jQuery.backstretch("<?php echo $bg; ?>", {speed: 150});
});
</script>
<?php } elseif(is_front_page()){ get_template_part('script'); } //END MAP STUFF

endwhile; endif;

get_sidebar();
get_footer(); 
?>