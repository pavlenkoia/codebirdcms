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
}
?>