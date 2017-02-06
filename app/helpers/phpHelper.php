<?php
/**
* @file
*
* The phpHelper.php includes several functions that are not existing
* in the basic PHP version. It also includes some functions that can be
* used as shortcuts for common actions.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/

/**
* Function to check if the "get_called_class" exists
* and create it if it does not.
*/
if (!function_exists('get_called_class')) {
    function get_called_class($flag=false, $indexFlag=1) {
        if (!$flag) {
        	$flag = debug_backtrace();
        }
        if (!isset($flag[$indexFlag]) && DEBUG) {
        	throw new Exception("Cannot find called class. Stack level too deep.");
        }
        if (!isset($flag[$indexFlag]['type']) && DEBUG) {
            throw new Exception ('type not set');
        }
        else switch ($flag[$indexFlag]['type']) {
            case '::':
                $indexFlagines = file($flag[$indexFlag]['file']);
                $i = 0;
                $callerLine = '';
                do {
                    $i++;
                    $callerLine = $indexFlagines[$flag[$indexFlag]['line']-$i] . $callerLine;
                } while (strpos($callerLine,$flag[$indexFlag]['function']) === false);
                preg_match('/([a-zA-Z0-9\_]+)::'.$flag[$indexFlag]['function'].'/',
                            $callerLine,
                            $matches);
                if (!isset($matches[1]) && DEBUG) {
                    throw new Exception ("Could not find caller class: originating method call is obscured.");
                }
                switch ($matches[1]) {
                    case 'self':
                    case 'parent':
                        return get_called_class($flag,$indexFlag+1);
                    default:
                        return $matches[1];
                }
            case '->': switch ($flag[$indexFlag]['function']) {
                    case '__get':
                        if (!is_object($flag[$indexFlag]['object'])) throw new Exception ("Edge case fail. __get called on non object.");
                        return get_class($flag[$indexFlag]['object']);
                    default: return $flag[$indexFlag]['class'];
                }
            default: 
                if (DEBUG) {
                    throw new Exception ("Unknown backtrace method type.");
                }
            break;
        }
    } 
}

/**
* Function to fill an array with keys.
*/
function array_fillkeys($target, $value='') {
    $filledArray = array();
    foreach($target as $key=>$val) {
        $filledArray[$val] = is_array($value) ? $value[$key] : $value;
    }
    return $filledArray;
}

/**
* Function to check if a URL exists.
*/
function url_exists($url) {
    //Check if a URL exists
    return (!$fp = curl_init($url)) ? false : true;
}

/**
* Function to remove an entire directory on the server.
*/
function rrmdir($dir) {
    //Remove an entire directoy 
    foreach(glob($dir.'/*') as $file) { 
        if(is_dir($file)) {
            rrmdir($file);
        } else {
            @unlink($file);
        }
    }
    @rmdir($dir);
}

/**
* Function to translate using the translation "code" of the LangTrans object.
*/
function __($code) {
    return LangTrans::translate($code);
}

/**
* Function to build an URL using the correct path to the website.
* 
* $url: The single path for the URL
* $admin: Boolean to determine if the URL is for the BackEnd
* 
* Example:
* echo url('about-us');
* > http://localhost/asterion/about-us
*/
function url($url='', $admin=false) {
    if (!is_array($url)) {
        return Url::getUrl($url, $admin);
    } else {
        return Url::getUrl($url[Lang::active()], $admin);
    }
}

/**
* Function to do a recursive glob search
*/
function rglob($pattern, $flags=0) {
    $files = glob($pattern, $flags); 
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $directory) {
        $files = array_merge($files, rglob($directory.'/'.basename($pattern), $flags));
    }
    return $files;
}
?>
