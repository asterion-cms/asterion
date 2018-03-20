<?php
/**
* @class FormFieldTextareaSmall
*
* This is a helper class to generate a small textarea form field.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class FormField_TextareaSmall extends FormField_DefaultTextarea {

    /**
    * The constructor of the object.
    */
    public function __construct($options) {
        parent::__construct($options);
        $this->options['cols'] = '70';
        $this->options['rows'] = '2';
    }

    /**
    * Render the element with an static function.
    */
    static public function create($options) {
        $options['cols'] = '70';
        $options['rows'] = '2';
        return FormField_DefaultTextarea::create($options);
    }

}
?>