<?php
/**
 * @class FormFieldTextTelephone
 *
 * This is a helper class to generate a telephone text form field.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class FormField_TextTelephone extends FormField_Text
{

    /**
     * The constructor of the object.
     */
    public function __construct($options)
    {
        parent::__construct($options);
        $this->options['size'] = '30';
    }

    /**
     * Render the element with an static function.
     */
    public static function create($options)
    {
        $options['size'] = '30';
        return FormField_Default::create($options);
    }

}
