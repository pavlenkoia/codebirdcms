<?php
/*
* Контроллер cm SEO
*/

class SeoController_Cm extends Controller_Base
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
        $file_content ='';
        $filename = Utils::getVar('filename');
        try{
             $file_handle = fopen($filename, "r+");
        
             if($file_handle){
                while (!feof($file_handle)) {
                    $line = fgets($file_handle);
                    $file_content.= $line;
                }
                fclose($file_handle);
             }
        }catch(Exception $e){}
       
        $object['file_content'] = $file_content;
        $object['file_name'] = $filename;
        
        $template = $this->createTemplate();
        $template->seo = $object;
        $template->render(); 
    }

    public function tree()
    {
        $template = $this->createTemplate();
        $data = $this->getData();
        $seos = $data->seos;
        $template->seos = $seos;
        $template->render();
    }

   
    public function save()
    {
        try {
            $file_content = Utils::getPost('html');
            $file_name = Utils::getPost('filename');
            $file_handle = fopen($file_name, "w");
            if($file_handle){
            fwrite($file_handle, $file_content); 
            }
            fclose($file_handle);        
        }catch(Exception $e){}
        
        $res['success'] = true;
        $res['msg'] = 'Сохранено';
        $this->setContent(json_encode($res));
    }

  
}

?>
