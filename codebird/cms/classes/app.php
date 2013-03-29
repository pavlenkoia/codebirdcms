<?php
/**
* Класс приложения
*/
class App
{
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
        $registry = self::GetRegistry();

        if($registry->NOT_SHOW_PROPERTY)
        {
            return;
        }

        $registry->AppBuffer = array();

        $registry->AppProperty = array();

        ob_start();

    }

    /**
     * Выводит отложеное свойство
     *
     * @param $name имя свойства
     */
    public static function ShowProperty($name)
    {
        $registry = self::GetRegistry();

        if($registry->NOT_SHOW_PROPERTY)
        {
            return;
        }

        $appBuffer = $registry->AppBuffer;

        $appBuffer[] = ob_get_clean();

        $appBuffer[] = array('name'=>$name);

        $registry->AppBuffer = $appBuffer;

        ob_start();
    }

    /**
     * Устанавливает отложеное свойство
     *
     * @param $name имя свойства
     * @param $value значение свойства
     */
    public static function SetProperty($name, $value)
    {
        $registry = self::GetRegistry();

        $appProperty = $registry->AppProperty;

        $appProperty[$name] = $value;

        $registry->AppProperty = $appProperty;
    }

    /**
     * Получить значение отложеного свойства
     *
     * @param $name имя свойства
     */
    public static function GetProperty($name)
    {
        $registry = self::GetRegistry();

        $appProperty = $registry->AppProperty;

        if(isset($appProperty[$name]))
        {
            return $appProperty[$name];
        }

        return null;
    }

    /**
     * Вывод буфера
     */
    public static function OutBuffer()
    {
        $registry = self::GetRegistry();

        if($registry->NOT_SHOW_PROPERTY)
        {
            return;
        }

        $appProperty = $registry->AppProperty;

        $appBuffer = $registry->AppBuffer;

        $appBuffer[] = ob_get_clean();

        $content = '';

        foreach($appBuffer as $buffer)
        {
            if(is_array($buffer))
            {
                $name = $buffer['name'];
                if(isset($appProperty[$name]))
                {
                    $content .= $appProperty[$name];
                }
            }
            else
            {
                $content .= $buffer;
            }
        }

        $registry->remove('AppBuffer');

        echo $content;
    }

    /**
     * Сброс буфера
     */
    public static function ResetBuffer()
    {
        $registry = Rself::GetRegistry();

        if($registry->NOT_SHOW_PROPERTY)
        {
            return;
        }

        ob_clean();

        $registry->AppBuffer = array();
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
}