<?php
/**
 * @class Text
 *
 * This is a helper class that has useful function for string operations.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Text
{

    /**
     * Strip a text string.
     */
    public static function strip($text)
    {
        return strip_tags(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Cut and decode a string by the number of words.
     */
    public static function cutWords($text, $numWords)
    {
        $html = '';
        $textOriginal = $text;
        $text = strip_tags($text);
        $text = htmlspecialchars_decode($text);
        $textArray = explode(' ', $text);
        $textArray = array_slice($textArray, 0, $numWords);
        foreach ($textArray as $word) {
            $html .= $word . ' ';
        }
        return $html;
    }

    /**
     * Cut a string by the number of words.
     */
    public static function cutWordsSimple($text, $numWords)
    {
        $html = '';
        foreach ($textArray as $word) {
            $word = (strlen($word) > 25) ? substr($word, 0, 25) . '...' : $word;
            $html .= $word . ' ';
        }
        return $html;
    }

    /**
     * Convert a text string into a friendly one.
     */
    public static function simple($text, $space = '-')
    {
        return str_replace('.', '', Text::simpleUrl($text, $space));
    }

    /**
     * Convert a basename into a friendly one.
     */
    public static function simpleUrlFileBase($text, $space = '-')
    {
        $info = pathinfo($text);
        return str_replace('.', '', Text::simpleUrl($info['filename'], $space));
    }

    /**
     * Convert a filename into a friendly one.
     */
    public static function simpleUrlFile($text, $space = '-')
    {
        $info = pathinfo($text);
        return str_replace('.', '', Text::simpleUrl($info['filename'], $space)) . '.' . $info['extension'];
    }

    /**
     * Convert a text string into a friendly url.
     */
    public static function simpleUrl($text, $space = '-')
    {
        $search = ['@', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'ñ', 'Ñ'];
        $replace = ['', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'n', 'N'];
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
    public static function simpleCode($text, $space = '-')
    {
        $search = ['@', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'ñ', 'Ñ'];
        $replace = ['', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'n', 'N'];
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
    public static function booleanText($text)
    {
        return ($text == 1 || $text == true) ? __('yes') : __('no');
    }

    /**
     * Format a date number.
     */
    public static function dateNumber($number)
    {
        return str_pad($number, 2, "0", STR_PAD_LEFT);
    }

    /**
     * Format a value into money.
     */
    public static function money($number)
    {
        return number_format(floatval($number), 2, ',', '');
    }

    /**
     * Format a value into money.
     */
    public static function moneyDollar($number)
    {
        return Text::money($number) . ' <span>$USD</span>';
    }

    /**
     * Format a value into money.
     */
    public static function moneyEuros($number)
    {
        return Text::money($number) . ' <span>&euro;</span>';
    }

    /**
     * Convert a CSV into an array.
     */
    public static function csvArray($text)
    {
        $list = explode(',', $text);
        $result = [];
        foreach ($list as $item) {
            if (trim($item) != '') {
                array_push($result, trim($item));
            }
        }
        return $result;
    }

    /**
     * Normalize a text string.
     */
    public static function normal($text)
    {
        return utf8_encode(html_entity_decode($text));
    }

    /**
     * Compress a text string.
     */
    public static function minimize($text)
    {
        return preg_replace(['/ {2,}/', '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'], [' ', ''], $text);
    }

    /**
     * Escape a text string.
     */
    public static function escape($text)
    {
        return str_replace('"', '\"', $text);
    }

    /**
     * Escape an entire array.
     */
    public static function escapeTab(&$tab)
    {
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
    public static function recodeText($text, $quotes = ENT_QUOTES, $charset = "utf-8")
    {
        $text = strtr($text, chr(146), "'");
        return trim(htmlentities(stripslashes($text), $quotes, $charset));
    }

    /**
     * Recode a text string with some special characters.
     */
    public static function recodeTextSQ($text)
    {
        $text = Text::recodeText($text, ENT_NOQUOTES);
        $text = str_replace("'", "&#039;", $text);
        $text = str_replace("&lt;", "<", $text);
        $text = str_replace("&gt;", ">", $text);
        return $text;
    }

    /**
     * Recode an entire array.
     */
    public static function recodeTab(&$tab, $sqOnly = [], $mce = [])
    {
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
    public static function escQuotes($text)
    {
        return str_replace('\"', "&quot;", $text);
    }

    /**
     * Decode a text string.
     */
    public static function decodeText($text, $quotes = ENT_QUOTES, $charset = "utf-8")
    {
        return html_entity_decode($text, $quotes, $charset);
    }

    /**
     * Return an array with all the words that start with a given character.
     */
    public static function arrayWordsStarting($character, $string)
    {
        preg_match_all('/(?<!\w)' . $character . '\w+/', $string, $matches);
        return (isset($matches[0])) ? $matches[0] : [];
    }

}
