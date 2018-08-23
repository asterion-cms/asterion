<?php
/**
* @class FormFieldText
*
* This is a helper class to generate a text form field.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class FormField_Text extends FormField_Default {

    /**
    * The constructor of the object.
    */
    public function __construct($options) {
        parent::__construct($options);
        $this->options['size'] = '40';
    }

    /**
    * Render the element with an static function.
    */
    static public function create($options) {
        $options['size'] = '40';
        return FormField_Default::create($options);
    }

}
?>