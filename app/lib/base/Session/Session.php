<?php
/**
* @class Session
*
* This is a helper class to manage the session in an easier way.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Session {
    
    /**
    * Get a session element.
    */
    static public function get($name) {
        return (isset($_SESSION[SESSION_NAME][$name])) ? $_SESSION[SESSION_NAME][$name] : '';
    }

    /**
    * Get the session login information.
    */
    static public function getLogin($name) {
        return (isset($_SESSION[SESSION_NAME]['info'][$name])) ? $_SESSION[SESSION_NAME]['info'][$name] : '';
    }

    /**
    * Set a session element.
    */
    static public function set($name, $value) {
        $_SESSION[SESSION_NAME][$name] = $value;
    }
    
    /**
    * Delete a session element.
    */
    static public function delete($name) {
        if (isset($_SESSION[SESSION_NAME][$name])) {
            $_SESSION[SESSION_NAME][$name] = '';
            unset($_SESSION[SESSION_NAME][$name]);
        }
    }
    
}
?>