<?php
/**
* @class FormFieldTextSmall
*
* This is a helper class to generate a small text form field.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class FormField_TextSmall extends FormField_Text {

    /**
    * The constructor of the object.
    */
    public function __construct($options) {
        parent::__construct($options);
        $this->options['size'] = '5';
    }

    /**
    * Render the element with an static function.
    */
    static public function create($options) {
        $options['size'] = '5';
        return FormField_Default::create($options);
    }

}
?>