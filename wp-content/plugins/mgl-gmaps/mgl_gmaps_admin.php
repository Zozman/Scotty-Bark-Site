<?php

if(isset($_POST['mgl_update'])) {
    
    update_option( 'mgl_gmaps', $_POST['mgl_gmaps'] );


    if(isset($_POST['mgl_gmaps_jquery'])) {

        update_option( 'mgl_gmaps_jquery', true );
    } else {
        update_option( 'mgl_gmaps_jquery', false );
    }


    ?>
    <div class="updated settings-error" id="setting-error-settings_updated"> 
        <p><strong><?php _e('Settings saved', 'mgl_gmaps'); ?></strong></p>
    </div>
    <?php
}


$mgl_gmaps_settings = get_option('mgl_gmaps', array('address' => 'Barcelona', 'zoom' => 13));

$mgl_instagram_account = get_option('mgl_instagram_userinfo');

?>
<div class="wrap"> 
    <div class="icon32 ">
        <br>
    </div> 
    <?php    echo "<h2>" . __( 'MaGeek Lab - Google Maps', 'mgl_gmaps' ) . "</h2>"; ?>
   <div class="col_left"> 
    
    <form name="mgl_gmaps_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>"> 
    <input type="hidden" name="mgl_update" value="Y"> 
    <h3><?php _e('Default map settings', 'mgl_gmaps'); ?></h3>
    <table class="form-table">
        <tr valign="top"> 
            <th scope="row"><?php _e('Address', 'mgl_gmaps'); ?></th>
            <td>
                <p class="description"> 
                    <input type="text" name="mgl_gmaps[address]" value="<?php echo $mgl_gmaps_settings['address']; ?>" />
                </p>
            </td>
        </tr>
        <tr valign="top"> 
            <th scope="row"><?php _e('Zoom', 'mgl_gmaps'); ?></th>
            <td>
                <p class="description"> 
                    <input type="text" name="mgl_gmaps[zoom]" value="<?php echo $mgl_gmaps_settings['zoom']; ?>" />
                </p>
            </td>
        </tr>
    </table> 
    <h3><?php _e('Configuration', 'mgl_gmaps'); ?></h3>
    <table class="form-table">
        <tr valign="top"> 
            <th scope="row"><?php _e('Load jQuery', 'mgl_gmaps'); ?></th>
            <td>
                <p class="description"> 
                    <input type="checkbox" name="mgl_gmaps_jquery" <?php if(get_option('mgl_gmaps_jquery', false) == true) { echo 'checked="checked"'; } ?> />
                    <?php _e('Unmark this if you are already loading jQuery', 'mgl_gmaps'); ?>
                </p>
            </td>
        </tr>
    </table> 
    <p class="submit">
        <input class="button" type="submit" name="Submit" value="<?php _e('Save settings', 'mgl_gmaps' ) ?>" />  
    </p>
    </form> 
</div> 
<div class="col_right">
    <a href="http://codecanyon.net/user/MaGeekLab?ref=mageeklab" title="Follow us on CodeCanyon" target="_blank"><img  title="Follow us on CodeCanyon"  alt="Follow us on CodeCanyon" src="<?php echo plugin_dir_url(__FILE__).'images/mageeklab_banner_codecanyon.png'; ?>" alt=""></a>
</div>
</div>
