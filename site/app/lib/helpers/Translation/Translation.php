<?php
/**
 * @class Translation
 *
 * This class contains all the translations of the phrases and words in Asterion.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Translation extends Db_Object
{

    /**
     * Get the translation of a phrase or a word using its code.
     */
    public static function translate($code)
    {
        return (isset($_ENV['translations'][$code])) ? $_ENV['translations'][$code] : $code;
    }

    /**
     * Load the translations for a specific language.
     */
    public static function load($idLanguage)
    {
        $query = 'SELECT code, translation_' . $idLanguage . ' as translation FROM ' . Db::prefixTable('translation');
        $items = [];
        foreach (Db::returnAll($query) as $item) {
            $items[$item['code']] = $item['translation'];
        }
        return $items;
    }

}
