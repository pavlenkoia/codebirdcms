<?php
/* 
 * Класс таблицы
 *
 * инкапсулирует методы для работы с таблицей
 */

class Table
{
    protected $registry;

    protected $db;

    protected $table;

    protected $id;

    protected $fields = null;

    public $errorInfo = null;

    public function __construct($table, $id="id")
    {
        $this->registry = Registry::__instance();
        $this->db = $this->registry->db;

        $this->id = $id;
        $this->table = $table;        
    }

    private function buidFieldsSet()
    {
       unset($this->fields);
       $this->fields = array();

       $res = $this->db->query("select * from $this->table where $this->id=-1");
       $ncols = $res->columnCount();
       $pos = 0;
       for ($i=0; $i < $ncols; $i++)
       {
           $meta[] = $res->getColumnMeta($i);
           if($meta[$i]["name"] != $this->id)
           {
               $this->fields[$pos++] = $meta[$i]["name"];
           }
       }
    }

    public function GetFields()
    {
        if(!isset($this->fields))
        {
            $this->buidFieldsSet();
        }

        return $this->fields;
    }

    public function getEntity($id=-1)
    {
        $sql = $this->db->prepare("select * from $this->table where $this->id=:id limit 1");
        $sql->bindParam(':id',$id);
        if($sql->execute())
        {
            $this->errorInfo = null;
        }
        else
        {
            $errorInfos = $sql->errorInfo();
            $this->errorInfo = $errorInfos[2];
        }
        return $sql->fetch(PDO::FETCH_OBJ);
    }

    public function getEntityAlias($alias, $field_name = null)
    {
        $field_name = $field_name == null ? 'alias' : $field_name;
        $sql = $this->db->prepare("select * from $this->table where $field_name=:alias limit 1");
        $sql->bindParam(":alias",$alias);
        if($sql->execute())
        {
            $this->errorInfo = null;
        }
        else
        {
            $errorInfos = $sql->errorInfo();
            $this->errorInfo = $errorInfos[2];
        }
        return $sql->fetch(PDO::FETCH_OBJ);
    }
    
    public function save($entity)
    {
        if(!isset($this->fields))
        {
            $this->buidFieldsSet();
        }

        $id = $this->id;
        if(!isset($entity->$id))
        {
            $fields = implode(",",$this->fields);
            $inserts = array();
            for($i=0; $i < count($this->fields); $i++)
            {
                $field = $this->fields[$i];
                if(isset($entity->$field))
                {
                    $inserts[$i] = ":".$this->fields[$i];
                }
                else
                {
                    $inserts[$i] = "null";
                }
            }
            $params = implode(",",$inserts);
            $tsql = "insert into $this->table($fields) values($params)";
        }
        else
        {
            $updates = array();
            for($i=0; $i < count($this->fields); $i++)
            {
                $field = $this->fields[$i];
                if(isset($entity->$field))
                {
                    $updates[$i] = $this->fields[$i]."=:".$this->fields[$i];
                }
                else
                {
                    $updates[$i] = $this->fields[$i]."=null";
                }
            }

            $update = implode(",",$updates);
            $tsql = "update $this->table set $update where $this->id=".$entity->$id;
        }
        $sql = $this->db->prepare($tsql);
        for($i=0; $i < count($this->fields); $i++)
        {
            $field = $this->fields[$i];

            if(isset($entity->$field))
            {
                $sql->bindParam(":".$this->fields[$i],$entity->$field);
            }
        }
        if($sql->execute())
        {
            $this->errorInfo = null;
        }
        else
        {
            $errorInfos = $sql->errorInfo();
            $this->errorInfo = $errorInfos[2];
        }

        if(!isset($entity->$id))
        {
            $insert_id = $this->db->lastInsertId();

            if(Event::HasHandlers('OnTableAddEntity'))
            {
                $params = array();
                $params['id'] = $insert_id;
                $params['object'] = $entity;
                $params['table'] = $this->table;
                Event::Execute('OnTableAddEntity', $params);
            }

            return $insert_id;
        }
        else
        {
            if(Event::HasHandlers('OnTableSaveEntity'))
            {
                $params = array();
                $params['id'] = $entity->$id;
                $params['object'] = $entity;
                $params['table'] = $this->table;
                Event::Execute('OnTableSaveEntity', $params);
            }

            return $entity->$id;
        }
    }

    public function delete($entity)
    {
        $id = $this->id;

        $tsql = "delete from $this->table where $this->id=".$entity->$id;

        $sql = $this->db->prepare($tsql);

        if(!$sql->execute())
        {
            $errorInfos = $sql->errorInfo();
            $this->errorInfo = $errorInfos[2];
        }
        else
        {
            $this->errorInfo = null;
        }
    }

    public function execute($tsql, $params=array())
    {
        $sql = $this->db->prepare($tsql);

        foreach($params as $name=>$param)
        {
            $sql->bindValue(":".$name, $param);
        }

        if(!$sql->execute())
        {
            $errorInfos = $sql->errorInfo();
            $this->errorInfo = $errorInfos[2];
        }
        else
        {
            $this->errorInfo = null;
        }
    }

    public function select($tsql, $params=array())
    {
        $sql = $this->db->prepare($tsql);

        foreach($params as $name=>$param)
        {
            $sql->bindValue(":".$name, $param);
        }

        if(!$sql->execute())
        {
            $errorInfos = $sql->errorInfo();
            $this->errorInfo = $errorInfos[2];
        }
        else
        {
            $this->errorInfo = null;
        }
        return $sql->fetchAll(PDO::FETCH_NAMED);

    }

    public function selectObj($tsql, $params=array())
    {
        $sql = $this->db->prepare($tsql);

        foreach($params as $name=>$param)
        {
            $sql->bindValue(":".$name, $param);
        }

        $sql->execute();

        $objs = array();

        while ($obj = $sql->fetch(PDO::FETCH_OBJ))
        {
            array_push($objs, $obj);
        }

        return $objs;
    }
}

?>
