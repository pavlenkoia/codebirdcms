<?php
/**
 *
 */
class CatalogModel_Action extends Model_Base
{
    private $section_table_name  = 'catalog_section';

    private $section_table;

    public function setSectionTableName($section_table_name = null)
    {
        $this->section_table_name = $section_table_name ? $section_table_name :  'catalog_section';
        $this->section_table = null;
    }

    public function getSectionTable()
    {
        if(!$this->section_table)
        {
            $this->section_table = new Table($this->section_table_name);
        }
        return $this->section_table;
    }

    public function getSection($name)
    {
        $section = $this->getSectionTable()->getEntityAlias($name);

        if(!$section)
        {
            $section = $this->getSectionTable()->getEntity($name);
        }

        return  $section;
    }

    public function getSections($name)
    {
        if(is_numeric($name))
        {
            $rows = $this->getSectionTable()->select('select * from catalog_section where parent_id=:id order by position',array('id'=>$name));
        }
        else
        {
            $rows = $this->getSectionTable()->select('select s1.* from catalog_section s1 left join catalog_section s2 on s1.parent_id=s2.id where s2.alias=:alias order by position',array('alias'=>$name));
        }

        return $rows;
    }


    //---- интерфейс для работы с таблицами ----

    public function getTableMeta($name)
    {
        $config = Config::__("catalog");

        if(isset($config->tables[$name]))
        {
            $res = $config->tables[$name];

            $res['fields'] = $config->$name;

            foreach($res['fields'] as $key=>$field)
            {
                if(isset($field['mode']))
                {
                    $modes = explode(",", $field['mode']);
                    $res['fields'][$key]['edit'] = in_array("edit", $modes);
                    $res['fields'][$key]['browse'] = in_array("browse", $modes);
                }
                else
                {
                    $res['fields'][$key]['edit'] = true;
                    $res['fields'][$key]['browse'] = true;
                }
            }

            return $res;
        }

        return null;
    }

    public function getTableMetaSection($name)
    {
        $config = Config::__("catalog");

        if(isset($config->tables_section[$name]))
        {
            $res = $config->tables_section[$name];

            $res['fields'] = $config->$name;

            foreach($res['fields'] as $key=>$field)
            {
                if(isset($field['mode']))
                {
                    $modes = explode(",", $field['mode']);
                    $res['fields'][$key]['edit'] = in_array("edit", $modes);
                    $res['fields'][$key]['browse'] = in_array("browse", $modes);
                }
                else
                {
                    $res['fields'][$key]['edit'] = true;
                    $res['fields'][$key]['browse'] = true;
                }
            }

            return $res;
        }

        return null;
    }

    //---- end ----
}

?>
