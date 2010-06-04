<?php
/**
 *
 */

class ModmanagerInstall extends Install_Base
{
    public $title = "Управление модулями";

    public $required = true;

    public $service = true;

    public $config =
'<?xml version="1.0" encoding="UTF-8"?>
<config>
    <params>
        <param type="array">
            <description>Установленные модули</description>
            <name>modules</name>
            <items></items>
        </param>
        <param type="array">
            <description>Установленные служебные модули</description>
            <name>service_modules</name>
            <items></items>
        </param>
    </params>
</config>';
}

?>