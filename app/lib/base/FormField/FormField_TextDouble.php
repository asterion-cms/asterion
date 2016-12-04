<?php
/**
* @class FormFieldTextDouble
*
* This is a helper class to generate a double text form field.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class FormField_TextDouble extends FormField_Text {

    /**
    * The constructor of the object.
    */
    public function __construct($options) {
        parent::__construct($options);
        $this->options['size'] = '10';
    }

    /**
    * Render the element with an static function.
    */
    static public function create($options) {
        $options['size'] = '10';
        return FormField_Default::create($options);
    }
    
}
?>