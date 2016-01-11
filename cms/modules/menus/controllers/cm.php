<?php
/*
 * Контроллер cm меню
 */

class MenusController_Cm extends Controller_Base
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

        $menus = $data->menus;

        $template->menus = $menus;

        $template->render();
    }

    public function tree_items()
    {
        $template = $this->createTemplate();

        $data = $this->getData();

        $parent_id = Utils::getVar("node");

        $menus_id = Utils::getVar("menus_id");

        $items = $data->getItems($menus_id, is_numeric($parent_id) ? $parent_id : null);

        $template->items = $items;

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
            $template->menu = $object;
            $template->render();
        }
    }

    public function editor_item()
    {
        $id = Utils::getVar('id');

        $data = $this->getData();

        $object = $data->getItem($id);

        if($object)
        {
            $template = $this->createTemplate();
            $template->item = $object;

            $type_label = '&lt; не выбрано &gt;';
            $type_link = '';

            $type = $object->type;
            if($type == "_pages")
            {
                $table = new Table('pages');
                $page = $table->getEntity($object->type_id);
                if($page)
                {
                    $type_label = "Страница: ".$page->title;
                    $type_link = '/'.$page->alias.'.html';
                }
            }
            elseif($type == "_link")
            {
                $type_label = "Внешняя ссылка";
                $type_link = $object->type_link;
            }
            elseif($type == "_label")
            {
                $type_label = "Просто заголовок";
            }
            elseif($type)
            {
                $modules = val("modmanager.cm.modules");
                if(array_key_exists($type,$modules))
                {
                    $type_label = "Модуль: ".$modules[$type]['title'];
                    $type_link = '/'.$type.'/';
                }
            }

            $template->type_label = $type_label;
            $template->type_link = $type_link;

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

            $object->title = Utils::getPost('title');
            $object->name = Utils::getPost('name');

            $data->save($object);

            $errorInfo = $data->getTable()->errorInfo;

            if($errorInfo)
            {
                throw new Exception($errorInfo);
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

    public function rename()
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
            $res['msg'] = 'Пареименовано';
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
                $errorInfo = $data->delete($object);
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

    public function add_item()
    {
        $res = array();

        try
        {
            $title = Utils::getPost('title');

            if($title === null || trim($title) == '')
            {
                throw new Exception('Заголовок не указан');
            }

            $menus_id = Utils::getPost('menus_id');

            $parent_id = Utils::getPost('parent_id');

            $data = $this->getData();

            $object = $data->getItem();

            $object->title = $title;
            $object->menus_id = $menus_id;
            $object->visible = 1;
            if($parent_id)
            {
                $object->parent_id = $parent_id;
                $object->position = count($data->getItems($menus_id, $parent_id))+1;
            }
            else
            {
                $object->position = count($data->getItems($menus_id))+1;
            }

            $errorInfo = $data->saveItem($object);
            if($errorInfo)
            {
                throw new Exception($errorInfo);
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

    public function reorder()
    {
        $res = array();

        try
        {
            $data = $this->getData();

            $id = Utils::getVar('id');

            $object = $data->getItem($id);

            if($object)
            {
                $parent_id = Utils::getPost("parent_id");
                $index = Utils::getPost("index");

                $object->parent_id = is_numeric($parent_id) ? $parent_id : null;
                $data->saveItem($object);
                $data->reorderItems($object, $index);
            }
            else
            {
                throw new Exception('Объект уже удален');
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

    public function delete_item()
    {
        $res = array();

        try
        {
            $data = $this->getData();

            $id = Utils::getVar('id');

            $object = $data->getItem($id);

            if($object)
            {
                $errorInfo = $data->deleteItem($object);
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

    public function save_item()
    {
        $res = array();

        try
        {
            $data = $this->getData();

            $id = Utils::getVar('id');

            $object = $data->getItem($id);

            if($object)
            {
                $object->title = Utils::getPost('title');
                $object->visible = (Utils::getPost('visible') == 1) ? 1 : 0;
                $object->type = Utils::getPost('type');
                $type_id = Utils::getPost('type_id');
                $object->type_id = is_numeric($type_id) ? $type_id : null;
                $object->type_link = Utils::getPost('type_link');
                $object->attr = Utils::getPost('attr');

                $errorInfo = $data->saveItem($object);
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

    public function tree_types()
    {
        $template = $this->createTemplate();

        $template->render();
    }

    public function form_menu()
    {
        $template = $this->createTemplate();

        $data = $this->getData();

        $id = Utils::getVar('id');

        $object = $data->getObject($id);

        $template->menu = $object;

        $template->render();
    }

    public function save_menu()
    {
        $res = array();

        try
        {
            $data = $this->getData();

            $id = Utils::getVar('id');

            $object = $data->getObject($id);

            if($object)
            {
                $object->title = Utils::getPost('title');
                $object->name = Utils::getPost('name');

                $data->save($object);
                $errorInfo = $data->getTable()->errorInfo;
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
            $res['msg'] = 'Готово';
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }

        $this->setContent(json_encode($res));
    }

    public function uploadimage()
    {
        $res = array();
        try
        {
            $data = $this->getData();
            $id = Utils::getVar('id');
            $object = $data->getItem($id);

            if($object)
            {
                if(isset($_FILES["file"]))
                {
                    $ext = '.jpg';
                    switch ($_FILES['file']['type'])
                    {
                        case 'image/jpeg':
                            $ext = '.jpg';
                            break;

                        case 'image/bmp':
                            $ext = '.bmp';
                            break;

                        case 'image/png':
                            $ext = '.png';
                            break;

                        case 'image/jpg':
                            $ext = '.jpg';
                            break;

                        case 'image/gif':
                            $ext = '.gif';
                            break;

                        default:
                            //$ext = '.';
                    }

                    if($ext == '.bmp' || $ext == '.')
                    {
                        throw new Exception('Картинка такого формата не поддерживается');
                    }

                    $img_src = "menu_item_".$id.md5(uniqid(rand(0, 1000000))).$ext;

                    $upload_path = SITE_PATH.'files/menus/upload/'.$img_src;
                    move_uploaded_file($_FILES['file']['tmp_name'], $upload_path);

                    $object->img_src = 'files/menus/upload/'.$img_src;
                    $data->saveItem($object);

                    $res['success'] = true;
                    $res['msg'] = 'Картинка загружена';
                    $res['src'] = get_cache_pic($object->img_src,100,100,true,'','files/menus/cache/');
                    $res['id'] = $id;
                }
                else
                {
                    $res['success'] = false;
                    $res['msg'] = 'Нет файла для загрузки';
                }
            }
            else
            {
                $res['success'] = false;
                $res['msg'] = 'Страница уже удалена';
            }
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
