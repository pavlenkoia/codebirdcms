<?php
/**
 * Базовый класс установки модуля
 */

abstract class Install_Base
{
    public $install = true;

    public $required = false;

    public $service = false;

    public $title = "";

    public $sql = "";

    public function exec_sql(){}

    public $dirs = array();

    public $config = '';

    private $module;

    
    public function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * Возвращает имя модуля
     * @return string имя модуля
     */
    public function getModule()
    {
        return $this->module;
    }
}

?>
