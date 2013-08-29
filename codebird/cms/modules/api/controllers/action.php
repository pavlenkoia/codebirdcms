<?php
/*
 *
 */

class ApiController_Action extends Controller_Base
{
    public function index()
    {
        $uri = Utils::getVar('uri');

        $params = explode("/",$uri);

        if($params[0])
        {
            $val = val('catalog.action.api_'.$params[0]);

            $this->setContent($val);
        }
    }

}

?>
