<?php
/**
 * @class FormFieldTextareaCkSimple
 *
 * This is a helper class to generate a simple CK textarea form field.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class FormField_TextareaCkSimple extends FormField_DefaultTextarea
{

    /**
     * The constructor of the object.
     */
    public function __construct($options)
    {
        parent::__construct($options);
        $this->options['cols'] = '70';
        $this->options['rows'] = '6';
        $this->options['class'] = 'ckeditorAreaSimple';
        $this->options['value'] = (isset($this->options['value'])) ? htmlspecialchars($this->options['value']) : '';
    }

    /**
     * Render the element with an static function.
     */
    public static function create($options)
    {
        $options['cols'] = '70';
        $options['rows'] = '6';
        $options['class'] = 'ckeditorAreaSimple';
        $options['value'] = (isset($options['value'])) ? htmlspecialchars($options['value']) : '';
        return FormField_DefaultTextarea::create($options);
    }

}
