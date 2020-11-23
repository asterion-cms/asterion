<?php
/**
 * @class FormFieldCheckbox
 *
 * This is a helper class to generate checkboxes.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class FormField_Checkbox
{

    /**
     * The constructor of the object.
     */
    public function __construct($options)
    {
        $this->item = $options['item'];
        $this->name = (string) $this->item->name;
        $this->object = $options['object'];
        $this->values = isset($options['values']) ? $options['values'] : [];
        $this->errors = isset($options['errors']) ? $options['errors'] : [];
        $this->options = [];
        $nameMultiple = (isset($options['nameMultiple']) && isset($options['idMultiple']) && $options['nameMultiple'] != '' && $options['idMultiple']);
        $this->options['nameSimple'] = $this->name;
        $this->options['name'] = $this->name;
        $this->options['name'] = ($nameMultiple) ? $options['nameMultiple'] . '[' . $options['idMultiple'] . '][' . $this->options['name'] . ']' : $this->options['name'];
        $this->options['value'] = $this->values[$this->name];
        $this->options['error'] = $this->errors[$this->name];
        $this->options['label'] = (string) $this->item->label;
        $this->options['placeholder'] = (string) $this->item->placeholder;
        $this->options['typeField'] = (isset($options['typeField'])) ? $options['typeField'] : 'checkbox';
    }

    /**
     * Render a checkbox element.
     */
    public function show()
    {
        return FormField_Checkbox::create($this->options);
    }

    /**
     * Render the element with an static function.
     */
    public static function create($options)
    {
        $name = (isset($options['name'])) ? 'name="' . $options['name'] . '"' : '';
        $id = (isset($options['id'])) ? $options['id'] : substr(md5(rand()), 0, 5);
        $label = (isset($options['label'])) ? '<label for="' . $id . '">' . __($options['label']) . '</label>' : '';
        $value = (isset($options['value']) && $options['value'] == "1") ? 'checked="checked" ' : '';
        $disabled = (isset($options['disabled'])) ? 'disabled="disabled"' : '';
        $error = (isset($options['error']) && $options['error'] != '') ? '<div class="error">' . $options['error'] . '</div>' : '';
        $errorClass = (isset($options['error']) && $options['error'] != '') ? 'error' : '';
        $class = (isset($options['class'])) ? $options['class'] : '';
        $class .= (isset($options['name'])) ? ' form_field-' . Text::simpleUrl($options['name']) : '';
        $layout = (isset($options['layout'])) ? $options['layout'] : '';
        $object = (isset($options['object'])) ? $options['object'] : '';
        switch ($layout) {
            default:
                return '<div class="checkbox form_field ' . $class . '">
                            ' . $error . '
                            <div class="checkbox_ins">
                                <input type="checkbox" id="' . $id . '" ' . $name . ' ' . $value . ' ' . $disabled . '/>
                                ' . $label . '
                            </div>
                        </div>';
                break;
            case 'simple':
                return '<input type="checkbox" id="' . $id . '" ' . $name . ' ' . $value . ' ' . $disabled . '/>';
                break;
        }
    }

}
