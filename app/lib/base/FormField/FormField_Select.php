<?php
/**
* @class FormFieldSelect
*
* This is a helper class to generate a select form field.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class FormField_Select extends FormField_DefaultSelect {

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
        return FormField_DefaultSelect::create($options);
    }

}
?>