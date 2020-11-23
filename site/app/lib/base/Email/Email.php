<?php
/**
 * @class Email
 *
 * This is a helper class to send emails
 *
 * @author Leano Martinet <info@asterion-cms.com>
 * @package Asterion
 * @version 4.0.0
 */
class Email
{

    /**
    * Format headers to send an email
    */
    static public function send($mailTo, $subject, $htmlMail, $replyToEmail='', $replyToName='') {
        $replyToEmail = ($replyToEmail!='') ? $replyToEmail : Params::param('email');
        $replyToName = ($replyToName!='') ? $replyToName : Params::param('title_page');
        require_once ASTERION_APP_FILE."helpers/mailer/PHPMailer.php";
        require_once ASTERION_APP_FILE."helpers/mailer/SMTP.php";
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->CharSet = 'utf-8';
        $mail->Host = ASTERION_MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = ASTERION_MAIL_USERNAME;
        $mail->Password = ASTERION_MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom(Params::param('email'), Params::param('title_page'));
        $mail->addReplyTo($replyToEmail, $replyToName);
        $mail->addAddress($mailTo);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlMail;
        $mail->AltBody = strip_tags($htmlMail);
        @$mail->send();
    }

}
