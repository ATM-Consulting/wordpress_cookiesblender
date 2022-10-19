<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}



add_action( 'wp_head', function() {

    $consentList = getCookiesblenderConsentList();

    $activeOnThisPage = array();
    $scriptsToAdd='';
    if(!empty($consentList)){
        $scriptsToAdd.='<!-- Start Cookies blender accepted script -->'."\r\n";

        foreach ($consentList as $key => $item){
            $enable = checkCookiesBlenderEnabled($key);
            if(!$enable || checkCookiesBlenderAccepted($key) < 1){ continue;  }

            $activeOnThisPage[] = $key;
            $scriptsToAdd.='<!-- Start script '.$key.' -->'."\r\n";
            $scriptsToAdd.=get_option($key.'_script')."\r\n";
            $scriptsToAdd.='<!-- End script '.$key.' -->'."\r\n";

        }

        $scriptsToAdd.='<!-- End Cookies blender accepted script -->'."\r\n";
    }

    $params = new stdClass();
    $params->siteUrl = get_option('siteurl');
    $params->activeOnThisPage = $activeOnThisPage;
    $params->langs = new stdClass();
    $params->langs->cookiesButtonTitle = __('Manage your cookies preference');

    print '<!-- Set cookies blender params -->'."\r\n";
    print '<script  type="text/javascript" id="cookiesblender-param-script">';
    print 'var cookiesBlenderParams = '.json_encode($params).';';
    print '</script>'."\r\n";

    print $scriptsToAdd;
});


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


    // disable recapcha
    $enable_wpcf7_recapcha = boolval(get_option('cookiesBlender_wpcf7_recapcha_cookies'));
    if (!$enable_wpcf7_recapcha && checkCookiesBlenderAccepted('required')){
        wp_dequeue_script( 'google-recaptcha' );
        wp_dequeue_script( 'google-invisible-recaptcha' );
    }
});



