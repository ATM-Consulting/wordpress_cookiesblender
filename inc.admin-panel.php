<?php


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


/**
 * Ajoute les scripts js et css au panneau admin
 */
add_action('admin_enqueue_scripts', function() {
    wp_enqueue_style('admin-styles', plugins_url('/css/cookiesblender.css', __FILE__));
});


/**
 * Ajoute les menus au panneau admin
 */
add_action('admin_menu', function(){
    //add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
    add_options_page( __('Cookies blender setup'), __('Cookies blender setup'), 'manage_options', 'cookiesblender-plugin', 'add_cookiesblender_config_page' );
});


/**
 * Ajoute l'enregistrement des options envoyées par les pages admin
 */
add_action('admin_init', function() { // whitelist options

    register_setting( 'cookiesblender-option-group', 'cookiesblenderDialogMessage' );
    register_setting( 'cookiesblender-option-group', 'cookiesblenderDialogTitle' );
    register_setting( 'cookiesblender-option-group', 'cookiesblenderDialogTitle' );
    register_setting( 'cookiesblender-option-group', 'cookiesBlender_wpcf7_recapcha_cookies' );


    $consentList = getCookiesblenderConsentList();
    foreach ($consentList as $key => $params){
        register_setting( 'cookiesblender-option-group', $key.'_cookies' );
        register_setting( 'cookiesblender-option-group', $key.'_script' );
        register_setting( 'cookiesblender-option-group', $key.'_description' );
        register_setting( 'cookiesblender-option-group', 'enable_'.$key.'_cookies' );
    }
});



function add_cookiesblender_config_page(){

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.') );
    }

    echo '<div class="wrap"><h1>'.__('Cookies blender setup page').'</h1>';
    print '<form class="form-table" method="post" action="options.php">';
    settings_fields( 'cookiesblender-option-group' );

    do_settings_sections( 'cookiesblender-option-group');

    // TODO utiliser le hook do_settings_sections plutot que print du html

    print '<table class="form-table">';


    print '<tr>';
    print '<th scope="row">'.__('Dialog title').'</th>';
    $dialogTitle = trim(get_option('cookiesblenderDialogTitle'));
    if(empty($dialogTitle)){

    }
    print '<td><input type="text"  class="regular-text" name="cookiesblenderDialogTitle" value="'.esc_attr( get_option('cookiesblenderDialogTitle') ).'" /></td>';
    print '</tr>';

    print '<tr>';
    print '<th scope="row">'.__('Cookies dialog description').'</th>';
    $settings = array();
    print '<td>';
    wp_editor( get_option_or_default('cookiesblenderDialogMessage', __('Nous utilisons des cookies pour vous garantir la meilleure expérience sur notre site web. ')), 'cookiesblenderDialogMessage', $settings);
    print '</td>';
    print '</tr>';


    $consentList = getCookiesblenderConsentList();

    foreach ($consentList as $key => $params){

        $enable = checkCookiesBlenderEnabled($key);

        print '<tr><td colspan="2"><hr/></td></tr>';

        print '<tr>';
        if($key != 'required') {
            print '<th scope="row">'.$params['title'].'</th>';
            print '<td>';
            print '<label class="cookiesblender-switch"><input type="checkbox"  name="enable_' . $key . '_cookies" value="1" ' . ($enable ? ' checked="checked" ' : '') . ' /><span class="cookiesblender-slider round"></span></label>';
            print '</td>';
        }
        else{
            print '<th scope="row" colspan="2">'.$params['title'].'</th>';
        }
        print '</tr>';

        print '<tr>';
        print '<th scope="row">'.__('Title on dialog').'</th>';
        print '<td>';
        print '<input type="text" class="regular-text" name="'.$key.'_cookies" value="'.esc_attr( get_option($key.'_cookies') ).'" />';
        print '</tr>';

        print '<tr>';
        print '<th scope="row">'.__('Cookies description').'</th>';
        $settings = array(
            'textarea_rows'       => 5,
            'teeny'               => false,
        );
        print '<td>';
        $wysiwyg = $key.'_description' ;
        wp_editor( get_option($wysiwyg), $wysiwyg, $settings);
        print '</td>';
        print '</tr>';

        print '<tr>';
        print '<th scope="row">'.__('Script if accepted').'</th>';
        print '<td>';
        print '<textarea style="width: 100%; min-height: 300px;" name="'.$key.'_script" >'.esc_attr( get_option($key.'_script')).'</textarea>';
        print '</td>';
        print '</tr>';

        if($key == 'required' && is_plugin_active( 'contact-form-7/wp-contact-form-7.php')){

            $name = 'cookiesBlender_wpcf7_recapcha_cookies';
            $enable = boolval(get_option($name));
            print '<tr>';
            print '<th scope="row">'.__('Disable ReCapchat of contact form 7 while required cookies not set').'</th>';
            print '<td>';
            print '<label class="cookiesblender-switch"><input type="checkbox"  name="'.$name.'" value="1" ' . ($enable ? ' checked="checked" ' : '') . ' /><span class="cookiesblender-slider round"></span></label>';
//            print '<p>';
//            print __('If this option is setted, you need to add this shortcode to your contact-form-7 forms to add fallback message in case of   ');
//            print '</p>';
            print '</tr>';
        }

    }




    print '</table>';

    submit_button();
    print '</form>';
    print '</div>';
}
