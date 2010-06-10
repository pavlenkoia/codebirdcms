<?php

class PagesConfig extends Config_Base
{
    /**
     * Шаблоны страниц
     *
     * @var array
     */
    public $templates = array(
        "main_template.php"=>"Главная страница",
        "page_template.php"=>"Шаблон страницы",
        "gallery_template.php"=>"Галерея",
        "guestbook_template.php"=>"Гостевая книга",
        "page_block_template.php"=>"Шаблон страницы с колонкой",
        "page_none_template.php"=>"Шаблон страницы без колонки и подменю",
        "page_nomenu_block_template.php"=>"Шаблон страницы c колонкой без подменю"
    );

    /**
     * Шаблон по умолчанию
     *
     * @var string
     */
    public $default_template = "page.tpl.php";

    
    /**
     * Включить чпу
     * 
     * @var boolean 
     */

    public $is_alias = true;


    public $plugins = array(
        "cloudtags"=>array("name"=>"cloudtags","label"=>"Облако тегов","mod"=>"cloudtags.admin.plugin","plug"=>true),
        "gallery"=>array("name"=>"gallery","label"=>"Галерея","mod"=>"gallery.admin.plugin","plug"=>true),
        "htmlblock"=>array("name"=>"htmlblock","label"=>"HTML блок","mod"=>"htmlblock.admin.plugin","plug"=>true),
        "submenu"=>array("name"=>"submenu","label"=>"Показывать в подменю","mod"=>"pages.menu.plugin","plug"=>false)
    );

    public $plugins_cm = array(
        "cloudtags"=>array("name"=>"cloudtags","label"=>"Облако тегов","mod"=>"cloudtags.cm.plugin","plug"=>true),
        "gallery"=>array("name"=>"gallery","label"=>"Галерея","mod"=>"gallery.cm.plugin","plug"=>true),
//        "htmlblock"=>array("name"=>"htmlblock","label"=>"HTML блок","mod"=>"htmlblock.cm.plugin","plug"=>true),
        "submenu"=>array("name"=>"submenu","label"=>"показывать подменю","mod"=>"pages.menu.plugin_cm","plug"=>true)
    );

    public function __construct()
    {
        if(Registry::__instance()->is_alias )
        {
            $this->is_alias = Registry::__instance()->is_alias;
        }
    }

    /**
     * Папка картинок страниц
     * @var string
     */
    public $image_path = "files/pages";

    /**
     * Ширина изображения страницы
     * @var integer
     */
    public $image_width = 100;

    /**
     * Высота изображения страницы
     * @var integer
     */
    public $image_height = 100;
}

?>
