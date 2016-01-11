<?php
/**
 *
 */

 class ModmanagerController_Cm Extends Controller_Base
{
    public function access()
    {
        return $this->login();
    }

    public function index()
    {
    }

    public function modules()
    {
        $this->setContent($this->config->modules);
    }

    public function config()
    {
        $module = Utils::getVar('module');
        
        if($module)
        {
            $template = $this->createTemplate();
            
            $params = array();

            $file = SITE_PATH.'config'.DS.$module.'.config.xml';
            
            if(file_exists($file))
            {
                $xml = simplexml_load_file($file);

                foreach($xml->xpath('/config/params/param') as $param)
                {
                    $description = (string)$param->description;
                    $name = (string)$param->name;
                    $ptype = (string)$param['type'];
                    if($param['invisible'] && 'true' == (string)$param['invisible']) continue;
                    switch($ptype)
                    {
                        case 'array':
                            $value = null;
                            $t = 'array';
                            break;
                        default:
                            $value = (string)$param->value;
                            $t = 'text';
                    }
                    $params[] = array("description"=>$description,"name"=>$name,"value"=>$value,"type"=>$t);
                }
            }

            $template->params = $params;

            $template->module = $module;

            $template->render();
        }
    }

    public function saveconfig()
    {
        $res = array();
        
        try
        {
            $module = Utils::getVar('module');
            
            if($module)
            {
                $file = SITE_PATH.'config'.DS.$module.'.config.xml';
                
                if(file_exists($file))
                {
                    
                    $template = $this->createTemplate();
                    
                    $xml = simplexml_load_file($file);
                    
                    foreach($xml->xpath('/config/params/param') as $param)
                    {
                        $name = (string)$param->name;
                        if(Utils::getPost($name))
                        {
                            $ptype = (string)$param['type'];
                            switch($ptype)
                            {
                                case 'array':
                                    continue;
                                    break;
                                default:
                                    $param->value = Utils::getPost($name);
                            }
                        }
                    }
                    
                    $xml->asXML($file);

                    $res['success'] = true;
                    $res['msg'] = 'Готово';
                }
                else
                {
                    $res['success'] = false;
                    $res['msg'] = 'Ошибка: файл настроек не найден';
                }
            }
            else
            {
                $res['success'] = false;
                $res['msg'] = 'Ошибка: модуль не задан';
            }
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = 'Ошибка: '.$e->getMessage();
        }
        
        $this->setContent(json_encode($res));
    }
}

?>
