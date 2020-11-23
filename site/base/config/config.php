<?php
/**
 * @file
 *
 * The config.php is the configuration file of the Asterion instance.
 * It has all the constants that are used in the framework and it loads
 * the autoload.php and phpHelper.php files.
 * Here are the common options that we can change from site to site.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */

/**
 * The ASTERION_TITLE constant defines the title of the page.
 * After the installation it is preferable to use the "title" parameter.
 */
define('ASTERION_TITLE', 'Base site');

/**
 * The following constants define the paths to the different
 * main folders of the framework.
 * The most important ones are ASTERION_LOCAL_URL and ASTERION_LOCAL_FILE
 * that must point to the public and private files of the website.
 */
define('ASTERION_SERVER_URL', 'https://www.plasticmails.net');
define('ASTERION_BASE_STRING', '/asterion/site/');
define('ASTERION_LOCAL_URL', ASTERION_SERVER_URL . ASTERION_BASE_STRING);
define('ASTERION_LOCAL_FILE', $_SERVER['DOCUMENT_ROOT'] . ASTERION_BASE_STRING);

/**
 * The ASTERION_DEBUG constant defines if we are in debug mode or not.
 * It it used to show error messages or build the database automatically.
 * When in production it must be set to false.
 */
define('ASTERION_DEBUG', true);

/**
 * If we want Asterion to deal with only one language we just need to activate this constant.
 */
//define('ASTERION_LANGUAGE_ID', 'en');

/**
 * The system starts a session with a proper name for the website.
 */
define('ASTERION_SESSION_NAME', 'asterion');

/**
 * The COOKIE_TIME constant defines the duration of cookies in the site.
 */
define('ASTERION_COOKIE_TIME', 3600000);

/**
 * The following constants are used to check when the system parses the URL.
 */
define('ASTERION_ADMIN_URL_STRING', 'admin');
define('ASTERION_PAGER_URL_STRING', 'page');

/**
 * The following lines define the access to the database.
 */
define('ASTERION_DB_USE', true);
define('ASTERION_DB_SERVER', 'localhost');
define('ASTERION_DB_USER', 'plastic_asterionprueba');
define('ASTERION_DB_PASSWORD', 'directorio123');
define('ASTERION_DB_PORT', '3306');
define('ASTERION_DB_NAME', 'plastic_asterionprueba');
define('ASTERION_DB_PREFIX', 'ast_');

/**
 * The ASTERION_LOGO constant defines the path to the logo for the website.
 */
define('ASTERION_LOGO', ASTERION_BASE_URL . 'visual/img/logo.jpg');

/**
 * The following values tell Asterion what sizes of images it should store.
 */
define('ASTERION_SAVE_IMAGE_ORIGINAL', true);
define('ASTERION_SAVE_IMAGE_HUGE', true);
define('ASTERION_SAVE_IMAGE_WEB', true);
define('ASTERION_SAVE_IMAGE_SMALL', true);
define('ASTERION_SAVE_IMAGE_THUMB', true);
define('ASTERION_SAVE_IMAGE_SQUARE', true);

/**
 * The following dimension constants are used to create the different
 * versions of the images in Asterion.
 */
define('ASTERION_WIDTH_HUGE', 1600);
define('ASTERION_HEIGHT_MAX_HUGE', 2400);
define('ASTERION_WIDTH_WEB', 600);
define('ASTERION_HEIGHT_MAX_WEB', 1400);
define('ASTERION_WIDTH_SMALL', 250);
define('ASTERION_HEIGHT_MAX_SMALL', 500);
define('ASTERION_WIDTH_THUMB', 120);
define('ASTERION_HEIGHT_MAX_THUMB', 120);
define('ASTERION_WIDTH_SQUARE', 100);

/**
 * The ASTERION_EMAIL constant defines the main email for the website.
 * After the installation it is preferable to use the "email" parameter.
 * We also define the constants for sending emails through the website.
 */
define('ASTERION_EMAIL', 'info@asterion-cms.com');
define('ASTERION_MAIL_HOST', 'mail.asterion-cms.com');
define('ASTERION_MAIL_USERNAME', 'noreply@asterion-cms.com');
define('ASTERION_MAIL_PASSWORD', 'Manzana123');


/**
 * The ASTERION_ROUTER_CONTROLLERS constant defines the default action for certain Controllers.
 */
define('ASTERION_ROUTER_CONTROLLERS', serialize(['user'=>'user']));

/**
 * Define a date timezone
 */
date_default_timezone_set('America/Los_Angeles');

/**
 * The framework loads the autoload.php and phpHelper.php files.
 */
require_once ASTERION_APP_FILE . 'config/config.php';
