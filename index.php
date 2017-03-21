<?php
/**
* @file
*
* The index.php file is one of the main files on the Asterion framework.
* It is in charge of loading the configuration, intializing the site,
* loading the content variables and handling the HTML response to the user.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/

/**
* The APP_FOLDER constant defines the public folder of the application.
* It can be used to load different versions of it like test, develop or production.
* Then, Asterion loads the proper configuration depending on that version.
*/
define('APP_FOLDER', 'recipes_ecu');
require_once(APP_FOLDER.'/config/config.php');

try {
    /**
    * If the DEBUG mode is activated on the configuration file, Asterion allows
    * error reporting and runs the script to create the basic tables.
    * It also saves the default data for the administration system.
    */
    if (DEBUG) {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        Init::initSite();
    }
    
    /**
    * Asterion initializes the common services.
    * The Url::init() function parses the URL of the request.
    * The Lang::init() function loads the translations in a global array.
    * The Params::init() function loads the parameters in a global array.
    */
    Url::init();
    Lang::init();
    Params::init();

    /**
    * Asterion loads the controller according to the "type" variable
    * defined in the URL, by default it will use the Navigation controller.
    * Then it loads the content and some extra informations for the template.
    */
    $control = Controller_Factory::factory($_GET, $_POST, $_FILES);
    $content = $control->controlActions();
    $title = $control->getTitle();
    $header = $control->getHeader();
    $metaKeywords = $control->getMetaKeywords();
    $metaDescription = $control->getMetaDescription();
    $metaImage = $control->getMetaImage();
    $metaUrl = $control->getMetaUrl();
    $mode = $control->getMode();
} catch (Exception $e) {
    $mode = 'ajax';
    $content = '<pre>'.$e->getMessage().'</pre>';
    $content .= (DEBUG) ? '<pre>'.$e->getTraceAsString().'</pre>' : '';
}

/**
* Asterion checks the "mode" variable to return the response.
* By default it uses the public.php template, however it is possible to
* create or add customized headers to the response.
*/
$mode = (isset($mode)) ? $mode : 'public';
switch ($mode) {
    default:
        $file = BASE_FILE.'visual/templates/'.$mode.'.php';
        if (file_exists($file)) {
            include($file);
        }
    break;
    case 'admin':
        include APP_FILE.'visual/templates/admin.php';
    break;
    case 'ajax':
        echo $content;
    break;
    case 'json':
        header('Content-Type: application/json');
        echo $content;
    break;
    case 'js':
        header('Content-Type: application/javascript');
        echo $content;
    break;
    case 'zip':
        header('Content-Type: application/zip');
        echo $content;
    break;
}
?>
