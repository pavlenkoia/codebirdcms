<?php
/*
 *
 */

class CmController_App Extends Controller_Base
{

    public function index()
    {
    }

    public function navigator()
    {
        $templates = $this->createTemplate();

        $templates->render("app_navigator");
    }

    public function appecho()
    {

    }
}

?>
