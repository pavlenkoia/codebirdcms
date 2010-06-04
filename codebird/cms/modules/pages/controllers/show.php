<?php
/*
 *
 */

Class PagesController_Show Extends Controller_Base
{
    private $page;

    private function check()
    {
        $id = Utils::getVar('id');
        $alias = Utils::getVar('alias');
        $mod = Utils::getVar('mod');

        if(!(isset($id) or isset($alias)) or !isset($mod) or $mod != $this->module)
        {
            return false;
        }

        return true;
    }

    private function getPage()
    {
        $id = Utils::getVar('id');
        $alias = Utils::getVar('alias');

        if(!isset($this->page) 
            or !isset($this->page->id) 
            or ($this->page->id != $id and $this->page->alias != $alias))
        {
            $table = new Table("pages");

            if($alias)
            {
               $this->page = $table->getEntityAlias($alias);
            }
            elseif($id)
            {
               $this->page = $table->getEntity($id); 
            }            
        }

        return $this->page;
    }

    public function index()
    {
    }

    public function title()
    {
        if(!$this->check())
        {
            return;
        }

        $page = $this->getPage();

        if($page)
        {
            $this->registry->mod_content = $page->title;
        }
    }

    public function title2()
    {
        if(!$this->check())
        {
            return;
        }

        $page = $this->getPage();

        if($page)
        {
            $this->registry->mod_content = $page->title2 == null || trim($page->title2) == '' ? $page->title : $page->title2;
        }
    }

    public function content()
    {
        if(!$this->check())
        {
            return;
        }

        $page = $this->getPage();

        $this->registry->mod_content = $page->content;
    }

    public function announcement()
    {
        if(!$this->check())
        {
            return;
        }

        $page = $this->getPage();

        $this->registry->mod_content = $page->announcement;
    }

    public function meta_keywords()
    {
        if(!$this->check())
        {
            return;
        }

        $page = $this->getPage();

        $this->registry->mod_content = $page->meta_keywords;
    }

    public function meta_description()
    {
        if(!$this->check())
        {
            return;
        }

        $page = $this->getPage();

        $this->registry->mod_content = $page->meta_description;
    }

    public function plugin()
    {
        if(!$this->check())
        {
            return;
        }

        $page = $this->getPage();

        $plugins = $page->plugins;

        $plugins = explode(";",$plugins);

        $name = $this->args->name;

        foreach($plugins as $plugin)
        {
            $plugs = explode(":",$plugin);
            if($plugs[0] == $name)
            {
                $plugin_value = $plugs[1];
                break;
            }
        }

        if(isset($plugin_value))
        {
            $plugin_values = explode("?",$plugin_value);
            $plugin_values = array_pad($plugin_values,2,null);

//            $template = $this->createTemplate();

            $plugin_mod = $plugin_values[0];
            if($page->mainpage == 1)
            {
                $plugin_values[1] .= "&mainpage=1";
            }
            $plugin_args = $plugin_values[1];

            $plugin_args = str_replace('%26','&',$plugin_args);

            if(isset($plugin_mod))
            {
                Registry::__instance()->mod_content = val($plugin_mod, $plugin_args);
            }
            else
            {
                Registry::__instance()->mod_content = "";
            }

//            $template->render('show_plugin');
        }
    }

    public function breadcrumbs()
    {
        if(!$this->check())
        {
            return;
        }

        $page = $this->getPage();

        if($page)
        {
            $template = $this->createTemplate();
            $pages = array();
            $table = new Table("pages");
            $parent_id = $page->parent_id;
            while($parent_id)
            {
                $parent_page = $table->getEntity($parent_id);
                $parent_id = $parent_page->parent_id;
                $pages[] = $parent_page;
            }
            $pages = array_reverse($pages);
            $template->page = $page;
            $template->pages = $pages;
            $template->render();
        }
    }
}

?>
