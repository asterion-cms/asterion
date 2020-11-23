<?php
/**
 * @class FormFieldEmail
 *
 * This is a helper class to generate an email text form field.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class FormField_TextEmail extends FormField_Text
{

    /**
     * The constructor of the object.
     */
    public function __construct($options)
    {
        parent::__construct($options);
        $this->options['size'] = '30';
        $this->options['typeField'] = 'email';
    }

    /**
     * Render the element with an static function.
     */
    public static function create($options)
    {
        $options['size'] = '30';
        $options['typeField'] = 'email';
        return FormField_Default::create($options);
    }

}
