<?php
/**
* @class Controller_Factory
*
* This class is the factory to load the controllers of the content objects.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Controller_Factory {

    /**
    * The main function to load the controller for the content object.
    * $GET : Array with the loaded $_GET values.
    * $POST : Array with the loaded $_POST values.
    * $FILES : Array with the loaded $_FILES values.
    */
    static function factory($GET=array(), $POST=array(), $FILES=array()) {
        $type = (isset($GET['type'])) ? $GET['type'] : '';
        $objectController = $type.'_Controller';
        $addLocation = $type.'/'.$objectController.'.php';
        foreach ($_ENV['locations'] as $location) {
            if (is_file($location.$addLocation)) {
                return new $objectController($GET, $POST, $FILES);
            }    
        }
        throw new Exception('The controller "'.$type.'" does not exist.');
    }

}
?>
