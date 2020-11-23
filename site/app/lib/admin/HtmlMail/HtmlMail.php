<?php
/**
 * @class HtmlMail
 *
 * This class represents the wrapup for the emails
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class HtmlMail extends Db_Object
{

    /**
     * Load an object using its code
     */
    public static function code($code)
    {
        return (new HtmlMail)->readFirst(['where' => 'code="' . $code . '"']);
    }

    /**
     * Send an email formatted with a template
     */
    public static function send($email, $code, $values = [], $template = 'basic')
    {
        $htmlMail = HtmlMail::code($code);
        Email::send($email, $htmlMail->get('subject'), $htmlMail->showUi('Mail', ['values' => $values, 'template' => $template]), $htmlMail->get('reply_to'));
    }

}
