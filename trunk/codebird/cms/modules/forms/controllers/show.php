<?php
/*
 *
 */

class FormsController_Show extends Controller_Base
{
    private function show($name, $args)
    {
        $this->setContent(val('catalog.forms.show',array('form'=>$name,'view'=>'forms.show.'.$name)));
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
