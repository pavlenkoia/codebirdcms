<?php
/*
 *
 */

class CmController_Site Extends Controller_Base
{
    public function index()
    {
    }

    public function template()
    {
        $is_login = val("security.login") && (val("security.inrole.admin") || val("security.inrole.user"));
        
        if($is_login)
        {
            $this->setContent($this->config->template);
        }
        else
        {
            $this->setContent($this->config->template_login);
        }
    }
}
?>