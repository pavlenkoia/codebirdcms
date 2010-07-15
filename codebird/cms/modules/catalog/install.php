<?php
/**
 *
 */
class CatalogInstall extends Install_Base
{
    public $title = "Разделы";

    public $dirs = array('catalog/cache','catalog/upload');

        public $sql =
"CREATE TABLE IF NOT EXISTS `catalog_section` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) default NULL,
  `title` varchar(500) collate utf8_unicode_ci NOT NULL,
  `position` int(11) default NULL,
  `alias` varchar(250) collate utf8_unicode_ci default NULL,
  `position_table` varchar(250) collate utf8_unicode_ci default NULL,
  `section_table` varchar(250) collate utf8_unicode_ci default NULL,
  `children_tpl` text collate utf8_unicode_ci,
  `leaf` smallint(6) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `section_news` (
  `id` int(11) NOT NULL,
  `page_size` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `position_news` (
  `id` int(11) NOT NULL auto_increment,
  `section_id` int(11) NOT NULL,
  `title` varchar(500) collate utf8_unicode_ci default NULL,
  `datestamp` int(11) default NULL,
  `description` text collate utf8_unicode_ci,
  `content` text collate utf8_unicode_ci,
  `img` varchar(200) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `section_form_feedback` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `email` varchar(250) collate utf8_unicode_ci default NULL,
  `efrom` varchar(500) collate utf8_unicode_ci default NULL,
  `efromname` varchar(500) collate utf8_unicode_ci default NULL,
  `esubject` varchar(500) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `section_page` (
  `id` int(11) NOT NULL,
  `content` text collate utf8_unicode_ci,
  `visible` smallint(6) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
";

            public $config =
'<?xml version="1.0" encoding="UTF-8"?>
<config>
    <params>
        <param type="array">
            <description>Таблицы разделов</description>
            <name>tables_section</name>
            <items>
                <item name="section_news" type="array">
                    <value name="title">Новости</value>
                    <value name="table">section_news</value>
                </item>
                <item name="section_form_feedback" type="array">
                    <value name="title">Форма обратной связи</value>
                    <value name="table">section_form_feedback</value>
                </item>
                <item name="section_page" type="array">
                    <value name="title">Страница</value>
                    <value name="table">section_page</value>
                </item>
            </items>
        </param>
        <param type="array">
            <description>Таблицы позиций</description>
            <name>tables</name>
            <items>
                <item name="position_news" type="array">
                    <value name="title">Новости</value>
                    <value name="table">position_news</value>
                    <value name="order">ORDER BY datestamp DESC</value>
                </item>
            </items>
        </param>
        <param type="array">
            <name>section_news</name>
            <items>
                <item name="page_size" type="array">
                    <value name="field">page_size</value>
                    <value name="title">Количество новостей на странице</value>
                    <value name="type">int</value>
                </item>
            </items>
        </param>
        <param type="array">
            <name>position_news</name>
            <items>
                <item name="datestamp" type="array">
                    <value name="field">datestamp</value>
                    <value name="title">Дата</value>
                    <value name="type">date</value>
                </item>
                <item name="img" type="array">
                    <value name="field">img</value>
                    <value name="title">Картинка</value>
                    <value name="type">image</value>
                    <value name="mode">edit</value>
                </item>
                <item name="title" type="array">
                    <value name="field">title</value>
                    <value name="title">Заголовок новости</value>
                    <value name="type">text</value>
                </item>
                <item name="description" type="array">
                    <value name="field">description</value>
                    <value name="title">Анонс</value>
                    <value name="type">memo</value>
                    <value name="mode">edit</value>
                </item>
                <item name="content" type="array">
                    <value name="field">content</value>
                    <value name="title">Содержание</value>
                    <value name="type">richtext</value>
                    <value name="mode">edit</value>
                </item>
            </items>
        </param>
        <param type="array">
            <name>section_form_feedback</name>
            <items>
                <item name="email" type="array">
                    <value name="field">email</value>
                    <value name="title">Email отправки формы</value>
                    <value name="type">text</value>
                </item>
                <item name="from" type="array">
                    <value name="field">efrom</value>
                    <value name="title">Email отправителя</value>
                    <value name="type">text</value>
                </item>
                <item name="fromname" type="array">
                    <value name="field">efromname</value>
                    <value name="title">Имя отправителя</value>
                    <value name="type">text</value>
                </item>
                <item name="subject" type="array">
                    <value name="field">esubject</value>
                    <value name="title">Заголовок письма отправителя</value>
                    <value name="type">text</value>
                </item>
            </items>
        </param>
        <param type="array">
            <name>section_page</name>
            <items>
                <item name="contenet" type="array">
                    <value name="field">content</value>
                    <value name="title">Содержание</value>
                    <value name="type">richtext</value>
                </item>
                <item name="visible" type="array">
                    <value name="field">visible</value>
                    <value name="title">Доступна</value>
                    <value name="type">check</value>
                </item>
            </items>
        </param>
    </params>
</config>';

}
?>
