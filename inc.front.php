<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * register jquery and style on initialization
 */
add_action('init', function() {
    wp_register_script( 'cookiesblender_dialog_js', plugins_url('/js/dialog.js', __FILE__));
    wp_register_script( 'cookiesblender_js', plugins_url('/js/cookiesblender.js', __FILE__), array('cookiesblender_dialog_js'));
    wp_register_style( 'cookiesblender_style', plugins_url('/css/cookiesblender.css', __FILE__), false, '1.0.0', 'all');
});

// use the registered jquery and style above
add_action('wp_enqueue_scripts', function(){
    wp_enqueue_script('cookiesblender_js');
    wp_enqueue_style( 'cookiesblender_style' );
});
