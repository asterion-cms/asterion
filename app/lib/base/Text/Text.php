<?php
/**
* @class Text
*
* This is a helper class that has useful function for string operations.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Text {

    /**
    * Strip a text string.
    */
    static public function strip($text) {
        return strip_tags(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
    }

    /**
    * Cut and decode a string by the number of words.
    */
    static public function cutWords($text, $numWords) {
        $html = '';
        $textOriginal = $text;
        $text = strip_tags($text);
        $text = htmlspecialchars_decode($text);
        $textArray = explode(' ',$text);
        $textArray = array_slice($textArray, 0, $numWords);
        foreach ($textArray as $word) {
            $html .= $word.' ';
        }
        return $html;
    }

    /**
    * Cut a string by the number of words.
    */
    static public function cutWordsSimple($text, $numWords) {
        $html = '';
        foreach ($textArray as $word) {
            $word = (strlen($word)>25) ? substr($word, 0, 25).'...' : $word;
            $html .= $word.' ';
        }
        return $html;
    }

    /**
    * Convert a text string into a friendly one.
    */
    static public function simple($text, $space='-') {
        return str_replace('.','',Text::simpleUrl($text, $space));
    }

    /**
    * Convert a basename into a friendly one.
    */
    static public function simpleUrlFileBase($text, $space='-') {
        $info = pathinfo($text);
        return str_replace('.', '', Text::simpleUrl($info['filename'], $space));
    }

    /**
    * Convert a filename into a friendly one.
    */
    static public function simpleUrlFile($text, $space='-') {
        $info = pathinfo($text);
        return str_replace('.', '', Text::simpleUrl($info['filename'], $space)).'.'.$info['extension'];
    }

    /**
    * Convert a text string into a friendly url.
    */
    static public function simpleUrl($text, $space='-') {
        $search    = array('@','À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý','à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ð','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ','ñ','Ñ');
        $replace = array('','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','y','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y','n','N');
        $text = str_replace($search, $replace, trim($text));
        $text = preg_replace('/([^a-z0-9.]+)/i', $space, $text);
        $text = str_replace('.', '', $text);
        $text = strtolower($text);
        $text = trim($text);
        $text = trim($text, '-');
        return $text;
    }

    /**
    * Convert a text string into a code text.
    */
    static public function simpleCode($text, $space='-') {
        $search    = array('@','À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý','à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ð','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ','ñ','Ñ');
        $replace = array('','A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','Y','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y','n','N');
        $text = str_replace($search, $replace, trim($text));
        $text = preg_replace('/([^a-z0-9.-_]+)/i', $space, $text);
        $text = str_replace('.', '', $text);
        $text = trim($text);
        $text = trim($text, '-');
        return $text;
    }

    /**
    * Check if the text string is boolean.
    */
    static public function booleanText($text) {
        return ($text==1 || $text==true) ? __('yes') : __('no');
    }

    /**
    * Format a date number.
    */
    static public function dateNumber($number) {
        return str_pad($number, 2, "0", STR_PAD_LEFT);
    }

    /**
    * Format a value into money.
    */
    static public function money($number) {
        return number_format(floatval($number), 2, ',', '');
    }

    /**
    * Format a value into money.
    */
    static public function moneyDollar($number) {
        return Text::money($number).' <span>$USD</span>';
    }

    /**
    * Format a value into money.
    */
    static public function moneyEuros($number) {
        return Text::money($number).' <span>&euro;</span>';
    }

    /**
    * Convert a CSV into an array.
    */
    static public function csvArray($text) {
        $list = explode(',',$text);
        $result = array();
        foreach($list as $item) {
            if (trim($item)!='') {
                array_push($result,trim($item));
            }
        }
        return $result;
    }

    /**
    * Normalize a text string.
    */
    static public function normal($text) {
        return utf8_encode(html_entity_decode($text));
    }

    /**
    * Compress a text string.
    */
    static public function minimize($text) {
        return preg_replace(array('/ {2,}/', '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'), array(' ',''), $text);
    }

    /**
    * Escape a text string.
    */
    static public function escape($text) {
        return str_replace('"', '\"', $text);
    }

    /**
    * Escape an entire array.
    */
    static public function escapeTab(& $tab) {
        while (list($k, $v) = each($tab)) {
            if (!is_array($tab[$k])) {
                $tab[$k] = Text::escape($tab[$k]);
            } else {
                Text::escape($tab[$k]);
            }
        }
    }

    /**
    * Recode a text string.
    */
    static public function recodeText($text, $quotes = ENT_QUOTES, $charset = "utf-8") {
        $text = strtr($text,chr(146),"'");
        return trim(htmlentities(stripslashes($text), $quotes, $charset));
    }

    /**
    * Recode a text string with some special characters.
    */
    static public function recodeTextSQ($text) {
        $text = Text::recodeText($text, ENT_NOQUOTES);
        $text = str_replace("'", "&#039;", $text);
        $text = str_replace("&lt;", "<", $text);
        $text = str_replace("&gt;", ">", $text);
        return $text;
    }

    /**
    * Recode an entire array.
    */
    static public function recodeTab(& $tab, $sqOnly = array(), $mce = array()) {
        while (list($k, $v) = each($tab)) {
            if (!is_array($tab[$k])) {
                if (in_array($k, $sqOnly)) {
                    $tab[$k] = Text::recodeTextSQ($tab[$k]);
                } elseif (in_array($k, $mce)) {
                } else {
                    $tab[$k] = Text::recodeText($tab[$k]);
                }
            } else {
                if (in_array($k, $sqOnly)) {
                    $exc = array_merge($sqOnly, array_keys($tab[$k]));
                    Text::recodeTab($tab[$k], $exc, $mce);
                } elseif (in_array($k, $mce)) {
                    $exc = array_merge($mce, array_keys($tab[$k]));
                    Text::recodeTab($tab[$k], $sqOnly, $exc);
                } else {
                    Text::recodeTab($tab[$k], $sqOnly);
                }
            }
        }
    }

    /**
    * Recode the escape quotes.
    */
    static public function escQuotes($text) {
        return str_replace('\"', "&quot;", $text);
    }

    /**
    * Decode a text string.
    */
    static public function decodeText($text, $quotes = ENT_QUOTES, $charset = "utf-8") {
        return html_entity_decode($text, $quotes, $charset);
    }

    /**
    * Return an array with all the words that start with a given character.
    */
    static public function arrayWordsStarting($character, $string) {
        preg_match_all('/(?<!\w)'.$character.'\w+/', $string, $matches);
        return (isset($matches[0])) ? $matches[0] : array();
    }

}
?>