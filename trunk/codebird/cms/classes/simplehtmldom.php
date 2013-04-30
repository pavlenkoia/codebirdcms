<?
include_once SITE_PATH.'/cms/lib/simplehtmldom/simple_html_dom.php';

class SimpleHtmlDom
{
    public static function file_get_html($url)
    {
        return file_get_html($url);
    }

    public static function str_get_html($str)
    {
        return str_get_html($str);
    }
}