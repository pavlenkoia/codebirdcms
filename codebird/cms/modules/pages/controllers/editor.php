
<?php
/*
 * Контроллер editor страниц
 */
class CatalogController_Editor extends Controller_Base
{
    private function editor_access()
    {
        return $this->login();
    }

    public function index()
    {
    }
}

?>
