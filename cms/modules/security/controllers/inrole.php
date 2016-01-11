<?php
/**
 * 
 */

class SecurityController_Inrole extends Controller_Base
{

    private function inrole($name)
    {
        $data = $this->getData();

        $res = $data->inRole($name);

        $this->setContent($res);
    }

    public function __call($name, $args)
    {
       $this->inrole($name);
    }

    public function index()
    {

    }


}

?>

