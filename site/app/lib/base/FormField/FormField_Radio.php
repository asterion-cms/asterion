<?php
/**
 * @class FormFieldRadio
 *
 * This is a helper class to generate a radio form field.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class FormField_Radio extends FormField_DefaultRadio
{

    /**
     * The constructor of the object.
     */
    public function __construct($options)
    {
        parent::__construct($options);
    }

    /**
     * Render the element with an static function.
     */
    public static function create($options)
    {
        return FormField_DefaultRadio::create($options);
    }

}
