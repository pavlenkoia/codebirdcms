<?php
/**
 *
 */
class CatalogInstall extends Install_Base
{
    public $required = true;

    public $title = "Разделы";

    public $dirs = array('catalog/cache','catalog/upload','catalog/modcache');

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

CREATE TABLE IF NOT EXISTS `position_forms` (
  `id` int(11) NOT NULL auto_increment,
  `section_id` int(11) NOT NULL,
  `name` varchar(250) collate utf8_unicode_ci default NULL,
  `position` int(11) default NULL,
  `type_id` varchar(15) collate utf8_unicode_ci default NULL,
  `valid_empty` smallint(6) default '0',
  `valid_email` smallint(6) default '0',
  `select_options` text collate utf8_unicode_ci,
  `nameid` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `section_forms` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `email` varchar(250) collate utf8_unicode_ci default NULL,
  `efrom` varchar(500) collate utf8_unicode_ci default NULL,
  `efromname` varchar(500) collate utf8_unicode_ci default NULL,
  `esubject` varchar(500) collate utf8_unicode_ci default NULL,
  `captcha` smallint(6) default '0',
  `header_mail` text collate utf8_unicode_ci,
  `title_form` varchar(250) collate utf8_unicode_ci default NULL,
  `success_message` text collate utf8_unicode_ci,
  `html` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `forms_field_type` (
  `id` varchar(15) collate utf8_unicode_ci NOT NULL,
  `name` varchar(250) collate utf8_unicode_ci NOT NULL,
  `position` smallint(6) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `forms_field_type` (`id`, `name`, `position`) VALUES
('text', 'Текстовая строка', 1),
('memo', 'Многострочный текст', 2),
('select', 'Выпадающий список', 3);
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
                <item name="section_forms" type="array">
                    <value name="title">Форма</value>
                    <value name="table">section_forms</value>
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
                <item name="position_forms" type="array">
                    <value name="title">Поля формы</value>
                    <value name="table">position_forms</value>
                    <value name="sql">
                        select p1.*, p2.name as type_name
                        from position_forms p1
                        left outer join forms_field_type p2 on p1.type_id=p2.id
                        where section_id=:section_id
                        ORDER BY position
                    </value>
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
            <name>position_forms</name>
            <items>
                <item name="position" type="array">
                    <value name="field">position</value>
                    <value name="title">Порядок</value>
                    <value name="type">int</value>
                </item>
                <item name="name" type="array">
                    <value name="field">name</value>
                    <value name="title">Название поля</value>
                    <value name="type">text</value>
                </item>
                <item name="type_id" type="array">
                    <value name="field">type_id</value>
                    <value name="display">type_name</value>
                    <value name="title">Тип поля</value>
                    <value name="type">select</value>
                    <value name="select">SELECT id, name AS display FROM forms_field_type ORDER BY position ASC</value>
                </item>
                <item name="select_options" type="array">
                    <value name="field">select_options</value>
                    <value name="title">Значения для выпадающего списка</value>
                    <value name="type">memo</value>
                    <value name="mode">edit</value>
                </item>
                <item name="valid_empty" type="array">
                    <value name="field">valid_empty</value>
                    <value name="title">Обязательно для заполнения</value>
                    <value name="type">check</value>
                    <value name="mode">edit</value>
                </item>
                <item name="valid_email" type="array">
                    <value name="field">valid_email</value>
                    <value name="title">Проверка Email</value>
                    <value name="type">check</value>
                    <value name="mode">edit</value>
                </item>
                <item name="id" type="array">
                    <value name="field">id</value>
                    <value name="title">ID</value>
                    <value name="type">text</value>
                    <value name="mode">browse</value>
                </item>
                <item name="nameid" type="array">
                    <value name="field">nameid</value>
                    <value name="title">Имя поля</value>
                    <value name="type">text</value>
                </item>
            </items>
        </param>
        <param type="array">
            <name>section_forms</name>
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
                    <value name="title">Заголовок письма</value>
                    <value name="type">text</value>
                </item>
                <item name="header_mail" type="array">
                    <value name="field">header_mail</value>
                    <value name="title">Вступительный текст письма</value>
                    <value name="type">memo</value>
                </item>
                <item name="captcha" type="array">
                    <value name="field">captcha</value>
                    <value name="title">Показывать CAPTCHA формы</value>
                    <value name="type">check</value>
                </item>
                <!--item name="title_form" type="array">
                    <value name="field">title_form</value>
                    <value name="title">Заголовок формы</value>
                    <value name="type">text</value>
                </item-->
                <item name="success_message" type="array">
                    <value name="field">success_message</value>
                    <value name="title">Сообщение после успешной отправки</value>
                    <value name="type">memo</value>
                </item>
                <!--item name="html" type="array">
                    <value name="field">html</value>
                    <value name="title">html код для вставки на страницу</value>
                    <value name="type">memo</value-->
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

    public function exec_sql()
    {
        $res = '';
        $table = new Table("catalog_section");
        $section = $table->getEntityAlias("forms_section");
        if(!$section)
        {
            $section = $table->getEntity();
            $section->title = 'Формы';
            $section->alias = 'forms_section';
            $section->children_tpl =
'<tpl>
  <section>section_forms</section>
  <position>position_forms</position>
  <leaf>1</leaf>
</tpl>';
            $table->save($section);
            if($table->errorInfo)
            {
                return $table->errorInfo;
            }
        }

        $rows = $table->select('select * from forms_field_type');

        if(count($rows) == 0)
        {
            $sql = "INSERT INTO `forms_field_type` (`id`, `name`, `position`) VALUES
('text', 'Текстовая строка', 1),
('memo', 'Многострочный текст', 2),
('select', 'Выпадающий список', 3);";
            $table->execute($sql);
        }

        return $res;
    }

}
?>
