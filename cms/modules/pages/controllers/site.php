<?php
/*
 *
 */

Class PagesController_Site Extends Controller_Base
{
    private $table = null;

    private function getTable()
    {
        if(!$this->table)
        {
            $this->table = new Table("pages");
        }
        return $this->table;
    }

    public function index()
    {
    }

    public function template()
    {
        $id = Utils::getVar('id');
        $alias = Utils::getVar('alias');

        $table = $this->getTable();

        if(isset($alias))
        {
            $page = $table->getEntityAlias($alias);
        }
        elseif(isset($id))
        {
            $page = $table->getEntity($id);  
        }

        if($page)
        {
            if($page->redirect == 1)
            {
                $objects = $table->selectObj('select * from pages where parent_id=:id order by position limit 1',
                        array('id'=>$page->id));
                if(count($objects) > 0)
                {
                    $page = $objects[0];
                    //$_REQUEST['alias'] = $page->alias;
                    App::Redirect('/'.$page->alias.'.html');
                }
            }

            $this->registry->mod_content = $page->template;
            $head_title = $page->head_title;
            $this->registry->title = (!$head_title || $head_title == '') ? $page->title : $head_title;
            $this->registry->page_title = $page->title;
            $this->registry->description = $page->meta_description;
            $this->registry->keywords = $page->meta_keywords;
        }

        
    }

    public function mainpage()
    {
        $table = $this->getTable();

        $res = $table->select("select * from pages where mainpage=1");

        if(count($res)>0)
        {
            $this->registry->mod_content = $res[0];

            $page = $res[0];

            $head_title = $page['head_title'];
            $this->registry->title = (!$head_title || $head_title == '') ? $page['title'] : $head_title;
            $this->registry->description = $page['meta_description'];
            $this->registry->keywords = $page['meta_keywords'];
        }
    }
}
?>
