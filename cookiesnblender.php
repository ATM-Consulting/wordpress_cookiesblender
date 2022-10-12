<?php
/**
 * Plugin Name: Cookies blender
 * Plugin URI: http://www.atm-consulting.fr
 * Description: A tool for cookies and R
 * Version: 1.0
 * Author: ATM Consulting
 * Author URI: http://www.atm-consulting.fr
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

require_once __DIR__ .'/api.php';


// register jquery and style on initialization
add_action('init', 'register_script');
function register_script() {
    wp_register_script( 'cookiesblender_dialog_js', plugins_url('/js/dialog.js', __FILE__));
    wp_register_script( 'cookiesblender_js', plugins_url('/js/cookiesblender.js', __FILE__), array('cookiesblender_dialog_js'));
    wp_register_style( 'cookiesblender_style', plugins_url('/css/cookiesblender.css', __FILE__), false, '1.0.0', 'all');
}

// use the registered jquery and style above
add_action('wp_enqueue_scripts', 'enqueue_style');
function enqueue_style(){
    wp_enqueue_script('cookiesblender_js');
    wp_enqueue_style( 'cookiesblender_style' );
}

// Update CSS within in Admin
function cookiesblender_admin_style() {
    wp_enqueue_style('admin-styles', plugins_url('/css/cookiesblender.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'cookiesblender_admin_style');



add_action('admin_menu', 'cookiesblender_plugin_setup_menu');

function cookiesblender_plugin_setup_menu(){

    //add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
    add_options_page( __('Cookies blender setup'), __('Cookies blender setup'), 'manage_options', 'cookiesblender-plugin', 'add_cookiesblender_config_page' );
}



add_action('admin_init', 'register_cookiesblendersettings' );
function register_cookiesblendersettings() { // whitelist options
    $consentList = getCookiesblenderConsentList();
    foreach ($consentList as $key => $params){
        register_setting( 'cookiesblender-option-group', $key.'_cookies' );
        register_setting( 'cookiesblender-option-group', $key.'_script' );
        register_setting( 'cookiesblender-option-group', 'enable_'.$key.'_cookies' );
    }
}

function getCookiesblenderConsentList(){
    return array(
        'analytics' => array(
            'title' => __('Analytics')
        ),
        'other01' => array(
            'title' => __('Other Script').' 01'
        )
    );
}

function getCookiesBlenderConsentDataList(){
    $consentList = getCookiesblenderConsentList();

    $data = array(
        'cookiesList' => array(),
        'pages' => array(
            'rgpdPageUrl' => get_option('rgpdPageUrl')
        ),
        'langs' => array(
            'WeAreCookies' => __('Bonjour, nous sommes les cookies !'),
            'RequiredCookies' => __('Cookies requis aux fonctionnement'),
            'SaveCookiesConfiguration' => __('Sauvegarder mes préférences'),
            'AcceptAll' => __('Accepter'),
        )
    );

    foreach ($consentList as $key => $params){
        $enable = boolval(get_option('enable_'.$key.'_cookies'));

        if(!$enable){
            continue;
        }

        $item = new stdClass();
        $item->title = $params['title'];
        $item->cookieKey = $key;
        $item->name = get_option($key.'_cookies');
        if(empty($item->name)){
            $item->name = $item->title;
        }
        $item->content = get_option($key.'_script');

        $data['cookiesList'][] = $item;
    }
    return $data;
}

function add_cookiesblender_config_page(){
    echo '<div class="wrap"><h1>Your Plugin Page Title</h1>';
    print '<form class="form-table" method="post" action="options.php">';
    settings_fields( 'cookiesblender-option-group' );
    do_settings_sections( 'cookiesblender-option-group');

    print '<table class="form-table">';


    print '<tr>';
    print '<th scope="row">'.__('Page for cookies policies').'</th>';
    print '<td><input type="url"  class="regular-text" name="rgpdPageUrl" value="'.esc_attr( get_option('rgpdPageUrl') ).'" /></td>';
    print '</tr>';


    $consentList = getCookiesblenderConsentList();

    foreach ($consentList as $key => $params){

        $enable = boolval(get_option('enable_'.$key.'_cookies'));
        print '<tr><td colspan="2"><hr/></td></tr>';

        print '<tr>';
        print '<th scope="row">'.$params['title'].'</th>';
        print '<td>';
        print '<label class="cookiesblender-switch"><input type="checkbox"  name="enable_'.$key.'_cookies" value="1" '.($enable?' checked="checked" ':'').' /><span class="cookiesblender-slider round"></span></label>';
        print '</tr>';
        print '</tr>';

        print '<tr>';
        print '<th scope="row">'.__('Title on dialog').'</th>';
        print '<td>';
        print '<input type="text" class="regular-text" name="'.$key.'_cookies" value="'.esc_attr( get_option($key.'_cookies') ).'" />';
        print '</tr>';
        print '</tr>';

        print '<tr>';
        print '<th scope="row">'.__('Script if accepted').'</th>';
        print '<td>';
        print '<textarea style="width: 100%; min-height: 300px;" name="'.$key.'_script" >'.esc_attr( get_option($key.'_script')).'</textarea>';

        print '</td>';
        print '</tr>';
    }




    print '</table>';

    submit_button();
    print '</form>';
    print '</div>';
}

