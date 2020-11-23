<?php
/**
 * @file
 *
 * The config.php is the configuration file of the Asterion instance.
 * It has all the constants that are used in the framework and it loads
 * the autoload.php and phpHelper.php files.
 * Here are the options that should remain static on every site,
 * however it is possible to change them globally.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 3.0.1
 */

/**
 * The following constants are used to define the working paths.
 * They are just shortcuts to certain common used folders in Asterion.
 */
define('ASTERION_BASE_URL', ASTERION_LOCAL_URL . APP_FOLDER . '/');
define('ASTERION_BASE_FILE', ASTERION_LOCAL_FILE . APP_FOLDER . '/');
define('ASTERION_APP_URL', ASTERION_LOCAL_URL . 'app/');
define('ASTERION_APP_FILE', ASTERION_LOCAL_FILE . 'app/');
define('ASTERION_MODEL_FILE', ASTERION_BASE_FILE . 'lib/');
define('ASTERION_FRAMEWORK_FILE', ASTERION_APP_FILE . 'lib/');
define('ASTERION_ADMIN_URL', ASTERION_LOCAL_URL . ASTERION_ADMIN_URL_STRING . '/');
define('ASTERION_STOCK_URL', ASTERION_BASE_URL . 'stock/');
define('ASTERION_STOCK_FILE', ASTERION_BASE_FILE . 'stock/');
define('ASTERION_DATA_FILE', ASTERION_APP_FILE . 'data/');

/**
 * Asterion defines where the objects must be checked when using the autoload.
 */
define('ASTERION_OBJECT_LOCATIONS', serialize([
    ASTERION_MODEL_FILE,
    ASTERION_FRAMEWORK_FILE . 'base/',
    ASTERION_FRAMEWORK_FILE . 'admin/',
    ASTERION_FRAMEWORK_FILE . 'helpers/'
]));

/**
 * The system starts a session with a proper name for the website.
 */
session_name(ASTERION_SESSION_NAME);
session_start();

/**
 * Asterion defines UTF8 for the internal encoding of the website.
 */
mb_internal_encoding('UTF-8');

/**
 * The framework loads the autoload.php and phpHelper.php files.
 */
require_once ASTERION_APP_FILE . 'helpers/autoload.php';
require_once ASTERION_APP_FILE . 'helpers/phpHelper.php';
