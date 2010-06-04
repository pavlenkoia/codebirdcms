<?php
/*
 *
 */

Class PagesController_Menu Extends Controller_Base
{

    public function index()
    {
    }

    public function mainmenu()
    {
        $template = $this->createTemplate();

        $data = $this->getData('Pages');

        $template->data = $data;

        $template->render("menu_mainmenu");
    }

    public function tupoemenu()
    {
        $template = $this->createTemplate();

        $data = $this->getData('Pages');

        $template->data = $data;

        $template->render("menu_tupoemenu");
    }

    public function submenu()
    {
        $template = $this->createTemplate();

        $data = $this->getData('Pages');

        $template->data = $data;

        $id = Utils::getVar('id');
        $alias = Utils::getVar('alias');
        $table = new Table("pages");
        if($alias)
        {
            $page = $table->getEntityAlias($alias);
        }
        elseif($id)
        {
            $page = $table->getEntity($id);
        }
        if(isset($page))
        {
            $template->page = $page;

            $template->render("menu_submenu");
        }
    }

    public function plugin()
    {
        $template = $this->createTemplate();

        $template->args = $this->args;

        $template->render('menu_plugin');
    }

    public function plugin_cm()
    {
        $template = $this->createTemplate();

        $template->args = $this->args;

        $template->render("menu_plugin_cm");
    }
}
?>
