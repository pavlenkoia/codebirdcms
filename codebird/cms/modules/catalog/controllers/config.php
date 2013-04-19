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

    public function editor_item()
    {
        $template = $this->createTemplate();

        $table_id = Utils::getVar("id");

        $data = $this->getData('config');

        $param_table = $data->GetParam($table_id);

        $template->param_table = $param_table;

        $tables = $data->GetParam('tables_section');
        if($tables[$table_id])
        {
            $template->table = $tables[$table_id];
            $template->is_section = true;
            $table = $tables[$table_id];
        }
        else
        {
            $tables = $data->GetParam('tables');
            if($tables[$table_id])
            {
                $template->table = $tables[$table_id];
                $template->is_position = true;
                $table = $tables[$table_id];
            }
        }

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

    public function fields_records()
    {
        $table_name = Utils::getVar("table_name");

        $data = $this->getData('config');

        $db_fields = $data->GetFields($table_name);

        $res = array();

        $res['rows'] = $db_fields;
        $res['results'] = count($db_fields);
        $res['success'] = true;

        $this->setContent(json_encode($res));
    }

    public function fields_add_form()
    {
        $table_name = Utils::getVar("table_name");

        $template = $this->createTemplate();

        $template->table_name = $table_name;

        $template->render();
    }

    public function fields_add()
    {
        $table_name = Utils::getVar("table_name");
        $field_name = Utils::getVar("name");

        $data = $this->getData('config');

        $error = $data->AddField($table_name,$field_name);

        $res = array();

        if($error)
        {
            $res['success'] = false;
            $res['msg'] = $error;
        }
        else
        {
            $res['success'] = true;
            $res['msg'] = 'Добавлено';
        }

        $this->setContent(json_encode($res));
    }
}
?>