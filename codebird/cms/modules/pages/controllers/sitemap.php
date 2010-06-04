<?php
/* 
 * Контроллер карты сайта страниц
 */

class PagesController_Sitemap extends Controller_Base
{

    public function index()
    {

    }

    public function show()
    {
        $template = $this->createTemplate();

        $data = $this->getData();

        $template->data = $data;

        $template->render();
    }
}

?>
