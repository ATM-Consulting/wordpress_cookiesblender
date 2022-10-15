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

/**
 * Contain module libraries
 */
require_once __DIR__ .'/inc.cookiesblender.lib.php';

/**
 * Contain hook declaration for RES API usage
 */
require_once __DIR__ .'/inc.api.php';

/**
 * Contain hook declaration for admin panel
 * including page setup
 */
require_once __DIR__ .'/inc.admin-panel.php';

/**
 * Contain hook declaration for front website
 */
require_once __DIR__ .'/inc.front.php';