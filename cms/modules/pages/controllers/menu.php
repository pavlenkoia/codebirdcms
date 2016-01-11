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

    public function pages()
    {
        $template = $this->createTemplate();

        $data = $this->getData();

        $template->data = $data;

        $alias = Utils::getVar('alias');
        $table = new Table("pages");

        $page = $table->getEntityAlias($alias);

        $result['items'] = array();

        if($page)
        {
            $pages = $data->getVisiblePages($page->id);
            if(count($pages) == 0 && $page->parent_id)
            {
                $pages = $data->getVisiblePages($page->parent_id);
            }
            foreach($pages as $row)
            {
                $item = array();

                $item['page'] = $row;

                $item['href'] = $row['alias'].'.html';

                $item['title'] = $row['title'];

                if($row['alias'] == $alias)
                {
                    $item['active'] = true;
                }

                $result['items'][] = $item;
            }
        }

        $template->page = $page;

        $template->result = $result;

        $template->render();
    }
}
?>
