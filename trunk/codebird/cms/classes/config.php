<?php
/*
 * Класс доступа к объекту настроек модуля
 */

class Config
{
    /**
     * Возвращает config (настройки) модуля если он загружен
     * @param string $module имя модуля
     * @return object config модуля
     */
    public static function __($module)
    {
        return Registry::__instance()->__get($module."_config");
    }

    /**
     * Возвращает config (настройки) модуля даже если он не загружен
     * @param string $module имя модуля
     * @return object config модуля
     */
    public static function getConfig($module)
    {
        $config = SITE_PATH.'cms'.DS.'modules'.DS.$module.DS.'config.php';

        if (is_readable($config) == true)
        {
            require_once ($config);
            $class = ucfirst($module).'Config';
            $config = new $class();
            $config->initConfigXml($module);
            return $config;
        }
        return null;
    }
}

?>
