<?php
/* 
 * 
 */

class PagesModel_Pages extends Model_Base
{
    private $p_id = null;

    public function getParent_id()
    {
        return $this->p_id;
    }

    public function setParent_id($parent_id)
    {
        $this->p_id = $parent_id;
    }

    public function createPage()
    {
        $page = new Page();
        return $page;
    }

    /**
     * Сохраняет страницу в базу
     *
     * @param object $page объект страницы
     * @return integer возвращает id сохраненной страницы
     */

    public function save($page)
    {
        if(!isset($page->id))
        {
            if(isset($page->parent_id))
            {
                $sql_tmp = $this->db->prepare("select count(*) from pages where parent_id=:parent_id");
                $sql_tmp->bindParam(':parent_id',$page->parent_id);
            }
            else
            {
                $sql_tmp = $this->db->prepare("select count(*) from pages where parent_id is null");
            }
            $sql_tmp->execute();
            $position = $sql_tmp->fetchColumn() + 1;
            $sql = $this->db->prepare("insert into pages(title,content,alias,position,parent_id,template) values(:title,:content,:alias,:position,:parent_id,:template)");
            $sql->bindParam(':position',$position);
            $sql->bindParam(':parent_id',$page->parent_id);
        }
        else
        {
            $sql = $this->db->prepare("update pages set title=:title, content=:content, alias=:alias, visible=:visible, template=:template, plugins=:plugins where id=:id");
            $sql->bindParam(':id',$page->id);
            $sql->bindParam(':visible',$page->visible);
            $sql->bindParam(':plugins',$page->plugins);
        }
        $sql->bindParam(':title',$page->title);
        $sql->bindParam(':content',$page->content);
        $page->content = Utils::getPost('content');
        $alias = $page->alias;
        if(!isset($alias) or trim($alias) == "")
        {
            $alias = $page->title;
        }
        $alias = Utils::translit($alias);
        $alias = isset($page->id) ?
            Utils::getUniqueAlias($alias, "pages", $page->id) :
            Utils::getUniqueAlias($alias, "pages");
        $sql->bindParam(':alias',$alias);
        $sql->bindParam(':template',$page->template);
        $sql->execute();
        if(!isset($page->id))
        {
            return $this->db->lastInsertId();
        }
        else
        {
            return $page->id;
        }
    }

    /**
     * Удаляет страницу из базы
     *
     * @param object $page объект удаляемой страницы
     */

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
        foreach($this->db->query("select id from pages where parent_id=$id", PDO::FETCH_BOTH) as $row)
        {
            $ids .= $row['id'].',';
            $ids = $this->select($row['id'],$ids);
        }
        return $ids;
    }

    public function getPages($parent_id=null)
    {
        if($parent_id)
        {
            $this->p_id = $parent_id;
        }
        if($this->p_id)
        {
            $sql = $this->db->prepare("select * from pages where parent_id=:parent_id order by position");
            $sql->bindParam(':parent_id',$this->p_id);
        }
        else
        {
            $sql = $this->db->prepare("select * from pages where parent_id is null order by position");
        }
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_NAMED);
    }

    public function getVisiblePages($parent_id=null)
    {
        if($parent_id)
        {
            $this->p_id = $parent_id;
        }
        if($this->p_id)
        {
            $sql = $this->db->prepare("select * from pages where parent_id=:parent_id and visible=1 order by position");
            $sql->bindParam(':parent_id',$this->p_id);
        }
        else
        {
            $sql = $this->db->prepare("select * from pages where parent_id is null and visible=1 order by position");
        }
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_NAMED);
    }

    /**
     * Получает страницу из базы
     * 
     * @param integer $id id страницы
     * @return object объект страницы, если страница не найдена - объект с пустыми полями 
     */

    public function getPage($id)
    {
        $sql = $this->db->prepare("select * from pages where id=:id");
        $sql->bindParam(':id',$id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_OBJ);
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

    public function setMainPage($id)
    {
        $sql = $this->db->prepare("update pages set mainpage=0");
        $sql->execute();
        $sql = $this->db->prepare("update pages set mainpage=1 where id=:id");
        $sql->bindParam(':id',$id);
        $sql->execute();
    }
}

?>
