<?php
/*
 *
 */

class CatalogController_Action extends Controller_Base
{

    private function action($name, $args)
    {
        $template = $this->createTemplate();

        $template->render();
    }

    public function __call($name, $args)
    {
       $this->action($name, $args);
    }

    public function index()
    {
    }

}

?>
