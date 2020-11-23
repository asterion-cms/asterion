<?php
/**
 * @class FormFieldCheckbox
 *
 * This is a helper class to generate a checkbox form field.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class FormField_SelectCheckbox extends FormField_DefaultSelect
{

    /**
     * The constructor of the object.
     */
    public function __construct($options)
    {
        parent::__construct($options);
        $this->options['checkbox'] = true;
    }

    /**
     * Render the element with an static function.
     */
    public static function create($options)
    {
        $options['checkbox'] = true;
        return FormField_DefaultSelect::create($options);
    }

}
