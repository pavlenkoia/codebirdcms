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
";

            public $config =
'<?xml version="1.0" encoding="UTF-8"?>
<config>
    <params>
        <param type="array">
            <description>Таблицы разделов</description>
            <name>tables_section</name>
            <items>

            </items>
        </param>
        <param type="array">
            <description>Таблицы позиций</description>
            <name>tables</name>
            <items>
                
            </items>
        </param>
    </params>
</config>';

}
?>
