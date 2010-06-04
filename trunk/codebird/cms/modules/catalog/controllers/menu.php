<?php
/*
 *
 */

class CatalogController_Menu extends Controller_Base
{

    private function show($name, $args)
    {
        $template = $this->createTemplate();

        $data = $this->getData();

        $table = $data->getSectionTable();

        $section = $table->getEntityAlias($name);

        $template->section = $section;

        $template->data = $data;

        $template->render();
    }

    public function __call($name, $args)
    {
       $this->show($name, $args);
    }

    public function index()
    {
    }

}

?>

