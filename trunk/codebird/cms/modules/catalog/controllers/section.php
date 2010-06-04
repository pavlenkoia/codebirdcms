<?php
/**
 *
 */

class CatalogController_Section extends Controller_Base
{

    private function show($name, $args)
    {
        $data = $this->getData();

        $section = $data->getSectionTable()->getEntityAlias($name);

        $this->setContent($section);
	
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
