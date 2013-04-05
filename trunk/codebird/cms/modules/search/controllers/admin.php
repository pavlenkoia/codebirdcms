<?php
    /*
    * Контроллер show search
    */

class SearchController_Admin extends Controller_Base
{
    public function index()
    {
    }

    public function reindex()
    {
        $template = $this->createTemplate();

        $data = $this->getData();

        $site = $this->config->site;

        $site = $site ? $site : $_SERVER['HTTP_HOST'];

        $template->domain = $site;

        $template->render();
    }
}


?>