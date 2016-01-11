<?php
/**
 * Класс доступа к объекту инсталятора модуля
 */

class Install
{
    public static function getInstall($module)
    {
        $install = SITE_PATH.'cms'.DS.'modules'.DS.$module.DS.'install.php';

        if (is_readable($install) == true)
        {
            require_once ($install);
            $class = ucfirst($module).'Install';
            $install = new $class($module);
            return $install;
        }
        return null;
    }
}

?>
