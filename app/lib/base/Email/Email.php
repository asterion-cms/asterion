<?php
/**
* @class Email
*
* This is a helper class to send emails
*
* @author Leano Martinet <info@asterion-cms.com>
* @package Asterion
* @version 3.0.1
*/
class Email {

    /**
    * Format headers to send an email
    */
    static public function send($mailTo, $subject, $htmlMail, $replyTo='') {
        $replyTo = ($replyTo=='') ? Params::param('email') : $replyTo;
        $headers = 'MIME-Version: 1.0'."\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
        $headers .= 'From: "'.Params::param('metainfo-titlePage').'" <'.Params::param('email').'>'."\r\n";
        $headers .= 'Reply-To: '.$replyTo.''."\r\n";
        $headers .= 'X-Mailer: PHP/'.phpversion();
        return @mail($mailTo, html_entity_decode($subject), utf8_decode($htmlMail), $headers);
    }

}
?>