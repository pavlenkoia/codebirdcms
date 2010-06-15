<?php

/**
 * Базовый класс контроллера
 */

class Args
{
    private $vars = array(); // array

    /**
     * Проверка наличия переменной
     *
     * @param srting $name имя переменной
     * @return bool true - если объект существует
     */
    public function has($name)
    {
        if (isset($this->vars[$name]))
        {
            return true;
        }
        return false;
    }


    /**
     * Overload
     *
     * Перегрузка метода __get для получения переменной по имени
     * в виде $registry-><name>
     *
     * @param string $name имя переменной
     * @return object
     */

    public function __get($name)
    {
        if ($this->has($name))
        {
            return $this->vars[$name];
        }
        else
        {
            return null;
        }
    }

    /**
     * Overload
     *
     * Перегрузка метода __set для установки переменной
     * в виде $registry-><name>=<var>
     *
     * @param string $name имя переменной
     * @param object|string $var значение переменной
     * @return boolean
     */
    public function __set($name,$var)
    {
        $this->vars[$name] = $var;

        return true;
    }

    /**
     * Удаление переменной
     *
     * Удаляет переменную из регистра
     *
     * @param string $name имя переменной
     */

    public function remove($name)
    {
        unset($this->vars[$name]);

    }

    public function toArray()
    {
        return $this->vars;
    }
}

abstract class Controller_Base
{

    protected $registry;

    protected $module;

    protected $controller;

    protected $action;

    protected $args;

    protected $config;
    
    private $path = "cms/modules";

    private $template;

    public function __construct($module) {
        
        $this->registry = Registry::__instance();
        $this->module = $module;
        $this->args = new Args();
        $this->config = Config::__($this->module);
    }

    /**
     * Действие контроллера по умолчанию
     */

    abstract function index();

    /**
     * Получение объекта модели
     *
     * @param string $model имя класса модели
     * @return Model_'model' экземпляр класса объекта
     */

    protected function getData($model=null)
    {
        if(!$model)
        {
            $model = $this->module;
        }

        $path = SITE_PATH.$this->path.DS.$this->module.DS."models".DS.strtolower($model).'.php';

        if (file_exists($path) == false)
        {
            throw new Exception('Model not found in '. $path);
            return false;
        }

        require_once($path);

        $class = ucfirst($this->module).'Model_'.ucfirst($model);

        $data = new $class($this->module);

        return $data;
    }

    /**
     * Устанавливает глобальную переменную mod_content
     *
     * @param object $mod_content значение глобальной переменной mod_content
     */
    protected function setContent($mod_content)
    {
        $this->registry->mod_content = $mod_content;
    }

    /**
     * Задает аргументы контроллера
     *
     * @param string $arg строка вида arg1=value1&arg2=value2&arg3=value3[...
     */

    public function setArg($arg, $dispatch_str=null)
    {
        $this->args = new Args();

        if(is_array($arg))
        {
            $output = $arg;
        }
        else
        {
            parse_str($arg, $output);
        }

        foreach($output as $name=>$var)
        {
            $this->args->__set($name, $var);
        }
    }

    /**
     * Установка текущих имен контроллера и действия
     *
     * @param string $controller имя контроллера
     * @param string $action имя действия
     */
    public function setCurrent($controller, $action)
    {
        $this->controller = $controller;
        $this->action = $action;
    }

    public function createTemplate()
    {
        $view = $this->controller.'.'.$this->action;

        $view_ext = $this->module.'.'.$this->controller.'.'.$this->action;
        if($this->args->has('view'))
        {
            $view_ext = $this->args->view;
        }
        
        $this->template = new Template($this->module, $view, $view_ext);

        $this->template->args = $this->args;

        $this->template->config = $this->config;

        return $this->template;
    }

    public function setError($error_message)
    {
        if(isset($this->template))
        {
            $this->template->error_message = $error_message;
        }
    }

    public function setMessage($info_message)
    {
        if(isset($this->template))
        {
            $this->template->info_message = $info_message;
        }
    }

    /**
     * Возвращает доступность контроллера
     *
     * @return boolean true если контроллер доступен
     */
    public function access()
    {
        return true;
    }

    /**
     * Проверка авторизации
     *
     * @return boolean true если есть авторизация
     */
    protected function login()
    {
         return val("security.login");
    }
    
}
?>
