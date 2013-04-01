<?
/**
 * Базовый класс класса модулей
 */

abstract class Module_Class
{
    protected function GetModule()
    {
        $module = explode('_', strtolower(get_called_class ()));

        return $module[0];
    }

    protected function getData($model=null)
    {
        $module = $this->GetModule();

        if(!$model)
        {
            $model = $module;
        }

        $path = SITE_PATH.'cms/modules'.DS.$module.DS."models".DS.strtolower($model).'.php';

        if (file_exists($path) == false)
        {
            throw new Exception('Model not found in '. $path);
            return false;
        }

        require_once($path);

        $class = ucfirst($module).'Model_'.ucfirst($model);

        $data = new $class($module);

        return $data;
    }
}
