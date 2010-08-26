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

            $domain = $this->config->source;

            

//            $content2 = preg_replace('/(href=")(.*)(")/#/Ui', "$1$domain$2$3", $content);

            $content = preg_replace('/(<img.*src=")(.*)(")/Ui', "$1$domain$2$3", $content);
            
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

