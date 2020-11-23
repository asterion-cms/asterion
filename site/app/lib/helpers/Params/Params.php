<?php
/**
 * @class Params
 *
 * This class contains the parameters to run the website.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Params extends Db_Object
{

    /**
     * Retrieve the values in the database and load them in memory.
     */
    public static function init()
    {
        if (ASTERION_DB_USE == true) {
            $query = 'SELECT code, information
                        FROM ' . Db::prefixTable('params');
            $items = [];
            $result = Db::returnAll($query);
            foreach ($result as $item) {
                $code = $item['code'];
                $items[$code] = Text::decodeText($item['information']);
                if (strpos($code, 'email_') !== false || strpos($code, 'metainfo_') !== false || strpos($code, 'linksocial_') !== false) {
                    $code = str_replace('email_', '', $code);
                    $code = str_replace('metainfo_', '', $code);
                    $code = str_replace('linksocial_', '', $code);
                    $items[$code] = $item['information'];
                }
            }
            $_ENV['params'] = $items;
        }
    }

    /**
     * Get the list of parameters.
     */
    public static function paramsList()
    {
        return $_ENV['params'];
    }

    /**
     * Get a parameter. The script also searches for the active language.
     */
    public static function param($code)
    {
        if (isset($_ENV['params'][$code . '_' . Language::active()])) {
            return $_ENV['params'][$code . '_' . Language::active()];
        } else {
            return (isset($_ENV['params'][$code])) ? $_ENV['params'][$code] : '';
        }
    }

    /**
     * Load the initial parameters for the website.
     */
    public static function saveInitialValues()
    {
        $params = new Params();
        $params->createTable();
        $params = (new Params)->countResults();
        if ($params == 0) {
            $itemsUrl = ASTERION_DATA_FILE . 'Params.json';
            $items = json_decode(file_get_contents($itemsUrl), true);
            foreach (Language::languages() as $language) {
                $items[] = ['code' => 'title_page_' . $language['id'], 'name' => 'Title Page - ' . $language['name'], 'information' => ASTERION_TITLE];
                $items[] = ['code' => 'meta_description_' . $language['id'], 'name' => 'Meta Description - ' . $language['name'], 'information' => ASTERION_TITLE . '...'];
                $items[] = ['code' => 'meta_keywords_' . $language['id'], 'name' => 'Meta Keywords - ' . $language['name'], 'information' => ASTERION_TITLE . '...'];
            }
            $items[] = ['code' => 'email', 'name' => 'Email', 'information' => ASTERION_EMAIL];
            $items[] = ['code' => 'email_contact', 'name' => 'Emails sent in the contact section', 'information' => ASTERION_EMAIL];
            foreach ($items as $item) {
                $itemSave = new Params();
                $itemSave->insert($item);
            }
        }
    }

}
