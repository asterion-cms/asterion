<?php
/**
 * @class FormField
 *
 * This is a helper class that is used as a factory to load a form field object.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class FormField
{

    /**
     * A factory function to show the form field using an object attribute.
     */
    public static function show($type, $options)
    {
        $objectName = 'FormField_' . snakeToCamel($type);
        $fileName = ASTERION_FRAMEWORK_FILE . 'base/FormField/' . $objectName . '.php';
        if (is_file($fileName)) {
            $field = new $objectName($options);
            return $field->show();
        } else {
            return 'The type ' . $type . ' is not valid';
        }
    }

    /**
     * A factory function to build the form field using an array of options.
     */
    public static function create($type, $options)
    {
        $objectName = 'FormField_' . snakeToCamel($type);
        $fileName = ASTERION_FRAMEWORK_FILE . 'base/FormField/' . $objectName . '.php';
        if (is_file($fileName)) {
            return forward_static_call([$objectName, 'create'], $options);
        } else {
            return 'The type ' . $type . ' is not valid';
        }
    }

}
