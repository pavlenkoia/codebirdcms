<?php
/* 
 * 
 */

class CatalogController_Show extends Controller_Base
{
    
    private function show($name, $args)
    {
        $template = $this->createTemplate();
        
        $template->render();
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
