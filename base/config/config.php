<?php
/**
* @file
*
* The config.php is the configuration file of the Asterion instance.
* It has all the constants that are used in the framework and it loads
* the autoload.php and phpHelper.php files.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/

/**
* The TITLE constant defines the title of the page.
* After the installation it is preferable to use the "title" parameter.
*/
define('TITLE', 'Base site');

/**
* The following constants define the paths to the different
* main folders of the framework.
* The most important ones are LOCAL_URL and LOCAL_FILE
* that must point to the public and private files of the website.
*/
define('SERVER_URL', 'https://www.plasticmails.net');
define('BASE_STRING','/base/');
define('LOCAL_URL', SERVER_URL.BASE_STRING);
define('LOCAL_FILE', $_SERVER['DOCUMENT_ROOT'].BASE_STRING);
define('BASE_URL', LOCAL_URL.APP_FOLDER.'/');
define('BASE_FILE', LOCAL_FILE.APP_FOLDER.'/');
define('APP_URL', LOCAL_URL.'app/');
define('APP_FILE', LOCAL_FILE.'app/');
define('DATA_URL', APP_URL.'data/');
define('DATA_FILE', APP_FILE.'data/');
define('DATA_LOCAL_URL', BASE_URL.'data/');
define('DATA_LOCAL_FILE', BASE_FILE.'data/');

/**
* The DEBUG constant defines if we are in debug mode or not.
* It it used to show error messages or build the database automatically.
* When in production it must be set to false.
*/
define('DEBUG', true);

/**
* The LANGUAGES constant defines the languages of the website.
* They are separated by the character ":", In this version we have only
* English, French and Spanish available.
*/
define('LANGS', 'en:es:fr');

/**
* The system starts a session with a proper name for the website.
*/
define('SESSION_NAME', 'asterion');
session_name(SESSION_NAME);
session_start();

/**
* The COOKIE_TIME constant defines the duration of cookies in the site.
*/
define('COOKIE_TIME', 3600000);

/**
* The following constants are used to check when the system parses the URL.
*/
define('ADMIN_URL_STRING', 'admin');
define('PAGER_URL_STRING', 'page');

/**
* The following constants are used to define the working paths.
* They are just shortcuts to certain common used folders in Asterion.
*/
define('MODEL_FILE', BASE_FILE.'lib/');
define('FRAMEWORK_FILE', APP_FILE.'lib/');
define('ADMIN_URL', LOCAL_URL.ADMIN_URL_STRING.'/');
define('STOCK_URL', BASE_URL.'stock/');
define('STOCK_FILE', BASE_FILE.'stock/');
define('HELPERS_URL', APP_URL.'helpers/');
define('HELPERS_FILE', APP_FILE.'helpers/');

/**
* Asterion defines where the objects must be checked when using the autoload.
*/
$_ENV['locations'][] = MODEL_FILE;
$_ENV['locations'][] = FRAMEWORK_FILE.'base/';
$_ENV['locations'][] = FRAMEWORK_FILE.'admin/';

/**
* The following lines define the access to the database.
*/
define('DB_USE', true);
define('DB_SERVER', 'localhost');
define('DB_USER', 'plastic_base');
define('DB_PASSWORD', 'directorio123');
define('DB_PORT', '3306');
define('DB_NAME', 'plastic_base');
define('DB_TYPE', 'mysql');
define('DB_PREFIX', 'base_');
define("PDO_DSN","mysql:host=".DB_SERVER.";port=".DB_PORT.";dbname=".DB_NAME);

/**
* The LOGO constant defines the path to the logo for the website.
*/
define('LOGO', BASE_URL.'visual/img/logo.jpg');

/**
* The following values tell Asterion what sizes of images it should store.
*/
define('SAVE_IMAGE_ORIGINAL', true);
define('SAVE_IMAGE_HUGE', true);
define('SAVE_IMAGE_WEB', true);
define('SAVE_IMAGE_SMALL', true);
define('SAVE_IMAGE_THUMB', true);
define('SAVE_IMAGE_SQUARE', true);

/**
* The following dimension constants are used to create the different
* versions of the images in Asterion.
*/
define('WIDTH_HUGE', 1600);
define('HEIGHT_MAX_HUGE', 2400);
define('WIDTH_WEB', 600);
define('HEIGHT_MAX_WEB', 1400);
define('WIDTH_SMALL', 250);
define('HEIGHT_MAX_SMALL', 500);
define('WIDTH_THUMB', 120);
define('HEIGHT_MAX_THUMB', 120);
define('WIDTH_SQUARE', 100);

/**
* The EMAIL constant defines the main email for the website.
* After the installation it is preferable to use the "email" parameter.
*/
define('EMAIL', 'info@asterion-cms.com');

/**
* Asterion defines UTF8 for the internal encoding of the website.
*/
mb_internal_encoding('UTF-8');

/**
* Define a date timezone
*/
date_default_timezone_set('America/Los_Angeles');

/**
* The framework loads the autoload.php and phpHelper.php files.
*/
require_once(APP_FILE.'helpers/autoload.php');
require_once(APP_FILE.'helpers/phpHelper.php');

?>