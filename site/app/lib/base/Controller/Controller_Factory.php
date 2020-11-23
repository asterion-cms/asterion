<?php
/**
 * @class Controller_Factory
 *
 * This class is the factory to load the controllers of the content objects.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Controller_Factory
{

    /**
     * The main function to load the controller for the content object.
     * $GET : Array with the loaded $_GET values.
     * $POST : Array with the loaded $_POST values.
     * $FILES : Array with the loaded $_FILES values.
     */
    public static function factory($GET = [], $POST = [], $FILES = [])
    {
        $type = (isset($GET['type'])) ? snakeToCamel($GET['type']) : '';
        $objectController = $type . '_Controller';
        $addLocation = $type . '/' . $objectController . '.php';
        foreach (unserialize(ASTERION_OBJECT_LOCATIONS) as $location) {
            if (is_file($location . $addLocation)) {
                return new $objectController($GET, $POST, $FILES);
            }
        }
        if (ASTERION_DEBUG) {
            throw new Exception('The controller "' . $type . '" does not exist.');
        } else {
            header('Location: ' . ASTERION_LOCAL_URL);
            exit();
        }
    }

}
