<?php

Class LoginController_Controller Extends Controller_Base
{    
    
    function index()
    {     
        $data = $this->getData('Authorization');
        
        if(!$data->isLogin)
        {
            $adm_l = isset($_POST['adm_l']) ? $_POST['adm_l'] : '';
            $adm_p = isset($_POST['adm_p']) ? $_POST['adm_p'] : '';
            $data->login($adm_l,$adm_p);
        }

        if(!$data->isLogin)
        {
            $template = $this->createTemplate();
            $template->registry = $this->registry;
            $template->render("view");
            echo $this->registry->mod_content;
            $this->registry->login = false;
        }
        else
        {
            $this->registry->login = true;
        }
    } 

}

?>
