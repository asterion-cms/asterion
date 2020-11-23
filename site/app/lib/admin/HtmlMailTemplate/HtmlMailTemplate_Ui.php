<?php
/**
 * @class HtmlMailTemplateUi
 *
 * This class manages the UI for the HtmlMailTemplate objects.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class HtmlMailTemplate_Ui extends Ui
{

    /**
     * Render an email template using values in the form #VALUE
     * Ex: Usually the template just accepts the #CONTENT variable, so we can fill it using:
     * renderTemplate(['values'=>['CONTENT'=>'The content of my email']])
     */
    public function renderTemplate($options = [])
    {
        $values = (isset($options['values']) && is_array($options['values'])) ? $options['values'] : [];
        $template = $this->object->get('template');
        foreach ($values as $key => $value) {
            $template = str_replace('#' . $key, $value, $template);
        }
        return $template;
    }

}
