<?php
/*
 *
 */

class CatalogController_Action extends Controller_Base
{

    private function action($name, $args)
    {
        $template = $this->createTemplate();

        $data = $this->getData('action');

        $template->data = $data;

        $alias = Utils::getVar('alias');

        $uri = Utils::getVar('uri');

        $params = explode("/",$uri);

        $params = array_pad($params,10,null);

        $template->alias = $alias;

        $template->params = $params;

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
