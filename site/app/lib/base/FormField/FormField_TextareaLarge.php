<?php
/**
 * @class FormFieldTextareaLarge
 *
 * This is a helper class to generate a large textarea form field.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class FormField_TextareaLarge extends FormField_DefaultTextarea
{

    /**
     * The constructor of the object.
     */
    public function __construct($options)
    {
        parent::__construct($options);
        $this->options['cols'] = '70';
        $this->options['rows'] = '15';
    }

    /**
     * Render the element with an static function.
     */
    public static function create($options)
    {
        $options['cols'] = '70';
        $options['rows'] = '15';
        return FormField_DefaultTextarea::create($options);
    }

}
