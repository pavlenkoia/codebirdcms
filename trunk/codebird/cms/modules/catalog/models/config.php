<?php
/**
 *
 */
class CatalogModel_Config extends Model_Base
{
    public function GetParam($param_name)
    {
        $res = array();

        $file = SITE_PATH.'config'.DS.'catalog.config.xml';

        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);

            foreach($xml->xpath('/config/params/param') as $param)
            {
                $name = (string)$param->name;

                if($name == $param_name)
                {
                    foreach($param->items->item as $item)
                    {
                        $name = (string)$item['name'];
                        $res[$name] =  array();
                        foreach($item->value as $value)
                        {
                            $res[$name][(string)$value['name']] = (string)$value;
                        }
                    }
                    break;
                }
            }
        }

        return $res;
    }

    public function SetParam($param_name, $pitems)
    {
        $file = SITE_PATH.'config'.DS.'catalog.config.xml';

        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);

            foreach($xml->xpath('/config/params/param') as $param)
            {
                $name = (string)$param->name;
                if($name == $param_name)
                {
                    $param->items = '';

                    foreach($pitems as $key=>$pitem)
                    {
                        $item = $param->items->addChild('item');
                        $item->addAttribute('name',$key);
                        $item->addAttribute('type','array');
                        foreach($pitem as $key_name=>$pvalue)
                        {
                            $value = $item->addChild('value',$pvalue);
                            $value->addAttribute('name',$key_name);
                        }
                    }

                    $str_xml = $this->xmlpp($xml->asXML());

                    $xml2 = simplexml_load_string($str_xml);

                    $xml2->asXML($file);

                    //$xml->asXML($file);

                    break;
                }
            }
        }
    }

    public function CreateParam($param_name, $pitems)
    {
        $file = SITE_PATH.'config'.DS.'catalog.config.xml';

        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);

            $exist = false;

            foreach($xml->xpath('/config/params/param') as $param)
            {
                $name = (string)$param->name;
                if($name == $param_name)
                {
                    $exist = true;
                    break;
                }
            }

            if(!$exist)
            {
                foreach($xml->xpath('/config/params') as $params)
                {
                    $param = $params->addChild('param');
                    $param->addAttribute('type','array');

                    $param->addChild('name',$param_name);

                    $param->addChild('items','');

                    $str_xml = $this->xmlpp($xml->asXML());

                    $xml2 = simplexml_load_string($str_xml);

                    $xml2->asXML($file);

                    break;
                }
            }
        }
    }

    public function DelParam($param_name)
    {
        $file = SITE_PATH.'config'.DS.'catalog.config.xml';

        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);

            foreach($xml->xpath('/config/params/param') as $param)
            {
                $name = (string)$param->name;
                if($name == $param_name)
                {
                    $oNode = dom_import_simplexml($param);

                    $oNode->parentNode->removeChild($oNode);

                    $str_xml = $this->xmlpp($xml->asXML());

                    $xml2 = simplexml_load_string($str_xml);

                    $xml2->asXML($file);

                    break;
                }
            }
        }
    }

    private function xmlpp($xml, $html_output=false) {
        $xml_obj = new SimpleXMLElement($xml);
        $level = 4;
        $indent = 0; // current indentation level
        $pretty = array();

        // get an array containing each XML element
        $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));

        // shift off opening XML tag if present
        if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {
            $pretty[] = array_shift($xml);
        }

        foreach ($xml as $el) {
            if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {
                // opening tag, increase indent
                $pretty[] = str_repeat(' ', $indent) . $el;
                $indent += $level;
            } else {
                if (preg_match('/^<\/.+>$/', $el)) {
                    $indent -= $level;  // closing tag, decrease indent
                }
                if ($indent < 0) {
                    $indent += $level;
                }
                $pretty[] = str_repeat(' ', $indent) . $el;
            }
        }
        $xml = implode("\n", $pretty);
        return ($html_output) ? htmlentities($xml) : $xml;
    }

    public function GetFields($table_name)
    {
        $table = new Table('catalog_section');

        $rows = $table->select('SHOW COLUMNS FROM `'.$table_name.'` ');

        $db_fields = array();

        foreach($rows as $row)
        {
            if(in_array($row['Field'],array('id','section_id'))) continue;

            $db_field = array(
                'Field'=>$row['Field'],
                'Type'=>$row['Type']
            );
            $db_fields[] = $db_field;
        }

        return $db_fields;
    }

    public function GetPositionTables()
    {
        $table = new Table('catalog_section');

        $rows = $table->select('SHOW TABLES');

        $db_tables = array();

        foreach($rows as $row)
        {
            $val = current($row);

            if(strpos($val,'position_') === 0)
            {
                $db_tables[] = $val;
            }
        }

        return $db_tables;
    }

    public function GetSectionTables()
    {
        $table = new Table('catalog_section');

        $rows = $table->select('SHOW TABLES');

        $db_tables = array();

        foreach($rows as $row)
        {
            $val = current($row);

            if(strpos($val,'section_') === 0)
            {
                $db_tables[] = $val;
            }
        }

        return $db_tables;
    }

    public function CreateSectionTable($table_name)
    {
        $table = new Table('catalog_section');

        $table->execute('CREATE TABLE `'.$table_name.'` (`id` int(11) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $res = $table->errorInfo;

        return $res;
    }

    public function CreatePositionTable($table_name)
    {
        $table = new Table('catalog_section');

        $table->execute('CREATE TABLE IF NOT EXISTS `'.$table_name.'` (
              `id` int(11) NOT NULL auto_increment,
              `section_id` int(11) NOT NULL,
              PRIMARY KEY  (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

        $res = $table->errorInfo;

        return $res;
    }

    public function CreateRelTable($table_name, $rel_table_name)
    {
        $table = new Table('catalog_section');

        $table->execute('CREATE TABLE IF NOT EXISTS `rel_'.$table_name.'_'.$rel_table_name.'` (
              `'.$table_name.'_id` int(11) NOT NULL,
              `'.$rel_table_name.'_id` int(11) NOT NULL,
              PRIMARY KEY (`'.$table_name.'_id`,`'.$rel_table_name.'_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');

        $res = $table->errorInfo;

        return $res;
    }

    public function AddField($table_name, $field_name, $field_type)
    {
        $table = new Table('catalog_section');

        $field_type_ddl = $this->GetFieldsTypeDDL($field_type);

        $table->execute('ALTER TABLE `'.$table_name.'` ADD `'.$field_name.'` '.$field_type_ddl);

        $res = $table->errorInfo;

        return $res;
    }

    public function EditField($table_name, $field_name, $field_old_name, $field_type)
    {
        $table = new Table('catalog_section');

        $field_type_ddl = $this->GetFieldsTypeDDL($field_type);

        $table->execute('ALTER TABLE `'.$table_name.'` CHANGE `'.$field_old_name.'` `'.$field_name.'` '.$field_type_ddl);

        $res = $table->errorInfo;

        return $res;
    }

    public function DelField($table_name, $field_name)
    {
        $table = new Table('catalog_section');

        $table->execute('ALTER TABLE `'.$table_name.'` DROP `'.$field_name.'`');

        $res = $table->errorInfo;

        return $res;
    }

    public function GetEditorsType()
    {
        $res = array();

        $res['text'] = 'Текстовая строка';
        $res['memo'] = 'Многострочный редактор';
        $res['richtext'] = 'Визуальный редактор';
        $res['date'] = 'Дата';
        $res['check'] = 'Чекбокс';
        $res['image'] = 'Картинка';
        $res['file'] = 'Файл';
        $res['int'] = 'Целое число';
        $res['dec'] = 'Десятичное число';
        $res['select'] = 'Выпадающий список справочника';
        $res['selecttext'] = 'Текстовая строка с выпадающим списком';
        $res['labeltext'] = 'Нередактируемая текстовая строка';
        $res['images'] = 'Картинки';
        $res['rel'] = 'Связанный элемент';

        return $res;
    }

    public function GetFieldsType()
    {
        $res = array();

        $res['varchar(256)'] = 'varchar(256)';
        $res['text'] = 'text';
        $res['int(11)'] = 'int(11)';
        $res['float'] = 'float';
        $res['smallint(6)'] = 'smallint(6)';

        return $res;
    }

    public function GetFieldsTypeDDL($field_type)
    {
        $field_type_ddl = 'varchar(256) collate utf8_unicode_ci default NULL';

        switch ($field_type) {
            case 'varchar(256)':
                $field_type_ddl = 'varchar(256) collate utf8_unicode_ci default NULL';
                break;
            case 'text':
                $field_type_ddl = 'text collate utf8_unicode_ci default NULL';
                break;
            case 'int(11)':
                $field_type_ddl = 'int(11) default NULL';
                break;
            case 'float':
                $field_type_ddl = 'float default NULL';
                break;
            case 'smallint(6)':
                $field_type_ddl = 'smallint(6) default NULL';
                break;
        }

        return $field_type_ddl;
    }

}
?>