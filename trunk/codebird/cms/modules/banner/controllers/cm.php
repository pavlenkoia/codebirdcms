<?php
/*
 * Контроллер cm баннера
 */

class BannerController_Cm extends Controller_Base
{
    public function access()
    {
        return $this->login();
    }

    public function index()
    {
    }

    public function navigator()
    {
        $template = $this->createTemplate();

        $template->render();
    }

    public function editor()
    {
        $id = Utils::getVar('id');

        $data = $this->getData();

        $object = $data->getObject($id);

        if($object)
        {
            $template = $this->createTemplate();
            $template->banner = $object;
            $template->render();
        }
    }

    public function tree()
    {
        $template = $this->createTemplate();

        $data = $this->getData();

        $banners = $data->banners;

        $template->banners = $banners;

        $template->render();
    }

    public function add()
    {
        $res = array();

        try
        {
            $data = $this->getData();
            $object = $data->getObject();

            $object->alias = Utils::getPost('alias');
            $object->name = Utils::getPost('name');

            $data->save($object);

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

    public function save()
    {
        $res = array();

        try
        {
            $data = $this->getData();

            $id = Utils::getVar('id');

            $object = $data->getObject($id);

            if($object)
            {
                $object->name = Utils::getPost('name');
                $object->alias = Utils::getPost('alias');
                $object->html = Utils::getPost('html');

                $data->save($object);
                $errorInfo = $data->table->errorInfo;
                if($errorInfo)
                {
                    throw new Exception($errorInfo);
                }
                $cache = SITE_PATH.'files'.DS.'banner'.DS.$object->alias;
                if(is_file($cache))
                {
                    unlink($cache);
                }
            }
            else
            {
                throw new Exception('Объект уже удален');
            }
            $res['success'] = true;
            $res['msg'] = 'Сохранено';
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }

        $this->setContent(json_encode($res));
    }

    public function delete()
    {
        $res = array();

        try
        {
            $data = $this->getData();

            $id = Utils::getVar('id');

            $object = $data->getObject($id);

            if($object)
            {
                $data->table->delete($object);
                $errorInfo = $data->table->errorInfo;
                if($errorInfo)
                {
                    throw new Exception($errorInfo);
                }
            }
            else
            {
                throw new Exception('Объект уже удален');
            }
            $res['success'] = true;
            $res['msg'] = 'Удалено';
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
