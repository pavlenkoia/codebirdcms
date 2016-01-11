<?php

/**
 * Базовый класс модели
 */

abstract Class Model_Base {

    protected $registry;

    function __construct($module=null)
    {
        $this->registry = Registry::__instance();
        if($module && is_string($module))
        {
            $this->tbl_name = $module; 
        }
    }

    private $tbl_name;
    private $tbl;

    public function getTable()
    {
        if(!$this->tbl && $this->tbl_name)
        {
            $this->tbl = new Table($this->tbl_name);
        }
        return $this->tbl;
    }

    public function getObject($id=-1)
    {
        return $this->getTable()->getEntity($id);
    }

    public function save($object)
    {
        return $this->getTable()->save($object);
    }

    /**
     * Переоопределенный метод __get для обращения к методам вида get<Name>()
     * как $obj-><name>
     *
     * @param <type> $name
     * @return <type>
     */

    function __get($name) {
        $method = 'get'.ucfirst($name);
        return method_exists($this, $method)
        ? $this->$method()
        : $this->{'_'.$name};
    }

    /**
     * Переопределение метода __set для обращения к методу вида set<Name>($<value>)
     * как $obj-><name>=<value>
     *
     * @param <type> $name
     * @param <type> $value
     */

    function __set($name, $value) {
        $method = 'set'.ucfirst($name);
        if (method_exists($this, $method)) {
            $this->$method($value);
        } else {
            $this->{'_'.$name} = $value;
        }
    }

    /**
     * Получение глобального объекта db для работы с базой данной
     *
     * @return PDO
     */

    protected function getDb(){

        return $this->registry->db;
    }

    /**
     * Метод для перемещения наверх заданой строки
     *
     * @param array $rows массив строк, обязательно с полями id
     * @param integer $id идентификатор перемещаемой строки
     * @return array ассоциативный массив position=>id
     */
    private function up($rows, $id)
    {
        $pos = 1;
        $positions = array();
        foreach($rows as $row)
        {
            if(isset($pre_id) and $row['id'] == $id)
            {
                $positions[$pos-1] = $id;
                $positions[$pos] = $pre_id;
            }
            else
            {
                $positions[$pos] = $row['id'];
            }
            $pre_id = $row['id'];
            $pos++;
        }
        return $positions;
    }

    /**
     * Метод для перемещения вниз заданой строки
     *
     * @param array $rows массив строк, обязательно с полями id
     * @param integer $id идентификатор перемещаемой строки
     * @return array ассоциативный массив position=>id
     */
    private function down($rows, $id)
    {
        $pos = 1;
        $positions = array();
        foreach($rows as $row)
        {
            if(isset($pre_id) and $pre_id == $id)
            {
                $positions[$pos] = $pre_id;
                $positions[$pos-1] = $row['id'];
            }
            else
            {
                $positions[$pos] = $row['id'];
            }
            $pre_id = $row['id'];
            $pos++;
        }
        return $positions;
    }

    /**
     * Метод перемещения заданной строки
     *
     * @param string $dir напрвление перемещения, возможные значения 'up', 'down'
     * @param string $table имя таблицы (таблица должна сожержать поля 'id' и 'position')
     * @param array $rows массив строк, обязательно с полями 'id'
     * @param integer $id идентификатор перемещаемой сторки
     */
    private function move($dir,$table,$rows,$id)
    {
        if($dir == "up")
        {
            $positions = $this->up($rows,$id);
        }
        else
        {
            $positions = $this->down($rows,$id);
        }

        $sql = $this->db->prepare("update $table set position=:position where id=:id");

        foreach($positions as $position=>$id)
        {
            $sql->bindParam(":id", $id);
            $sql->bindParam(":position", $position);
            $sql->execute();
        }
    }

    /**
     * Премещения наверх заданной строки в порядке следования
     *
     * @param string $table имя таблицы (таблица должна сожержать поля 'id' и 'position')
     * @param array $rows массив строк, обязательно с полями 'id'
     * @param integer $id идентификатор перемещаемой сторки
     */
    protected function moveup($table,$rows,$id)
    {
        $this->move('up',$table,$rows,$id);
    }

    /**
     * Премещения вниз заданной строки в порядке следования
     *
     * @param string $table имя таблицы (таблица должна сожержать поля 'id' и 'position')
     * @param array $rows массив строк, обязательно с полями 'id'
     * @param integer $id идентификатор перемещаемой сторки
     */
    protected function movedown($table,$rows,$id)
    {
        $this->move('down',$table,$rows,$id);
    }

    protected function reorder($table,$rows,$id,$index)
    {
        if($index+1 > count($rows)) $index--;
        
        $pos = 0;
        $positions = array();
        foreach($rows as $row)
        {
            if($row['id'] != $id)
            {
                $positions[$pos] = $row['id'];
            }
            $pos++;
        }

        $positions = array_merge(array_slice($positions, 0, $index), array($id), array_slice($positions, $index));

        $sql = $this->db->prepare("update $table set position=:position where id=:id");

        foreach($positions as $position=>$id)
        {
            $position++;
            $sql->bindParam(":id", $id);
            $sql->bindParam(":position", $position);
            $sql->execute();
        }
    }
}

?>
