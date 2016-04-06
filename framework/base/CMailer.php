<?php
/**
* CMailer class file
* 
* @author Jitse van Ameijde <djitsz@yahoo.com>
* 
*/

defined('ALL_SYSTEMS_GO') or die;
/**
* CMailer provides functionality for sending emails
* 
* 
*/
    class CMailer {
        
        /**
        * sendEmail - sends an email with the specified settings to an email address
        * 
        * @param string $from
        * @param string $replyTo
        * @param string $to
        * @param string $subject
        * @param string $html
        * @param string $text
        * @param array $extraHeaders
        */
        static function sendEmail($from,$replyTo,$to,$subject,$html,$text) {
            $settings = isset(CWebApplication::getInstance()->config['mailer'][$_SERVER['SERVER_NAME']]) ? CWebApplication::getInstance()->config['mailer'][$_SERVER['SERVER_NAME']] : CWebApplication::getInstance()->config['mailer']['default'];
            require_once(CWebApplication::getInstance()->frameworkRoot . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'Swift' . DIRECTORY_SEPARATOR . 'swift_required.php');
            $xhtml = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8" />';
            $xhtml .= '<style type="text/css" >body { color: #555; } table {border: none; max-width: 500px;} th,td { text-align: left; padding:8px; } .bold {font-weight:bold;} .light {color: #999;}</style>';
            $xhtml .= '</head><body>' . $html . '</body></html>';
            $transport = Swift_SmtpTransport::newInstance($settings['host'],$settings['port'])->setUsername($settings['username'])->setPassword($settings['password']);
            $mailer = Swift_Mailer::newInstance($transport);
            $message = Swift_Message::newInstance($subject)->setFrom($from)->setTo($to)->setBody($text,'text/plain')->addPart($xhtml,'text/html');
            $errors = array();
            if(!$mailer->send($message,$errors)) {
                throw new Exception('Error sending mail from: ' . $from . ' to: ' . $to . ' subject: ' . $subject);
            }
        }
        
        /**
        * sendEmailCampaign - sends out an email campaign to the provided contact list
        * 
        * @param string $from
        * @param string $replyTo
        * @param string $subject
        * @param string $html
        * @param string $text
        * @param CModelCollection $contacts
        * @return int - the number of emails sent successfully
        */
        static function sendEmailCampaign($from,$replyTo,$subject,$html,$text,$contacts) {
            $settings = isset(CWebApplication::getInstance()->config['mailer'][$_SERVER['SERVER_NAME']]) ? CWebApplication::getInstance()->config['mailer'][$_SERVER['SERVER_NAME']] : CWebApplication::getInstance()->config['mailer']['default'];
            require_once(CWebApplication::getInstance()->frameworkRoot . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'Swift' . DIRECTORY_SEPARATOR . 'swift_required.php');
            $transport = Swift_SmtpTransport::newInstance($settings['host'],25)->setUsername($settings['username'])->setPassword($settings['password']);
            $mailer = Swift_Mailer::newInstance($transport);
            $numSent = 0;
            foreach($contacts as $contact) {
                $htmlContent = preg_replace_callback('/\$([a-zA-Z0-9-_]+)/',function($matches) use($contact){
                        if($contact->hasProperty($matches[1])) return $contact->$matches[1];
                        return $matches[0];
                    },$html);
                $textContent = preg_replace_callback('/\$([a-zA-Z0-9-_]+)/',function($matches) use($contact){
                        if($contact->hasProperty($matches[1])) return $contact->$matches[1];
                        return $matches[0];
                    },$text);
                $subjectLine = preg_replace_callback('/\$([a-zA-Z0-9-_]+)/',function($matches) use($contact){
                        if($contact->hasProperty($matches[1])) return $contact->$matches[1];
                        return $matches[0];
                    },$subject);
                if(Swift_Validate::email($contact->email)) {
                    $message = Swift_Message::newInstance($subjectLine)->setFrom($from)->setReplyTo($replyTo)->setTo($contact->email)->setBody($textContent,'text/plain')->addPart($htmlContent,'text/html');
                    $errors = array();
                    if(!$mailer->send($message,$errors)) {
                        throw new Exception('Error sending mail from: ' . $from . ' to: ' . $to . ' subject: ' . $subject);
                    }
                    else $numSent++;
                }
            }
            return $numSent;
        }
    }  
?>