<?php

/**
 * Класс шаблонизации представления
 *
 * Служит для вызова определенного представления view в соответствующем модуле,
 * задания и генерации для него переменных
 */

Class Template {

    /*
     * @the registry
     * @access private
     */
    private $registry;

    private $path = "cms/modules";

    private $module;

    private $view = "view";

    private $view_ext;

    /*
     * @Variables array
     * @access private
     */
    private $vars = array();

    /**
     * Конструктор
     * 
     * @param string $module имя модуля
     * @param string $view имя представления
     * * @param string $view имя внешнего представления
     */
    function __construct($module, $view = null, $view_ext = null)
    {
        $this->registry = Registry::__instance();
        $this->module = $module;
        $this->view = $view;
        $this->view_ext = $view_ext;
    }


    /**
     * Overlaod
     *
     * Установка переменной шаблона
     *
     * @set undefined vars
     *
     * @param string $index
     *
     * @param mixed $value
     *
     * @return void
     *
     */
    public function __set($varname, $value) {
        $this->vars[$varname] = $value;
    }

    /**
     *
     * Удаление переменных
     *
     * @param string $varname
     * @return bool
     */
    function remove($varname) {

        unset($this->vars[$varname]);

        return true;

    }

    /**
     * Выводит шаблон и определяет переменные
     *
     * @param string $view  имя представления, по умолчания 'view'
     * @return void
     */
    public function render($view = null)
    {
        if(!$view)
        {
            $view = $this->view;
        }

        $file = SITE_PATH.$this->path.DS.$this->module.DS."views".DS.$view . '.php';

        if($this->view_ext)
        {
            $file_ext = SITE_PATH.'html'.DS.'views'.DS.$this->view_ext. '.php';
            if(file_exists($file_ext))
            {
                $file = $file_ext;
            }
        }

        if (file_exists($file) == false)
        {
            throw new Exception('Template not found in '. $file);
            return false;
        }

        // Определения переменных
        foreach ($this->vars as $key => $value)
        {
            $$key = $value;
        }
        ob_start();
        include ($file);
        $this->registry->mod_content = ob_get_clean();
        $this->registry->NOT_SHOW_PROPERTY = false;
    }
     
    /**
     * Выводит внешний шаблон и определяет переменные
     * 
     * @param @param string $view  имя внешнего представления
     * @return boolean возвращает true если внешний шаблон существует и выведен
     */ 
    public function renderExt($view)
    {

        $file = SITE_PATH.'html'.DS.'views'.DS.$view . '.php';

        if (!file_exists($file))
        {
            return false;
        }

        // Определения переменных
        foreach ($this->vars as $key => $value)
        {
            $$key = $value;
        }
        ob_start();
        include ($file);
        $this->registry->mod_content = ob_get_clean();
        $this->registry->NOT_SHOW_PROPERTY = false;
        
        return true;
    }
    
    

}

?>
