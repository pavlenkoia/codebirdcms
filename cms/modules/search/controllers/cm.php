<?php
/*
 * Контроллер cm
 */

class SearchController_Cm extends Controller_Base
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

        $template->sites_rows = $data->getSites();

        $template->render();
    }

    public function editor()
    {
        $id = Utils::getVar('id');

        $id = str_replace('site_', '', $id);

        $data = $this->getData();

        $site = $data->getSite($id);

        $template = $this->createTemplate();

        $template->site = $site;

        $template->status = 'Не индексировано';
        if($site['indexdate'])
        {
            $template->status =  $site['pending']?'Индексирование не закончено':'Проиндексировано';
        }

        $template->links_count = $site['links_count'];

        $template->render();
    }

    public function delete()
    {
        $id = Utils::getVar('id');

        $id = str_replace('site_', '', $id);

        $data = $this->getData();

        $data->deleteSite($id);

        $res = array();

        $res['success'] = true;

        $this->setContent(json_encode($res));
    }

    public function add()
    {
        $url = Utils::getVar('url');

        $data = $this->getData();

        $site_id = $data->addSite($url);

        $res = array();

        if($site_id)
        {
            $res['success'] = true;
            $res['id'] = $site_id;
        }
        else
        {
            $res['success'] = false;
        }



        $this->setContent(json_encode($res));
    }

}