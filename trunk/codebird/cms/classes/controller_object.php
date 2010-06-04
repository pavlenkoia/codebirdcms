<?php
/**
 *
 */

 abstract class Controller_Object extends Controller_Base
{
    protected $table_name;

    private $object;

    protected function getObject($table_name=null)
    {
        if(!$this->object || $table_name != $this->table_name)
        {
            $mod = Utils::getVar('mod');
            if($mod == $this->module)
            {
                $uri = Utils::getVar("uri");
                if($uri)
                {
                    $uri = explode("/",$uri);
                    $id = null;
                    $alias = count($uri) > 0 ? $uri[0] : null;
                }
                else
                {
                    $id = Utils::getVar('id');
                    $alias = Utils::getVar('alias');
                }
                if(!$this->object or ($this->object->id != $id and $this->object->alias != $alias))
                {
                    $this->table_name = $table_name;
                    if($table_name == null)
                    {
                        $table_name = $this->module;
                    }
                    $table = new Table($table_name);

                    if($alias)
                    {
                        $this->object = $table->getEntityAlias($alias);
                    }
                    elseif($id)
                    {
                        $this->object = $table->getEntity($id);
                    }
                }
            }
        }
        return $this->object;
    }
}

?>
