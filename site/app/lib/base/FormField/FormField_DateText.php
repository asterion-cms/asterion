<?php
/**
 * @class FormFieldDateText
 *
 * This is a helper class to generate a text-date field.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class FormField_DateText extends FormField_Default
{

    /**
     * The constructor of the object.
     */
    public function __construct($options)
    {
        parent::__construct($options);
        $this->options['typeField'] = 'text';
        $this->options['class'] = 'date_text';
        $this->options['value'] = (isset($this->options['value'])) ? substr($this->options['value'], 0, 10) : '';
    }

    /**
     * Render the element with an static function.
     */
    public static function create($options)
    {
        $options['typeField'] = 'text';
        $options['class'] = 'date_text';
        return FormField_Default::create($options);
    }

}
