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

    public function AddField($table_name, $field_name)
    {
        $table = new Table('catalog_section');

        $table->execute('ALTER TABLE `'.$table_name.'` ADD `'.$field_name.'` varchar(256) collate utf8_unicode_ci default NULL');

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

        return $res;
    }
}
?>