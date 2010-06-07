<?php
/*
 * Контроллер editor каталога
 */
class CatalogController_Editor extends Controller_Base
{
    public function access()
    {
        return $this->login();
    }

    public function index()
    {
    }

    public function header()
    {
        $template = $this->createTemplate();
        $template->render();
    }
}
