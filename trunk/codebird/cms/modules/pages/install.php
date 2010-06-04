<?php
/**
 *
 */

class PagesInstall extends Install_Base
{
    public $required = true;

    public $title = "Страницы";

    public $sql = "
CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title2` varchar(500) collate utf8_unicode_ci default NULL,
  `content` text COLLATE utf8_unicode_ci,
  `position` int(11) NOT NULL,
  `visible` smallint(6) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `alias` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `template` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `plugins` text COLLATE utf8_unicode_ci,
  `mainpage` smallint(6) NOT NULL DEFAULT '0',
  `meta_keywords` text COLLATE utf8_unicode_ci,
  `meta_description` text COLLATE utf8_unicode_ci,
  `announcement` text COLLATE utf8_unicode_ci,
  `img_src` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `head_title` varchar(500) collate utf8_unicode_ci default NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
INSERT INTO `pages` (`title`, `content`, `position`, `visible`, `alias`, `template`, `mainpage`)
  select 'Главная','',1,0,'index','main_template.php',1 from `pages`
  where NOT EXISTS (SELECT * FROM `pages` WHERE `mainpage`=1) limit 1 ;";

    public function exec_sql()
    {
        $res = "";
        $table = new Table("pages");
        $rows = $table->select("select count(*) as count from pages");
        if($rows[0]['count'] == 0)
        {
            $sql = "INSERT INTO `pages` (`title`, `content`, `position`, `visible`, `alias`, `template`, `mainpage`)
  values('Главная','',1,0,'index','main_template.php',1) ";
            $table->execute($sql);
            if($table->errorInfo)
            {
                return $table->errorInfo;
            }
        }
        return $res;
    }

    public $dirs = array('pages');

    public $config =
    '<?xml version="1.0" encoding="UTF-8"?>
<config>
    <params>
        <param type="array">
            <description>Плагины</description>
            <name>plugins</name>
            <items>
            </items>
        </param>
        <param type="array">
            <description>Плагины</description>
            <name>plugins_cm</name>
            <items>
            </items>
        </param>
        <param type="array">
            <description>Шаблоны страниц</description>
            <name>templates</name>
            <items>
                <item name="main_template.php">Главная страница</item>
		<item name="page_template.php">Шаблон страницы</item>
            </items>
        </param>
    </params>
</config>';
}

?>
