<?php
/**
 *
 */

class CmController_Cm Extends Controller_Base
{

    public function index()
    {
    }

    public function manager_tree()
    {
        $template = $this->createTemplate();
        $template->render();
    }
}

?>
