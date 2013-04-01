<?

class Security_User extends Module_Class
{
    public static function Test()
    {
        $instance = new self;

        $data = $instance->getData();

        if(!App::Get_Cookie('sess'))
        {
            echo 'none';
            echo App::Set_Cookie('sess','123')?' да':' нет';
        }

        return App::Get_Cookie('sess');

        //return sha1('root'.'397567'.time());

        //return $instance->GetModule();

        //return '<p>Тест: '.'<pre>'.print_r($data->user).'</pre></p>';
    }

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

}