<?php
/**
 * Базовый класс настроек модуля
 */

abstract class Config_Base
{
    protected function configXml($module)
    {
        $file = SITE_PATH.'config'.DS.$module.'.config.xml';

        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
            foreach($xml->xpath('/config/params/param') as $param)
            {
                $name = (string)$param->name;
                $ptype = (string)$param['type'];
                switch($ptype)
                {
                    case 'array':
                        $items = array();
                        foreach($param->items->item as $item)
                        {
                            $key = (string)$item['name'];
                            if($item['type'] == 'array')
                            {
                                $items2 = array();
                                foreach($item->value as $value)
                                {
                                    $items2[(string)$value['name']] = (string)$value;
                                }
                                $items[$key] = $items2;
                            }
                            else
                            {
                                $items[$key] = (string)$item;
                            }
                        }
                        $this->$name = $items;
                        break;
                    default:
                        $this->$name = (string)$param->value;
                }                
            }
        }
    }

    public function initConfigXml($module)
    {
        $this->configXml($module);
    }
}

?>
