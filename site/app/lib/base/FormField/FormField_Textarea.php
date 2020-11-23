<?php
/**
 * @class FormFieldTextarea
 *
 * This is a helper class to generate a textarea form field.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class FormField_Textarea extends FormField_DefaultTextarea
{

    /**
     * The constructor of the object.
     */
    public function __construct($options)
    {
        parent::__construct($options);
        $this->options['cols'] = '70';
        $this->options['rows'] = '5';
    }

    /**
     * Render the element with an static function.
     */
    public static function create($options)
    {
        $options['cols'] = '70';
        $options['rows'] = '5';
        return FormField_DefaultTextarea::create($options);
    }

}
