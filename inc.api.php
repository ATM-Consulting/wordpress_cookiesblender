<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


add_action( 'rest_api_init', function (){
    // exemple : /wp-json/cookiesblender/v1/getscripts
    register_rest_route( 'cookiesblender/v1', '/getscripts/', array(
            'methods' => 'GET',
            'callback' => 'getCookiesBlenderConsentDataList',
        )
    );
});
