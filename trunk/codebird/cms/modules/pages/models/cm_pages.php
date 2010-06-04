<?php
/**
 * Модель cm_pages модуля pages
 */

class PagesModel_Cm_pages extends Model_Base
{
    private $table_pages;

    public function getTablePages()
    {
        if(!$this->table_pages)
        {
            $this->table_pages = new Table("pages");
        }
        return $this->table_pages;
    }

    public function getPage($id=-1)
    {
        $table = $this->getTablePages();

        return $table->getEntity($id);
    }

    public function setMainPage($id)
    {
        $sql = $this->db->prepare("update pages set mainpage=0");
        $sql->execute();
        $sql = $this->db->prepare("update pages set mainpage=1 where id=:id");
        $sql->bindParam(':id',$id);
        $sql->execute();
    }

    public function save($page)
    {
        $table = $this->getTablePages();

        $table->save($page);

        return $table->errorInfo;
    }
    
    public function getPages($parent_id=null)
    {
        $table = $this->getTablePages();
        if($parent_id)
        {
            return $table->select("select * from pages where parent_id=:parent_id order by position",array("parent_id"=>$parent_id));
        }
        else
        {
            return $table->select("select * from pages where parent_id is null order by position");
        }
    }

    public function reorderPages($id,$parent_id,$index)
    {
        $rows = $this->getPages($parent_id);

        $this->reorder("pages", $rows, $id, $index);
    }

    public function delete($page)
    {
        $ids = "";
        $ids = trim($this->select($page->id, $ids),",");
        if($ids != "")
        {
            $sql = $this->db->prepare("delete from pages where id in ($ids)");
            $sql->execute();
        }

        $parent_id = $page->parent_id;
        
        $sql = $this->db->prepare("delete from pages where id=:id");
        $sql->bindParam(':id',$page->id);
        $sql->execute();

        // Пересортировка
        $pos = 1;
        $positions = array();
        foreach($this->getPages($parent_id) as $row)
        {
           $positions[$pos++] = $row['id'];
        }
        $this->savePositions($positions);
    }

    private function select($id,$ids)
    {
        $table = $this->getTablePages();
        foreach($table->select("select id from pages where parent_id=$id") as $row)
        {
            $ids .= $row['id'].',';
            $ids = $this->select($row['id'],$ids);
        }
        return $ids;
    }

    public function savePositions($positions)
    {
        $sql = $this->db->prepare("update pages set position=:position where id=:id");
        $sql->bindParam(':position',$position);
        $sql->bindParam(':id',$id);
        foreach($positions as $position=>$id)
        {
            $sql->execute();
        }
    }
}

?>
