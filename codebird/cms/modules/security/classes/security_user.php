<?

class Security_User extends Module_Class
{
    public static function  IsLogin()
    {
        $instance = new self;

        $data = $instance->getData();

        return $data->isLogin;
    }

    public static function  GetUser()
    {
        $instance = new self;

        $data = $instance->getData();

        return $data->user;
    }

    public static function inRole($name)
    {
        $instance = new self;

        $data = $instance->getData();

        return $data->inRole($name);
    }

}