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

        $template->render();
    }

}