<?php
/**
* @class FormFieldDefault
*
* This is a helper class to generate a default form field.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class FormField_Default {

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
        $nameMultiple = (isset($options['nameMultiple']) && isset($options['idMultiple']) && $options['nameMultiple']!='' && $options['idMultiple']!='');
        $this->options['nameSimple'] = $this->name;
        $this->options['name'] = ($nameMultiple) ? $options['nameMultiple'].'['.$options['idMultiple'].']['.$this->name.']' : $this->name;
        $this->options['value'] = $this->values[$this->name];
        $this->options['error'] = $this->errors[$this->name];
        $this->options['label'] = (string)$this->item->label;
        $this->options['placeholder'] = (string)$this->item->placeholder;
        $this->options['lang'] = (string)$this->item->lang;
        $this->options['layout'] = (string)$this->item->layout;
        $this->options['required'] = ((string)$this->item->required!='') ? true : false;
        $this->options['typeField'] = (isset($options['typeField'])) ? $options['typeField'] : 'text';
    }

    /**
    * Render a default input element.
    */
    public function show() {
        if ((string)$this->item->lang == 'true') {
            $fields = '';
            $optionsName = $this->options['name'];
            foreach (Lang::langs() as $lang) {
                $nameLang = $this->name.'_'.$lang;
                $this->options['name'] = str_replace($this->name, $nameLang, $optionsName);
                $this->options['labelLang'] = Lang::getLabel($lang);
                $this->options['value'] = $this->values[$nameLang];
                $this->options['class'] = 'formField_'.$nameLang.' formField_'.$this->name;
                $fields .= FormField_Default::create($this->options);
            }
            return '<div class="formFieldLangs">'.$fields.'</div>';
        } else {
            return FormField_Default::create($this->options);
        }
    }

    /**
    * Render the element with an static function.
    */
    static public function create($options) {
        $type = (isset($options['typeField'])) ? $options['typeField'] : 'text';
        $typeField = 'type="'.$type.'"';
        $name = (isset($options['name'])) ? 'name="'.$options['name'].'" ' : '';
        $id = (isset($options['id'])) ? 'id="'.$options['id'].'"' : '';
        $labelLang = (isset($options['labelLang']) && $options['labelLang']!='') ? ' <span>('.$options['labelLang'].')</span>' : '';
        $label = (isset($options['label'])) ? '<label>'.__($options['label']).$labelLang.'</label>' : '';
        $value = (isset($options['value'])) ? 'value="'.$options['value'].'" ' : '';
        $disabled = (isset($options['disabled']) && $options['disabled']!=false) ? 'disabled="disabled"' : '';
        $size = (isset($options['size'])) ? 'size="'.$options['size'].'" ' : '';
        $error = (isset($options['error']) && $options['error']!='') ? '<div class="error">'.$options['error'].'</div>' : '';
        $class = (isset($options['class'])) ? $options['class'] : '';
        $class .= (isset($options['name'])) ? ' formField-'.Text::simpleUrl($options['name']) : '';
        $classError = (isset($options['error']) && $options['error']!='') ? 'error' : '';
        $placeholder = (isset($options['placeholder'])) ? 'placeholder="'.__($options['placeholder']).'"' : '';
        $required = (isset($options['required']) && $options['required']) ? 'required' : '';
        $layout = (isset($options['layout'])) ? $options['layout'] : '';
        $autocomplete = (isset($options['autocomplete'])) ? 'autocomplete="'.$options['autocomplete'].'" ' : '';
        switch ($layout) {
            default:
                return '<div class="'.$type.' formField '.$class.' '.$required.' '.$classError.'">
                            <div class="formFieldIns">
                                '.$label.'
                                '.$error.'
                                <input '.$typeField.' '.$name.' '.$size.' '.$value.' '.$id.' '.$disabled.' '.$placeholder.' '.$required.' '.$autocomplete.'/>
                            </div>
                        </div>';
            break;
            case 'color':
                return '<div class="'.$type.' formField '.$class.' '.$required.' '.$classError.'">
                            <div class="formFieldIns">
                                '.$label.'
                                '.$error.'
                                <input class="color" '.$typeField.' '.$name.' '.$size.' '.$value.' '.$id.' '.$disabled.' '.$placeholder.' '.$required.' '.$autocomplete.'/>
                            </div>
                        </div>';
            break;
            case 'simple':
                return '<input '.$typeField.' '.$name.' '.$size.' '.$value.' '.$id.' '.$disabled.' '.$placeholder.' '.$autocomplete.' class="'.$class.'"/>';
            break;
        }
    }

}
?>