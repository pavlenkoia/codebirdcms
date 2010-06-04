<?php
/* 
 * Модель авторизации
 */

Class LoginModel_Authorization extends Model_Base
{

    public function login($login, $password)
    {

        if($login==$this->registry->adm_login and $password==$this->registry->adm_passw)
        {
            $_SESSION['adm_switch']='ON';
            return true;
        }

        return false;
    }

    public function getIsLogin()
    {
        if(isset($_SESSION['adm_switch']) and $_SESSION['adm_switch']=='ON')
        {
            return true;
        }
        return false;
    }

}

?>
