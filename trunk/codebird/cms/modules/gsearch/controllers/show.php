<?php
/* 
 * Контроллер show gsearch
 */

class GsearchController_Show extends Controller_Base
{

    public function index()
    {
    }

    public function form()
    {
        $template = $this->createTemplate();

        $searchquery = Utils::getVar('q') ? urldecode(Utils::getVar('q')) : null;

        $template->searchquery = $searchquery;

        $template->render();
    }

    public function result()
    {
        $searchquery = Utils::getVar('q') ? urldecode(Utils::getVar('q')) : null;

        $data = $this->getData();

        $result = $data->getResult();

        $template = $this->createTemplate();

        $template->results = $result->responseData->results;

        $template->resultCount = $result->responseData->cursor->estimatedResultCount;

        $template->pages = $result->responseData->cursor->pages;

        $template->currentPageIndex = $result->responseData->cursor->currentPageIndex;

        $template->searchquery = $searchquery;

        $template->render();
    }
}

?>

