<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
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
//            'rgpdPageUrl' => get_option('rgpdPageUrl')
        ),
        'langs' => array(
            'WeAreCookies' => get_option('cookiesblenderDialogTitle') ? get_option('cookiesblenderDialogTitle') :  __('Bonjour, nous sommes les cookies !'),
            'RequiredCookies' => __('Cookies requis aux fonctionnement'),
            'SaveCookiesConfiguration' => __('Sauvegarder mes préférences'),
            'AcceptAll' => __('Accepter'),
            'optionWithStartAreRequired' => __('Option obligatoire ne pouvant être retirée'),
        ),
        'dialogContent' => get_option('cookiesblenderDialogMessage')
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
        $item->accepted = checkCookiesBlenderAccepted($key);
        if(empty($item->name)){
            $item->name = $item->title;
        }
        $item->content = get_option($key.'_script');

        $data['cookiesList'][] = $item;
    }
    return $data;
}

/**
 * @param $cookieKey
 * @return int -1 for not set, 0 for refused 1 for accepted
 */
function checkCookiesBlenderAccepted($cookieKey){

    if(!isset($_COOKIE['cookiesblender_consent_'.$cookieKey])){
        return -1;
    }

    return intval($_COOKIE['cookiesblender_consent_'.$cookieKey]);
}