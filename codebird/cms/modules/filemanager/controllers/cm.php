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
        if(Utils::getVar('node') == 'root')
        {
            $path = SITE_PATH.'files'.DS.'pages';
            $sd = scandir($path);
            foreach($sd as $file)
            {

            }

        }

        $template = $this->createTemplate();

        $template->files = $files;

        $template->render();
    }
}
