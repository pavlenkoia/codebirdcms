<?php
/**
 * 
 */
class CatalogModel_Catalog extends Model_Base
{
    private $section_table_name  = 'catalog_section';

    private $section_table;

    public function setSectionTableName($section)
    {
        $table = null;
        $config = Config::__("catalog");
        if(isset($config->sections) && $config->sections[$section])
        {
            $table = $config->sections[$section]['table'];
        }
        if(!$table)
        {
            $table = 'catalog_section';
        }
        $this->section_table_name = $table;
        $this->section_table = null;
    }

    public function getSectionConfig($section)
    {
        $config = Config::__("catalog");
        if(isset($config->sections) && $config->sections[$section])
        {
            return $config->sections[$section]['table'];
        }

        return null;
    }    

    public function getSectionTable()
    {
        if(!$this->section_table)
        {
            $this->section_table = new Table($this->section_table_name);
        }
        return $this->section_table;
    }

    public function getSections($parent_id=null)
    {
        if($parent_id === null)
        {
            $rows = $this->getSectionTable()->select("select p1.*, count(p1.id) as count, p2.id as id2 from ".$this->section_table_name." p1 left outer join ".$this->section_table_name." p2 on ( p2.parent_id=p1.id) where p1.parent_id is null group by p1.id order by p1.position");
        }
        else
        {
            $rows = $this->getSectionTable()->select("select p1.*, count(p1.id) as count, p2.id as id2 from ".$this->section_table_name." p1 left outer join ".$this->section_table_name." p2 on ( p2.parent_id=p1.id) where p1.parent_id=:parent_id group by p1.id order by p1.position",array("parent_id"=>$parent_id));
        }
        return $rows;
    }

    public function getSection($id=null)
    {
        $table = $this->getSectionTable();

        return $id ? $table->getEntity($id) : $table->getEntity();
    }

    public function reorderItems($item,$index)
    {
        $rows = $this->getSections($item->parent_id);

        $this->reorder($this->section_table_name, $rows, $item->id, $index);
    }

    //---- интерфейс для работы с таблицами ----

    public function getTables()
    {
        $tables = array();

        $config = Config::__("catalog");

        foreach($config->tables as $key=>$table)
        {
            $tables[] = array("id"=>$key,"name"=>$table['title']);
        }

        return $tables;
    }

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

    public function getTableRows($name)
    {
        $table_meta = $this->getTableMeta($name);
        $table = new Table($table_meta['table']);

        if(isset($table_meta['sql']))
        {
                $tsql = $table_meta['sql'];
        }
        else
        {
                $tsql = "select * from ".$table_meta['table'];
        }

        $rows = $table->select($tsql);

        return $rows;
    }

    public function getTablesSection()
    {
        $tables = array();

        $config = Config::__("catalog");

        foreach($config->tables_section as $key=>$table)
        {
            $tables[] = array("id"=>$key,"name"=>$table['title']);
        }

        return $tables;
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
