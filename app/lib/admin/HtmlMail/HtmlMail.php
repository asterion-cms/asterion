<?php
/**
* @class HtmlMail
*
* This class represents the wrapup for the emails
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class HtmlMail extends Db_Object {

    /**
    * Load an object using its code
    */
    static public function code($code) {
        return HtmlMail::readFirst(array('where'=>'code="'.$code.'"'));
    }

    /**
    * Send an email formatted with a template
    */
    static public function send($email, $code, $values=array(), $template='basic') {
        $htmlMail = HtmlMail::code($code);
        Email::send($email, $htmlMail->get('subject'), $htmlMail->showUi('Mail', array('values'=>$values, 'template'=>$template)), $htmlMail->get('replyTo'));
    }

}
?>