<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Контент менеджер - <?php echo htmlspecialchars(Registry::__instance()->site_name) ?></title>
        <base href="<?php echo $base ?>" />
        <link rel="stylesheet" type="text/css" href="cms/modules/cm/html/css/application.css" />
        <style type="text/css">
            html, body {
                margin:0;
                padding:0;
                border:0 none;
                overflow:hidden;
                height:100%;
            }
        </style>
    </head>
    <body scroll="no" id="docs">
        <div id="loading-mask" style="width:100%;height:100%;background:#c3daf9;position:absolute;z-index:20000;left:0;top:0;">&#160;</div>
        <div id="loading">
            <div class="loading-indicator"><img src="jscripts/ext/resources/images/default/grid/loading.gif" style="width:16px;height:16px;" align="absmiddle">&#160;Загрузка...</div>
        </div>

        <link rel="stylesheet" type="text/css" href="jscripts/ext/resources/css/ext-all.css" />

        <link rel="stylesheet" type="text/css" href="jscripts/ext/ux/css/CenterLayout.css" />

        <link rel="stylesheet" type="text/css" href="cms/modules/cm/html/css/application.css" />

        <link rel="stylesheet" type="text/css" href="cms/modules/cm/html/css/chooser.css" />

        <script type="text/javascript" src="jscripts/ext/adapter/ext/ext-base.js"></script>
        <script type="text/javascript" src="jscripts/ext/ext-all.js"></script>

        <script type="text/javascript" src="jscripts/ext/src/locale/ext-lang-ru.js"></script>

        <!-- extensions -->
        <script type="text/javascript" src="jscripts/ext/ux/CenterLayout.js"></script>
        <script type="text/javascript" src="jscripts/ext/ux/RowLayout.js"></script>
        <script type="text/javascript" src="jscripts/ext/ux/TabCloseMenu.js"></script>
        <script type="text/javascript" src="jscripts/ext/ux/FileUploadField.js"></script>
        <script type="text/javascript" src="jscripts/ext/ux/XmlTreeLoader.js"></script>

        <link rel="stylesheet" type="text/css" href="jscripts/ext/ux/css/fileuploadfield.css"/>

        <script type="text/javascript" src="jscripts/fckeditor/fckeditor.js"></script>

        <script type="text/javascript" src="cms/modules/cm/html/js/application.js"></script>

        <div id="header">
            <h1><?php echo Config::__("cm")->title?> v.<?php mod("cm.version")?> - <?php echo htmlspecialchars(Registry::__instance()->site_name) ?></h1>
            <a id="onexit" href="#">Выход(<?php $user=val('security.login.user'); echo $user['name']; ?>)</a>
            <a id="onsite" href="" target="_blank">На сайт</a>
        </div>
    </body>
</html>
