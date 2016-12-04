<?php
/**
* @class HtmlMailTemplate
*
* This class represents the template for the emails
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class HtmlMailTemplate extends Db_Object {

	/**
    * Load an object using its code
    */
    static public function code($code) {
        return HtmlMailTemplate::readFirst(array('where'=>'code="'.$code.'"'));
    }

}
?>