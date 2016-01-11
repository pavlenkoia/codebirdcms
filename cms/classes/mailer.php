<?php
/* 
 * Класс для работы с почтой
 *
 * расширяет базовый класс PHPMailer
 */

require_once SITE_PATH.DS.'cms'.DS.'lib'.DS.'phpmailer'.DS.'class.phpmailer.php';

class Mailer extends PHPMailer
{
//    /**
//     * приоритет почты поумолчанию: 1 – высоко, 3 – нормально, 5 – низко
//     * @var integer
//     */
//    var $priority = 3;
//  
//    /**
//     * имя получателя
//     * @var string
//     */
//    var $to_name;
//
//    /**
//     * адрес получателя
//     * @var string
//     */
//    var $to_email;
//
//    /**
//     *  адрес, с которого посылается письмо
//     * @var string
//     */
//    var $From = null;
//
//    /**
//     * имя отправителя
//     * @var string
//     */
//    var $FromName = null;
//
//    /**
//     *
//     * @var string
//     */
//    var $Sender = null;


    public function Mailer()
    {
        $this->Priority = 3;
        $this->CharSet = "utf-8";
    }
}


?>
