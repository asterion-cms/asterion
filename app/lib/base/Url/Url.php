<?php
/**
* @class Url
*
* This is a helper class to manage URLs.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Url {

    /**
    * Format a URL, adding the proper http, https or www if it's missing.
    */
    static public function format($url) {
        if (substr($url,0,8)=='https://' || substr($url,0,7)=='http://') {
            return $url;
        } else {
            if (substr($url,0,3)=='www') {
                return 'http://'.$url;
            } else {
                return 'http://www.'.$url;
            }
        }
    }
    
    /**
    * Return the current URL.
    */
    static public function currentUrl() {
        $url = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$url .= "s";}
            $url .= "://";
        if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
            $url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $url;
    }

    /**
    * Initialize the url when using multiple language.
    */
    static public function initLang() {
        $url = (isset($_GET['url'])) ? $_GET['url'] : '';
        $info = explode('/', $url);
        if (isset($info[0]) && $info[0]==ADMIN_URL_STRING) {
            //If the url points to the admin area
            $_GET['mode'] = 'admin';
            $_GET['lang'] = (isset($info[1])) ? $info[1] : '';
            $_GET['type'] = (isset($info[2]) && $info[2]!='') ? $info[2] : 'NavigationAdmin';
            $_GET['action'] = (isset($info[3])) ? $info[3] : '';
            $_GET['id'] = (isset($info[4])) ? $info[4] : '';
            $_GET['extraId'] = (isset($info[5])) ? $info[5] : '';
            $_GET['addId'] = (isset($info[6])) ? $info[6] : '';
        } else {
            //If the url points to the public area
            $_GET['lang'] = (isset($info[0])) ? $info[0] : '';
            $_GET['type'] = 'Navigation';
            $_GET['action'] = (isset($info[1])) ? $info[1] : '';
            $_GET['id'] = (isset($info[2])) ? $info[2] : '';
            $_GET['extraId'] = (isset($info[3])) ? $info[3] : '';
            $_GET['addId'] = (isset($info[4])) ? $info[4] : '';
        }
        $_GET['action'] = (isset($_GET['action']) && $_GET['action']!='') ? $_GET['action'] : 'intro';
    }

    /**
    * Initialize the url when using only one language.
    */
    static public function init() {
        if (count(Lang::langs())>1) {
            return Url::initLang();
        }
        $url = (isset($_GET['url'])) ? $_GET['url'] : '';
        $info = explode('/', $url);
        if (isset($info[0]) && $info[0]==ADMIN_URL_STRING) {
            //If the url points to the admin area
            $_GET['mode'] = 'admin';
            $_GET['type'] = (isset($info[1]) && $info[1]!='') ? $info[1] : 'NavigationAdmin';
            $_GET['action'] = (isset($info[2])) ? $info[2] : '';
            $_GET['id'] = (isset($info[3])) ? $info[3] : '';
            $_GET['extraId'] = (isset($info[4])) ? $info[4] : '';
            $_GET['addId'] = (isset($info[5])) ? $info[5] : '';
        } else {
            //If the url points to the public area
            $_GET['type'] = 'Navigation';
            $_GET['action'] = (isset($info[0])) ? $info[0] : '';
            $_GET['id'] = (isset($info[1])) ? $info[1] : '';
            $_GET['extraId'] = (isset($info[2])) ? $info[2] : '';
            $_GET['addId'] = (isset($info[3])) ? $info[3] : '';
        }
        $_GET['lang'] = LANGS;
        $_GET['action'] = (isset($_GET['action']) && $_GET['action']!='') ? $_GET['action'] : 'intro';
    }

    /**
    * Create an URL using the language code.
    */
    static public function urlLang($newLang) {
        $url = (isset($_GET['url'])) ? $_GET['url'] : '';
        $info = explode('/', $url);
        if (isset($info[0]) && $info[0]==ADMIN_URL_STRING) {
            $info[1] = $newLang;
        } else {
            $info[0] = $newLang;
        }
        return LOCAL_URL.implode('/', $info);
    }

    /**
    * Create an URL.
    */
    static public function urlPage($page) {
        $url = (isset($_GET['url'])) ? $_GET['url'] : '';
        $pageUrl = (__('pageUrl')!='pageUrl') ? __('pageUrl') : PAGER_URL_STRING;
        return LOCAL_URL.$url.'?'.$pageUrl.'='.$page;
    }

    /**
    * Format an URL using the language code.
    */
    static public function getUrlLang($url='', $admin=false) {
        if ($admin) {
            return LOCAL_URL.ADMIN_URL_STRING.'/'.Lang::active().'/'.$url;
        } else {
            return LOCAL_URL.Lang::active().'/'.$url;
        }
    }

    /**
    * Format an URL.
    */
    static public function getUrl($url='', $admin=false) {
        if (count(Lang::langs())>1) {
            return Url::getUrlLang($url, $admin);
        }
        if ($admin) {
            return LOCAL_URL.ADMIN_URL_STRING.'/'.$url;
        } else {
            return LOCAL_URL.$url;
        }
    }

}
?>