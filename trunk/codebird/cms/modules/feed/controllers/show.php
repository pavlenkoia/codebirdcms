<?php
/*
 * Контроллер show feed
 */

class FeedController_Show extends Controller_Base
{

    private function show($alias)
    {
        $data = $this->getData();

        $feed = $data->getFeed($alias);

        if($feed)
        {
            $data->import($feed);

            $template = $this->createTemplate();

            $template->data = $data;
            $template->feed = $feed;
            $template->feed_items = $data->getFeed_items($feed, $this->args->limit);

            $template->render("show");
        }
    }

    public function __call($alias, $args)
    {
       $this->show($alias);
    }

    public function index()
    {

    }

    
}

?>
