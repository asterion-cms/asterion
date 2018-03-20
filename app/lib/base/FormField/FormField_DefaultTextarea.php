<?php
/**
* @class FormFieldDefaultTextarea
*
* This is a helper class to generate a default textarea form field.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class FormField_DefaultTextarea {

    /**
    * The constructor of the object.
    */
    public function __construct($options) {
        $this->item = $options['item'];
        $this->name = (string)$this->item->name;
        $this->object = $options['object'];
        $this->values = isset($options['values']) ? $options['values'] : array();
        $this->errors = isset($options['errors']) ? $options['errors'] : array();
        $this->options = array();
        $nameMultiple = (isset($options['nameMultiple']) && isset($options['idMultiple']) && $options['nameMultiple']!='' && $options['idMultiple']);
        $this->options['nameSimple'] = $this->name;
        $this->options['name'] = ($nameMultiple) ? $options['nameMultiple'].'['.$options['idMultiple'].']['.$this->name.']' : $this->name;
        $this->options['value'] = $this->values[$this->name];
        $this->options['error'] = $this->errors[$this->name];
        $this->options['label'] = (string)$this->item->label;
        $this->options['placeholder'] = (string)$this->item->placeholder;
        $this->options['required'] = ((string)$this->item->required!='') ? true : false;
        $this->options['typeField'] = (isset($options['typeField'])) ? $options['typeField'] : 'textarea';
        $this->options['maxlength'] = (string)$this->item->maxlength;
    }

    /**
    * Render a default textarea element.
    */
    public function show() {
        if ((string)$this->item->lang == 'true') {
            $fields = '';
            $optionsName = $this->options['name'];
            foreach (Lang::langs() as $lang) {
                $nameLang = $this->name.'_'.$lang;
                $this->options['name'] = str_replace($this->name, $nameLang, $optionsName);
                $this->options['labelLang'] = Lang::getLabel($lang);
                $this->options['value'] = $this->values[$this->options['name']];
                $fields .= FormField_DefaultTextarea::create($this->options);
            }
            return '<div class="formFieldLangs">'.$fields.'</div>';
        } else {
            return FormField_DefaultTextarea::create($this->options);
        }
    }

    /**
    * Render the element with an static function.
    */
    static public function create($options) {
        $type = (isset($options['typeField'])) ? $options['typeField'] : 'textarea';
        $typeField = (isset($options['typeField'])) ? 'type="'.$options['typeField'].'" ' : 'type="text"';
        $name = (isset($options['name'])) ? 'name="'.$options['name'].'" ' : '';
        $id = (isset($options['id'])) ? 'id="'.$options['id'].'"' : '';
        $labelLang = (isset($options['labelLang']) && $options['labelLang']!='') ? ' <span>('.$options['labelLang'].')</span>' : '';
        $label = (isset($options['label'])) ? '<label>'.__($options['label']).$labelLang.'</label>' : '';
        $value = (isset($options['value'])) ? $options['value'] : '';
        $disabled = (isset($options['disabled']) && $options['disabled']!=false) ? 'disabled="disabled"' : '';
        $cols = (isset($options['cols'])) ? 'cols="'.$options['cols'].'" ' : '';
        $rows = (isset($options['rows'])) ? 'rows="'.$options['rows'].'" ' : '';
        $error = (isset($options['error']) && $options['error']!='') ? '<div class="error">'.$options['error'].'</div>' : '';
        $class = (isset($options['class'])) ? $options['class'] : '';
        $class .= (isset($options['name'])) ? ' formField-'.Text::simpleUrl($options['name']) : '';
        $classError = (isset($options['error']) && $options['error']!='') ? 'error' : '';
        $placeholder = (isset($options['placeholder'])) ? 'placeholder="'.__($options['placeholder']).'"' : '';
        $required = (isset($options['required']) && $options['required']) ? 'required' : '';
        $layout = (isset($options['layout'])) ? $options['layout'] : '';
        $maxlength = (isset($options['maxlength']) && $options['maxlength']!='') ? 'maxlength="'.$options['maxlength'].'" ' : '';
        switch ($layout) {
            default:
                return '<div class="'.$type.' formField '.$class.' '.$required.' '.$classError.'">
                            <div class="formFieldIns">
                                '.$label.'
                                '.$error.'
                                <textarea '.$name.' '.$cols.' '.$rows.' '.$id.' '.$placeholder.' '.$required.' '.$maxlength.'>'.$value.'</textarea>
                            </div>
                        </div>';
            break;
            case 'simple':
                return '<textarea '.$name.' '.$cols.' '.$rows.' '.$id.' '.$placeholder.' '.$maxlength.'>'.$value.'</textarea>';
            break;
        }
    }

}
?>