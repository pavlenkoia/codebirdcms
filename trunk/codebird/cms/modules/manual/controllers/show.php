<?php
/**
 *
 */

class ManualController_Show Extends Controller_Base
{

     private function show($name, $args)
    {
        if($this->config->source)
        {
            $content = file_get_contents($this->config->source.'/manual/content/'.$name.'/');
            
            $this->setContent($content);
        }
        else
        {
            $table = new Table('section_manual');
            
            $section = $table->getEntity($name);
            
            if(!$section) return;
            
            $this->setContent($section->content);
        }


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

