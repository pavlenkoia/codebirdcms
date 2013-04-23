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
        $template->table_id = $table_id;

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

    public function fields_edit_form()
    {
        $table_name = Utils::getVar("table_name");
        $field_name = Utils::getVar("field_name");

        $data = $this->getData('config');

        $db_fields = $data->GetFields($table_name);
        foreach($db_fields as $db_field)
        {
            if($db_field['Field'] == $field_name)
            {
                $field_type = $db_field['Type'];
                break;
            }
        }

        $template = $this->createTemplate();

        $template->table_name = $table_name;
        $template->field_name = $field_name;
        $template->field_type = $field_type;

        $template->render();
    }

    public function fields_del()
    {
        $table_name = Utils::getVar("table_name");
        $field_name = Utils::getVar("field_name");

        $data = $this->getData('config');

        $error = $data->DelField($table_name,$field_name);

        $res = array();

        if($error)
        {
            $res['success'] = false;
            $res['msg'] = $error;
        }
        else
        {
            $res['success'] = true;
            $res['msg'] = 'Удалено';
        }

        $this->setContent(json_encode($res));
    }

    public function editors_records()
    {
        $table_id = Utils::getVar("table_id");

        $data = $this->getData('config');

        $param_table = $data->GetParam($table_id);

        $res = array();

        $res['rows'] = array();
        $res['results'] = count($param_table);
        $res['success'] = true;

        foreach($param_table as $key=>$param)
        {
            $row = array(
                'id'=>$key,
                'title'=>$param['title'],
                'field'=>$param['field'],
                'type'=>$param['type']
            );
            $res['rows'][] = $row;
        }

        $this->setContent(json_encode($res));
    }

    public function editors_add()
    {
        $table_id = Utils::getVar("table_id");

        $data = $this->getData('config');

        $param_table = $data->GetParam($table_id);

        $param_table['xxx'] = array(
            'field'=>'xxx',
            'title'=>'Тест',
            'type'=>'text'
        );

        $data->SetParam($table_id,$param_table);
    }

    public function editor_edit_form()
    {
        $table_id = Utils::getVar("table_id");
        $table_name = Utils::getVar("table_name");
        $editor_name = Utils::getVar("editor_name");

        $data = $this->getData('config');

        $editors = $data->getParam($table_id);

        $editor = $editors[$editor_name];

        $template = $this->createTemplate();

        if($editor)
        {
            $template->table_id = $table_id;
            $template->editor_name = $editor_name;
            $template->editor_title = $editor['title'];
            $template->editor_field = $editor['field'];
            $template->editor_type = $editor['type'];
            $fields = $data->getFields($table_name);
            $ar_fields = array();
            foreach($fields as $field)
            {
                $ar_fields[] = $field['Field'];
            }
            $template->fields = $ar_fields;
        }

        $template->render();
    }
}
?>