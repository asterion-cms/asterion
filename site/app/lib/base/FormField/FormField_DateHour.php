<?php
/**
 * @class FormFieldDateHour
 *
 * This is a helper class to generate hour fields with selectboxes.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class FormField_DateHour extends FormField_DefaultDate
{

    /**
     * The constructor of the object.
     */
    public function __construct($options)
    {
        parent::__construct($options);
        $this->options['view'] = 'hour';
    }

    /**
     * Render the element with an static function.
     */
    public static function create($options)
    {
        $options['view'] = 'hour';
        return FormField_DefaultDate::create($options);
    }

}
