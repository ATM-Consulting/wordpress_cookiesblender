<?php


add_action( 'wp_head', 'cookiesBlenderApiHeader');
function cookiesBlenderApiHeader(){
    print '<script  type="text/javascript">var cookiesBlenderSiteUrl =  '.json_encode(get_option('siteurl')).'</script>';
}

add_action( 'rest_api_init', function () {

    // exemple : /wp-json/cookiesblender/v1/getscripts
    register_rest_route( 'cookiesblender/v1', '/getscripts/', array(
            'methods' => 'GET',
            'callback' => 'getCookiesBlenderConsentDataList',
        )
    );

});
