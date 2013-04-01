<?php
session_start();
include "config.php";
include "cms.php";
$registry = Registry::__instance();

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
{
    if(Utils::getPost("mod_action"))
    {
        mod(Utils::getPost("mod_action"));
    }
}
else
{
    //setcookie('name', 'value', time()+60*60*24*30*12, '/');
    App::StartBuffer();

    if(Utils::getPost("mod_action"))
    {
        $mod_actions = explode(";", Utils::getPost("mod_action"));
        foreach($mod_actions as $mod_action)
        {
            mod_content($mod_action);
        }
    }

    if(Utils::getVar('uri'))
    {
        $uri = Utils::getVar('uri');
        $uri = explode("/",$uri);

        $_REQUEST['mod'] = "pages";
        $_REQUEST['alias'] = $uri[0];

        array_shift($uri);

        $_REQUEST['uri'] = implode("/",$uri);

        $template = val("pages.site.template");

        if(!$template)
        {
            $template = val($_REQUEST['alias'].'.site.template','quitcontroller');
        }
    }
    else
    {
        $mainpage = val("pages.site.mainpage");
        $template = $mainpage['template'];
        $_REQUEST['mod'] = "pages";
        $_REQUEST['id'] = $mainpage['id'];
        $registry->mainpage = true;
    }

    if(!isset($template) || !$template)
    {
        $template = "404.tpl.php";
    }

    if(strpos($template,'/') === 0)
    {
        include ROOT.SF.$template;
    }
    else
    {
        include "html/templates/$template";
    }


    App::OutBuffer();
}
?>