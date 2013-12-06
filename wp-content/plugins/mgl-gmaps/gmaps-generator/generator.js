(function($){
    // If we have editor load the button
    if (typeof tinymce != 'undefined') {
        (function() {
            tinymce.create('tinymce.plugins.MageekLab_Colorfull_GoogleMaps', {
                init : function(ed, url) {
                    ed.addButton('MageekLab_Colorfull_GoogleMaps', {
                        title : 'Generate an awesome Google Map',
                        image : url+'/../images/icon.png',
                        onclick : function() {
                                mgl_googlemaps_generator(ed, ed.selection.getContent(), 'editor');
                        }
                    });
                },
                createControl : function(n, cm) {
                    return null;
                },
            });
            tinymce.PluginManager.add('MageekLab_Colorfull_GoogleMaps', tinymce.plugins.MageekLab_Colorfull_GoogleMaps);

        })();
    }

//Sidebar Form navigation
function mgl_map_generator_menu_navigation(){
    $('#mgl_map_generator_menu li a').each(function(){
        $( $(this).attr('href') ).hide();
    });
    $('#mgl_map_settings').show();
    //$('#markersForm').show();

    $('#mgl_map_generator_menu li a').click(function(){
        
        var divId = new Array();
        $('#mgl_map_generator_menu li a').each(function(){
             $( $(this).attr('href') ).hide();
             $(this).removeClass('menu_active');
        });

        $(this).addClass('menu_active');
        $( $(this).attr('href') ).show();

        

        return false;
    });
}

function mgl_googlemaps_generator (ed, previous_content, media) {
    global_ed = ed;
    
    jQuery('body').prepend('<div class="mgl_bg"><div id="mgl_gmaps_modal"><div class="container"></div></div></div>');
    
    generator_url = mgl_gmap_values.plugin_url+'gmaps-generator/generator.php';
 
    var data = {
        action: 'mgl_gmap_generator',
        previous_shortcode: previous_content      // We pass php values differently!
    };
    // We can also pass the url value separately from ajaxurl for front end AJAX implementations
    jQuery.post(ajax_object.ajax_url, data, function(response) {

        $('#mgl_gmaps_modal .container').append(response);

        //Menu navigation
        mgl_map_generator_menu_navigation();


        // Load map
         $("#mgl_map_generator").gMap({zoom: Number(mgl_map_defaults.zoom), address: mgl_map_defaults.address, controls: { mapTypeControl: false, zoomControl: true, panControl: true, streetViewControl: true } });

         skins();
        
        // Set defaults fields
        if($('#mgl_address').attr('value') == '') { 

            // Set address field
            if($('#mgl_lat').attr('value') == '' && $('#mgl_long').attr('value') != '') {
                $('#mgl_address').attr('value',mgl_map_defaults.address);
            }
            // If not coordinates defined, find them from the address
            
            if($('#mgl_lat').attr('value') == '' && $('#mgl_long').attr('value') == '') {
                var geo = new google.maps.Geocoder;
                geo.geocode({'address':mgl_map_defaults.address},function(results, status){
                      if (status == google.maps.GeocoderStatus.OK) {
                        $('#mgl_lat').attr('value', Number(results[0].geometry.location.lat()));
                        $('#mgl_long').attr('value', Number(results[0].geometry.location.lng()));
                      }

               });
            }

        }

        // Check for zoom
        
        if($('#mgl_zoom').attr('value') == '') { 
            $('#mgl_zoom').attr('value', Number(mgl_map_defaults.zoom)); 
        } else {
            $("#mgl_map_generator").data("gMap.reference").setZoom(Number($('#mgl_zoom').attr('value')));
        }
        
         // Bind skin selector
         
         $( "#mgl_skin_switcher" ).change(function() {
            
            skin = $( "#mgl_skin_switcher option:selected" ).attr('value');
            if(skin == 'one_color') {
                // If is one color show the field
                $('#mgl_skin_one_color').parent().show();
            } else {
                // Else load the skin and hide the field
                loadSkin(skin);
                $('#mgl_skin_one_color').parent().hide();
            }
               
        });

        // Search for the address

        $('#mgl_address').keyup(function() {
            address = $( "#mgl_address" ).attr('value');
            
            var geo = new google.maps.Geocoder;

            geo.geocode({'address':address},function(results, status){
                  if (status == google.maps.GeocoderStatus.OK) {
                    $("#mgl_map_generator").data("gMap.reference").setCenter(results[0].geometry.location);
                  }

           });
        });

        // Check for the zoom

        $('#mgl_zoom').keyup(function() {
            
            zoom = Number($( "#mgl_zoom" ).attr('value'));
            if($.isNumeric(zoom)) {
                if(zoom < 0) { zoom = 0; $( "#mgl_zoom" ).attr('value', 0); }
                if(zoom > 18) { zoom = 18; $( "#mgl_zoom" ).attr('value', 18); }

                $("#mgl_map_generator").data("gMap.reference").setZoom(zoom);
            }
            

        });

        // If lat long center map

        $('#mgl_lat, #mgl_long').keyup(setMapCenter);


        // Check for width & height

        $('#mgl_width').keyup(function() {
            
            map_width = $( "#mgl_width" ).attr('value');

            if(map_width == '') { map_width = '100%'; }

            $("#mgl_map_generator").animate({'width' : map_width}, 200, function(){
                 google.maps.event.trigger($("#mgl_map_generator").data("gMap.reference"), 'resize');
            });

        });

        $('#mgl_height').keyup(function() {
            
            map_height = $( "#mgl_height" ).attr('value');

            if(map_height == '') { map_height = '100%'; }

            $("#mgl_map_generator").animate({'height' : map_height}, 200, function(){
                 google.maps.event.trigger($("#mgl_map_generator").data("gMap.reference"), 'resize');
            });

        });

        // Load map skin based on color
        $('#mgl_skin_one_color').keyup(function() {
            if(/^#[0-9A-F]{6}$/i.test($(this).attr('value'))) {

                var one_color_styles = [ 
                    { "stylers": [ { "hue": $(this).attr('value') }, { "saturation": 1 }, { "lightness": 1 } ] }
                  ];
                
                var one_colorstyledMap = new google.maps.StyledMapType(one_color_styles, {name: "OneColor_style"});
                
                jQuery("#mgl_map_generator").data("gMap.reference").mapTypes.set('OneColor_style', one_colorstyledMap);

                $("#mgl_map_generator").data("gMap.reference").setMapTypeId('OneColor_style');
            }
        });

        // Remove generator

        $('#mgl_gmaps_modal .media-modal-close').click( function(e){
            $('.mgl_bg').off();
            $('.mgl_bg').remove();
        });

        // Insert generator

        $('#mgl_gmaps_modal .mgl-insert-map').click( function(e){
            mapSettings = '';
            mgl_insert_map(mapSettings, media);
        });

        /* Markers */

        $('#addNewFormMarker').click(function(){
            i = $('#markersForm #markersCont > div').length;
            addFormMarker(i);
        });

        // Marker Address
        $('.mgl_bg').on('keyup', ".marker_address", function(e){
            
            var id = $(this).parent().parent().attr('id');

            id = id.split('_')

            i = id[1];

            address = $( "#markerForm_"+ i +" .marker_address" ).attr('value');

            var geo = new google.maps.Geocoder;

            geo.geocode({'address':address},function(results, status){
                  if (status == google.maps.GeocoderStatus.OK) {
                    window['marker'+i].setPosition(results[0].geometry.location);
                    $('#markerForm_'+i+ ' .marker_lat').attr('value',results[0].geometry.location.lat());
                    $('#markerForm_'+i+ ' .marker_long').attr('value',results[0].geometry.location.lng());
                  } else {

                    window['marker'+i].setPosition($("#mgl_map_generator").data("gMap.reference").getCenter());
                  }

           });
        });


        // Marker Content
        $('.mgl_bg').on('keyup', ".marker_content", function(e){
            
            var id = $(this).parent().attr('id');

            id = id.split('_')

            i = id[1];

            marker_content = $( "#markerForm_"+ i +" .marker_content" ).val();

            //alert(marker_content);

            /* var infowindow = new google.maps.InfoWindow({
                  content: marker_content
            });

            google.maps.event.addListener(window['marker'+i], 'click', function() {
                infowindow.open($("#mgl_map_generator").data("gMap.reference"),window['marker'+i]);
            }); */

        });

        // Delete markers
        $('.mgl_bg').on('click', ".delete_marker", function(e){

            var id = $(this).parent().attr('id');

            id = id.split('_')

            i = id[1];

            
            $(this).parent().slideUp(400, function() {
               $(this).remove();
               window['marker'+i].setMap(null);
            });
        });

        // Window for marker selection
        $('.mgl_bg').on('click', ".marker_skin", function(e){

            // Check if we're opening or closing the window
            if($(this).parent().find('.markers_skins').length == 0) {
                // Open
                $(this).parent().append('<div class="markers_skins">' + $('#markers_skins').html() + '</div>');

                // If theres a custom icon, load the url in the custom textfield
                if (/(jpg|gif|png)$/.test($(this).parent().parent().parent().parent().find('.marker_icon').attr('value'))){
                    $(this).parent().find(".markers_skins .marker_skin_custom").attr('value', $(this).parent().parent().parent().parent().find('.marker_icon').attr('value'));
                }

            } else {
                // Close
                $(this).parent().find('.markers_skins').remove();

            }

            e.preventDefault();
        }); 
        
        // Icon selector
        $('.mgl_bg').on('click', ".marker_skin_selector", function(e){
            e.preventDefault();

            var id = $(this).parent().parent().parent().parent().attr('id');

            id = id.split('_')

            i = id[1];
            if($(this).attr('href') == 'default') {
                $(this).parent().parent().parent().parent().find('.marker_icon').attr('value', ''); 
                window['marker'+i].setIcon('');            
            } else {
                $(this).parent().parent().parent().parent().find('.marker_icon').attr('value', $(this).attr('href'));
                window['marker'+i].setIcon($(this).find('img').attr('src'));
            }

            $(this).parent().parent().parent().find('.marker_selected').attr('src', $(this).find('img').attr('src'));

            $(this).parent().remove();
            
        });

        // Custom Icon selector
        $('.mgl_bg').on('keyup', ".marker_skin_custom", function(e){
            
            var id = $(this).parent().parent().parent().parent().attr('id');

            id = id.split('_')

            i = id[1];
            
            var input = $(this).attr('value');

            if (/(jpg|gif|png)$/.test(input)){
                window['marker'+i].setIcon(input);
                $(this).parent().parent().parent().find('.marker_selected').attr('src', input);
                $(this).parent().parent().parent().parent().find('.marker_icon').attr('value', input);
            } else {
                window['marker'+i].setIcon(''); 
            }

        });
             


        // Load previous markers
         
         

         google.maps.event.addListenerOnce($("#mgl_map_generator").data("gMap.reference"), 'idle', initialize);

    });


}

function setMapCenter() {
    mgl_lat = Number($( "#mgl_lat" ).attr('value'));
    mgl_long = Number($( "#mgl_long" ).attr('value'));
    if($.isNumeric(mgl_lat) && $.isNumeric(mgl_long) && mgl_lat != '' && mgl_long != '') {
        $("#mgl_map_generator").gMap('centerAt', { latitude: mgl_lat, longitude: mgl_long, zoom: $("#mgl_map_generator").data("gMap.reference").getZoom() });
    }
}

function initialize() {

    // Setup map widht & height

    map_width = $( "#mgl_width" ).attr('value');
    if(map_width == '') { map_width = '100%'; }
    $("#mgl_map_generator").css({'width' : map_width});

    map_height = $( "#mgl_height" ).attr('value');
    if(map_height == '') { map_height = '100%'; }
    $("#mgl_map_generator").css({'height' : map_height});
        
    // Load current skin

    var skin = $( "#mgl_skin_switcher option:selected" ).attr('value');
            
    if(skin != '') { loadSkin(skin); }
    if(skin != 'one_color') { $('#mgl_skin_one_color').parent().hide(); }

    // Add previous Markers

    $('#markersForm #markersCont > div').each(function( index ) {

        var i = Number($(this).attr('id').replace("markerForm_", ""));

        // If marker has address load by it
        if($('#markerForm_'+i+ ' .marker_address').attr('value') != '') {
            
            // Check if is a valid address
            var geo = new google.maps.Geocoder;

            geo.geocode({'address':$('#markerForm_'+i+ ' .marker_address').attr('value')},function(results, status){
                  if (status == google.maps.GeocoderStatus.OK) {
                    
                    // If it is, put the marker there
                    window['marker'+i] = new google.maps.Marker({
                        position : results[0].geometry.location,
                        draggable: true,
                    });
                    console.log(i+' - position found!');

                    initMarker(i);
                  } else {

                    //Ifnot center it in the map
                    window['marker'+i] = new google.maps.Marker({
                        position : $("#mgl_map_generator").data("gMap.reference").getCenter(),
                        draggable: true,
                    });

                    initMarker(i);
                  }
           });
        } else if($('#markerForm_'+i+ ' .marker_lat').attr('value') != '')  {
            console.log(i+' - LatLong!');
            // Setup by latLong
            
            var latlng = new google.maps.LatLng($('#markerForm_'+i+ ' .marker_lat').attr('value'), $('#markerForm_'+i+ ' .marker_long').attr('value'));
            
            window['marker'+i] = new google.maps.Marker({
                position : latlng,
                draggable: true,
            });

            initMarker(i);
        } else {
  
            map_center = $("#mgl_map_generator").data("gMap.reference").getCenter();
            if(map_center == undefined) {
                map_center = new google.maps.LatLng( Number($('#mgl_lat').attr('value')), Number($('#mgl_long').attr('value')));
            }
            // By default set it in center
            window['marker'+i] = new google.maps.Marker({
                position : map_center,
                draggable: true,
            });

            initMarker(i);
        }

        
     });

    setMapCenter();

    // Bind fields to events in case the map center or zoom chantes
        
    google.maps.event.addListener($("#mgl_map_generator").data("gMap.reference"), 'center_changed', function(event) {
      $( "#mgl_lat" ).attr('value',$("#mgl_map_generator").data("gMap.reference").getCenter().lat());
      $( "#mgl_long" ).attr('value',$("#mgl_map_generator").data("gMap.reference").getCenter().lng());
    });

    google.maps.event.addListener($("#mgl_map_generator").data("gMap.reference"), 'dragend', function(event) {
      $( '#mgl_address' ).attr('value', '');
    });

    google.maps.event.addListener($("#mgl_map_generator").data("gMap.reference"), 'zoom_changed', function(event) {
      $( "#mgl_zoom" ).attr('value',$("#mgl_map_generator").data("gMap.reference").getZoom());
    });
}



function mgl_insert_map (mapSettings, media) {
    var fields = $("#mgl_map_settings :input").serializeArray();

    result = '[mgl_gmap ';
    
    // Set map attributes
    $.each(fields, function(i, field){
        if(field.value != '') {
            result += field.name + ' = "' + field.value + '" ';
        }
        
    });
    result += ']'
    
    // Add markers
    resultMarkers = '';

    jQuery.each($('#markersForm #markersCont > div'), function(i){
      marker_icon = $(this).find('.marker_icon').attr('value');   if(marker_icon != '') { marker_icon = 'icon="'+marker_icon+'"'; }
      marker_address = $(this).find('.marker_address').attr('value');   if(marker_address != '') { marker_address = 'address="'+marker_address+'"'; }
      marker_long = $(this).find('.marker_long').attr('value');         if(marker_long != '') { marker_long = 'long="'+marker_long+'"'; }
      marker_lat = $(this).find('.marker_lat').attr('value');           if(marker_lat != '') { marker_lat = 'lat="'+marker_lat+'"'; }
      marker_content = $(this).find('.marker_content' ).val();
      resultMarkers += '[mgl_marker '+marker_address+' '+marker_lat+' '+marker_long+' '+marker_icon+']'+marker_content+'[/mgl_marker]';
    });

    result += resultMarkers + '[/mgl_gmap]';
    
    if(media == 'widget') {
        $('#'+current_map_field).attr('value', result);
    } else {
        global_ed.selection.setContent(result);
    }
    
   
    $('.mgl_bg').off();
    $('.mgl_bg').remove();
}

function addFormMarker(i) {

    while($("#markerForm_"+ i).length > 0) { i++; } 

    $('#markersForm #markersCont').prepend('<div id="markerForm_'+ i +'" class="markerForm">'+
        '<div class="marker_icon_selector"><div class="marker_skin_container"><a href="" class="marker_skin">'+
        '<img class="marker_selected" src="'+mgl_gmap_values.plugin_url+'/images/marker_default.png" />' +
        '</a></div></div>'+
        '<strong class="marker_title">Marker ' + Number(i+1) + '</strong>'+
        '<a class="delete_marker" href="#">X</a>'+
        '<div class="marker_fields">'+
        '<input type="hidden" name="marker['+i+'][icon]" class="marker_icon" />'+
        '<input type="text" name="marker['+i+'][address]" class="marker_address" placeholder="Address" />'+
        '<input type="text" name="marker['+i+'][lat]" class="marker_lat" placeholder="Latitude" />'+
        '<input type="text" name="marker['+i+'][long]" class="marker_long" placeholder="Longitude" />'+
        '<textarea class="marker_content" placeholder="Your text here" ></textarea>'+
        '</div></div>'
    );

    window['marker'+i] = new google.maps.Marker({
        position : $("#mgl_map_generator").data("gMap.reference").getCenter(),
        draggable: true,
    });

    initMarker(i);
    
}

function loadSkin(skin) {
    switch(skin) {
        case 'satellite':
        case 'roadmap':
            $("#mgl_map_generator").data("gMap.reference").setMapTypeId(skin);
        break;
        case 'hybrid':
            $("#mgl_map_generator").data("gMap.reference").setMapTypeId(google.maps.MapTypeId.HYBRID);
        break;
        case 'terrain':
            $("#mgl_map_generator").data("gMap.reference").setMapTypeId(google.maps.MapTypeId.TERRAIN);
        break;
        case 'one_color':
            
            var one_color_styles = [ 
                { "stylers": [ { "hue": $('#mgl_skin_one_color').attr('value') }, { "saturation": 1 }, { "lightness": 1 } ] }
            ];   
            
            var one_colorstyledMap = new google.maps.StyledMapType(one_color_styles, {name: "OneColor_style"});
                
            $("#mgl_map_generator").data("gMap.reference").mapTypes.set('OneColor_style', one_colorstyledMap);
            $("#mgl_map_generator").data("gMap.reference").setMapTypeId('OneColor_style');
        break;
        default:
            $("#mgl_map_generator").data("gMap.reference").setMapTypeId(skin+'_style');
        break;
    }
}

function initMarker(i) {
    window['marker'+i].setMap($("#mgl_map_generator").data("gMap.reference"));
    console.log('in');
    google.maps.event.addListener(window['marker'+i],'dragend',function(event) {
        $('#markerForm_'+i+ ' .marker_lat').attr('value',event.latLng.lat());
        $('#markerForm_'+i+ ' .marker_long').attr('value',event.latLng.lng());

        $('#markerForm_'+i+ ' .marker_address').attr('value','');
    }); 

    if($('#markerForm_'+i+ ' .marker_icon').attr('value') != '')  {
        window['marker'+i].setIcon($('#markerForm_'+i).find('.marker_selected').attr('src'));
    }
}

$(document).ready(function(){
   //mgl_googlemaps_generator('meh','');
});

// Show the generator on the widgets page

$('body').on('click', ".mgl_widget_generator", function(e){
    
    current_map_field = $(this).parent().find('.mgl_widget_map_hidden').attr('id');

    current_saved_map = $(this).parent().find('.mgl_widget_map_hidden').attr('value');

    mgl_googlemaps_generator('',current_saved_map, 'widget');
});

function skins() {
     // Cartoon
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
      ];

      // Grey Scale
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
      ];

      // Black & White
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
      ];

      // Retro
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
      ];

      // Night
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
      ];   

      // Night Light
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
      ]; 

      // Papiro  
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
      ];

    var styledMap = new google.maps.StyledMapType(grey_styles, {name: "Grey"});
    var bwstyledMap = new google.maps.StyledMapType(bw_styles, {name: "Black & White"});
    var retrostyledMap = new google.maps.StyledMapType(retro_styles, {name: "Retro"});
    var nightstyledMap = new google.maps.StyledMapType(night_styles, {name: "Night"});
    var nightlightstyledMap = new google.maps.StyledMapType(night_light_styles, {name: "Night Light"});
    var papirostyledMap = new google.maps.StyledMapType(papiro_styles, {name: "Papiro"});
    var cartoonstyledMap = new google.maps.StyledMapType(cartoon_styles, {name: "Cartoon"});


    jQuery("#mgl_map_generator").data("gMap.reference").mapTypes.set('cartoon_style', cartoonstyledMap);
    jQuery("#mgl_map_generator").data("gMap.reference").mapTypes.set('grey_style', styledMap);
    jQuery("#mgl_map_generator").data("gMap.reference").mapTypes.set('bw_style', bwstyledMap);
    jQuery("#mgl_map_generator").data("gMap.reference").mapTypes.set('retro_style', retrostyledMap);
    jQuery("#mgl_map_generator").data("gMap.reference").mapTypes.set('night_style', nightstyledMap);
    jQuery("#mgl_map_generator").data("gMap.reference").mapTypes.set('night_light_style', nightlightstyledMap);
    jQuery("#mgl_map_generator").data("gMap.reference").mapTypes.set('papiro_style', papirostyledMap);

}

})(jQuery);