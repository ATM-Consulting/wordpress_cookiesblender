<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


add_action( 'wp_head', function() {
    print '<script  type="text/javascript">var cookiesBlenderSiteUrl =  '.json_encode(get_option('siteurl')).'</script>';
});

add_action( 'rest_api_init', function (){
    // exemple : /wp-json/cookiesblender/v1/getscripts
    register_rest_route( 'cookiesblender/v1', '/getscripts/', array(
            'methods' => 'GET',
            'callback' => 'getCookiesBlenderConsentDataList',
        )
    );
});
