<?php
/*
 * Контроллер cm безопасности
 */

class SecurityController_Cm extends Controller_Base
{
    public function access()
    {
        return $this->login();
    }

    public function index()
    {
    }

    public function editor_users()
    {
        $template = $this->createTemplate();

        $template->render();
    }

    public function manager_tree()
    {
        $template = $this->createTemplate();

        $data = $this->getData();

        $template->data = $data;

        $template->render();
    }

    public function users()
    {
        $template = $this->createTemplate();

        $data = $this->getData();

        $start = Utils::getVar('start');
        $limit = Utils::getVar('limit');

        $template->rows = $data->getUsers($start,$limit);
        $template->count = $data->getUsersCount();

        $template->render();
    }

    public function add_user_form()
    {
        $template = $this->createTemplate();

        $table = new Table('security_role');

        $roles = $table->select('select * from security_role');

        $template->roles = $roles;

        $template->render();
    }

    public function add_user()
    {
        $res = array();
        try
        {
            $name = Utils::getPost('name');

            if(!$name)  throw new Exception('Не задан логин');

            $name = trim($name);

            $table = new Table('security_user');

            $user = $table->getEntityAlias($name, 'name');

            if($user && strtolower($name) == strtolower($user->login))  throw new Exception('Пользователь с таким именем уже зарегистрирован');

            $pass = Utils::getPost('pass');

            $pass2 = Utils::getPost('pass2');

            if($pass != $pass2) throw new Exception('Пароль и подтверждение не совпадают');

            $user = $table->getEntity();

            $user->name = $name;

            $user->password = md5($pass);

            $user->disabled = 0;

            $id = $table->save($user);

            if($table->errorInfo) throw new Exception($table->errorInfo);

            $roles = $table->select('select * from security_role');

            foreach($roles as $role)
            {
                $var = Utils::getPost('role_'.$role['id']);
                if($var == 1)
                {
                    $table->execute('insert into security_user_role(user_id,role_id) values(:user_id,:role_id)',
                            array('user_id'=>$id,'role_id'=>$role['id']));
                }
            }

            $res['success'] = true;
            $res['msg'] = 'Добавлено';
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }

        $this->setContent(json_encode($res));
    }

    public function delete_user()
    {
        $res = array();
        try
        {
            $id = Utils::getVar('id');

            $id = trim($id, ',');

            $table = new Table('security_user');

            $table->execute('delete from security_user_role where user_id in('.$id.')');

            if($table->errorInfo) throw new Exception($table->errorInfo);

            $table->execute('delete from security_user where id in('.$id.')');

            if($table->errorInfo) throw new Exception($table->errorInfo);

            $res['success'] = true;
            $res['msg'] = 'Готово';
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }

        $this->setContent(json_encode($res));
    }

    public function edit_user_form()
    {
        $template = $this->createTemplate();

        $id = Utils::getPost('id');

        $table = new Table('security_user');

        $user = $table->getEntity($id);

        $template->user = $user;

        $roles = $table->select('select * from security_role');

        $template->roles = $roles;

        $res = $table->select('select role_id from security_user_role where user_id=:id',array('id'=>$user->id));

        $user_roles = array();
        foreach($res as $row)
        {
            $user_roles[] = $row['role_id'];
        }

        $template->user_roles = $user_roles;

        $template->render();
    }

    public function edit_user()
    {
        $res = array();
        try
        {
            $id = Utils::getPost('id');

            $table = new Table('security_user');

            $user = $table->getEntity($id);

            if(!$user) throw new Exception('Объект уже удален');

            $pass = Utils::getPost('pass');

            $pass2 = Utils::getPost('pass2');

            if($pass)
            {
                if($pass != $pass2) throw new Exception('Пароль и подтверждение не совпадают');

                $user->password = md5($pass);
            }

            $disabled = Utils::getPost('disabled');

            $user->disabled = $disabled == 1 ? 1 : 0;

            $table->save($user);

            $errorInfo = $table->errorInfo;

            if($errorInfo)
            {
                throw new Exception($errorInfo);
            }
            
            $table->execute('delete from security_user_role where user_id=:id',array('id'=>$user->id));

            $roles = $table->select('select * from security_role');

            foreach($roles as $role)
            {
                $var = Utils::getPost('role_'.$role['id']);
                if($var == 1)
                {
                    $table->execute('insert into security_user_role(user_id,role_id) values(:user_id,:role_id)',
                            array('user_id'=>$id,'role_id'=>$role['id']));
                }
            }

            $res['success'] = true;
            $res['msg'] = 'Готово';
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }

        $this->setContent(json_encode($res));
    }
}

?>
