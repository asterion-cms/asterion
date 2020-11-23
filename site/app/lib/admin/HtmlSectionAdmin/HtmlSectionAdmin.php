<?php
/**
 * @class HtmlSectionAdmin
 *
 * This class represents a simple HTML section for the administration area
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class HtmlSectionAdmin extends Db_Object
{

    /**
     * Load an object using its code
     */
    public static function code($code)
    {
        return (new HtmlSectionAdmin)->readFirst(['where' => 'code="' . $code . '"']);
    }

    /**
     * Show directly the content of a section just using its code
     */
    public static function show($code)
    {
        $html = HtmlSectionAdmin::code($code);
        return $html->showUi();
    }

}
