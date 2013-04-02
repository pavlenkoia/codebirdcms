<?php
/**
* Класс приложения
*/
class App
{
    private static $AppBuffer = array();
    private static $AppProperty = array();

    /**
     * Возвращает регистр
     */
    public static function GetRegistry()
    {
        return Registry::__instance();
    }

    /**
     * Старт буферизации страницы
     */
    public static function StartBuffer()
    {
        $level = ob_get_level();

        if($level > 1)
        {
            return;
        }

        self::$AppBuffer = array();

        self::$AppProperty = array();

        ob_start();

    }

    /**
     * Выводит отложеное свойство
     *
     * @param $name имя свойства
     */
    public static function ShowProperty($name)
    {
        $level = ob_get_level();

        for($i = 0; $i < $level; $i++)
        {
            self::$AppBuffer[] = ob_get_clean();
        }

        self::$AppBuffer[] = array('name'=>$name);

        for($i = 0; $i < $level; $i++)
        {
            ob_start();
        }
    }

    /**
     * Устанавливает отложеное свойство
     *
     * @param $name имя свойства
     * @param $value значение свойства
     */
    public static function SetProperty($name, $value)
    {
        self::$AppProperty[$name] = $value;
    }

    /**
     * Получить значение отложеного свойства
     *
     * @param $name имя свойства
     */
    public static function GetProperty($name)
    {
        if(isset(self::$AppProperty[$name]))
        {
            return self::$AppProperty[$name];
        }

        return null;
    }

    /**
     * Вывод буфера
     */
    public static function OutBuffer()
    {
        $level = ob_get_level();

        for($i = 0; $i < $level; $i++)
        {
            self::$AppBuffer[] = ob_get_clean();
        }

        $content = '';

        foreach(self::$AppBuffer as $buffer)
        {
            if(is_array($buffer))
            {
                $name = $buffer['name'];
                if(isset(self::$AppProperty[$name]))
                {
                    $content .= self::$AppProperty[$name];
                }
            }
            else
            {
                $content .= $buffer;
            }
        }
        self::$AppBuffer = array();

        echo $content;
    }

    /**
     * Сброс буфера
     */
    public static function ResetBuffer()
    {
        $level = ob_get_level();

        for($i = 0; $i < $level; $i++)
        {
            ob_clean();
        }

        self::$AppBuffer = array();
    }

    /**
     * Выполняется перенаправление браузера на указанную страницу
     *
     * @param $url URL на который будет перенаправлен браузер. Необходимо использовать абсолютные адреса и адреса ведущих на другие сайты и начинающихся со следующих протоколов: "http://", "https://", "ftp://"
     */
    public static function Redirect($url)
    {
        if(preg_match("'^(http://|https://|ftp://)'i", $url))
        {
            header("Request-URI: ".$url);
            header("Content-Location: ".$url);
            header("Location: ".$url);
        }
        else
        {
            if(strpos($url, "/") !== 0)
                $url = '/'.$url;

            $host = $_SERVER['HTTP_HOST'];
            if($_SERVER['SERVER_PORT'] <> 80 && $_SERVER['SERVER_PORT'] <> 443 && $_SERVER['SERVER_PORT'] > 0 && strpos($_SERVER['HTTP_HOST'], ":") === false)
                $host .= ":".$_SERVER['SERVER_PORT'];

            $protocol = "http";

            header("Request-URI: ".$protocol."://".$host.$url);
            header("Content-Location: ".$protocol."://".$host.$url);
            header("Location: ".$protocol."://".$host.$url);
        }

        exit;
    }

    public static function SetCookie($name, $value)
    {
        return setcookie($name, $value, time()+60*60*24*30*12*2, '/');
    }

    public static function GetCookie($name)
    {
        if(isset($_COOKIE[$name])){
            return $_COOKIE[$name];
        }

        return null;
    }

    public  static function DelCookie($name)
    {
        setcookie ($name, '', time()-3600, '/');
    }
}