<?php
/**
 * @class Form
 *
 * This is a helper class to create and format forms.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Form
{

    /**
     * A form is created using an XML model, it uses values and errors with the same names as the object properties.
     */
    public function __construct($values = [], $errors = [], $object = '')
    {
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
    public function fromArray($values = [], $errors = [])
    {
        $formClass = get_class($this);
        return new $formClass($values, $errors);
    }

    /**
     * Create a form from an object.
     */
    public static function fromObject($object)
    {
        $formClass = get_class($object) . '_Form';
        return new $formClass($object->values, [], $object);
    }

    /**
     * Get a form value.
     */
    public function get($name)
    {
        return (isset($this->$name)) ? $this->$name : '';
    }

    /**
     * Get all the form values.
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Set a form value.
     */
    public function setValue($key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * Add values to the form.
     */
    public function addValues($values, $errors = [])
    {
        $this->values = array_merge($this->values, $values);
        $this->errors = array_merge($this->errors, $errors);
    }

    /**
     * Prepare the values.
     */
    public function prepareValues()
    {
        foreach ($this->object->getAttributes() as $item) {
            $name = (string) $item->name;
            $this->values[$name] = isset($this->values[$name]) ? $this->values[$name] : '';
            $this->errors[$name] = isset($this->errors[$name]) ? $this->errors[$name] : '';
            switch ((string) $item->type) {
                default:
                    if ((string) $item->language == 'true') {
                        foreach (Language::languages() as $language) {
                            $nameLanguage = $name . '_' . $language['id'];
                            $this->values[$nameLanguage] = isset($this->values[$nameLanguage]) ? $this->values[$nameLanguage] : '';
                            $this->errors[$nameLanguage] = isset($this->errors[$nameLanguage]) ? $this->errors[$nameLanguage] : '';
                        }
                    }
                    break;
                case 'checkbox':
                    $this->values[$name] = (isset($this->values[$name])) ? $this->values[$name] : 0;
                    $this->values[$name] = ($this->values[$name] === 'on') ? 1 : $this->values[$name];
                    break;
                case 'point':
                    $this->values[$name . '_lat'] = (isset($this->values[$name . '_lat'])) ? $this->values[$name . '_lat'] : '';
                    $this->values[$name . '_lng'] = (isset($this->values[$name . '_lng'])) ? $this->values[$name . '_lng'] : '';
                    break;
            }
        }
    }

    /**
     * Create the form fields.
     */
    public function createFormFields($options = [])
    {
        $html = '';
        $options['multiple'] = (isset($options['multiple']) && $options['multiple']) ? true : false;
        $options['idMultiple'] = ($options['multiple']) ? md5(rand() * rand() * rand()) : '';
        $options['idMultiple'] = (isset($options['idMultipleJs']) && $options['idMultipleJs'] != '') ? $options['idMultipleJs'] : $options['idMultiple'];
        $options['nameMultiple'] = (isset($options['nameMultiple'])) ? $options['nameMultiple'] : '';
        if ($this->object->hasOrd()) {
            $nameOrd = ($options['nameMultiple'] != '') ? $options['nameMultiple'] . '[' . $options['idMultiple'] . '][ord]' : 'ord';
            $html .= FormField_Hidden::create(array_merge(['name' => $nameOrd, 'value' => $this->object->get('ord'), 'class' => 'fieldOrd'], $options));
        }
        foreach ($this->object->getAttributes() as $item) {
            if (!((string) $item->type == 'password' && $this->object->get('password') != '')) {
                $html .= $this->createFormField($item, $options);
            }
        }
        return $html;
    }

    /**
     * Create the form field.
     */
    public function createFormField($item, $options = [])
    {
        $name = (string) $item->name;
        $label = (string) $item->label;
        $type = (string) $item->type;
        $options = array_merge($options,
            ['item' => $item,
                'values' => $this->values,
                'errors' => $this->errors,
                'typeField' => $type,
                'object' => $this->object]);
        switch (Db_ObjectType::baseType($type)) {
            default:
                return FormField::show($type, $options);
                break;
            case 'select':
                switch ($type) {
                    default:
                        return FormField::show('select', $options);
                        break;
                    case 'select_link':
                        return FormField::show('selectLink', $options);
                        break;
                    case 'select_link-simple':
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
                    case 'hidden_login':
                        $login = UserAdmin_Login::getInstance();
                        $options['values'][$name] = $login->id();
                        return FormField::show('hidden', $options);
                        break;
                    case 'id_varchar':
                        return FormField::show('textSmall', $options) . '
                                ' . FormField::create('hidden', ['name' => $name . '_oldId', 'value' => $this->object->id()]);
                        break;
                }
                break;
            case 'autocomplete':
                return '<div class="autocompleteItem autocompleteItem-' . $name . '" data-url="' . url($refObject . '/autocomplete/' . $refAttribute, true) . '">
                            <div class="autocompleteItemIns">
                                ' . $autocomplete . '
                            </div>
                        </div>';
                break;
            case 'multiple':
                switch ($type) {
                    case 'multiple-select':
                        $this->object->loadMultipleValuesAll();
                        $refObject = (string) $item->refObject;
                        $refObjectIns = new $refObject();
                        $selected = [];
                        foreach ($refObjectIns->basicInfoArray() as $key => $item) {
                            foreach ($this->object->get($name) as $itemsIns) {
                                if ($key == $itemsIns[$refObjectIns->primary]) {
                                    $selected[] = $key;
                                }
                            }
                        }
                        $options = ['name' => $name . '[]',
                            'label' => $label,
                            'multiple' => true,
                            'size' => '5',
                            'value' => $refObjectIns->basicInfoAdminArray(),
                            'selected' => $selected];
                        $multipleSelected = FormField_Select::create($options);
                        return '<div class="multiple_checkboxes multiple_checkboxes-' . $name . '">
                                    <div class="multiple_checkboxes_ins">
                                        ' . $multipleSelected . '
                                    </div>
                                </div>';
                        break;
                    case 'multiple_autocomplete':
                        $this->object->loadMultipleValuesAll();
                        $refObject = (string) $item->refObject;
                        $refObjectIns = new $refObject();
                        $refAttribute = (string) $item->refAttribute;
                        $autocompleteItems = '';
                        foreach ($refObjectIns->basicInfoArray() as $key => $item) {
                            foreach ($this->object->get($name) as $itemsIns) {
                                if ($key == $itemsIns[$refObjectIns->primary]) {
                                    $autocompleteItems .= $item . ', ';
                                }
                            }
                        }
                        $autocompleteItems = substr($autocompleteItems, 0, -2);
                        $options = ['name' => $name,
                            'label' => $label,
                            'size' => '60',
                            'value' => $autocompleteItems];
                        $autocomplete = FormField_Text::create($options);
                        return '<div class="autocompleteItem autocompleteItem-' . $name . '" data-url="' . url($refObject . '/autocomplete/' . $refAttribute, true) . '">
                                    <div class="autocompleteItemIns">
                                        ' . $autocomplete . '
                                    </div>
                                </div>';
                        break;
                    case 'multiple_checkbox':
                        $this->object->loadMultipleValuesAll();
                        $refObject = (string) $item->refObject;
                        $refObjectIns = new $refObject();
                        $label = ((string) $item->label != '') ? '<label>' . __((string) $item->label) . '</label>' : '';
                        $multipleCheckbox = '';
                        foreach ($refObjectIns->basicInfoAdminArray() as $key => $item) {
                            $value = 0;
                            foreach ($this->object->get($name) as $itemsIns) {
                                if ($key == $itemsIns[$refObjectIns->primary]) {
                                    $value = 1;
                                }
                            }
                            $options = ['name' => $name . '[' . $key . ']',
                                'label' => $item,
                                'value' => $value];
                            $multipleCheckbox .= FormField_Checkbox::create($options);
                        }
                        return '<div class="multiple_checkboxes multiple_checkboxes-' . $name . '">
                                    ' . $label . '
                                    <div class="multiple_checkboxes_ins">
                                        ' . $multipleCheckbox . '
                                    </div>
                                </div>';
                        break;
                    case 'multiple_object':
                        $this->object->loadMultipleValuesAll();
                        $refObject = (string) $item->refObject;
                        $refObjectForm = $refObject . '_Form';
                        $nested_form_field = '';
                        $multipleOptions = ['multiple' => true, 'nameMultiple' => $name, 'idMultipleJs' => '#ID_MULTIPLE#'];
                        $refObjectFormIns = new $refObjectForm();
                        $label = ((string) $item->label != '') ? '<label>' . __((string) $item->label) . '</label>' : '';
                        $orderNested = ($refObjectFormIns->object->hasOrd()) ? '<div class="nested_form_field_order"><i class="fa fa-arrows-alt"></i></div>' : '';
                        $nested_form_field_empty = '<div class="nested_form_field_empty">
                                                        <div class="nested_form_field_options">
                                                            <div class="nested_form_field_delete">
                                                                <i class="fa fa-delete"></i>
                                                            </div>
                                                            ' . $orderNested . '
                                                        </div>
                                                        <div class="nested_form_field_content">
                                                            ' . $refObjectFormIns->createFormFields($multipleOptions) . '
                                                        </div>
                                                    </div>';
                        foreach ($this->object->get($name) as $itemValues) {
                            $refObjectIns = new $refObject($itemValues);
                            $refObjectFormIns = new $refObjectForm($itemValues, [], $refObjectIns);
                            $multipleOptionsIns = ['multiple' => true, 'nameMultiple' => $name];
                            $orderNested = ($refObjectFormIns->object->hasOrd()) ? '<div class="nested_form_field_order"><i class="fa fa-arrows-alt"></i></div>' : '';
                            $nested_form_field .= '<div class="nested_form_field_object" data-id="' . $refObjectIns->id() . '">
                                                        <div class="nested_form_field_options">
                                                            <div class="nested_form_field_delete" data-url="' . url($refObject . '/delete/' . $refObjectIns->id(), true) . '">
                                                                <i class="fa fa-delete"></i>
                                                            </div>
                                                            ' . $orderNested . '
                                                        </div>
                                                        <div class="nested_form_field_content">
                                                            ' . $refObjectFormIns->createFormFields($multipleOptionsIns) . '
                                                        </div>
                                                    </div>';
                        }
                        $classSortable = ($refObjectFormIns->object->hasOrd()) ? 'nested_form_fieldSortable' : '';
                        return '<div class="nested_form_field nested_form_field-' . $name . '">
                                    ' . $label . '
                                    <div class="nested_form_field_ins ' . $classSortable . '">
                                        ' . $nested_form_field . '
                                    </div>
                                    <div class="nested_form_fieldNew">
                                        ' . $nested_form_field_empty . '
                                        <div class="nested_form_field_add">' . __('addNewRegister') . '</div>
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
    public function field($attribute, $options = [])
    {
        return $this->createFormField($this->object->attributeInfo($attribute), $options);
    }

    /**
     * Create a form.
     */
    public static function createForm($fields, $options = [])
    {
        $action = (isset($options['action'])) ? $options['action'] : '';
        $method = (isset($options['method'])) ? $options['method'] : 'post';
        $submit = (isset($options['submit'])) ? $options['submit'] : __('send');
        $submitName = (isset($options['submitName'])) ? $options['submitName'] : 'submit';
        $class = (isset($options['class'])) ? $options['class'] : 'form_admin';
        $id = (isset($options['id'])) ? 'id="' . $options['id'] . '"' : '';
        if ($submit == 'ajax') {
            $submitButton = '<div class="submitBtn"></div>';
        } else {
            if (is_array($submit)) {
                $submitButton = '';
                foreach ($submit as $keySubmit => $submitIns) {
                    $submitButton .= '<input type="submit" name="submit-' . $keySubmit . '" class="form_submit form_submit' . ucwords($keySubmit) . '" value="' . $submitIns . '"/>';
                }
                $submitButton = '<div class="submit_buttons">
                                    ' . $submitButton . '
                                </div>';
            } else {
                $submitButton = FormField::show('submit', ['name' => $submitName,
                    'class' => 'form_submit',
                    'value' => $submit]);
            }
        }
        $submitButton = ($submit == 'none') ? '' : $submitButton;
        return '<form ' . $id . ' action="' . $action . '" method="' . $method . '" enctype="multipart/form-data" class="' . $class . '" accept-charset="UTF-8">
                    <fieldset>
                        ' . $fields . '
                        ' . $submitButton . '
                    </fieldset>
                </form>';
    }

    /**
     * Check if the form is valid.
     */
    public function isValid()
    {
        $errors = [];
        foreach ($this->object->getAttributes() as $item) {
            $error = $this->isValidField($item);
            if (count($error) > 0) {
                $errors = array_merge($error, $errors);
            }
        }
        return $errors;
    }

    /**
     * Checks if an item is valid.
     */
    public function isValidField($item, $required = '')
    {
        $error = [];
        $name = (string) $item->name;
        $required = ($required == '') ? (string) $item->required : $required;
        switch ($required) {
            case 'not_empty':
                if ((string) $item->language == 'true') {
                    foreach (Language::languages() as $language) {
                        if (!isset($this->values[$name . '_' . $language['id']]) || strlen(trim($this->values[$name . '_' . $language['id']])) == 0) {
                            $error[$name . '_' . $language['id']] = __('not_empty');
                        }
                    }
                } else {
                    if (!isset($this->values[$name]) || trim($this->values[$name]) == '') {
                        $error[$name] = __('not_empty');
                    }
                }
                break;
            case 'not_empty_point':
                if (!isset($this->values[$name . '_lat']) || trim($this->values[$name . '_lat']) == '') {
                    $error[$name] = __('not_empty');
                }
                if (!isset($this->values[$name . '_lng']) || trim($this->values[$name . '_lng']) == '') {
                    $error[$name] = __('not_empty');
                }
                break;
            case 'email':
                $error = array_merge($error, $this->isValidField($item, 'not_empty'));
                if (!filter_var($this->values[$name], FILTER_VALIDATE_EMAIL)) {
                    $error[$name] = __('errorMail');
                }
                break;
            case 'password':
                if (!isset($this->values[$name]) || trim($this->values[$name]) == '') {
                    $error[$name] = __('not_empty');
                } else {
                    $errorPassword = Form::validatePassword($this->values[$name]);
                    if ($errorPassword != '') {
                        $error[$name] = $errorPassword;
                    }
                }
                break;
            case 'unique':
                $error = array_merge($error, $this->isValidField($item, 'not_empty'));
                $whereId = ($this->object->id() != '') ? $this->object->primary . '!="' . $this->object->id() . '" AND ' : '';
                if ((string) $item->language == 'true') {
                    foreach (Language::languages() as $language) {
                        $existingObject = $this->object->readFirst(['where' => $whereId . $name . '_' . $language['id'] . '="' . $this->values[$name . '_' . $language['id']] . '"']);
                        if ($existingObject->id() != '') {
                            $error[$name . '_' . $language['id']] = __('errorExisting');
                        }
                    }
                } else {
                    $existingObject = $this->object->readFirst(['where' => $whereId . $name . '="' . $this->values[$name] . '"']);
                    if ($existingObject->id() != '') {
                        $error[$name] = __('errorExisting');
                    }
                }
                break;
            case 'unique_email':
                $error = array_merge($error, $this->isValidField($item, 'email'));
                $error = array_merge($error, $this->isValidField($item, 'unique'));
                break;
        }
        return $error;
    }

    /**
     * Validate a strong password with at least 8 characters, one uppercase and a digit.
     */
    public static function validatePassword($password)
    {
        if (strlen($password) < 8) {
            return __('error_password_size');
        }
        if (!preg_match('@[A-Z]@', $password)) {
            return __('error_password_uppercase');
        }
        if (!preg_match('@[a-z]@', $password)) {
            return __('error_password_lowercase');
        }
        if (!preg_match('@[0-9]@', $password)) {
            return __('error_password_number');
        }
        return '';
    }

}
