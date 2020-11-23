<?php
/**
 * @class Session
 *
 * This is a helper class to manage the session in an easier way.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Session
{

    /**
     * Get a session element.
     */
    public static function get($name)
    {
        return (isset($_SESSION[ASTERION_SESSION_NAME][$name])) ? $_SESSION[ASTERION_SESSION_NAME][$name] : '';
    }

    /**
     * Get the session login information.
     */
    public static function getLogin($name)
    {
        return (isset($_SESSION[ASTERION_SESSION_NAME]['info'][$name])) ? $_SESSION[ASTERION_SESSION_NAME]['info'][$name] : '';
    }

    /**
     * Set a session element.
     */
    public static function set($name, $value)
    {
        $_SESSION[ASTERION_SESSION_NAME][$name] = $value;
    }

    /**
     * Delete a session element.
     */
    public static function delete($name)
    {
        if (isset($_SESSION[ASTERION_SESSION_NAME][$name])) {
            $_SESSION[ASTERION_SESSION_NAME][$name] = '';
            unset($_SESSION[ASTERION_SESSION_NAME][$name]);
        }
    }

}
