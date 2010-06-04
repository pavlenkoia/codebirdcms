<?php
/**
 *
 */

 class SecurityController_Login Extends Controller_Base
{

    public function index()
    {
        $data = $this->getData();

        $this->setContent($data->isLogin);
    }

    public function login()
    {
        $data = $this->getData();

        $res = $data->login($this->args->name, $this->args->password);

        $this->setContent($res);
    }

    public function logout()
    {
        $data = $this->getData();

        $data = $this->getData();

        $data->logout();
    }

    public function user()
    {
        $data = $this->getData();

        $this->setContent($data->user);
    }
}

?>
