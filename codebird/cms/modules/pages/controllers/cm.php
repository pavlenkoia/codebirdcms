<?php
/*
 *
 */

class PagesController_Cm Extends Controller_Base
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

        $template->args = $this->args;

        $template->render("cm_navigator");
    }

    public function treepages()
    {
        $template = $this->createTemplate();

        $id = Utils::getVar("node");

        $table = new Table("pages");

        if(!is_numeric($id))
        {
            $id = null;
            $sql = "select p1.*, count(p1.id) as count, p2.id as id2 from pages p1 left outer join pages p2 on ( p2.parent_id=p1.id) where p1.parent_id is null group by p1.id order by p1.position";
            $template->pages = $table->select($sql);

        }
        else
        {
            $sql = "select p1.*, count(p1.id) as count, p2.id as id2 from pages p1 left outer join pages p2 on ( p2.parent_id=p1.id) where p1.parent_id=:id group by p1.id order by p1.position";
            $template->pages = $table->select($sql,array("id"=>$id));
        }

        $template->render("cm_treepages");
    }

    public function editor()
    {
        $template = $this->createTemplate();
        
        $id = Utils::getVar("id");

        $table = new Table("pages");

        $page = $table->getEntity($id);

        $template->page = $page;

        $plugins = $this->config->plugins_cm;

        $template->plugins = Config::__("pages")->plugins_cm;

        $template->render("cm_editor");
    }

    public function save()
    {
        $template = $this->createTemplate();

        $template->success = 'true';
        $template->msg = 'Страница сохранена.';

        $data = $this->getData('cm_pages');

        $id = Utils::getPost("id");

        $page = $data->getPage($id);

        if($page)
        {
            $title = Utils::getPost("title");
            $title2 = Utils::getPost("title2");
            $alias = Utils::getPost("alias");
            $content = Utils::getPost("content");
            $page_template = Utils::getPost('template');
            $visible = Utils::getPost('visible');
            $mainpage = Utils::getPost('mainpage');
            $meta_keywords = Utils::getPost('meta_keywords');
            $meta_description = Utils::getPost('meta_description');
            $announcement = Utils::getPost('announcement');
            $head_title = Utils::getPost('head_title');
            $redirect = Utils::getPost('redirect');
            $tag = Utils::getPost('tag');

            $page->title = $title;
            $page->title2 = $title2;

            $page->meta_keywords = $meta_keywords;
            $page->meta_description = $meta_description;

            $page->announcement = $announcement;

            $page->head_title = $head_title;

            $page->redirect = $redirect;
            $page->tag = $tag;

            if(!$alias or trim($alias) == "")
            {
                $alias = $title;
            }
            $alias = Utils::translit($alias);
            $alias = Utils::getUniqueAlias($alias, "pages", $page->id);
            $page->alias = $alias;
            
            $page->template = $page_template;

            $page->visible = ($visible and $visible == 1) ? 1 : 0;            

            $plugins = array();
            foreach(Config::__("pages")->plugins_cm as $plugin)
            {
                $param = Utils::getPost('plugin_'.$plugin['name']);
                if($param)
                {
                    array_push($plugins, $param);
                }
            }
            $plugins = implode(";",$plugins);

            $page->plugins = $plugins;            

            if($mainpage == 1)
            {
                $data->setMainPage($page->id);
                $page->mainpage = 1;
            }

            if($content !== null)
            {
                $page->content = $content;
            }

            $data->save($page);
        }
        else
        {
            $template->success = 'false';
            $template->msg = 'Сохраняемой страницы не существует.';
        }

        $template->render("cm_save");
    }

    public function reorder()
    {
        $id = Utils::getPost("id");
        $parent_id = Utils::getPost("parent_id");
        $index = Utils::getPost("index");

        $data = $this->getData('cm_pages');

        $page = $data->getPage($id);

        if($page)
        {
            $page->parent_id = is_numeric($parent_id) ? $parent_id : null;
            $data->save($page);
            $data->reorderPages($page->id, $page->parent_id, $index);
        }

        $this->registry->mod_content = "{id:'$id',parent_id:'$parent_id',index:'$index'}";
    }

    public function add()
    {
        $id = Utils::getPost("id");

        $data = $this->getData('cm_pages');

        $page = $data->getPage();

        $title = Utils::getPost("title");
        $page->title = $title;
        $page->parent_id = $id;
        $page->visible = 1;
        $page->position = count($data->getPages($id))+1;
        $alias = Utils::translit($title);
        $alias = Utils::getUniqueAlias($alias, "pages");
        $page->alias = $alias;
        $page->mainpage = 0;
        $page->template = $this->config->default_template;

        $errorInfo = $data->save($page);

        $this->registry->mod_content = "{errorInfo:'$errorInfo'}";
    }

    public function delete()
    {
        $id = Utils::getPost("id");

        $data = $this->getData('cm_pages');

        $page = $data->getPage($id);

        if($page)
        {
            $data->delete($page);

//            $this->registry->mod_content = "{errorInfo:'$errorInfo'}";
        }  
    }

    public function uploadimage()
    {
        $res = array();

        try
        {
            $id = Utils::getVar('id');

            $data = $this->getData('cm_pages');

            $object = $data->getPage($id);

            if($object)
            {
                if(isset($_FILES["file"]))
                {
                    $config = $this->config;

                    $image_width = $config->image_width;
                    $image_height = $config->image_height;
                    $image_path = $config->image_path;

                    $img_src = "img_page_".$id.".jpg";

                    $img_file = SITE_PATH.$image_path.DS.$img_src;

                    Utils::img_resize($_FILES["file"]["tmp_name"], $img_file, $image_width, $image_height);

                    $object->img_src = $img_src;

                    $data->save($object);

                    $res['success'] = true;
                    $res['msg'] = 'Картинка загружена';
                    $res['src'] = "$image_path/$img_src";


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
