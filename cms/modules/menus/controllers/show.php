<?php
/*
 * Контроллер show меню
 */

class MenusController_Show extends Controller_Base
{

    private function show($menus_name)
    {
        $data = $this->getData();

        $menu = $data->getMenu($menus_name);

        if($menu)
        {
            $template = $this->createTemplate();

            $template->data = $data;
            $template->menu = $menu;


            // одноуровневое меню
            $menuItems = $data->getMenuItems($menu->id);
            /*$menuItems = array();

            $items = $data->getItems($menu->id);

            $uri_orig = $_SERVER['REQUEST_URI'];
            $uris = explode('?', $uri_orig);
            $url = $uris[0];

            $url2 = '/'.Utils::getVar('alias').'.html';

            $pages = $data->getPages();

            foreach($items as $row)
            {
                if($row['visible'] != 1) continue;

                $href = $row['type_link'];

                $row['href'] = $href;

                if($url == $href || $url2 == $href || ($row['type_id'] && in_array($href,$pages)))
                {
                    $row['active'] = true;
                }
                else
                {
                    $row['active'] = false;
                }

                $menuItems[] = $row;
            }*/

            $template->items = $menuItems;
            // end одноуровневое меню


            $template->render("show");
        }
    }

    public function __call($name, $args)
    {
       $this->show($name);
    }

    public function index()
    {
        
    }


}

?>
