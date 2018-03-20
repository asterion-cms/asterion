<?php
/**
* @class FormFieldHidden
*
* This is a helper class to generate a hidden form field.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class FormField_Hidden extends FormField_Default {

    /**
    * The constructor of the object.
    */
    public function __construct($options) {
        parent::__construct($options);
        $this->options['layout'] = 'simple';
        $this->options['typeField'] = 'hidden';
    }

    /**
    * Render the element with an static function.
    */
    static public function create($options) {
        $options['layout'] = 'simple';
        $options['typeField'] = 'hidden';
        return FormField_Default::create($options);
    }

}
?>