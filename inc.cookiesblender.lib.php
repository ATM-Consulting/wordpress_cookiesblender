<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}


function getCookiesblenderConsentList(){
    return array(
        'required' => array(
            'title' => __('Cookies requis aux fonctionnement')
        ),
        'analytics' => array(
            'title' => __('Analytics')
        ),
        'other01' => array(
            'title' => __('Other Script').' 01'
        )
    );
}

function get_option_or_default($optionKey, $default = '') {
    $value = get_option($optionKey);
    if($value === false){
        return $default;
    }

    return $value;
}

function getCookiesBlenderConsentDataList(){
    $consentList = getCookiesblenderConsentList();

    $data = array(
        'cookiesList' => array(),
        'pages' => array(
//            'rgpdPageUrl' => get_option('rgpdPageUrl')
            'privacyPolicy' => get_privacy_policy_url()
        ),
        'langs' => array(
            'WeAreCookies' => get_option_or_default('cookiesblenderDialogTitle', __('Bonjour, nous sommes les cookies !')),
            'RequiredCookies' => __('Cookies requis aux fonctionnement'),
            'SaveCookiesConfiguration' => __('Sauvegarder mes préférences'),
            'AcceptAll' => __('Accepter'),
            'optionWithStartAreRequired' => __('Option obligatoire ne pouvant être retirée'),
            'CheckPrivacyPolicyPage' => __('Consultez notre politique de confidentialité'),
        ),
        'dialogContent' => get_option_or_default('cookiesblenderDialogMessage')
    );

    foreach ($consentList as $key => $params){
        $enable = checkCookiesBlenderEnabled($key);

        if(!$enable){
            continue;
        }

        $item = new stdClass();
        $item->title = $params['title'];
        $item->cookieKey = $key;
        $item->name = get_option_or_default($key.'_cookies');
        $item->accepted = checkCookiesBlenderAccepted($key);
        if(empty($item->name)){
            $item->name = $item->title;
        }
        $item->content = get_option_or_default($key.'_script');

        $data['cookiesList'][] = $item;
    }
    return $data;
}

/**
 * @param $cookieKey
 * @return int -1 for not set, 0 for refused 1 for accepted
 */
function checkCookiesBlenderAccepted($cookieKey){

    // les cookies obligatoires ne sont validés qu'après la première action de l'internaute
    if($cookieKey == 'required' && (isset($_COOKIE['cookieDialogAnswer']) || isset($_COOKIE['cookiesblender_consent_required']))){
        return true;
    }


    if(!isset($_COOKIE['cookiesblender_consent_'.$cookieKey])){
        return -1;
    }

    return intval($_COOKIE['cookiesblender_consent_'.$cookieKey]);
}



/**
 * @param $cookieKey
 * @return bool
 */
function checkCookiesBlenderEnabled($cookieKey){
    if($cookieKey == 'required'){
        return true;
    }

    return boolval(get_option('enable_'.$cookieKey.'_cookies'));
}