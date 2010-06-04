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
