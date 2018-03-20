<?php
/**
* @class FormFieldPassword
*
* This is a helper class to generate a password form field.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class FormField_Password extends FormField_Default {

    /**
    * The constructor of the object.
    */
    public function __construct($options) {
        parent::__construct($options);
        $this->options['size'] = 50;
        $this->options['typeField'] = 'password';
        $this->options['autocomplete'] = 'off';
    }

    /**
    * Render the element with an static function.
    */
    static public function create($options) {
        $options['size'] = 50;
        $options['typeField'] = 'password';
        $options['autocomplete'] = 'off';
        return FormField_Default::create($options);
    }

}
?>