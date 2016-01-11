<?php
/**
 *
 */

class InfoblockInstall extends Install_Base
{
    public $title = "Инфоблоки";

    public $sql = '
CREATE TABLE IF NOT EXISTS `infoblock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `html` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
';

    public $dirs = array('infoblock');
}

?>
