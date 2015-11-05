<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 05.11.2015
 * Time: 14:26
 */

namespace app\components;


use mpf\base\LogAwareObject;

/**
 * Class SwiftMailer
 * To use this "swiftmailer/swiftmailer" : "*" must be added to composer requirements
 * @package app\components
 */
class SwiftMailer extends LogAwareObject {

    public $smtpHost = '127.0.0.1';
    public $smtpPort = 25;
    public $smtpUser;
    public $smtpPassword;
    public $smtpSecure = true;

    /**
     * @var SwiftMailer
     */
    protected static $inst;

    /**
     * @return SwiftMailer
     */
    public static function get() {
        if (self::$inst) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    protected function init($config = array()) {
        $transport = new \Swift_SmtpTransport($this->smtpHost, $this->smtpPort, $this->smtpSecure);
        $transport->setUsername($this->smtpUser);
        $transport->setPassword($this->smtpPassword);
        $this->mailer = new \Swift_Mailer($transport);
        return parent::init();
    }

    /**
     * @param $to
     * @param $from
     * @param $subject
     * @param $message
     * @param $attachaments
     * @param $html
     * @return int
     */
    public function send($to, $from, $subject, $message, $attachaments, $html) {
        $mail = new \Swift_Message();
        $mail->setSubject($subject)
            ->setFrom($from['email'], $from['name'])
            ->setReplyTo($from['reply-to']['email'], $from['reply-to']['name'])
            ->setBody($message, $html ? 'text/html' : 'text/plain');
        if (is_array($to)){
            foreach ($to as $add=>$name){
                if (is_int($add)){
                    $mail->setTo($name);
                } else {
                    $mail->setTo([$add=>$name]);
                }
            }
        }
        foreach ($attachaments as $attachment) {
            $mail->attach(\Swift_Attachment::fromPath($attachment));
        }
        return $this->mailer->send($mail);
    }

}