<?php
/* 
 * 
 */

class SitemapController_Show extends Controller_Base
{

    public function index()
    {
        $template = $this->createTemplate();

        $template->render();
    }
}

?>
