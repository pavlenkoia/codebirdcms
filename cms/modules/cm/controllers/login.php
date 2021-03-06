<?php
/**
 *
 */
class CmController_Login Extends Controller_Base
{
    public function index()
    {
//        $_SESSION['user'] = true;
        $res = array();
        
        $name = Utils::getPost('name');
        $password = Utils::getPost('password');
        $store = Utils::getPost('store')==1 ? true : false;

        $login = val("security.login.login", array("name"=>$name,"password"=>$password,'store'=>$store)) && (val("security.inrole.admin") || val("security.inrole.user"));

        if($login)
        {
            $res['success'] = true;
            $res['msg'] = '/cm/';
        }
        else
        {
            $res['success'] = false;
            $res['msg'] = 'Неверное имя или пароль';
        }

        $this->setContent(json_encode($res));
    }

    public function logout()
    {
        val("security.login.logout");

        $res = array();
        $res['success'] = true;
        $res['msg'] = '/cm/';

        $this->setContent(json_encode($res));
    }

    public function form()
    {
        $template = $this->createTemplate();

        $template->render();
    }

    public function form_login()
    {
        $template = $this->createTemplate();

        $template->render();
    }

    public function form_logout()
    {
        $template = $this->createTemplate();

        $template->render();
    }
}
