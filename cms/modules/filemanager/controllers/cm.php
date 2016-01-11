<?php
/*
 * Контроллер cm
 */

class FilemanagerController_Cm extends Controller_Base
{
    public function access()
    {
        return $this->login();
    }

    public function index()
    {
    }

    public function editor()
    {
        $template = $this->createTemplate();
        
        $template->render();
    }

    public function tree()
    {
        $files = array();

        if(Utils::getVar('node') != 'root')
        {
            $parent = '';
            if(Utils::getVar('node') == '/')
            {
                $path = SITE_PATH.'files'.DS.'pages';
            }
            else
            {
                $parent = Utils::getVar('node');
                $path = SITE_PATH.'files'.DS.'pages'.DS.$parent;
            }

            $sd = scandir($path);
            foreach($sd as $file)
            {
                $cur = array();
                /*if (is_file($path.DS.$file))
                {
                     $cur['name'] = $file;
                     $cur['type'] = 'file';
                     $files[] = $cur;
                }
                else*/if(is_dir($path.DS.$file) && $file !== "." && $file !== ".." && $path != SITE_PATH)
                {
                    $cur['name'] = $file;
                    $cur['parent'] = $parent;
                    $cur['type'] = 'folder';
                    $files[] = $cur;
                }                
            }
        }

        $template = $this->createTemplate();

        $template->files = $files;

        $template->render();
    }

    public function files()
    {
        $files = array();

        if(Utils::getVar('node') != 'root')
        {
            $parent = '';
            if(Utils::getVar('node') == '/')
            {
                $path = SITE_PATH.'files'.DS.'pages';
            }
            else
            {
                $parent = Utils::getVar('node');
                $path = SITE_PATH.'files'.DS.'pages'.DS.$parent;
            }

            $sd = scandir($path);
            foreach($sd as $file)
            {
                $cur = array();
                if (is_file($path.DS.$file))
                {
                     $cur['name'] = $file;
                     $cur['url'] = 'files'.DS.'pages'.$parent.DS.$file;
                     $files[] = $cur;
                }                
            }
        }

        $template = $this->createTemplate();

        $template->files = $files;

        $template->render();
    }

    public function delete_file()
    {
        $res = array();

        try
        {
            $id = Utils::getVar('id');

            $ids = explode(';', $id);

            foreach($ids as $id)
            {
                $file = SITE_PATH.$id;
                if (is_file($file) && is_writable($file))
                {
                    unlink($file);
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
