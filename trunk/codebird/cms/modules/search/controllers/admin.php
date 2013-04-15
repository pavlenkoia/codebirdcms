<?php
    /*
    * Контроллер show search
    */

class SearchController_Admin extends Controller_Base
{
    public function index()
    {
    }

    public function reindex()
    {
        $template = $this->createTemplate();

        $data = $this->getData();

        $site = $this->config->site;

        $site = $site ? $site : $_SERVER['HTTP_HOST'];

        $template->domain = $site;

        $template->data = $data;

        $template->render();
    }

    public function exec_index()
    {
        set_time_limit (0);

        $site_id = Utils::GetVar("id");

        $data = $this->getData();

        $row = $data->getSite($site_id);

        $content = '';
        $pending = false;
        $indexdate = '';
        $status = '';
        $links_count = '';

        if($row)
        {
            $domain = str_replace("http:",'',$row['url']);
            $domain = str_replace("/",'',$domain);

            $content = $data->httpIndex($domain,true);

            if($row2 = $data->getSite($row['id']))
            {
                if($row2['pending'])
                {
                    $pending = true;
                    $status = 'Индексирование не закончено';
                }
                else
                {
                    $status = 'Проиндексировано';
                }
                $indexdate = $row2['indexdate'];
                $links_count = $row2['links_count'];
            }
        }

        $template = $this->createTemplate();

        $template->pending = $pending;
        $template->content = $content;
        $template->indexdate = $indexdate;
        $template->status = $status;
        $template->links_count = $links_count;

        $template->render();
    }

    public function status()
    {
        $site_id = Utils::GetVar("id");

        $data = $this->getData();

        $row = $data->getSite($site_id);

        $indexdate = '';
        $status = '';
        $pending = false;
        $links_count = '';

        if($row)
        {
            if($row['pending'])
            {
                $status = 'Индексирование не закончено';
                $pending = true;
            }
            else
            {
                $status = 'Проиндексировано';
            }
            $indexdate = $row['indexdate'];
            $links_count = $row['links_count'];
        }

        $res = array();
        $res['indexdate'] = $indexdate;
        $res['status'] = $status;
        $res['pending'] = $pending;
        $res['links_count'] = $links_count;

        $this->setContent(json_encode($res));
    }


}


?>