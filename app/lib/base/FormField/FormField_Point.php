<?php
/**
* @class FormFieldPoint
*
* This is a helper class to generate a point form field.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class FormField_Point {

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
        $this->options['name'] = $this->name;
        $this->options['name'] = ($nameMultiple) ? $options['nameMultiple'].'['.$options['idMultiple'].']['.$this->options['name'].']' : $this->options['name'];
        $this->options['value'] = $this->values[$this->name];
        $this->options['valueLat'] = $this->values[$this->name.'_lat'];
        $this->options['valueLng'] = $this->values[$this->name.'_lng'];
        $this->options['error'] = $this->errors[$this->name];
        $this->options['label'] = (string)$this->item->label;
        $this->options['placeholder'] = (string)$this->item->placeholder;
        $this->options['lang'] = (string)$this->item->lang;
        $this->options['layout'] = (string)$this->item->layout;
        $this->options['showHideMap'] = (string)$this->item->showHideMap;
        $this->options['typeField'] = (isset($options['typeField'])) ? $options['typeField'] : 'point';
    }

    /**
    * Render a default input element
    */
    public function show() {
        return FormField_Point::create($this->options);
    }

    /**
    * Render the element with an static function.
    */
    static public function create($options) {
        $id = (isset($options['id'])) ? $options['id'] : '';
        $label = (isset($options['label'])) ? '<label>'.__($options['label']).'</label>' : '';
        $value = (isset($options['value'])) ? $options['value'] : '';
        $valueLat = (isset($options['valueLat'])) ? $options['valueLat'] : '';
        $valueLng = (isset($options['valueLng'])) ? $options['valueLng'] : '';
        $name = (isset($options['name'])) ? $options['name'] : '';
        $disabled = (isset($options['disabled'])) ? 'disabled="disabled"' : '';
        $error = (isset($options['error']) && $options['error']!='') ? '<div class="error">'.$options['error'].'</div>' : '';
        $errorClass = (isset($options['error']) && $options['error']!='') ? 'error' : '';
        $class = (isset($options['class'])) ? $options['class'] : '';
        $class .= (isset($options['name'])) ? ' formField-'.Text::simpleUrl($options['name']) : '';
        $layout = (isset($options['layout'])) ? $options['layout'] : '';
        switch ($layout) {
            default:
                return '<div class="point formField '.$class.' '.$errorClass.'">
                            '.$label.'
                            '.$error.'
                            <label><span>'.__('latitude').'</span></label>
                            <input type="text" name="'.$name.'_lat" value="'.$valueLat.'" id="'.$id.'_lat" '.$disabled.'/>
                            <label><span>'.__('longitude').'</span></label>
                            <input type="text" name="'.$name.'_lng" value="'.$valueLng.'" id="'.$id.'_lng" '.$disabled.'/>
                        </div>';
            break;
            case 'map':
                $valueLat = ($valueLat!='' && $valueLat!='0') ? $valueLat : '';
                $valueLng = ($valueLng!='' && $valueLng!='0') ? $valueLng : '';
                $valueZoom = Params::param('initZoom');
                $idMap = substr(md5(rand()*rand()*rand()), 0, 6);
                $showHideMap = (isset($options['showHideMap']) && $options['showHideMap']=='true') ? FormField::create('checkbox', array('label'=>__('showHideMap'), 'class'=>'showHide')) : '';
                return '<div class="point pointMap formField '.$class.' '.$errorClass.'" data-initlat="'.Params::param('initLat').'" data-initlng="'.Params::param('initLng').'" data-initlng="'.Params::param('initZoom').'">
                            '.$label.'
                            '.$showHideMap.'
                            <div class="map" data-lat="'.$valueLat.'" data-lng="'.$valueLng.'" data-zoom="'.$valueZoom.'">
                                <div class="mapIns" id="'.$idMap.'"></div>
                                <input type="hidden" name="'.$name.'_lat" value="'.$valueLat.'" class="inputLat" '.$disabled.'/>
                                <input type="hidden" name="'.$name.'_lng" value="'.$valueLng.'" class="inputLng" '.$disabled.'/>
                            </div>
                        </div>';
            break;
        }
    }
    
}
?>