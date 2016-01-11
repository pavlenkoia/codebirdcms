<?php
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
{
    include "config.php";
    include "cms.php";
    if(Utils::getVar("action"))
    {        
        Registry::__instance()->install = true;
        mod(Utils::getVar("action"));
    }
}
else{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Установка CMS</title>
        <base href="<?php echo "http://".$_SERVER['HTTP_HOST']."/" ?>" />
        <link rel="stylesheet" type="text/css" href="cms/modules/install/html/css/application.css" />
        <link type="image/gif" href="cms/modules/install/html/images/config.gif" rel="icon" />
    </head>
    <body scroll="no" id="docs">
        <div id="loading-mask" style="width:100%;height:100%;background:#c3daf9;position:absolute;z-index:20000;left:0;top:0;">&#160;</div>
        <div id="loading">
            <div class="loading-indicator"><img src="jscripts/ext/resources/images/default/grid/loading.gif" style="width:16px;height:16px;" align="absmiddle">&#160;Загрузка...</div>
        </div>

        <link rel="stylesheet" type="text/css" href="jscripts/ext/resources/css/ext-all.css" />

        <link rel="stylesheet" type="text/css" href="jscripts/ext/ux/css/CenterLayout.css" />

        <link rel="stylesheet" type="text/css" href="cms/modules/install/html/css/application.css" />

        <script type="text/javascript" src="jscripts/ext/adapter/ext/ext-base.js"></script>
        <script type="text/javascript" src="jscripts/ext/ext-all.js"></script>

        <script type="text/javascript" src="jscripts/ext/src/locale/ext-lang-ru.js"></script>

        <!-- extensions -->

        <script type="text/javascript" src="cms/modules/install/html/js/install.js"></script>

    </body>
</html>
<?php }?>