<?php
/* 
 * 
 */

class CatalogController_Forms extends Controller_Base
{
    private function form($name, $args)
    {

        $data = $this->getData('action');

        $section = $data->getSection($name);

        if(!$section) return;

        $template = $this->createTemplate();        

        $template->data = $data;

        $alias = Utils::getVar('alias');

        $uri = Utils::getVar('uri');

        $params = explode("/",$uri);

        $params = array_pad($params,10,null);

        $template->alias = $alias;

        $template->params = $params;

        $template->render();
    }

//    public function __call($name, $args)
//    {
//       $this->form($name, $args);
//    }

    public function index()
    {
    }

    public function show()
    {
        $alias = $this->args->form;

        $data = $this->getData('action');

        $section = $data->getSection($alias);

        if(!$section) return;

        $table = new Table('section_forms');

        $form = $table->getEntity($section->id);

        if(!$form) return;

        $field_rows = $table->select('select * from position_forms where section_id=:id order by position',
                array('id'=>$form->id));

        $template = $this->createTemplate();

        $template->data = $data;

        $alias = Utils::getVar('alias');

        $uri = Utils::getVar('uri');

        $params = explode("/",$uri);

        $params = array_pad($params,10,null);

        $template->alias = $alias;

        $template->params = $params;

        $template->form = $form;

        $template->field_rows = $field_rows;

        $template->render();
    }
}

?>
