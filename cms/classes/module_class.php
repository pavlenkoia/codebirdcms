<?
/**
 * Базовый класс класса модулей
 */

abstract class Module_Class
{
    private function getCalledClass(){
        $arr = array();
        $arrTraces = debug_backtrace();
        foreach ($arrTraces as $arrTrace){
            if(!array_key_exists("class", $arrTrace)) continue;
            if(count($arr)==0) $arr[] = $arrTrace['class'];
            else if(get_parent_class($arrTrace['class'])==end($arr)) $arr[] = $arrTrace['class'];
        }
        return end($arr);
    }

    protected function GetModule()
    {
        $module = explode('_', strtolower(!function_exists('get_called_class')?$this->getCalledClass():get_called_class ()));

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
