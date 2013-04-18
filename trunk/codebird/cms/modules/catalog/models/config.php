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
}
?>