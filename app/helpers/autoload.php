<?php
/**
* @file
*
* The autoload.php file charges the proper class in memory
* when an object is instantiated. It searches in the locations defined
* in the configuration file.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
spl_autoload_register(function ($className) {
    $objectName = $className;
    if (strpos($className, '_')!==false) {
        $class = explode('_', $className);
        $objectName = $class[0];
    }
    $addLocation = $objectName.'/'.$className.'.php';
    foreach ($_ENV['locations'] as $location) {
        if (is_file($location.$addLocation)) {
            require_once($location.$addLocation);
            return true;
        }
    }
    throw new Exception('Error on Autoload: The file for '.$className.' does not exist');
});
?>
