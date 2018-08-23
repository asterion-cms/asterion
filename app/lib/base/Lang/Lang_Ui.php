<?php
/**
* @class LangUi
*
* This class manages the UI for the Lang objects.
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Lang_Ui extends Ui{

    /**
    * Render the set of available languages.
    */
    static public function showLangs($simple=false) {
        $langActive = Lang::active();
        $langs = Lang::langLabels();
        if (count($langs) > 1) {
            $html = '';
            foreach ($langs as $lang) {
                $html .= '<div class="lang lang_'.$lang['idLang'].'">';
                $name = ($simple) ? $lang['idLang'] : $lang['name'];
                if ($lang['idLang'] == $langActive) {
                    $html .= '<span title="'.$lang['name'].'">'.$name.'</span> ';
                } else {
                    $linkLang = Url::urlLang($lang['idLang']);
                    $html .= '<a href="'.$linkLang.'" title="'.$lang['name'].'">'.$name.'</a> ';
                }
                $html .= '</div>';
            }
            return '<div class="langs">'.$html.'</div>';
        }
    }

    /**
    * Render the set of available languages in a simple way.
    */
    static public function showLangsSimple() {
        return Lang::showLangs(true);
    }

}
?>