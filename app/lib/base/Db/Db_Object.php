<?php
/**
* @class DbObject
*
* This class the main class for all of the content objects, they all inherit the functions contained here.
* It basically maps the information on the database into a PHP object.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Db_Object extends Db_Sql {

    /**
    * Construct the object.
    */
    public function __construct($values=array()) {
        $values = (is_array($values)) ? $values : array();
        parent::__construct($values);
        $this->syncValues($values);
        $this->loadedMultiple = false;
    }
    
    /**
    * Reload the object.
    */
    public function reloadObject() {
        $values = $this->readObjectValues($this->id());
        $this->syncValues($values);
    }

    /**
    * Synchronize the values of an object.
    */
    public function syncValues($newValues=array()) {
        if (isset($newValues[$this->primary]) && $newValues[$this->primary]=='') {
            unset($newValues[$this->primary]);
        }
        $values = (is_array($newValues)) ? array_merge($this->values, $newValues) : $this->values;
        foreach($this->info->attributes->attribute as $item) {
            $name = (string)$item->name;
            if ((string)$item->lang == 'true') {
                foreach (Lang::langs() as $lang) {
                    $keyLang = $name.'_'.$lang;
                    $this->values[$keyLang] = isset($values[$keyLang]) ? $values[$keyLang] : '';
                }
            } else {
                $this->values[$name] = isset($values[$name]) ? $values[$name] : '';
                if ((string)$item->type == 'textarea-ck') {
                    $this->values[$name] = Text::decodeText($this->values[$name]);
                }
                if ((string)$item->type == 'point') {
                    if (isset($values[$name]) && $values[$name]!='') {
                        $infoPoint = explode(':', $values[$name]);
                        $this->values[$name.'_lat'] = (isset($infoPoint[0])) ? $infoPoint[0] : '';
                        $this->values[$name.'_lng'] = (isset($infoPoint[1])) ? $infoPoint[1] : '';
                    } else {
                        if (isset($values[$name.'_lat']) && $values[$name.'_lat']!='') {
                            $this->values[$name.'_lat'] = $values[$name.'_lat'];
                        }
                        if (isset($values[$name.'_lng']) && $values[$name.'_lng']!='') {
                            $this->values[$name.'_lng'] = $values[$name.'_lng'];
                        }
                    }
                }
            }
        }
    }

    /**
    * Load all the multiple values of the object.
    */
    public function loadMultipleValuesAll() {
        if ($this->loadedMultiple != true) {
            foreach($this->info->attributes->attribute as $item) {
                $type = (string)$item->type;
                if (Db_ObjectType::baseType($type) == 'multiple') {
                    $this->loadMultipleValues($item);
                }
            }
            $this->loadedMultiple = true;
        }
    }

    /**
    * Load the multiple values of a certain attribute.
    */
    public function loadMultipleValues($item) {
        $name = (string)$item->name;
        $type = (string)$item->type;
        switch($type) {
            case 'multiple-object':
                $refObject = (string)$item->refObject;
                $lnkAttribute = (string)$item->lnkAttribute;
                $refObjectIns = new $refObject();
                $order = ($refObjectIns->hasOrd()) ? 'ord' : $refObjectIns->orderBy();
                $list = $refObjectIns->readListObject(array('where'=>Db::prefixTable($refObject).'.'.$lnkAttribute.'="'.$this->id().'"',
                                                            'completeList'=>false, 
                                                            'order'=>$order));
                $this->set($name, $list);
            break;
            case 'multiple-checkbox':
            case 'multiple-select':
            case 'multiple-autocomplete':
                $refObject = (string)$item->refObject;
                $refObjectIns = new $refObject();
                if ((string)$item->lnkObject!='') {                
                    $lnkObject = (string)$item->lnkObject;
                    $repeat = (string)$item->repeat;
                    $lnkObjectIns = new $lnkObject();
                    $lnkAttribute = $refObjectIns->primary;
                    $list = $lnkObjectIns->readListObject(array('object'=>$refObject,
                                                                'table'=>$lnkObject.','.$refObject,
                                                                'fields'=>Db::prefixTable($refObject).'.*',
                                                                'where'=>Db::prefixTable($refObject).'.'.$lnkAttribute.'='.Db::prefixTable($lnkObject).'.'.$lnkAttribute.'
                                                                        AND '.Db::prefixTable($lnkObject).'.'.$this->primary.'="'.$this->id().'"',
                                                                'completeList'=>false));
                } else {
                    $list = $refObjectIns->readListObject(array('where'=>$this->primary.'="'.$this->get($this->primary).'"',
                                                                'completeList'=>false));
                }
                $this->set($name, $list);
            break;
        }
    }

    /**
    * Get the id of an object, defined in the XML final as "primary".
    */
    public function id() {
        return (isset($this->values[$this->primary])) ? $this->values[$this->primary] : '';
    }

    /**
    * Gets the basic info of an object, normally this function should be overwritten for each object.
    */
    public function getBasicInfo() {
        $label = (string)$this->info->info->form->label;
        if ($label != '') {
            return $this->decomposeText($label);
        } else {
            return $this->id();
        }
    }

    /**
    * Function to decompose a text for the labels.
    */
    public function decomposeText($label) {
        $info = explode('_', $label);
        $result = '';
        foreach ($info as $item) {
            if (substr($item, 0, 1)=='#') {
                $result .= $this->get(substr($item, 1));
            } else {
                if (substr($item, 0, 1)=='?') {
                    $result .= __(substr($item, 1));
                } else {
                    $result .= $item;
                }
            }
        }
        return $result;
    }

    /**
    * Gets the basic info of an object, used in the admin level.
    */
    public function getBasicInfoAdmin() {
        return $this->getBasicInfo();
    }

    /**
    * Gets the basic info of an object, used for the autocompletion inputs.
    */
    public function getBasicInfoAutocomplete() {
        return $this->getBasicInfo();
    }

    /**
    * Returns all the attributes information in the XML file.
    */
    public function getAttributes() {
        return $this->info->attributes->attribute;
    }

    /**
    * Gets the public url of an object based on the "publicUrl" value on the XML file.
    */
    public function url() {
        $publicUrl = (string)$this->info->info->form->publicUrl;
        $attributes = Text::arrayWordsStarting('#', $publicUrl);
        foreach ($attributes as $attribute) {
            $publicUrl = str_replace($attribute, $this->get(str_replace('#', '', $attribute)), $publicUrl);
        }
        $translations = Text::arrayWordsStarting('@', $publicUrl);
        foreach ($translations as $translation) {
            $publicUrl = str_replace($translation, Text::simpleUrl(__(str_replace('@', '', $translation))), $publicUrl);
        }
        return url(str_replace(' ', '', $publicUrl));
    }

    /**
    * Gets the public url of a list of objects based on the "publicUrlList" value on the XML file.
    */
    public function urlList() {
        $publicUrlList = (string)$this->info->info->form->publicUrlList;
        $attributes = Text::arrayWordsStarting('#', $publicUrlList);
        foreach ($attributes as $attribute) {
            $publicUrlList = str_replace($attribute, $this->get(str_replace('#', '', $attribute)), $publicUrlList);
        }
        $translations = Text::arrayWordsStarting('@', $publicUrlList);
        foreach ($translations as $translation) {
            $publicUrlList = str_replace($translation, Text::simpleUrl(__(str_replace('@', '', $translation))), $publicUrlList);
        }
        return url(str_replace(' ', '', $publicUrlList));
    }

    /**
    * Gets the url to modify an object in an admin level.
    */
    public function urlAdmin() {
        return url($this->className.'/modifyView/'.$this->id(), true);
    }

    /**
    * Gets the url of a select-link attribute.
    */
    public function urlLink($attribute) {
        $info = explode('_', $this->get($attribute));
        switch ($info[0]) {
            case 'homePage':
                return url();
            break;
            case 'public':
                if (isset($info[1])) {
                    $objectName = $info[1];
                    $object = new $objectName();
                    return $object->urlList();
                }
            break;
            case 'item':
                if (isset($info[2])) {
                    $objectName = $info[1];
                    $object = new $objectName();
                    $object = $object->readObject($info[2]);
                    return $object->url();
                }
            break;
        }
    }

    /**
    * Gets the html basic link of an object.
    */
    public function link() {
        return '<a href="'.$this->url().'">'.$this->getBasicInfo().'</a>';
    }

    /**
    * Gets the html basic link of an object that opens in a new window.
    */
    public function linkNew() {
        return '<a href="'.$this->url().'" target="_blank">'.$this->getBasicInfo().'</a>';
    }

    /**
    * Gets the html link of list of objects.
    */
    public function linkList() {
        return '<a href="'.$this->urlList().'">'.$this->title.'</a>';
    }

    /**
    * Gets the html basic link of an object in an admin level.
    */
    public function linkAdmin() {
        return '<a href="'.$this->urlAdmin().'">'.$this->urlAdmin().'</a>';
    }

    /**
    * Returns the values of the object.
    */
    public function valuesArray() {
        return (is_array($this->values)) ? $this->values : array();
    }

    /**
    * Gets all the values of the object.
    */
    public function getValues($attribute, $admin=false) {
        $info = $this->attributeInfo($attribute);
        if (isset($info->refObject) && (string)$info->refObject != '') {
            $refObjectName = (string)$info->refObject;
            $refObject = new $refObjectName;
            return ($admin) ? $refObject->basicInfoAdminArray() : $refObject->basicInfoArray();
        } else {
            $values = (isset($info->values)) ? (array)$info->values : array();
            $result = array();
            if (isset($values['value']) && is_array($values['value'])) {
                foreach ($values['value'] as $key=>$value) {
                    $result[] = __($value);
                }                
            }
            return $result;
        }
    }

    /**
    * Returns an array with the basic information of a list of objects using the ids as keys.
    */
    public function basicInfoArray($options=array()) {
        $options = ($this->orderBy() != "") ? array_merge(array('order'=>$this->orderBy()), $options) : $options;
        $items = $this->readList($options);
        $result = array();
        foreach ($items as $item) {
            $result[$item->id()] = $item->getBasicInfo();
        }
        return $result;
    }

    /**
    * Returns an array with the basic information of a list of objects using the ids as keys, in an admin level.
    */
    public function basicInfoAdminArray($options=array()) {
        $orderAttribute = $this->orderBy();
        $order = '';
        if ($orderAttribute!='') {
            $orderInfo = $this->attributeInfo($orderAttribute);
            $order = (is_object($orderInfo) && (string)$orderInfo->lang == 'true') ? $orderAttribute.'_'.Lang::active() : $orderAttribute;
        }
        $options = ($order != "") ? array_merge(array('order'=>$order), $options) : $options;
        $items = $this->readList($options);
        $result = array();
        foreach ($items as $item) {
            $result[$item->id()] = $item->getBasicInfoAdmin();
        }
        return $result;
    }

    /**
    * Gets the information on the search properties of the object.
    */
    public function infoSearch() {
        return (string)$this->info->info->form->search;
    }

    /**
    * Reload the object
    */
    public function infoSearchQuery() {
        return (string)$this->info->info->form->searchQuery;
    }
    
    /**
    * Gets the value of an attribute.
    */
    public function get($name) {
        $nameLang = $name.'_'.Lang::active();
        $result = (isset($this->values[$name])) ? $this->values[$name] : '';
        $result = (isset($this->values[$nameLang])) ? $this->values[$nameLang] : $result;
        return $result;
    }

    /**
    * Gets the attribute information in the XML file.
    */
    public function attributeInfo($attribute) {
        $match = $this->info->xpath("/object/attributes/attribute//name[.='".$attribute."']/..");
        return (isset($match[0])) ? $match[0] : '';
    }

    /**
    * Returns all the attribute names.
    */
    public function attributeNames() {
        $list = array($this->primary, 'ord', 'created', 'modified');
        foreach($this->info->attributes->attribute as $item){
            $list[] = (string)$item->name;
        }
        return $list;
    }
    
    /**
    * Gets the label of an attribute, it works for attributes as selects of multiple objects.
    */
    public function label($attribute, $admin=false) {
        $info = $this->attributeInfo($attribute);
        if ((string)$info->type == 'autocomplete') {
            $refObject = (string)$info->form->refObject;
            $object = new $refObject;
            $object = $object->readObject($this->get($attribute));
            return ($admin) ? $object->getBasicInfoAdmin() : $object->getBasicInfo();
         } else {
            if ((string)$info->refObject != '') {
                $refObjectName = (string)$info->refObject;
                $refObject = new $refObjectName;
                $refObject = $refObject->readObject($this->get($attribute));
                return $refObject->getBasicInfo();
            } else {
                $values = $this->getValues($attribute, $admin);
                return (isset($values[$this->get($attribute)])) ? $values[$this->get($attribute)] : '';
            }
        }
    }

    /**
    * Gets the link to the file that the attribute points.
    */
    public function getFileLink($attributeName) {
        $file = $this->getFileUrl($attributeName);
        return ($file!='') ? '<a href="'.$file.'" target="_blank" class="download">'.__('viewFile').'</a>' : '';
    }

    /**
    * Gets the url to the file that the attribute points.
    */
    public function getFileUrl($attributeName) {
        $file = STOCK_URL.$this->className.'Files/'.$this->get($attributeName);
        return (is_file(str_replace(STOCK_URL, STOCK_FILE, $file))) ? $file : '';
    }

    /**
    * Gets the base to the file that the attribute points.
    */
    public function getFile($attributeName) {
        $file = STOCK_FILE.$this->className.'Files/'.$this->get($attributeName);
        return (is_file($file)) ? $file : '';
    }
    
    /**
    * Gets the HTML image that the attribute points.
    */
    public function getImage($attributeName, $version='', $alternative='') {
        $imageUrl = $this->getImageUrl($attributeName, $version);
        if ($imageUrl!='') {
            return '<img src="'.$imageUrl.'" alt="'.$this->getBasicInfo().'"/>';
        } else {
            return $alternative;
        }
    }

    /**
    * Gets the HTML image that exists and fits to an icon.
    */
    public function getImageIcon($attributeName) {
        $imageUrl = $this->getImage($attributeName, 'thumb');
        if ($imageUrl!='') {
            return $imageUrl;
        } else {
            $imageUrl = $this->getImage($attributeName, 'small');
            if ($imageUrl!='') {
                return $imageUrl;
            } else {
                $imageUrl = $this->getImage($attributeName, 'web');
                if ($imageUrl!='') {
                    return $imageUrl;
                }
            }
        }
    }

    /**
    * Gets the url of an image that the attribute points.
    */
    public function getImageUrl($attributeName, $version='') {
        $version = ($version != '') ? '_'.strtolower($version) : '';
        $file = STOCK_FILE.$this->className.'/'.$this->get($attributeName).'/'.$this->get($attributeName).$version.'.jpg';
        if (is_file($file)) {
            return str_replace(STOCK_FILE, STOCK_URL, $file);
        }
    }

    /**
    * Reload the object.
    */
    public function orderBy() {
        return (string)$this->info->info->form->orderBy;
    }

    /**
    * Sets the id of an object.
    */
    public function setId($id) {
        $this->values[$this->primary] = $id;
    }

    /**
    * Sets a value to an attribute of an object.
    */
    public function set($name, $value='') {
        if (is_array($name)) {
            foreach ($name as $key=>$item) {
                $this->set($key,$item);
            }
        } else {
            $this->values[$name] = $value;
        }
    }

    /**
    * Checks if the object has the attribute "created".
    */
    public function hasCreated() {
        return ((string)$this->info->info->sql->created == 'true');
    }

    /**
    * Checks if the object has the attribute "modified".
    */
    public function hasModified() {
        return ((string)$this->info->info->sql->modified == 'true');
    }

    /**
    * Checks if the object has the attribute "order".
    */
    public function hasOrd() {
        return ((string)$this->info->info->sql->order == 'true');
    }

    /**
    * Checks if the object has an auto-incremented id.
    */
    public function hasIdAutoIncrement() {
        if (!is_object($this->attributeInfo($this->primary))) {
            return false;
        }
        return ((string)$this->attributeInfo($this->primary)->type == 'id-autoincrement');
    }

    /**
    * Creates an instance of the UI object and returns a function to render.
    */
    public function showUi($functionName='Public', $params=array()) {
        $render = 'render'.ucwords($functionName);
        $fileHtml = BASE_FILE.'cache/'.$this->className.'/'.$render.'_'.$this->id().'.htm';
        if (is_file($fileHtml)) {
            return file_get_contents($fileHtml);
        }
        $fileHtml = BASE_FILE.'cache/'.$this->className.'/'.$render.'.htm';
        if (is_file($fileHtml)) {
            return file_get_contents($fileHtml);
        }
        $uiObjectName = $this->className.'_Ui';
        $uiObject = new $uiObjectName($this);
        return $uiObject->$render($params);
    }
    
}
?>