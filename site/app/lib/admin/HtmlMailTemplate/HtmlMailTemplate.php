<?php
/**
 * @class HtmlMailTemplate
 *
 * This class represents the template for the emails
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class HtmlMailTemplate extends Db_Object
{

    /**
     * Load an object using its code
     */
    public static function code($code)
    {
        return (new HtmlMailTemplate)->readFirst(['where' => 'code="' . $code . '"']);
    }

}
