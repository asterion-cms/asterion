<?php
/**
* @class FormFieldDefaultDate
*
* This is a helper class to generate a default date form field.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class FormField_DefaultDate {

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
        $this->options['nameMultiple'] = $nameMultiple;
        $this->options['nameSimple'] = $this->name;
        $this->options['name'] = $this->name;
        $this->options['name'] = ($nameMultiple) ? $options['nameMultiple'].'['.$options['idMultiple'].']['.$this->options['name'].']' : $this->options['name'];
        $this->options['error'] = $this->errors[$this->name];
        $this->options['label'] = (string)$this->item->label;
        $this->options['placeholder'] = (string)$this->item->placeholder;
        $this->options['checkboxDate'] = (string)$this->item->checkboxDate;
        $this->options['typeField'] = (isset($options['typeField'])) ? $options['typeField'] : 'date';
        $this->options['value'] = (isset($this->values[$this->name]) && $this->values[$this->name]!='') ? $this->values[$this->name] : date('Y-m-d h:i');
    }
    
    /**
    * Render a date element using selectboxes.
    */
    public function show() {
        return FormField_DefaultDate::create($this->options);
    }

    /**
    * Render the element with an static function.
    */
    static public function create($options) {
        $label = (isset($options['label'])) ? '<label>'.__($options['label']).'</label>' : '';
        $value = (isset($options['value'])) ? $options['value'] : '';
        $disabled = (isset($options['disabled'])) ? $options['disabled'] : '';
        $error = (isset($options['error']) && $options['error']!='') ? '<div class="error">'.$options['error'].'</div>' : '';
        $errorClass = (isset($options['error']) && $options['error']!='') ? 'error' : '';
        $class = (isset($options['class'])) ? $options['class'] : '';
        $class .= (isset($options['name'])) ? ' formField-'.Text::simpleUrl($options['name']) : '';
        $layout = (isset($options['layout'])) ? $options['layout'] : '';
        $checkboxVal = ($value!='') ? "1" : "0";
        $checkbox = (isset($options['checkboxDate']) && $options['checkboxDate']=='true') ? FormField_Checkbox::create(array('name'=>'check_'.$options['name'], 'value'=>$checkboxVal, 'class'=>'checkBoxInlineDate')) : '';
        $checkboxHidden = (isset($options['checkboxDate']) && $options['checkboxDate']=='true') ? FormField_Hidden::create(array('name'=>'checkhidden_'.$options['name'], 'value'=>"1")) : '';
        $checkboxClass = (isset($options['checkboxDate']) && $options['checkboxDate'] == 'true') ? 'selectCheckbox' : '';
        return '<div class="select selectDate formField '.$class.' '.$errorClass.' '.$checkboxClass.'">
                    '.$label.'
                    '.$error.'
                    <div class="selectIns">
                        '.$checkbox.'
                        '.$checkboxHidden.'
                        '.FormField_Date::createDate($options).'
                    </div>
                </div>';
    }

    public static function createComplete($options) {
        return FormField_Date::createDate($options).'
                '.FormField_Date::createTime($options);
    }

    public static function createDate($options) {
        $options['value'] = isset($options['value']) ? $options['value'] : date('Y-m-d h:i');
        $date = Date::sqlArray($options['value']);
        unset($options['label']);
        $view = (isset($options['view'])) ? $options['view'] : '';
        $options['layout'] = 'simple';
        $result = '';
        switch ($view) {
            default:
                $options['selected'] = $date['day'];
                $result .= FormField_Date::createDay($options);
                $options['selected'] = $date['month'];
                $result .= FormField_Date::createMonth($options);
                $options['selected'] = $date['year'];
                $result .= FormField_Date::createYear($options);
            break;
            case 'hour':
                $options['selected'] = $date['hour'];
                $result .= FormField_Date::createHour($options);
                $options['selected'] = $date['minutes'];
                $result .= FormField_Date::createMinutes($options);
            break;
            case 'complete':
                $options['selected'] = $date['day'];
                $result = FormField_Date::createDay($options);
                $options['selected'] = $date['month'];
                $result .= FormField_Date::createMonth($options);
                $options['selected'] = $date['year'];
                $result .= FormField_Date::createYear($options);
                $options['selected'] = $date['hour'];
                $result .= FormField_Date::createHour($options);
                $options['selected'] = $date['minutes'];
                $result .= FormField_Date::createMinutes($options);
            break;
            case 'year':
                $options['selected'] = $date['year'];
                $result .= FormField_Date::createYear($options);
            break;
        }
        return $result;
    }
    
    public static function createTime($options) {
        $date = Date::sqlArray($options['value']);
        $options['selected'] = $date['hour'];
        $result = FormField_Date::createHour($options);
        $options['selected'] = $date['minutes'];
        $result .= FormField_Date::createMinutes($options);
        return $result;
    }
    
    public static function createDay($options) {
        $options['value'] = array_fillkeys(range(1, 31), range(1, 31));
        if ($options['nameMultiple']) {
            $options['name'] = (isset($options['name'])) ? substr($options['name'], 0, -1).'day]' : 'day';
        } else {        
            $options['name'] = (isset($options['name'])) ? $options['name'].'day' : 'day';
            $options['name'] = str_replace('[]day', 'day[]', $options['name']);
        }
        return FormField_DefaultSelect::create($options);
    }
    
    public static function createMonth($options){
        $options['value'] = array_fillkeys(range(1, 12), range(1, 12));
        $options['value'] = Date::textMonthArray();
        if ($options['nameMultiple']) {
            $options['name'] = (isset($options['name'])) ? substr($options['name'], 0, -1).'mon]' : 'mon';
        } else {        
            $options['name'] = (isset($options['name'])) ? $options['name'].'mon' : 'mon';
            $options['name'] = str_replace('[]mon', 'mon[]', $options['name']);
        }
        return FormField_DefaultSelect::create($options);
    }

    public static function createMonthSimple($options){
        $options['value'] = array_fillkeys(range(1, 12), range(1, 12));
        $options['value'] = Date::textMonthArraySimple();
        if ($options['nameMultiple']) {
            $options['name'] = (isset($options['name'])) ? substr($options['name'], 0, -1).'mon]' : 'mon';
        } else {        
            $options['name'] = (isset($options['name'])) ? $options['name'].'mon' : 'mon';
            $options['name'] = str_replace('[]mon', 'mon[]', $options['name']);
        }
        return FormField_DefaultSelect::create($options);
    }
    
    public static function createYear($options){
        $fromYear = isset($options['fromYear']) ? $options['fromYear'] : date('Y')-90;
        $toYear = isset($options['toYear']) ? $options['toYear'] : date('Y')+20;
        $options['value'] = array_fillkeys(range($fromYear, $toYear), range($fromYear, $toYear));
        if ($options['nameMultiple']) {
            $options['name'] = (isset($options['name'])) ? substr($options['name'], 0, -1).'yea]' : 'yea';
        } else {        
            $options['name'] = (isset($options['name'])) ? $options['name'].'yea' : 'yea';
            $options['name'] = str_replace('[]yea', 'yea[]', $options['name']);
        }
        return FormField_DefaultSelect::create($options);
    }
    
    public static function createHour($options){
        $options['value'] = array_fillkeys(range(0, 23), range(0, 23));
        foreach ($options['value'] as $key=>$value) {
            $options['value'][$key]=str_pad((string)$value, 2, "0", STR_PAD_LEFT);
        }
        if ($options['nameMultiple']) {
            $options['name'] = (isset($options['name'])) ? substr($options['name'], 0, -1).'hou]' : 'hou';
        } else {        
            $options['name'] = (isset($options['name'])) ? $options['name'].'hou' : 'hou';
            $options['name'] = str_replace('[]hou', 'hou[]', $options['name']);
        }
        return FormField_DefaultSelect::create($options);
    }
    
    public static function createMinutes($options){
        $options['value'] = array_fillkeys(range(0, 59), range(0, 59));
        foreach ($options['value'] as $key=>$value) {
            $options['value'][$key]=str_pad((string)$value, 2, "0", STR_PAD_LEFT);
        }
        if ($options['nameMultiple']) {
            $options['name'] = (isset($options['name'])) ? substr($options['name'], 0, -1).'min]' : 'min';
        } else {        
            $options['name'] = (isset($options['name'])) ? $options['name'].'min' : 'min';
            $options['name'] = str_replace('[]min', 'min[]', $options['name']);
        }
        return FormField_DefaultSelect::create($options);
    }

}
?>