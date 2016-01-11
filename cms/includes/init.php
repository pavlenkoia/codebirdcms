<?php

/**
 * Функция для загрузки классов «на лету»
 */
function __autoload($class_name)
{
    $filename = strtolower($class_name) . '.php';

    $file = SITE_PATH . 'cms' . DS . 'classes' . DS . $filename;

    if (!file_exists($file))
    {

        // загрузка классов модулей

        $module = explode('_', strtolower($class_name));

        if(isset($module[0]))
        {
            $module = $module[0];

            $file = SITE_PATH . DS . 'cms' . DS . 'modules' . DS . $module . DS . 'classes' . DS . $filename;

            if (!file_exists($file))
            {
                return false;
            }
        }
        else
        {
            return false;
        }

    }

    require_once ($file);

}

 $registry = Registry::__instance();

 // Создание глобального роутера

$registry->router = new Router();

/**
 * Функция вывода глобальной переменной
 *
 * @param string $name имя переменной
 */

function out($name)
{
    $registry = Registry::__instance();

    $val = $registry->__get($name);

    echo $val;
}

function mod_content($name, $arg=null)
{
    $registry = Registry::__instance();

    $names = explode(".", $name);

    $names = array_pad($names, 3, null);

    $module = $names[0];
    $controller = $names[1];
    $action = $names[2];

    $registry->router->dispatch($module,$controller,$action,$arg);
}

function mod($name, $arg=null)
{
    mod_content($name, $arg);

    out('mod_content');

    Registry::__instance()->mod_content = "";
}

function val($name, $arg=null)
{
    mod_content($name, $arg);
    
    $mod_content = Registry::__instance()->mod_content;

    Registry::__instance()->mod_content = "";

    return $mod_content;
}

Registry::__instance()->tohead = "";

function tohead($name)
{
    $registry = Registry::__instance();

    $registry->tohead = $registry->tohead." ".val($name."_tohead");
}

function memo()
{
    return  time();
}

// Создание глобального объекта для работы с базой данных

if(isset($db_host))
{
    try
    {
        $db = new PDO("mysql:host=$db_host;dbname=$db_name",
            $db_user,
            $db_user_pass,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
        $registry->db = $db;
    }
    catch (PDOException $e)
    {
        //echo 'Ошибка соединения: ' . $e->getMessage();
        //exit;
    }

    $registry->site_name = $site_name;
}


/**
* Escapes special characters
* Function escapes ", \, /, \n and \r symbols so that not to cause JavaScript error or
* data loss
*
* @param string $string
* @return string
*/
function escapeJSON($string) {
//    return str_replace(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'), $string);
    return json_encode($string);
}

class phpException extends exception {
    public function __construct($errno, $errstr, $errfile, $errline) {
        parent::__construct();
        $this->code = $errno;
        $this->message = $errstr;
        $this->file = $errfile;
        $this->line = $errline;
    }
}

function err2exc($errno, $errstr, $errfile, $errline) {

    // TODO: отключить или нет E_NOTICE
    if($errno == E_NOTICE || $errno == E_WARNING)
    {
        /*$app_errors = App::GetProperty('APP_ERRORS');
        $app_errors .= $errno.' '.$errstr.' '.$errfile.' '.$errline.'<br>';
        App::SetProperty('APP_ERRORS',$app_errors);*/
    }
    else
    {
        throw new phpException($errno, $errstr, $errfile, $errline);
    }
}

function offset()
{
    exit;
}

set_error_handler('err2exc', E_ERROR | E_NOTICE | E_WARNING);
//error_reporting(E_ERROR);



function get_cache_pic($photo, $w = 100, $h = 100, $orig_ratio=true, $watermark='')
{
	if ($photo == null || trim($photo)=='' || !is_file(SITE_PATH.$photo)) return '';

        $ps = explode('/', $photo);
        $p = $ps[count($ps)-1];
        if(count($ps) > 2 && $ps[1] != 'catalog')
        {
            $p = str_replace('/', '_', $photo);
        }
	//$p = str_replace('files/catalog/upload/', '', $photo);
	$cache_url = "files/catalog/cache/" . $w . 'x' . $h . '_' . $p; 

	if ($w > 0 && $h > 0) $t = 2;
	else $t = 1;

	if (!is_file(SITE_PATH.$cache_url))
	{
            Utils::img_resize(  SITE_PATH.$photo,
                                SITE_PATH.$cache_url,
                                $w,
                                $h,
                                $orig_ratio);
            if($watermark)
            {
                Utils::img_watermark(SITE_PATH.$watermark, SITE_PATH.$cache_url,SITE_PATH.$cache_url);
            }
	}

	return $cache_url;
}


if (!isset($_SERVER['SCRIPT_URL']) && isset($_SERVER['SCRIPT_NAME']))
{
    $_SERVER['SCRIPT_URL'] = $_SERVER['SCRIPT_NAME'];
}

include (SITE_PATH.'config'.DS.'init.php');
?>