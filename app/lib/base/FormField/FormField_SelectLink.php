<?php
/**
* @class FormFieldSelectLink
*
* This is a helper class to generate a select form field filled with internal links.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class FormField_SelectLink extends FormField_DefaultSelect {

    /**
    * The constructor of the object.
    */
    public function __construct($options) {
        $options['value'] = FormField_SelectLink::valuesInternal();
        parent::__construct($options);
    }

    /**
    * Render the element with an static function.
    */
    static public function create($options) {
        return FormField_DefaultSelect::create($options);
    }

    /**
    * Create an array with all the accesible internal links.
    */
    static public function valuesInternal() {
        $values = array('homePage'=>__('homePage'));
        $objectNames = File::scanDirectoryObjectsBase();
        foreach ($objectNames as $objectName) {
            $object = new $objectName();
            $objectTitle = (string)$object->info->info->form->title;
            $viewPublic = (string)$object->info->info->form->viewPublic;
            $publicUrlList = (string)$object->info->info->form->publicUrlList;
            if ($viewPublic == 'true') {
                $values[$objectName] = array('label'=>__($objectTitle), 'items'=>array());
                $values[$objectName]['items']['list_'.$objectName] = '&raquo; '.__('listItems').' - '.__($objectTitle);
                if ($publicUrlList!='') {
                    $values[$objectName]['items']['public_'.$objectName] = '&raquo; '.__('mainPage').' - '.__($objectTitle);
                }
                //Public items
                $items = $object->readList();
                foreach ($items as $item) {
                    $values[$objectName]['items']['item_'.$objectName.'_'.$item->id()] = $item->getBasicInfoAdmin();
                }
            }
        }
        $values['external'] = '&raquo; '.__('externalLinkLabel');
        return $values;
    }

}
?>