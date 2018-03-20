<?php
/**
* @class FormFieldDate
*
* This is a helper class to generate date fields with selectboxes.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class FormField_Date extends FormField_DefaultDate {

    /**
    * The constructor of the object.
    */
    public function __construct($options) {
        parent::__construct($options);
    }

    /**
    * Render the element with an static function.
    */
    static public function create($options) {
        return FormField_DefaultDate::create($options);
    }

}
?>