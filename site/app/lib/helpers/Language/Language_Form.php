<?php
/**
 * @class LanguageController
 *
 * This class manages the forms for the Language objects.
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Language_Form extends Form
{

    /**
     * Render the set of available ISO languages as options.
     */
    public static function createFormFieldsIso()
    {
        $languages = Language::isoList();
        ksort($languages);
        $content = '';
        foreach ($languages as $key => $language) {
            $content .= '<div class="iso_language">
                            ' . FormField_Checkbox::create(['name' => 'language['.$key.']', 'id' => 'language_' . $key]) . '
                            <label for="language_' . $key . '" class="iso_language_info">
                                <p><strong>' . $key . '</strong> ' . $language['name'] . '</p>
                                <p><span>' . $language['local_names'] . '</span></p>
                            </label>
                        </div>';
        }
        return '<div class="iso_languages same_height">' . $content . '</div>';
    }

    /**
     * Render the set of available ISO languages as options.
     */
    public static function createFormIso()
    {
        return Form::createForm(Language_Form::createFormFieldsIso(), ['submit' => __('save'), 'class'=>'form_admin_simple']);
    }

}
