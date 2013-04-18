<?php
class CatalogController_Config extends Controller_Base
{
    public function access()
    {
        return $this->login();
    }

    public function index()
    {
    }

    public function editor()
    {
        $template = $this->createTemplate();

        $template->render();
    }

    public function tree_tables()
    {
        $node_id = Utils::getVar("node");

        $data = $this->getData('config');

        $nodes = array();

        if($node_id == '_root')
        {
            $node = array();
            $node['text'] = 'Таблицы разделов';
            $node['id'] = '_tables_section';

            $nodes[] = $node;

            $node = array();
            $node['text'] = 'Таблицы позиций';
            $node['id'] = '_tables_position';

            $nodes[] = $node;
        }
        elseif($node_id == '_tables_section')
        {
            $tables = $data->GetParam('tables_section');

            foreach($tables as $name => $table)
            {
                $node = array();
                $node['text'] = $table['title'];
                $node['id'] = $name;
                $node['leaf'] = true;

                $nodes[] = $node;
            }

            /*echo '<pre>';print_r($tables);echo '</pre>';
            die();*/
        }
        elseif($node_id == '_tables_position')
        {
            $tables = $data->GetParam('tables');

            foreach($tables as $name => $table)
            {
                $node = array();
                $node['text'] = $table['title'];
                $node['id'] = $name;
                $node['leaf'] = true;

                $nodes[] = $node;
            }
        }

        $this->setContent(json_encode($nodes));
    }
}
?>