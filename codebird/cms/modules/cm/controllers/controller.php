<?php
/**
 *
 */
class CmController_Controller Extends Controller_Base
{
    public function index()
    {

        $login = val("security.login");

        if($login)
        {
            if(Utils::getVar("mod_action"))
            {
                $this->setContent(val(Utils::getVar("mod_action")));
            }
        }
        else
        {
            header('HTTP/1.0 403 Access allowed only for registered users');
        }
    }
}