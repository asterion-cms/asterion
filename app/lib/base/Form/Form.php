<?php
/**
* @class Form
*
* This is a helper class to create and format forms.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Form {

    /**
    * A form is created using an XML model, it uses values and errors with the same names as the object properties.
    */
    public function __construct($values=array(), $errors=array(), $object='') {
        if (!is_object($object)) {
            $this->className = str_replace('_Form', '', get_class($this));
            $this->object = new $this->className($values);            
        } else {
            $this->object = $object;
            $this->className = $object->className;
        }
        $this->values = $values;
        $this->errors = $errors;
         $this->prepareValues();
    }

    /**
    * Return an object using new values and errors.
    */
    public function newArray($values=array(), $errors=array()) {
        $formClass = get_class($this);
        return new $formClass($values, $errors);
    }

    /**
    * Create a form from an object.
    */
    static public function newObject($object) {
        $formClass = get_class($object).'_Form';
        return new $formClass($object->values, array(), $object);
    }

    /**
    * Get a form value.
    */
    public function get($name) {
        return (isset($this->$name)) ? $this->$name : '';
    }

    /**
    * Get all the form values.
    */
    public function getValues() {
        return $this->values;
    }

    /**
    * Set a form value.
    */
    public function setValue($key, $value) {
        $this->values[$key] = $value;
    }

    /**
    * Add values to the form.
    */
    public function addValues($values, $errors=array()) {
        $this->values = array_merge($this->values, $values);
        $this->errors = array_merge($this->errors, $errors);
    }

    /**
    * Prepare the values.
    */
    public function prepareValues() {
        foreach($this->object->getAttributes() as $item) {
            $name = (string)$item->name;
            $this->values[$name] = isset($this->values[$name]) ? $this->values[$name] : '';
            $this->errors[$name] = isset($this->errors[$name]) ? $this->errors[$name] : '';
            switch((string)$item->type) {
                default:
                    if ((string)$item->lang == 'true') {
                        foreach (Lang::langs() as $lang) {
                            $nameLang = $name.'_'.$lang;
                            $this->values[$nameLang] = isset($this->values[$nameLang]) ? $this->values[$nameLang] : '';
                            $this->errors[$nameLang] = isset($this->errors[$nameLang]) ? $this->errors[$nameLang] : '';
                        }
                    }
                break;
                case 'checkbox':
                    $this->values[$name] = (isset($this->values[$name])) ? $this->values[$name] : 0;
                    $this->values[$name] = ($this->values[$name]==='on') ? 1 : $this->values[$name];
                break;
                case 'point':
                    $this->values[$name.'_lat'] = (isset($this->values[$name.'_lat'])) ? $this->values[$name.'_lat'] : '';
                    $this->values[$name.'_lng'] = (isset($this->values[$name.'_lng'])) ? $this->values[$name.'_lng'] : '';
                break;
            }
        }
    }
    
    /**
    * Create the form fields.
    */
    public function createFormFields($options=array()) {
        $html = '';
        $options['multiple'] = (isset($options['multiple']) && $options['multiple']) ? true : false;
        $options['idMultiple'] = ($options['multiple']) ? md5(rand()*rand()*rand()) : '';
        $options['idMultiple'] = (isset($options['idMultipleJs']) && $options['idMultipleJs']!='') ? $options['idMultipleJs'] : $options['idMultiple'];
        $options['nameMultiple'] = (isset($options['nameMultiple'])) ? $options['nameMultiple'] : '';
        if ($this->object->hasOrd()) {
            $nameOrd = ($options['nameMultiple']!='') ? $options['nameMultiple'].'['.$options['idMultiple'].'][ord]' : 'ord';
            $html .= FormField_Hidden::create(array_merge(array('name'=>$nameOrd, 'value'=>$this->object->get('ord'), 'class'=>'fieldOrd'), $options));
        }
        foreach($this->object->getAttributes() as $item) {
            if (!((string)$item->type=='password' && $this->object->get('password')!='')) {
                $html .= $this->createFormField($item, $options);
            }
        }
        return $html;
    }

    /**
    * Create the form field.
    */
    public function createFormField($item, $options=array()) {
        $name = (string)$item->name;
        $label = (string)$item->label;
        $type = (string)$item->type;
        $options = array_merge($options, 
                                array('item'=>$item, 
                                        'values'=>$this->values, 
                                        'errors'=>$this->errors, 
                                        'typeField'=>$type, 
                                        'object'=>$this->object));
        switch (Db_ObjectType::baseType($type)) {
            default:
                return FormField::show($type, $options);
            break;
            case 'select':
                switch ($type) {
                    default:
                        return FormField::show('select', $options);
                    break;
                    case 'select-link':
                        return FormField::show('selectLink', $options);
                    break;
                    case 'select-link-simple':
                        return FormField::show('selectLinkSimple', $options);
                    break;
                }
            break;
            case 'id':
            case 'linkid':
            case 'hidden':
                switch ($type) {
                    default:
                        return FormField::show('hidden', $options);
                    break;
                    case 'hidden-login':
                        $login = User_Login::getInstance();
                        $options['values'][$name] = $login->id();
                        return FormField::show('hidden', $options);
                    break;
                    case 'id-varchar':
                        return FormField::show('textSmall', $options).'
                                '.FormField::create('hidden', array('name'=>$name.'_oldId', 'value'=>$this->object->id()));
                    break;
                }
            break;
            case 'multiple':
                switch($type) {
                    case 'multiple-select':
                        $this->object->loadMultipleValuesAll();
                        $refObject = (string)$item->refObject;
                        $refObjectIns = new $refObject();
                        $selected = array();
                        foreach($refObjectIns->basicInfoArray() as $key=>$item) {
                            foreach($this->object->get($name) as $itemsIns) {
                                if ($key == $itemsIns[$refObjectIns->primary]) {
                                    $selected[] = $key;
                                }
                            }
                        }
                        $options = array('name'=>$name.'[]',
                                            'label'=>$label,
                                            'multiple'=>true,
                                            'size'=>'5',
                                            'value'=>$refObjectIns->basicInfoAdminArray(),
                                            'selected'=>$selected);
                        $multipleSelected = FormField_Select::create($options);
                        return '<div class="multipleCheckboxes multipleCheckboxes-'.$name.'">
                                    <div class="multipleCheckboxesIns">
                                        '.$multipleSelected.'
                                    </div>
                                </div>';
                    break;
                    case 'multiple-autocomplete':
                        $this->object->loadMultipleValuesAll();
                        $refObject = (string)$item->refObject;
                        $refObjectIns = new $refObject();
                        $refAttribute = (string)$item->refAttribute;
                        $autocompleteItems = '';
                        foreach($refObjectIns->basicInfoArray() as $key=>$item) {
                            foreach($this->object->get($name) as $itemsIns) {
                                if ($key == $itemsIns[$refObjectIns->primary]) {
                                    $autocompleteItems .= $item.', ';
                                }
                            }
                        }
                        $autocompleteItems = substr($autocompleteItems, 0, -2);
                        $options = array('name'=>$name,
                                            'label'=>$label,
                                            'size'=>'60',
                                            'value'=>$autocompleteItems);
                        $autocomplete = FormField_Text::create($options);
                        return '<div class="autocompleteItem autocompleteItem-'.$name.'" rel="'.url($refObject.'/autocomplete/'.$refAttribute, true).'">
                                    <div class="autocompleteItemIns">
                                        '.$autocomplete.'
                                    </div>
                                </div>';
                    break;
                    case 'multiple-checkbox':
                        $this->object->loadMultipleValuesAll();
                        $refObject = (string)$item->refObject;
                        $refObjectIns = new $refObject();
                        $label = ((string)$item->label!='') ? '<label>'.__((string)$item->label).'</label>' : '';
                        $multipleCheckbox = '';
                        foreach($refObjectIns->basicInfoAdminArray() as $key=>$item) {
                            $value = 0;
                            foreach($this->object->get($name) as $itemsIns) {
                                if ($key == $itemsIns[$refObjectIns->primary]) {
                                    $value = 1;
                                }
                            }
                            $options = array('name'=>$name.'['.$key.']',
                                            'label'=>$item,
                                            'value'=>$value);
                            $multipleCheckbox .= FormField_Checkbox::create($options);
                        }
                        return '<div class="multipleCheckboxes multipleCheckboxes-'.$name.'">
                                    '.$label.'
                                    <div class="multipleCheckboxesIns">
                                        '.$multipleCheckbox.'
                                    </div>
                                </div>';
                    break;
                    case 'multiple-object':
                        $this->object->loadMultipleValuesAll();
                        $refObject = (string)$item->refObject;
                        $refObjectForm = $refObject.'_Form';
                        $nestedFormField = '';
                        $multipleOptions = array('multiple'=>true, 'nameMultiple'=>$name, 'idMultipleJs'=>'#ID_MULTIPLE#');
                        $refObjectFormIns = new $refObjectForm();
                        $label = ((string)$item->label!='') ? '<label>'.__((string)$item->label).'</label>' : '';
                        $orderNested = ($refObjectFormIns->object->hasOrd()) ? '<div class="nestedFormFieldOrder"></div>' : '';
                        $nestedFormFieldEmpty = '<div class="nestedFormFieldEmpty">
                                                        <div class="nestedFormFieldOptions">
                                                            <div class="nestedFormFieldDelete"></div>
                                                            '.$orderNested.'
                                                        </div>
                                                        <div class="nestedFormFieldContent">
                                                            '.$refObjectFormIns->createFormFields($multipleOptions).'
                                                        </div>
                                                    </div>';
                        foreach ($this->object->get($name) as $itemValues) {
                            $refObjectIns = new $refObject($itemValues);
                            $refObjectFormIns = new $refObjectForm($itemValues, array(), $refObjectIns);
                            $multipleOptionsIns = array('multiple'=>true, 'nameMultiple'=>$name);
                            $orderNested = ($refObjectFormIns->object->hasOrd()) ? '<div class="nestedFormFieldOrder"></div>' : '';
                            $nestedFormField .= '<div class="nestedFormFieldObject" rel="'.$refObjectIns->id().'">
                                                        <div class="nestedFormFieldOptions">
                                                            <div class="nestedFormFieldDelete" rel="'.url($refObject.'/delete/'.$refObjectIns->id(), true).'"></div>
                                                            '.$orderNested.'
                                                        </div>
                                                        <div class="nestedFormFieldContent">
                                                            '.$refObjectFormIns->createFormFields($multipleOptionsIns).'
                                                        </div>
                                                    </div>';
                        }
                        $classSortable = ($refObjectFormIns->object->hasOrd()) ? 'nestedFormFieldSortable' : '';
                        return '<div class="nestedFormField nestedFormField-'.$name.'">
                                    '.$label.'
                                    <div class="nestedFormFieldIns '.$classSortable.'">
                                        '.$nestedFormField.'
                                    </div>
                                    <div class="nestedFormFieldNew">
                                        '.$nestedFormFieldEmpty.'
                                        <div class="nestedFormFieldAdd">'.__('addNewRegister').'</div>
                                    </div>
                                </div>';
                    break;
                }
            break;
        }
    }

    /**
    * Return a form field.
    */
    public function field($attribute, $options=array()) {
        return $this->createFormField($this->object->attributeInfo($attribute), $options);
    }

    /**
    * Create a form.
    */
    public static function createForm($fields, $options=array()) {
        $action = (isset($options['action'])) ? $options['action'] : '';
        $method = (isset($options['method'])) ? $options['method'] : 'post';
        $submit = (isset($options['submit'])) ? $options['submit'] : __('send');
        $submitName = (isset($options['submitName'])) ? $options['submitName'] : 'submit';
        $class = (isset($options['class'])) ? $options['class'] : 'formAdmin';
        $id = (isset($options['id'])) ? 'id="'.$options['id'].'"' : '';
        if ($submit=='ajax') {
            $submitButton = '<div class="submitBtn"></div>';
        } else {
            if (is_array($submit)) {
                $submitButton = '';
                foreach ($submit as $keySubmit=>$submitIns) {
                    $submitButton .= '<input type="submit" name="submit-'.$keySubmit.'" class="formSubmit formSubmit'.ucwords($keySubmit).'" value="'.$submitIns.'"/>';
                }
                $submitButton = '<div class="submitButtons">
                                    '.$submitButton.'
                                </div>';
            } else {
                $submitButton = FormField::show('submit', array('name'=>$submitName,
                                                                'class'=>'formSubmit',
                                                                'value'=>$submit));
            }
        }
        $submitButton = ($submit=='none') ? '' : $submitButton;
        return '<form '.$id.' action="'.$action.'" method="'.$method.'" enctype="multipart/form-data" class="'.$class.'" accept-charset="UTF-8">
                    <fieldset>
                        '.$fields.'
                        '.$submitButton.'
                    </fieldset>
                </form>';
    }

    /**
    * Check if the form is valid.
    */
    public function isValid() {
        $errors = array();
        foreach($this->object->getAttributes() as $item) {
            $error = $this->isValidField($item);
            if (count($error)>0) {
                $errors = array_merge($error, $errors);
            }
        }
        return $errors;
    }

    /**
    * Checks if an item is valid.
    */
    public function isValidField($item, $required='') {
        $error = array();
        $name = (string)$item->name;
        $required = ($required=='') ? (string)$item->required : $required;
        switch ($required) {
            case 'notEmpty':
                if ((string)$item->lang == 'true') {
                    foreach (Lang::langs() as $lang) {
                        if (!isset($this->values[$name.'_'.$lang]) || strlen(trim($this->values[$name.'_'.$lang])) == 0) {
                            $error[$name.'_'.$lang] = __('notEmpty');
                        }
                    }
                } else {
                    if (!isset($this->values[$name]) || strlen(trim($this->values[$name])) == 0) { 
                        $error[$name] = __('notEmpty');
                    }
                }
            break;
            case 'notEmptyPoint':
                if (!isset($this->values[$name.'_lat']) || strlen(trim($this->values[$name.'_lat'])) == 0) { 
                    $error[$name] = __('notEmpty');
                }
                if (!isset($this->values[$name.'_lng']) || strlen(trim($this->values[$name.'_lng'])) == 0) { 
                    $error[$name] = __('notEmpty');
                }
            break;
            case 'email':
                $error = array_merge($error, $this->isValidField($item, 'notEmpty'));
                if (!filter_var($this->values[$name], FILTER_VALIDATE_EMAIL)) {
                    $error[$name] = __('errorMail');
                }
            break;
            case 'password':
                if (!isset($this->values[$name]) || strlen(trim($this->values[$name])) == 0) { 
                    $error[$name] = __('notEmpty');
                } else {
                    if (strlen($this->values[$name]) < 6) { 
                        $error[$name] = __('errorPasswordSize');
                    }
                    if (preg_match('/^[a-z0-9]+$/i', $this->values[$name])==false) {
                        $error[$name] = __('errorPasswordAlpha');
                    }
                }
            break;
            case 'unique':
                $error = array_merge($error, $this->isValidField($item, 'notEmpty'));
                $whereId = ($this->object->id()!='') ? $this->object->primary.'!="'.$this->object->id().'" AND ' : '';
                if ((string)$item->lang == 'true') {
                    foreach (Lang::langs() as $lang) {
                        $existingObject = $this->object->readFirst(array('where'=>$whereId.$name.'_'.$lang.'="'.$this->values[$name.'_'.$lang].'"'));
                        if ($existingObject->id()!='') {
                            $error[$name.'_'.$lang] = __('errorExisting');
                        }
                    }
                } else {
                    $existingObject = $this->object->readFirst(array('where'=>$whereId.$name.'="'.$this->values[$name].'"'));
                    if ($existingObject->id()!='') {
                        $error[$name] = __('errorExisting');
                    }
                }
            break;
            case 'unique-email':
                $error = array_merge($error, $this->isValidField($item, 'email'));
                $error = array_merge($error, $this->isValidField($item, 'unique'));
            break;
        }
        return $error;
    }

    /**
    * Check if a value is empty.
    */
    public function isValidEmpty($field, &$errors) {
        if (!isset($this->values[$field]) || strlen(trim($this->values[$field])) == 0) { 
            $errors[$field] = __('notEmpty');
        }
    }

}
?>