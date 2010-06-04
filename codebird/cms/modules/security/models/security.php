<?php
/**
 *
 */

class SecurityModel_Security extends Model_Base
{

    private $tblUser;

    public function getTableUser()
    {
        if(!$this->tblUser)
        {
            $this->tblUser = new Table('security_user');
        }
        return $this->tblUser;
    }

    public function getUsers($start=null,$limit=null)
    {
        $table = $this->getTableUser();

        $tsql = "select * from security_user";

        if($start != null)
        {
            $lim = " limit ".$start.",".$limit;

            $tsql .= $lim;
        }

        return $table->select($tsql);
    }

    public function getUsersCount()
    {
        $table = $this->getTableUser();
        $res = $table->select("select count(*) as count from security_user");
        $count = $res[0]["count"];
        return $count;
    }
    
    public function getIsLogin()
    {
        if(isset($_SESSION['user']))
        {
            return true;
        }
        return false;
    }

    public function login($name, $password)
    {
        $users = Config::__("security")->users;

        foreach($users as $user)
        {
            if($user['name'] == $name && $user['password'] == $password)
            {
                $user['roles'][] = 'admin';
                $_SESSION['user'] = $user;
                return true;
            }
        }
        
        $password = md5($password);

        $users = $this->getTableUser()->select('select * from security_user where disabled <>1 and name=:name and password=:password limit 1',array('name'=>$name,'password'=>$password));
        if(count($users) > 0)
        {
            $user = $users[0];

            $rows = $this->getTableUser()->select(
                    'select t2.name from security_user_role t1, security_role t2
                    where t1.role_id=t2.id and user_id=:id',array('id'=>$user['id']));

            foreach($rows as $row)
            {
                $user['roles'][] = $row['name'];
            }

            $_SESSION['user'] = $user;

            return true; 
        }

        return false;
    }

    public function logout()
    {
        unset($_SESSION['user']);
    }

    public function getUser()
    {
        if(isset($_SESSION['user']))
        {
            return $_SESSION['user'];
        }
        return null;
    }

    public function inRole($name)
    {
        if(isset($_SESSION['user']))
        {
            $user = $_SESSION['user'];
            if(isset($user['roles']) && in_array($name,$user['roles']))
            {
                return true;
            }
        }
        return false;
    }
}

?>
