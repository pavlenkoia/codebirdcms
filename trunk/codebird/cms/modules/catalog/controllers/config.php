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

        $editors_type = $data->GetEditorsType();

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
                'type'=>($editors_type[$param['type']])?$editors_type[$param['type']]:$param['type']
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

    public function editors_edit_form()
    {
        $table_id = Utils::getVar("table_id");
        $table_name = Utils::getVar("table_name");
        $editor_name = Utils::getVar("editor_name");

        $data = $this->getData('config');

        $editors = $data->getParam($table_id);

        $editor = $editors[$editor_name];

        $template = $this->createTemplate();

        $template->table_id = $table_id;

        if($editor)
        {
            $template->editor_name = $editor_name;
            $template->editor_title = $editor['title'];
            $template->editor_field = $editor['field'];
            $template->editor_type = $editor['type'];
            $template->editor_mode = $editor['mode'];
        }

        $fields = $data->getFields($table_name);
        $ar_fields = array();
        foreach($fields as $field)
        {
            $ar_fields[] = $field['Field'];
        }
        $template->fields = $ar_fields;

        $template->editors_type = $data->GetEditorsType();

        $template->render();
    }

    public function  editors_del()
    {
        $table_id = Utils::getVar("table_id");
        $editor_name = Utils::getVar("editor_name");

        $data = $this->getData('config');

        $param_table = $data->GetParam($table_id);

        unset($param_table[$editor_name]);

        $data->SetParam($table_id,$param_table);

        $res = array();

        $res['success'] = true;
        $res['msg'] = 'Удалено';

        $this->setContent(json_encode($res));
    }

    public function editors_edit()
    {
        $res = array();

        $res['success'] = true;
        $res['msg'] = 'Готово';

        $data = $this->getData('config');

        $table_id = Utils::getVar("table_id");
        $editor_name = Utils::getVar("editor_name");

        $param_table = $data->GetParam($table_id);

        $field = Utils::getVar("field");
        $title = Utils::getVar("title");
        $type = Utils::getVar("type");
        $mode = Utils::getVar("mode");

        if($res['success'])
        {
            if($editor_name)
            {
                $name = $editor_name;
            }
            else
            {
                $i = 2;
                $name = $field;
                while($param_table[$name])
                {
                    $name = $field.$i++;
                }
            }

            $param_table[$name]['field'] = $field;
            $param_table[$name]['title'] = $title;
            $param_table[$name]['type'] = $type;
            if($mode)
            {
                $param_table[$name]['mode'] = $mode;
            }
            else
            {
                unset($param_table[$name]['mode']);
            }

            $data->SetParam($table_id,$param_table);
        }

        $this->setContent(json_encode($res));
    }

    public function editors_pos()
    {
        $data = $this->getData('config');

        $table_id = Utils::getVar("table_id");
        $editor_name = Utils::getVar("editor_name");
        $pos = Utils::getVar("pos");

        $param_table = $data->GetParam($table_id);

        if($param_table[$editor_name])
        {
            $keys = array_keys($param_table);

            $index = array_search($editor_name,$keys);

            if($pos == 'up')
            {
                if($index > 0 )
                {
                    $val = $keys[$index-1];
                    $keys[$index-1] = $keys[$index];
                    $keys[$index] = $val;
                }
            }
            elseif($pos == 'down')
            {
                if($index+1 < count($keys) )
                {
                    $val = $keys[$index+1];
                    $keys[$index+1] = $keys[$index];
                    $keys[$index] = $val;
                }
            }

            $param_table2 = array();

            foreach($keys as $key)
            {
                $param_table2[$key] = $param_table[$key];
            }

            $data->SetParam($table_id,$param_table2);
        }

        $this->setContent(print_r($param_table2,1));
    }

    public function table_edit_form()
    {
        $template = $this->createTemplate();

        $data = $this->getData('config');

        $table_id = Utils::getVar("table_id");

        $template->table_id = $table_id;

        $tables = $data->GetParam('tables_section');
        if($tables[$table_id])
        {
            $template->table = $tables[$table_id];
            $template->is_section = true;
        }
        else
        {
            $tables = $data->GetParam('tables');
            if($tables[$table_id])
            {
                $template->table = $tables[$table_id];
                $template->is_position = true;
            }
        }

        $template->render();
    }
}
?>