<?php
/* 
 * 
 */

class PagesController_Admin Extends Controller_Base
{

    public function index()
    {
        $template = $this->createTemplate();

        $data = $this->getData('Pages');

        if(Utils::getVar('p_id'))
        {
            $parent_page = $data->getPage(Utils::getVar('p_id'));
            $template->parent_page = $parent_page;
        }

        $template->data = $data;

        $template->render("add_page");
    }

    public function edit()
    {
        $template = $this->createTemplate();

        $data = $this->getData('Pages');

        $template->data = $data;

        $template->page = $data->getPage(Utils::getVar('id'));

        $template->render("edit_page");
    }

    public function save()
    {
        $template = $this->createTemplate();

        $data = $this->getData('Pages');

        $id = Utils::getVar('id') ? Utils::getVar('id') : '-1';

        $page = $data->getPage(Utils::getVar('id'));

        $title = Utils::getPost('title');
        $page->title = $title;
        $page->content = Utils::getPost('content');
        $page->alias = Utils::getPost('alias');
        $page->template = Utils::getPost('template');
        
        $visible = Utils::getPost('visible');
        $page->visible = ($visible and $visible == 1) ? 1 : 0;

        if(Utils::getPost('mainpage') and isset($page->id))
        {
            $data->setMainPage($page->id);
        }

        if(Utils::getVar('p_id'))
        {
            $page->parent_id = Utils::getVar('p_id');
        }

        $plugins = array();
        foreach(Config::__("pages")->plugins as $plugin)
        {
            $param = Utils::getPost('plugin_'.$plugin['name']);
            if($param)
            {
                array_push($plugins, $param);
            }
        }
        $plugins = implode(";",$plugins);

        $page->plugins = $plugins;

        if(!Utils::getPost('title') or trim(Utils::getPost('title')) == '')
        {
            $template->error_message = "Не задан заголовок";
            $template->data = $data;
            $template->page = $page;
            $template->render(Utils::getVar('mod_view'));
            return;
        }      

        $id = $data->save($page);

        $template->data = $data;

        $template->page = $data->getPage($id);

        $template->info_message = "Сохранено";

        $template->render("edit_page");
    }

    private function deleter_R($i)
    {
        $fs = scandir($i);
        foreach($fs as $f)
        {
            if (is_file($i.DS.$f) && is_writable($i.DS.$f))
            {
                unlink($i.DS.$f);
            }
            elseif(is_dir($i.DS.$f) && $f !== "." && $f !== ".." && $i != SITE_PATH)
            {
                $this->deleter_R($i.DS.$f);
            }

        }
    }

    public function deleter()
    {
        $id = explode(";", Utils::getVar("id"));
        foreach($id as $i)
        {
            $this->deleter_R(SITE_PATH.trim($i));
        }
    }

    public function delete()
    {
        $id = Utils::getVar('id');

        $data = $this->getData('Pages');

        $template = $this->createTemplate();

        $page = $data->getPage($id);

        if(isset($page->id))
        {
            $data->delete($page);
        }

        $template->data = $data;
        $template->render("add_page");
    }

    private function up($pages, $id)
    {
        $pos = 1;
        $positions = array();
        foreach($pages as $row)
        {
            if(isset($pre_id) and $row['id'] == $id)
            {
                $positions[$pos-1] = $id;
                $positions[$pos] = $pre_id;
            }
            else
            {
                $positions[$pos] = $row['id'];
            }
            $pre_id = $row['id'];
            $pos++;
        }
        return $positions;
    }

    private function down($pages, $id)
    {
        $pos = 1;
        $positions = array();
        foreach($pages as $row)
        {
            if(isset($pre_id) and $pre_id == $id)
            {
                $positions[$pos] = $pre_id;
                $positions[$pos-1] = $row['id'];
            }
            else
            {
                $positions[$pos] = $row['id'];
            }
            $pre_id = $row['id'];
            $pos++;
        }
        return $positions;
    }

    private function move($dir)
    {
        $template = $this->createTemplate();

        $id = Utils::getVar('id');

        $data = $this->getData('Pages');

        $page = $data->getPage($id);

        $data->parent_id = $page->parent_id;
        $pages = $data->getPages();

        $data->savePositions($dir == 'up' ? $this->up($pages,$id) : $this->down($pages,$id));

        $template->data = $data;
        $template->page = $page;
        $template->render(Utils::getVar('mod_view') ? Utils::getVar('mod_view') : "add_page");
    }
    
    public function moveup()
    {
        $this->move('up');
    }

    public function movedown()
    {
        $this->move('down');
    }
}

?>
