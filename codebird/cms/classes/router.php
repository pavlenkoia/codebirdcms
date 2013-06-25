<?php

/**
 * Класс роутера
 *
 * Осуществляет поиск и вызов соответствующего контроллера
 * в соответствующем модуле
 */

Class Router
{

    private $path = "cms/modules";

    /**
     * Метод диспетчеризации
     *
     * Вызывает соответственно действие в соответствующем контроллере
     * соответствующего модуля
     *
     * @param string $module имя модуля
     * @param string $controller имя контроллера
     * @param string $action имя действия
     */

    public function dispatch($module, $controller="controller", $action="index", $arg=null)
    {
        if(!isset($controller))
        {
            $controller="controller";
        }

        if(!isset($action))
        {
            $action="index";
        }

        $registry = Registry::__instance();

        if($registry->has("controller_".$module."_".$controller))
        {
            $_controller = $registry->__get("controller_".$module."_".$controller);
        }
        else
        {
            // Подключаем config модуля
            $config_name = $module.'_config';
            if(!$registry->has($config_name))
            {
                $config = SITE_PATH.$this->path.DS.$module.DS.'config.php';

                if (is_readable($config) == true)
                {
                    require_once ($config);
                    $class = ucfirst($module).'Config';
                    $registry->__set($config_name, new $class());
                    $registry->$config_name->initConfigXml($module);
                }
            }

            // Находим файл контроллера

            $controller_path = SITE_PATH.$this->path.DS.$module.DS."controllers";

            $file = $controller_path.DS.$controller.".php";


            // Файл доступен?

            if (is_readable($file) == false)
            {
                if($arg == 'quitcontroller') return;
                die ('404  Controller '.$controller.' Not Found');
            }

            // Подключаем файл

            require_once ($file);

            // Создаём экземпляр контроллера

            $class = ucfirst($module).'Controller_' . $controller;

            $_controller = new $class($module);

            
            $registry->__set("controller_".$module."_".$controller,$_controller);
        }

        if(!$_controller->access())
        {
            die ('403 Controller Not Access');
        }

        // Устанавливаем аргументы
        if(isset($arg))
        {
            $_controller->setArg($arg);
        }

        // Действие доступно?

        if (is_callable(array($_controller, $action)) == false)
        {
            die ('404 Action Not Found');
        }

        $_controller->setCurrent($controller, $action);

        // Выполняем действие

        $_controller->$action();
    }
}

?>