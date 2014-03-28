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
        $data = $this->getData('config');

        $table_name = Utils::getVar("table_name");

        $template = $this->createTemplate();

        $template->table_name = $table_name;

        $template->fields_type = $data->GetFieldsType();

        $template->render();
    }

    public function fields_add()
    {
        $table_name = Utils::getVar("table_name");
        $field_name = Utils::getVar("name");
        $field_type = Utils::getVar("type");

        $data = $this->getData('config');

        $error = $data->AddField($table_name,$field_name,$field_type);

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

        $template->fields_type = $data->GetFieldsType();

        $template->render();
    }

    public function fields_edit()
    {
        $table_name = Utils::getVar("table_name");
        $field_name = Utils::getVar("name");
        $field_old_name = Utils::getVar("old_name");
        $field_type = Utils::getVar("type");

        $data = $this->getData('config');

        $error = $data->EditField($table_name,$field_name,$field_old_name,$field_type);

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

            if($editor['type'] == 'memo')
            {
                $template->editor_editor_height = $editor['editor_height'];
            }
            if($editor['type'] == 'selecttext')
            {
                $template->editor_select2 = $editor['select'];
                $template->editor_sql = $editor['sql'];
            }
            if($editor['type'] == 'select')
            {
                $template->editor_select = $editor['select'];
                $template->editor_display = $editor['display'];
            }
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

            unset($param_table[$name]['editor_height']);
            if($type == 'memo' && Utils::getVar("editor_height"))
            {
                $param_table[$name]['editor_height'] = Utils::getVar("editor_height");
            }

            unset($param_table[$name]['sql']);
            if($type == 'selecttext' && Utils::getVar("sql"))
            {
                $param_table[$name]['sql'] = Utils::getVar("sql");
            }
            unset($param_table[$name]['select']);
            if($type == 'selecttext' && Utils::getVar("select2"))
            {
                $param_table[$name]['select'] = Utils::getVar("select2");
            }

            if($type == 'select' && Utils::getVar("select"))
            {
                $param_table[$name]['select'] = Utils::getVar("select");
            }
            unset($param_table[$name]['display']);
            if($type == 'select' && Utils::getVar("display"))
            {
                $param_table[$name]['display'] = Utils::getVar("display");
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

        if($create = Utils::getVar('create'))
        {
            $template->create = true;
            if($create == 'section')
            {
                $template->is_section = true;
                $template->db_tables = $data->GetSectionTables();
            }
            if($create == 'position')
            {
                $template->is_position = true;
                $template->db_tables = $data->GetPositionTables();
            }

            $template->table_id = 0;
        }
        else
        {
            $table_id = Utils::getVar("table_id");

            $template->table_id = $table_id;

            $tables = $data->GetParam('tables_section');
            if($tables[$table_id])
            {
                $template->table = $tables[$table_id];
                $template->is_section = true;
                $template->db_tables = $data->GetSectionTables();
            }
            else
            {
                $tables = $data->GetParam('tables');
                if($tables[$table_id])
                {
                    $template->table = $tables[$table_id];
                    $template->is_position = true;
                    $template->db_tables = $data->GetPositionTables();
                }
            }
        }



        $template->render();
    }

    public function table_save()
    {
        $data = $this->getData('config');

        $title = Utils::GetVar('title');
        $table_id = Utils::GetVar('table_id');
        $table_name = Utils::GetVar('table');
        $is_position = Utils::GetVar('is_position');
        $order = Utils::GetVar('order');
        $sql = Utils::GetVar('sql');

        $error = null;

        if(!$table_id)
        {
            if($is_position)
            {
                $data->CreatePositionTable($table_name);

                $table_id = $table_name;

                $param = $data->GetParam('tables');

                $i = 2;
                while($param[$table_id])
                {
                    $table_id = $table_name.$i++;
                }

                $param[$table_id] = array();

                $data->SetParam('tables',$param);

                $createParam = array($table_id=>'');

                $data->CreateParam($table_id,$createParam);


            }
            else
            {
                /*$error = */$data->CreateSectionTable($table_name);

                $table_id = $table_name;

                $param = $data->GetParam('tables_section');

                $i = 2;
                while($param[$table_id])
                {
                    $table_id = $table_name.$i++;
                }

                $param[$table_id] = array();

                $data->SetParam('tables_section',$param);

                $createParam = array($table_id=>'');

                $data->CreateParam($table_id,$createParam);
            }
        }

        $res = array();

        if($error)
        {
            $res['success'] = false;
            $res['msg'] = $error;
        }
        else
        {
            if($is_position)
            {
                $param = $data->GetParam('tables');

                $param[$table_id]['title'] = $title;
                $param[$table_id]['table'] = $table_name;
                if($order)
                {
                    $param[$table_id]['order'] = $order;
                }
                else
                {
                    unset($param[$table_id]['order']);
                }
                if($sql)
                {
                    $param[$table_id]['sql'] = $sql;
                }
                else
                {
                    unset($param[$table_id]['sql']);
                }
                $data->SetParam('tables',$param);
            }
            else
            {
                $param = $data->GetParam('tables_section');

                $param[$table_id]['title'] = $title;
                $param[$table_id]['table'] = $table_name;

                $data->SetParam('tables_section',$param);
            }

            $res['item']['name'] = $title;

            $res['success'] = true;
            $res['msg'] = 'Готово';

        }

        $this->setContent(json_encode($res));
    }

    public function table_del()
    {
        $res = array();

        $data = $this->getData('config');

        $table_id = Utils::GetVar('table_id');
        $is_position = Utils::GetVar('is_position');

        if($is_position)
        {
            $param = $data->GetParam('tables');
            unset($param[$table_id]);
            $data->SetParam('tables',$param);
        }
        else
        {
            $param = $data->GetParam('tables_section');
            unset($param[$table_id]);
            $data->SetParam('tables_section',$param);
        }

        $data->DelParam($table_id);

        $res['success'] = true;
        $res['msg'] = 'Готово';

        $this->setContent(json_encode($res));
    }
}
?>