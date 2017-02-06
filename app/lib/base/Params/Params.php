<?php
/**
* @class Params
*
* This class contains the parameters to run the website.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Params extends Db_Object {

    /**
    * Retrieve the values in the database and load them in memory.
    */
    static public function init() {
        $query = 'SELECT code, information
                    FROM '.Db::prefixTable('Params');
        $items = array();
        $result = Db::returnAll($query);
        foreach ($result as $item) {
            $code = $item['code'];
            $items[$code] = Text::decodeText($item['information']);
            if (strpos($code, 'email-')!==false || strpos($code, 'metainfo-')!==false || strpos($code, 'linksocial-')!==false) {
                $code = str_replace('email-', '', $code);
                $code = str_replace('metainfo-', '', $code);
                $code = str_replace('linksocial-', '', $code);
                $items[$code] = $item['information'];
            }
        }
        $_ENV['params'] = $items;
    }

    /**
    * Get the list of parameters.
    */
    static public function paramsList(){
        return $_ENV['params'];
    }

    /**
    * Get a parameter. The script also searches for the active language.
    */
    static public function param($code){
        if (isset($_ENV['params'][$code.'-'.Lang::active()])) {
            return $_ENV['params'][$code.'-'.Lang::active()];
        } else {
            return (isset($_ENV['params'][$code])) ? $_ENV['params'][$code] : '';
        }
    }

    /**
    * Load the initial parameters for the website.
    */
    static public function saveInitialValues() {
        $params = new Params();
        $params->createTable();
        $params = Params::countResults();
        if ($params == 0) {
            $itemsUrl = DATA_LOCAL_FILE.'Params.json';
            if (!file_exists($itemsUrl)) {
                $itemsUrl = DATA_FILE.'Params.json';
            }
            $items = json_decode(file_get_contents($itemsUrl), true);
            foreach (Lang::langs() as $lang) {
                $items[] = array('code'=>'metainfo-titlePage-'.$lang, 'name'=>'Title Page - '.Lang::getLabel($lang), 'information'=>TITLE);
                $items[] = array('code'=>'metainfo-metaDescription-'.$lang, 'name'=>'Meta Description - '.Lang::getLabel($lang), 'information'=>TITLE.'...');
                $items[] = array('code'=>'metainfo-metaKeywords-'.$lang, 'name'=>'Meta Keywords - '.Lang::getLabel($lang), 'information'=>TITLE.'...');
            }
            $items[] = array('code'=>'email', 'name'=>'Email', 'information'=>EMAIL);
            $items[] = array('code'=>'email-contact', 'name'=>'Emails sent in the contact section', 'information'=>EMAIL);
            foreach ($items as $item) {
                $itemOld = Params::readFirst(array('where'=>'code="'.$item['code'].'"'));
                if ($itemOld->id()=='') {                    
                    $itemSave = new Params();
                    $itemSave->insert($item);
                }
            }
        }
    }

}
?>