<?php
/**
* @class FormFieldDefaultSelect
*
* This is a helper class to generate a default select form field.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class FormField_DefaultSelect {

    protected $options;

    public function __construct($options) {
        $this->item = $options['item'];
        $this->name = (string)$this->item->name;
        $this->object = $options['object'];
        $this->values = isset($options['values']) ? $options['values'] : array();
        $this->errors = isset($options['errors']) ? $options['errors'] : array();
        $this->options = array();
        $nameMultiple = (isset($options['nameMultiple']) && isset($options['idMultiple']) && $options['nameMultiple']!='' && $options['idMultiple']);
        $this->options['nameSimple'] = $this->name;
        $this->options['name'] = $this->name;
        $this->options['name'] = ($nameMultiple) ? $options['nameMultiple'].'['.$options['idMultiple'].']['.$this->options['name'].']' : $this->options['name'];
        $this->options['error'] = $this->errors[$this->name];
        $this->options['label'] = (string)$this->item->label;
        $this->options['placeholder'] = (string)$this->item->placeholder;
        $this->options['firstSelect'] = (string)$this->item->firstSelect;
        $this->options['checkbox'] = (boolean)$this->item->checkbox;
        $this->options['multiple'] = (boolean)$this->item->multiple;
        $this->options['typeField'] = (isset($options['typeField'])) ? $options['typeField'] : 'select';
        //Load the values
        $refObject = (string)$this->item->refObject;
        $query = (string)$this->item->query;
        if (isset($options['value'])) {
            $this->options['value'] = $options['value'];
        } elseif ($refObject != "") {
            $refObjectIns = new $refObject();
            $this->options['value'] = $refObjectIns->basicInfoAdminArray();
        } elseif ($query != "") {
            $this->options['value'] = $this->loadQueryValues($query);
        } else {
            $this->options['value'] = array();
            $i = 0;
            foreach ($this->item->values->value as $itemIns) {
                $id = (isset($itemIns['id'])) ? (string)$itemIns['id'] : $i;
                $this->options['value'][$id] = (string)$itemIns;
                $i++;
            }
        }
        //Load the selected values
        $this->options['selected'] = $this->values[$this->name];
    }

    /**
    * Render a default input element.
    */
    public function show() {
        return FormField_Select::create($this->options);
    }


    /**
    * Render the element with an static function.
    */
    static public function create($options) {
        $typeField = (isset($options['typeField'])) ? 'type="'.$options['typeField'].'" ' : 'type="text"';
        $name = (isset($options['name'])) ? 'name="'.$options['name'].'" ' : '';
        $name = (isset($options['multiple']) && $options['multiple']==true) ? 'name="'.$options['name'].'[]" ' : $name;
        $nameSelect = (isset($options['name'])) ? $options['name'] : '';
        $id = (isset($options['id'])) ? 'id="'.$options['id'].'"' : '';
        $label = (isset($options['label'])) ? '<label>'.__($options['label']).'</label>' : '';
        $value = (isset($options['value'])) ? $options['value'] : '';
        $selected = (isset($options['selected'])) ? $options['selected'] : '';
        $selected = (isset($options['multiple']) && $options['multiple']==true && isset($options['class']) && $options['class']=='select2' && $selected!='') ? json_decode($selected, true) : $selected;
        $disabled = (isset($options['disabled']) && $options['disabled']!=false) ? 'disabled="disabled"' : '';
        $multiple = (isset($options['multiple']) && $options['multiple']==true) ? 'multiple="multiple"' : '';
        $checkboxValue = ($selected!='') ? 1 : 0;
        $checkbox = (isset($options['checkbox']) && $options['checkbox']!=false) ? FormField_Checkbox::create(array('name'=>$options['name'].'_checkbox', 'value'=>$checkboxValue, 'class'=>'checkBoxInline')) : '';
        $classCheckbox = (isset($options['checkbox']) && $options['checkbox']!=false) ? 'selectCheckbox' : '';
        $size = (isset($options['size'])) ? 'size="'.$options['size'].'" ' : '';
        $error = (isset($options['error']) && $options['error']!='') ? '<div class="error">'.$options['error'].'</div>' : '';
        $class = (isset($options['class'])) ? $options['class'] : '';
        $class .= (isset($options['name'])) ? ' formField-'.Text::simpleUrl($options['name']) : '';
        $errorClass = (isset($options['error']) && $options['error']!='') ? 'error' : '';
        $placeholder = (isset($options['placeholder'])) ? 'placeholder="'.__($options['placeholder']).'"' : '';
        $layout = (isset($options['layout'])) ? $options['layout'] : '';
        $htmlOptions = '';
        if (is_array($value) && count($value)>0) {
            if (isset($options['firstSelect']) && $options['firstSelect']!='') {
                $htmlOptions .= '<option value="">'.__($options['firstSelect']).'</option>';
            }
            foreach ($value as $key=>$item) {
                if (is_array($item)) {
                    $itemsOptions = '';
                    foreach ($item['items'] as $keyIns=>$itemIns) {
                        $isSelected = ($keyIns==$selected || (is_array($selected) && in_array($keyIns, $selected))) ? 'selected="selected"' : '';
                        $itemsOptions .= '<option value="'.$keyIns.'" '.$isSelected.'>'.__($itemIns).'</option>';
                    }
                    $htmlOptions .= '<optgroup label="'.$item['label'].'">'.$itemsOptions.'</optgroup>';
                } else {
                    $isSelected = ($key==$selected || (is_array($selected) && in_array($key, $selected))) ? 'selected="selected"' : '';
                    $htmlOptions .= '<option value="'.$key.'" '.$isSelected.'>'.__($item).'</option>';
                }
            }
            switch ($layout) {
                default:
                    return '<div class="select formField '.$class.' '.$classCheckbox.' '.$errorClass.'">
                                <div class="formFieldIns">
                                    '.$label.'
                                    '.$error.'
                                    <div class="selectIns">
                                        '.$checkbox.'
                                        <select '.$name.' '.$id.' '.$disabled.' '.$multiple.' '.$size.'>'.$htmlOptions.'</select>
                                        <input type="hidden" name="select_'.$nameSelect.'" value="true"/>
                                    </div>
                                </div>
                            </div>';
                break;
                case 'simple':
                    return $checkbox.'<select '.$name.' '.$id.' '.$disabled.' '.$multiple.' '.$size.'>'.$htmlOptions.'</select>';
                break;
            }
        }
    }

    static public function loadQueryValues($query) {
        $result = array();
        $items = Db::returnAll($query);
        foreach ($items as $item) {
            $result[$item['id']] = $item['value'];
        }
        return $result;
    }

}
?>