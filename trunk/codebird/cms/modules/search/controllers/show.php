<?php
/* 
 * Контроллер show gsearch
 */

class SearchController_Show extends Controller_Base
{

    public function index()
    {
    }

    public function form()
    {
        $template = $this->createTemplate();



        $template->render();
    }

    public function result()
    {
        $template = $this->createTemplate();

        $searchquery = Utils::getVar('q') ? Utils::getVar('q') : null;

        $data = $this->getData();

        $result = $data->getResult($searchquery);

        $template->searchquery = $searchquery;

        $template->searchresult = $result;

        if($result)
        {
            $template->results = $result->qry_results;
            $template->resultCount = $result->total_results;
            $template->pages = $result->other_pages;
            $template->currentPageIndex = $result->start;
        }

        $template->alias = Utils::getVar('alias');

        $template->render();
    }
}

?>

