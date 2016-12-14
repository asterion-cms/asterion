<?php
/**
* @class FormFieldFile
*
* This is a helper class to generate a file form field.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class FormField_File {

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
        $this->options['name'] = ($nameMultiple) ? $options['nameMultiple'].'-'.$options['idMultiple'].'-'.$this->name : $this->name;
        $this->options['value'] = $this->values[$this->name];
        $this->options['error'] = $this->errors[$this->name];
        $this->options['label'] = (string)$this->item->label;
        $this->options['placeholder'] = (string)$this->item->placeholder;
        $this->options['lang'] = (string)$this->item->lang;
        $this->options['mode'] = (string)$this->item->mode;
        $this->options['layout'] = (string)$this->item->layout;
        $this->options['className'] = $this->object->className;
        $this->options['required'] = ((string)$this->item->required!='') ? true : false;
        $this->options['typeField'] = (isset($options['typeField'])) ? $options['typeField'] : 'file';
    }

    /**
    * Render a default file input element
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
                $this->options['class'] = 'formField_'.$nameLang;
                $fields .= FormField_File::create($this->options);
            }
            return $fields;
        } else {
            return FormField_File::create($this->options);
        }
    }

    /**
    * Render the element with an static function.
    */
    static public function create($options) {
        $type = (isset($options['typeField'])) ? $options['typeField'] : 'text';
        $typeField = (isset($options['typeField'])) ? 'type="'.$options['typeField'].'"' : 'type="text"';
        $name = (isset($options['name'])) ? 'name="'.$options['name'].'" ' : '';
        $id = (isset($options['id'])) ? 'id="'.$options['id'].'"' : '';
        $labelLang = (isset($options['labelLang']) && $options['labelLang']!='') ? ' <span>('.$options['labelLang'].')</span>' : '';
        $label = (isset($options['label'])) ? '<label>'.__($options['label']).$labelLang.'</label>' : '';
        $value = (isset($options['value'])) ? 'value="'.$options['value'].'" ' : '';
        $valueFile = (isset($options['value'])) ? $options['value'] : '';
        $disabled = (isset($options['disabled']) && $options['disabled']!=false) ? 'disabled="disabled"' : '';
        $size = (isset($options['size'])) ? 'size="'.$options['size'].'" ' : '';
        $error = (isset($options['error']) && $options['error']!='') ? '<div class="error">'.$options['error'].'</div>' : '';
        $class = (isset($options['class'])) ? $options['class'] : '';
        $class .= (isset($options['name'])) ? ' formField-'.Text::simpleUrl($options['name']) : '';
        $classError = (isset($options['error']) && $options['error']!='') ? 'error' : '';
        $placeholder = (isset($options['placeholder'])) ? 'placeholder="'.__($options['placeholder']).'"' : '';
        $required = (isset($options['required']) && $options['required']) ? 'required' : '';
        $layout = (isset($options['layout'])) ? $options['layout'] : '';
        $mode = (isset($options['mode'])) ? $options['mode'] : '';
        $htmlShowImage = '';
        $htmlShowFile = '';
        switch ($mode) {
            default:
                $file = STOCK_FILE.$options['className'].'Files/'.$valueFile;
                if (is_file($file)) {
                    $htmlShowFile = '<div class="formFieldsFile">
                                        <div class="formFieldsFileIns">
                                            <a href="'.str_replace(STOCK_FILE, STOCK_URL, $file).'" target="_blank">'.__('downloadFile').'</a>
                                        </div>
                                    </div>';
                }
            break;
            case 'image':
                $file = STOCK_FILE.$options['className'].'/'.$valueFile.'/'.$valueFile.'_thumb.jpg';
                $file = (!is_file($file)) ? STOCK_FILE.$options['className'].'/'.$valueFile.'/'.$valueFile.'_small.jpg' : $file;
                $file = (!is_file($file)) ? STOCK_FILE.$options['className'].'/'.$valueFile.'/'.$valueFile.'_web.jpg' : $file;
                if (is_file($file)) {
                    $htmlShowImage = '<div class="formFieldsImage">
                                        <div class="formFieldsImageIns">
                                            <img src="'.str_replace(STOCK_FILE, STOCK_URL, $file).'?v='.substr(md5(rand()*rand()), 0, 5).'" alt=""/>
                                        </div>
                                    </div>';
                }
            break;
            case 'adaptable':
                $file = STOCK_FILE.$options['className'].'/'.$valueFile.'/'.$valueFile.'_thumb.jpg';
                $file = (!is_file($file)) ? STOCK_FILE.$options['className'].'/'.$valueFile.'/'.$valueFile.'_small.jpg' : $file;
                $file = (!is_file($file)) ? STOCK_FILE.$options['className'].'/'.$valueFile.'/'.$valueFile.'_web.jpg' : $file;
                if (is_file($file)) {
                    $htmlShowImage = '<div class="formFieldsImage">
                                        <div class="formFieldsImageIns">
                                            <img src="'.str_replace(STOCK_FILE, STOCK_URL, $file).'?v='.substr(md5(rand()*rand()), 0, 5).'" alt=""/>
                                        </div>
                                    </div>';
                } else {
                    $file = STOCK_FILE.$options['className'].'Files/'.$valueFile;
                    if (is_file($file)) {
                        $htmlShowFile = '<div class="formFieldsFile">
                                            <div class="formFieldsFileIns">
                                                <a href="'.str_replace(STOCK_FILE, STOCK_URL, $file).'" target="_blank">'.__('downloadFile').'</a>
                                            </div>
                                        </div>';
                    }
                }
            break;
        }
        switch ($layout) {
            default:
                return '<div class="'.$type.' formField '.$class.' '.$classError.'">
                            <div class="formFieldIns">
                                '.$label.'
                                '.$error.'
                                '.$htmlShowImage.'
                                <input '.$typeField.' '.$name.' '.$size.' '.$value.' '.$id.' '.$disabled.' '.$placeholder.' '.$required.'/>
                                '.$htmlShowFile.'
                            </div>
                        </div>';
            break;
            case 'url':
                return '<div class="'.$type.' formField '.$class.' '.$classError.'">
                            <div class="formFieldIns">
                                '.$label.'
                                '.$error.'
                                '.$htmlShowImage.'
                                <input type="text" '.$name.' '.$size.' '.$id.' '.$disabled.' '.$placeholder.' '.$required.'/>
                                '.$htmlShowFile.'
                            </div>
                        </div>';
            break;
            case 'simple':
                return '<input '.$typeField.' '.$name.' '.$size.' '.$value.' '.$id.' '.$disabled.' '.$placeholder.'/>';
            break;
        }
    }
    
}
?>