<?php
/**
 * @class FormFieldTextareaLong
 *
 * This is a helper class to generate a long textarea form field.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class FormField_TextareaLong extends FormField_DefaultTextarea
{

    /**
     * The constructor of the object.
     */
    public function __construct($options)
    {
        parent::__construct($options);
        $this->options['cols'] = '80';
        $this->options['rows'] = '3';
    }

    /**
     * Render the element with an static function.
     */
    public static function create($options)
    {
        $options['cols'] = '80';
        $options['rows'] = '3';
        return FormField_DefaultTextarea::create($options);
    }

}
