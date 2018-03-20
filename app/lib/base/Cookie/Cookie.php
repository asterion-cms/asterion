<?php
/**
* @class Cookie
*
* This is a helper class to manage cookies in an easier way.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Cookie {

    /**
    * Get a cookie value
    */
    static public function get($name) {
        return (isset($_COOKIE[$name])) ? $_COOKIE[$name] : '';
    }

    /**
    * Set a cookie value
    */
    static public function set($name, $value) {
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
        setcookie($name, $value, time() + COOKIE_TIME, '/', $domain, false);
    }

    /**
    * Delete a cookie
    */
    static public function delete($name) {
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
        setcookie($name, '', time() - COOKIE_TIME, '/', $domain, false);
        unset($_COOKIE[$name]);
    }

}
?>