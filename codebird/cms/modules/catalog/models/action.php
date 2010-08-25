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

    /**
     * Функция получения пэджинга для разделов
     *
     * @param integer $section_id идентификатор родительского раздела
     * @param integer $page_size размер страницы
     * @return array массив, где индекс 'count' - количество дочерних элементов, 'limit' - подстрока limit sql запроса
     */
    public function getSectionPagerArray($section_id, $page_size, $rows=null)
    {
        $page = Utils::getGET("page") ? Utils::getGET("page") : 1;
        $page = $page < 1 ? 1 : $page;
        if($rows == null)
        {
            $rows = $this->getSectionTable()->select('select count(*) as count from catalog_section where parent_id=:id',
                    array('id'=>$section_id));
        }
        $total = $rows[0]['count'];
        $count = ceil((int)$total / (int)$page_size);

        $limit = '  limit '.$page_size*($page-1).','.$page_size;

        $res = array();

        $res['count'] = $count;
        $res['limit'] = $limit;

        return $res;
    }

    /**
     * Функция получения пэджинга для разделов
     *
     * @param string $table_name имя таблицы позиций
     * @param integer $section_id идентификатор раздела
     * @param integer $page_size размер страницы
     * @return array массив, где индекс 'count' - количество дочерних элементов, 'limit' - подстрока limit sql запроса
     */
    public function getPositionPagerArray($table_name, $section_id, $page_size, $where=null)
    {
        $page = Utils::getGET("page") ? Utils::getGET("page") : 1;
        $page = $page < 1 ? 1 : $page;
        $where = $where ? ' and '.$where : '';
        $rows = $this->getSectionTable()->select('select count(*) as count from '.$table_name.' where section_id=:id '.$where,
                array('id'=>$section_id));
        $total = $rows[0]['count'];
        $count = ceil((int)$total / (int)$page_size);

        $limit = '  limit '.$page_size*($page-1).','.$page_size;

        $res = array();

        $res['count'] = $count;
        $res['limit'] = $limit;

        return $res;
    }

    public function getSectionsData($table_name, $parent_id)
    {
        $rows = $this->getSectionTable()->select('select s1.title, s2.* from catalog_section s1 inner join '.$table_name.' s2 on s1.id=s2.id where s1.parent_id=:id order by s1.position',
            array('id'=>$parent_id));

        return $rows;
    }
}

?>
