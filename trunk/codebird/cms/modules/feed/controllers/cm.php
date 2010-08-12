<?php
/*
 * Контроллер cm фида
 */

class FeedController_Cm extends Controller_Base
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

    public function tree()
    {
        $template = $this->createTemplate();

        $data = $this->getData();

        $template->feeds = $data->feeds;

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
            $template->feed = $object;
            $template->render();
        }
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
            $object->url = Utils::getPost('url');
            $object->interval_update = $this->config->interval_update;

            $id = $data->save($object);

            $res['success'] = true;
            $res['msg'] = 'Добавлено';
            $res['id'] = $id;
            $res['title'] = $object->name;
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
                $object->url = Utils::getPost('url');
                $object->interval_update = Utils::getPost('interval_update');
                $object->datestamp_update = null;

                $data->save($object);
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
                $data->table->execute("delete from feed_item where feed_id=:feed_id", array("feed_id"=>$object->id));
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
