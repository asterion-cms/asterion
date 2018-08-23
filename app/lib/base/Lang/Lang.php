<?php
/**
* @class Lang
*
* This class represents a language for the website.
* It is used to manage the different translations.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Lang extends Db_Object {


    /**
    * Initialize the translations.
    */
    static public function init() {
        if (isset($_GET['lang'])) {
            $langs = Lang::configLangs();
            if (count($langs)>1) {
                $lang = Lang::read($_GET['lang']);
                if ($lang->id()=='') {
                    $lang = Lang::readFirst(array('order'=>'ord'));
                }
                Session::set('lang', $lang->id());
            } else {
                Session::set('lang', $langs[0]);
            }
            $query = 'SELECT code, translation_'.Session::get('lang').' as translation
                            FROM '.Db::prefixTable('LangTrans');
            $items = array();
            $result = Db::returnAll($query);
            foreach ($result as $item) {
                $items[$item['code']] = $item['translation'];
            }
            $_ENV['lang'] = $items;
        }
    }

    /**
    * Get the languages.
    */
    static public function langs() {
        if (!isset($_ENV['langs'])) {
            Lang::fillInfo();
        }
        return $_ENV['langs'];
    }

    /**
    * Get the language labels.
    */
    static public function langLabels() {
        if (!isset($_ENV['langLabels'])) {
            Lang::fillInfo();
        }
        return $_ENV['langLabels'];
    }

    /**
    * Fill the laguange code and labels into ENV variables.
    */
    static public function fillInfo() {
        $query = 'SELECT * FROM '.Db::prefixTable('Lang').' ORDER BY ord';
        $langIds = array();
        $result = Db::returnAll($query);
        foreach ($result as $item) {
            $langIds[] = $item['idLang'];
        }
        $_ENV['langs'] = $langIds;
        $_ENV['langLabels'] = $result;
    }

    /**
    * Get the active language.
    */
    static public function active() {
        return Session::get('lang');
    }

    /**
    * Get the label of a language.
    */
    static public function getLabel($lang) {
        foreach (Lang::langLabels() as $langLabel) {
            if ($langLabel['idLang']==$lang) {
                return $langLabel['name'];
            }
        }
    }

    /**
    * Format the table field using the languages.
    */
    static public function field($field) {
        $result = '';
        foreach (Lang::langs() as $lang) {
            $result .= $field.'_'.$lang.',';
        }
        return substr($result, 0, -1);
    }

    /**
    * Initialize the table with default values.
    */
    static public function saveInitialValues() {
        $lang = new Lang();
        $lang->createTable();
        if ($lang->countResults()==0) {
            foreach (Lang::configLangs() as $code) {
                $lang = Lang::read($code);
                if ($lang->id()=='') {
                    $lang->insert(array('idLang'=>$code, 'name'=>Lang::langCode($code)), array('initial'=>true));
                }
            }
        }
    }

    /**
    * Return the language label.
    */
    static public function langCode($code) {
        $langs = array('en'=>'English', 'fr'=>'Français', 'es'=>'Español');
        return (isset($langs[$code])) ? $langs[$code] : $code;
    }

    /**
    * Check the langs in the config file.
    */
    static public function configLangs() {
        return explode(':',LANGS);
    }

    /**
    * Overwrite the insert function and check all the objects.
    */
    public function insert($values, $options=array()) {
        parent::insert($values, $options);
        if ($this->id()!='' && !isset($options['initial'])) {
            $this->updateObjects('insert', $values);
            Init::saveLangTrans($this->id());
        }
    }

    /**
    * Overwrite the modify function and check all the objects.
    */
    public function modify($values, $options=array()) {
        $values['oldId'] = Text::simpleUrl($values['idLang_oldId'], '');
        $values['newId'] = Text::simpleUrl($values['idLang'], '');
        parent::modify($values, $options);
        if ($this->id()!='' && $values['oldId']!='' && $values['newId']!='' && $values['oldId']!=$values['newId']) {
            $this->updateObjects('modify', $values);
        }
    }

    /**
    * Overwrite the delete function and check all the objects.
    */
    public function delete() {
        parent::delete();
        if ($this->id()!='') {
            $this->updateObjects('delete', array());
        }
    }

    /**
    * Modify all the objects that have translatable attributes.
    */
    public function updateObjects($mode, $values) {
        $objectNames = File::scanDirectoryObjects();
        foreach ($objectNames as $objectName) {
            $object = new $objectName();
            $attributes = $object->getAttributes();
            foreach ($attributes as $attribute) {
                $attributeName = (string)$attribute->name;
                if ((string)$attribute->lang=='true') {
                    switch ($mode) {
                        case 'insert':
                            $query = 'ALTER TABLE '.Db::prefixTable($objectName).'
                                        ADD '.$attributeName.'_'.$values['idLang'].'
                                        VARCHAR(255) COLLATE utf8_unicode_ci';
                            Db::execute($query);
                        break;
                        case 'modify':
                            $query = 'ALTER TABLE '.Db::prefixTable($objectName).'
                                        CHANGE '.$attributeName.'_'.$values['oldId'].' '.$attributeName.'_'.$values['newId'].'
                                        VARCHAR(255) COLLATE utf8_unicode_ci';
                            Db::execute($query);
                        break;
                        case 'delete':
                            $query = 'ALTER TABLE '.Db::prefixTable($objectName).'
                                        DROP '.$attributeName.'_'.$this->id();
                            Db::execute($query);
                        break;
                    }
                }
            }
        }
    }

}
?>