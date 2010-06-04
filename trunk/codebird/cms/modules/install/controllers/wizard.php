<?php
/**
 *
 */
class InstallController_Wizard Extends Controller_Base
{
    public function access()
    {
        return $this->registry->install == true;
    }

    public function index()
    {
    }

    public function next()
    {
        try
        {
            $step = Utils::getPost("step");

            switch ($step)
            {
                case 0:
                    $checklist = $this->getChecklist();
                    $check = end($checklist);
                    $this->wizCheck($check['check'] ? 1 : 0,$checklist);
                    break;
                case 1:
                    $checklist = $this->getChecklist();
                    $check = end($checklist);
                    if($check['check'])
                    {
                        $this->wizConfig($step+1);
                    }
                    else
                    {
                        $this->wizCheck(0,$checklist);
                    }
                    break;
                case 2:
                    $db_host2 = Utils::getPost('db_host');
                    $db_user2 = Utils::getPost('db_user');
                    $db_user_pass2 = Utils::getPost('db_user_pass');
                    $db_name2 = Utils::getPost('db_name');
                    $site_name2 = Utils::getPost('site_name');
                    include SITE_PATH.'config.php';
                    if($db_host != $db_host2 || $db_user != $db_user2 || $db_user_pass != $db_user_pass2 || $db_name != $db_name2 || $site_name != $site_name2)
                    {
                        $this->saveConfig($db_host2, $db_user2, $db_user_pass2, $db_name2, $site_name2);
                    }
                    $check = $this->dbCheck($db_host2, $db_user2, $db_user_pass2, $db_name2);
                    if($check == "")
                    {
                        $this->wizModules($step+1);
                    }
                    else
                    {
                        $this->wizConfig($step, "Ошибка при подключении к базе данных: $check");
                    }
                    break;
                case 3:
                    $this->installModules();
                    $this->wizModulesOrder($step+1);
                    break;
                case 4:
                    $this->moduleOrder();
                    $this->wizFinish($step+1);
                    break;
                default:
                    $res = array();
                    $res['success'] = false;
                    $res['msg'] = "Неверный шаг установки";
                    $this->setContent(json_encode($res));
                    break;
            }
        }
        catch(Exception $e)
        {
           $res = array();
           $res['success'] = false;
           $res['msg'] = $e->getMessage();
           $this->setContent(json_encode($res));
        }
    }

    public function back()
    {
        try
        {
            $step = Utils::getPost("step");

            switch ($step)
            {
                case 2:
                    $checklist = $this->getChecklist();
                    $this->wizCheck($step-1,$checklist);
                    break;
                case 3:
                    $this->wizConfig($step-1);
                    break;
                case 4:
                    $this->wizModules($step-1);
                    break;
                case 5:
                    $this->wizModulesOrder($step-1);
                    break;
            }
        }
        catch(Exception $e)
        {
           $res = array();
           $res['success'] = false;
           $res['msg'] = $e->getMessage();
           $this->setContent(json_encode($res));
        }
    }

    private function getChecklist()
    {
        $check = true;
        $checklist = array();

        $file = SITE_PATH.'config.php';
        if(is_writable($file))
        {
            $checklist[] = array("check"=>true,"msg"=>"Файл <b>config.php</b> доступен");
        }
        else
        {
            $checklist[] = array("check"=>false,"msg"=>"Файл <b>config.php</b> недоступен для записи");
            $check = false;
        }

        $file = SITE_PATH.'config';
        if(is_writable($file))
        {
            $checklist[] = array("check"=>true,"msg"=>"Папка <b>config</b> доступна");
        }
        else
        {
            $checklist[] = array("check"=>false,"msg"=>"Папка <b>config</b> недоступна для записи");
            $check = false;
        }

        $file = SITE_PATH.'files';
        if(is_writable($file))
        {
            $checklist[] = array("check"=>true,"msg"=>"Папка <b>files</b> доступна");
        }
        else
        {
            $checklist[] = array("check"=>false,"msg"=>"Папка <b>files</b> недоступна для записи");
            $check = false;
        }

        if($check)
        {
            $checklist[] = array("check"=>true,"msg"=>"Все готово, нажмите 'Далее&nbsp;&gt;'");
        }
        else
        {
            $checklist[] = array("check"=>false,"msg"=>"Дальнейшая установка не будет произведена без выполнения этих условий<br/><br/>исправьте и нажмите 'Далее&nbsp;&gt;'");
        }

        return $checklist;
    }

    private function wizCheck($step, $checklist)
    {
        $template = $this->createTemplate();

        $template->step = $step;

        $check = array_pop($checklist);

        $template->check = $check;
        
        $template->checklist = $checklist;

        $template->render("wizard_check");
    }

    private function dbCheck($db_host,$db_user,$db_user_pass,$db_name)
    {
        try
        {
            $db = new PDO("mysql:host=$db_host;dbname=$db_name",
                $db_user,
                $db_user_pass,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
        }
        catch (PDOException $e)
        {
            return $e->getMessage();
        }
        return "";
    }

    private function saveConfig($db_host,$db_user,$db_user_pass,$db_name,$site_name)
    {
        $file = fopen (SITE_PATH.'config.php',"w");

  	$str = '<?php
// MySQL
$db_user = "'.$db_user.'";
$db_user_pass = "'.$db_user_pass.'";
$db_host = "'.$db_host.'";
$db_name = "'.$db_name.'";

// base for header
$base = "http://".$_SERVER[\'HTTP_HOST\']."/";

// Site name
$site_name = "'.str_replace('"','\\"',$site_name).'";
?>';

        fwrite ($file, $str);
  	fclose ($file);
    }

    private function wizConfig($step, $check=null)
    {
        $template = $this->createTemplate();

        $template->step = $step;

        include SITE_PATH.'config.php';

        $template->db_user = $db_user;
        $template->db_user_pass = $db_user_pass;
        $template->db_name = $db_name;
        $template->site_name = $site_name;
        $template->db_host = $db_host;

        $template->check = $check;

        $template->render("wizard_config");
    }

    private function saveModmanagerConfig($modules)
    {
        $file = SITE_PATH.'config'.DS.'modmanager.config.xml';
        if(is_file($file))
        {
            $xml = simplexml_load_file($file);
            foreach($xml->xpath('/config/params/param') as $param)
            {
                $name = (string)$param->name;
                if($name == "modules")
                {
                    $param->items = new SimpleXMLElement("<items></items>");
                    foreach($modules as $module)
                    {
                        if($module['service']) continue;
                        $item = $param->items->addChild("item");
                        $item->addAttribute("name",$module['module']);
                        $item->addAttribute("type","array");
                        $value = $item->addChild("value", $module['title']);
                        $value->addAttribute("name","title");
                    }
                }
                elseif($name == "service_modules")
                {
                    $param->items = new SimpleXMLElement("<items></items>");
                    foreach($modules as $module)
                    {
                        if(!$module['service']) continue;
                        $item = $param->items->addChild("item");
                        $item->addAttribute("name",$module['module']);
                        $item->addAttribute("type","array");
                        $value = $item->addChild("value", $module['title']);
                        $value->addAttribute("name","title");
                    }
                }
            }
            $xml->asXML($file);
        }
    }

    private function installModules()
    {
        $dir    = SITE_PATH.'cms'.DS.'modules';
        $files = scandir($dir);

        $modmanager_config = Config::getConfig("modmanager");

        $modules = array();

        foreach($files as $file)
        {
            if(is_dir($dir.DS.$file))
            {
                $install = Install::getInstall($file);
                if($install)
                {
                    $installed = array_key_exists($file,$modmanager_config->modules)
                            || array_key_exists($file,$modmanager_config->service_modules);
                    $required = $install->required;
                    $to_install = Utils::getPost("$file-module") != null;

                    if($to_install || $required)
                    {
                        if(!$installed)
                        {
                            $installer = new Installer();
                            $installer->install($install);
                            if($installer->getInstallCode() > 0)
                            {                                
                                throw new Exception("Ошибка установки модуля '".$install->title."': ".str_replace("\n","<br/>",$installer->getInstallMessage()));
                            }
                        }
                        $modules[$file] = array("module"=>$file,"title"=>$install->title,"service"=>$install->service);
                    }
                }
            }
        }

        $modules_exists = array();

        foreach($modmanager_config->modules as $key=>$module)
        {
            if(array_key_exists($key,$modules))
            {
                $modules_exists[$key] = $modules[$key];
            }
        }

        $modules_new = array_merge($modules_exists, $modules);
        
        $this->saveModmanagerConfig($modules_new);
    }

    private function wizModules($step, $back=false)
    {
        $template = $this->createTemplate();

        $template->step = $step;

        $dir    = SITE_PATH.'cms'.DS.'modules';
        $files = scandir($dir);

        $modmanager_config = Config::getConfig("modmanager");

        $modules = array();

        foreach($files as $file)
        {
            if(is_dir($dir.DS.$file))
            {
                $install = Install::getInstall($file);
                if($install)
                {
                    $installed = array_key_exists($file,$modmanager_config->modules)
                                || array_key_exists($file,$modmanager_config->service_modules);
                    $required = $install->required;
                    if($required) $installed = true;
                    $modules[] = array("name"=>$file,"title"=>$install->title,"required"=>$required,"installed"=>$installed);
                }                
            }
        }

        $template->modules = $modules;

        $template->render("wizard_modules");
    }

    private function moduleOrder()
    {
        $modules = array();
        $orders = array();
        $modmanager_config = Config::getConfig("modmanager");
        foreach($modmanager_config->modules as $name=>$module)
        {
            $order = Utils::getPost("$name-order");
            $title = Utils::getPost("$name-title");
            $modules[$name] = array("order"=>$order,"module"=>$name,"title"=>$title,"service"=>false);
            $orders[$name] = $order;
        }
        array_multisort($orders,SORT_ASC,$modules);

        foreach($modmanager_config->service_modules as $name=>$module)
        {
            $modules[$name] = array("order"=>$order,"module"=>$name,"title"=>$title,"service"=>true);
        }

        $this->saveModmanagerConfig($modules);
    }

    private function wizModulesOrder($step)
    {
        $template = $this->createTemplate();

        $template->step = $step;

        $modmanager_config = Config::getConfig("modmanager");

        $modules = array();
        $order = 1;
        foreach($modmanager_config->modules as $name=>$module)
        {
            $modules[] = array("order"=>$order++,"module"=>$name,"title"=>$module['title']);
        }

        $template->modules = $modules;

        $template->render("wizard_modules_order");
    }

    private function wizFinish($step)
    {
        $template = $this->createTemplate();

        $template->step = $step;

        $template->render("wizard_finish");
    }
}

?>
