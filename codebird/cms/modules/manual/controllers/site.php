<?php
/*
 *
 */

class ManualController_Site Extends Controller_Base
{
    public function index()
    {
    }

    public function template()
    {
        $this->setContent('/cms/modules/manual/html/templates/content.tpl.php');
    }
}
?>
