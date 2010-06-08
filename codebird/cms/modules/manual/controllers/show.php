<?php
/**
 *
 */

class ManualController_Show Extends Controller_Base
{

     private function show($name, $args)
    {
        $table = new Table('section_manual');

        $section = $table->getEntity($name);

        if(!$section) return;
        
        $this->setContent($section->content);

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

