<?php
/**
 *
 */

class MenusModel_Menus extends Model_Base
{
    private $table_item;
    
    public function getTableItem()
    {
        if(!$this->table_item)
        {
            $this->table_item = new Table("menus_item");
        }
        return $this->table_item;
    }


    public function getMenus()
    {
        return $this->getTable()->select("select * from menus order by title");
    }

    public function getMenu($menus_name)
    {
        return $this->getTable()->getEntityAlias($menus_name,"name");
    }

    public function delete($menu)
    {
        $this->table->execute("delete from menus_item where menus_id=".$menu->id);

        $errorInfo = $this->table->errorInfo;

        if(!$errorInfo)
        {
            $this->table->delete($menu);

            $errorInfo = $this->table->errorInfo;
        }

        return $errorInfo;
    }

    public function getItems($menus_id,$parent_id=null)
    {
        if($parent_id === null)
        {
            $rows = $this->getTable()->select("select p1.*, count(p1.id) as count, p2.id as id2 from menus_item p1 left outer join menus_item p2 on ( p2.parent_id=p1.id) where p1.parent_id is null and p1.menus_id=:menus_id group by p1.id order by p1.position",array("menus_id"=>$menus_id));
        }
        else
        {
            $rows = $this->getTable()->select("select p1.*, count(p1.id) as count, p2.id as id2 from menus_item p1 left outer join menus_item p2 on ( p2.parent_id=p1.id) where p1.parent_id=:parent_id and p1.menus_id=:menus_id group by p1.id order by p1.position",array("menus_id"=>$menus_id,"parent_id"=>$parent_id));
        }
        return $rows;
    }

    public function getItem($id=-1)
    {
        $table = $this->getTableItem();

        return $table->getEntity($id);
    }

    public function saveItem($item)
    {
        $table = $this->getTableItem();

        $table->save($item);

        return $table->errorInfo;
    }

    public function deleteItem($item)
    {
        $table = $this->getTableItem();

        $table->delete($item);

        return $table->errorInfo;
    }

    public function reorderItems($item,$index)
    {
        $rows = $this->getItems($item->menus_id, $item->parent_id);

        $this->reorder("menus_item", $rows, $item->id, $index);
    }

    public function getPages($alias=null)
    {
        $pages[] = array();
        $alias = $alias ? $alias : Utils::getVar('alias');
        if($alias)
        {
            $table = new Table("pages");
            $page = $table->getEntityAlias($alias);
            if($page)
            {
                $parent_id = $page->parent_id;
                while($parent_id)
                {
                    $parent_page = $table->getEntity($parent_id);
                    $parent_id = $parent_page->parent_id;
                    $pages[] = '/'.$parent_page->alias.'.html';
                }
            }
        }
        return $pages;
    }
}

?>
