<?php
/**
* @class HtmlSectionAdmin
*
* This class represents a simple HTML section for the administration area
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class HtmlSectionAdmin extends Db_Object {

    /**
    * Load an object using its code
    */
    static public function code($code) {
        return HtmlSectionAdmin::readFirst(array('where'=>'code="'.$code.'"'));
    }

    /**
    * Show directly the content of a section just using its code
    */
    static public function show($code) {
        $html = HtmlSectionAdmin::code($code);
        return $html->showUi();
    }

}
?>