<?php
/**
* @class HtmlMailTemplateUi
*
* This class manages the UI for the HtmlMailTemplate objects.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class HtmlMailTemplate_Ui extends Ui{

    /**
    * Render an email template using values in the form #VALUE
    * Ex: Usually the template just accepts the #CONTENT variable, so we can fill it using:
    * renderTemplate(array('values'=>array('CONTENT'=>'The content of my email')))
    */
    public function renderTemplate($options=array()) {
        $values = (isset($options['values']) && is_array($options['values'])) ? $options['values'] : array();
        $template = $this->object->get('template');
        foreach ($values as $key=>$value) {
            $template = str_replace('#'.$key, $value, $template);
        }
        return $template;
    }

}
?>