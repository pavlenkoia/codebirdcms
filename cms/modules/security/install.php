<?php
/**
 *
 */

class SecurityInstall extends Install_Base
{
    public $title = "Безопасность";

    public $required = true;

    public $service = true;

    public $config =
'<?xml version="1.0" encoding="UTF-8"?>
<config>
    <params>
        <param type="array">
            <description>Пользователи</description>
            <name>users</name>
            <items>
                <item name="admin" type="array">
                    <value name="name">root</value>
                    <value name="password">397</value>
                </item>
            </items>
        </param>
    </params>
</config>';


    public $sql =
"
CREATE TABLE IF NOT EXISTS `security_role` (
  `id` int(11) NOT NULL,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `security_user` (
  `id` int(11) NOT NULL auto_increment,
  `disabled` smallint(6) default '0',
  `name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `password` varchar(250) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `login` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

CREATE TABLE IF NOT EXISTS `security_user_role` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `security_user_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session` varchar(250) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `session` (`session`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
"
    ;

    public function exec_sql()
    {
        $res = null;

        $table = new Table("security_role");

        $table->execute("delete from security_role");

        if($table->errorInfo) return $table->errorInfo;

        $table->execute("INSERT INTO `security_role` (`id`, `name`) VALUES
            (1, 'admin'),
            (2, 'user');");

        if($table->errorInfo) return $table->errorInfo;

        return $res;
    }

}

?>
