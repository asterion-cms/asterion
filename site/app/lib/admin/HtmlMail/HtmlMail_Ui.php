<?php
/**
 * @class HtmlMailUi
 *
 * This class manages the UI for the HtmlMail objects.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class HtmlMail_Ui extends Ui
{

    /**
     * Render an email using values in the form #VALUE and a template code of the HtmlMailTemplate object
     * Ex: If the template has the #NAME and #LASTNAME fields, we can fill them using:
     * renderMail(['values'=>['NAME'=>'Ray', 'LASTNAME'=>'Bradbury']])
     * Ex: If we also need to use an specific template, we use:
     * renderMail(['values'=>['NAME'=>'Ray', 'LASTNAME'=>'Bradbury'], 'template'=>'welcomeToWebsite'])
     */
    public function renderMail($options = [])
    {
        $values = (isset($options['values']) && is_array($options['values'])) ? $options['values'] : [];
        if (isset($options['template'])) {
            $template = HtmlMailTemplate::code($options['template']);
        } else {
            $template = HtmlMailTemplate::code('basic');
        }
        $content = $this->object->get('mail');
        foreach ($values as $key => $value) {
            $content = str_replace('#' . $key, $value, $content);
        }
        return $template->showUi('Template', ['values' => ['CONTENT' => $content]]);
    }

}
