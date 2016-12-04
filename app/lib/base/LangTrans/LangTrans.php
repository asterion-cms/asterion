<?php
/**
* @class LangTrans
*
* This class contains all the translations of the phrases and words in Asterion.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class LangTrans extends Db_Object {

    /**
    * Get the translation of a phrase or a word using its code.
    */
    static public function translate($code) {
        return (isset($_ENV['lang'][$code])) ? $_ENV['lang'][$code] : $code;
    }

}
?>