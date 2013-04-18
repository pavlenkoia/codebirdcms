<?php
class CatalogController_Config extends Controller_Base
{
    public function access()
    {
        return $this->login();
    }

    public function index()
    {
    }

    public function editor()
    {
        $template = $this->createTemplate();

        $template->render();
    }
}
?>