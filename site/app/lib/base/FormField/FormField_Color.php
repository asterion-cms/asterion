<?php
/**
 * @class FormFieldColor
 *
 * This is a helper class to generate a color text form field.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class FormField_Color extends FormField_Default
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
        return FormField_Default::create($options);
    }

}
