<?php
error_reporting (E_ALL);

// Проверка версии php

if (version_compare(phpversion(), '5.1.0', '<') == true) { die ('PHP5.1 Only'); }

// Константы:

// разделитель
define ('DS', DIRECTORY_SEPARATOR);

// путь до файлов сайта
$path_parts = pathinfo(__FILE__);
$site_path = $path_parts['dirname'].DS;
define ('SITE_PATH', $site_path);

$DOCUMENT_ROOT = rtrim( getenv("DOCUMENT_ROOT"), "/\\" );
define ('ROOT', $DOCUMENT_ROOT);

// подпапка в которой стоит CMS
$SUB_FOLDER = str_replace( str_replace("\\", "/", $DOCUMENT_ROOT), "", str_replace("\\", "/", dirname(__FILE__)) );
define ('SF', $SUB_FOLDER);

// Включение init.php file
include (SITE_PATH.'cms'.DS.'includes'.DS.'init.php');
?>