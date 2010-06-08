<?php
/* 
 * Контроллер cm каталога
 */
class CatalogController_Cm extends Controller_Base
{
    public function access()
    {
        return $this->login();
    }

    public function index()
    {
    }

    public function navigator()
    {
        $template = $this->createTemplate();

        $template->render();
    }

    public function tree()
    {
        $template = $this->createTemplate();

        $data = $this->getData();

        $parent_id = Utils::getVar("node");

        $alias = Utils::getVar("alias");

        $parent_id = is_numeric($parent_id) ? $parent_id : null;

        if($alias && !$parent_id)
        {
            $table = $data->getSectionTable();
            $section = $table->getEntityAlias($alias);
            $sections = $data->getSections($section->id);
        }
        else
        {
            $sections = $data->getSections($parent_id);
        }

        $template->sections = $sections;

        $template->render();
    }

    public function editor()
    {
        $id = Utils::getVar('id');

        $data = $this->getData();

        $object = $data->getSection($id);

        if($object)
        {
            $template = $this->createTemplate();
            
            $template->section = $object;
            
            $table_meta = $data->getTableMeta($object->position_table);
            
            if($table_meta)
            {
                $template->table_id = $object->position_table;

                $template->table_title = $table_meta['title'];

                $template->table_name = $table_meta['table'];

                $template->page_size = isset($table_meta['page_size']) ? $table_meta['page_size'] : 100;

                $template->fields = $table_meta['fields'];
            }
            else
            {
                $template->table_id = null;
            }

            $table_meta_section = $data->getTableMetaSection($object->section_table);

            if($table_meta_section)
            {
                $template->fields_section = $table_meta_section['fields'];

                $table_section = new Table($table_meta_section['table']);

                $section_data = $table_section->getEntity($id);

                if(!$section_data)
                {
                    $table_section->execute('insert into '.$table_meta_section['table'].'(id) values(:id)', array('id'=>$id));
                }

                $section_data = $table_section->getEntity($id);

                $template->section_data = $section_data;
            }

            $template->render();
        }
    }

    public function add_section_form()
    {
        $template = $this->createTemplate();

        $data = $this->getData();

        $parent_id = Utils::getVar("parent_id");

        $alias = Utils::getPost('alias');
        
        if(!$parent_id && $alias)
        {
            $section_alias = $data->getSectionTable()->getEntityAlias($alias);
            $parent_id = $section_alias->id;
        }

        $parent_section = $data->getSection($parent_id);

        $template->parent_section = $parent_section;

        $tables = $data->getTables();

        $template->tables = $tables;

        $tables_section = $data->getTablesSection();

        $template->tables_section = $tables_section;

        $template->alias = Utils::getVar("alias");

        $template->render();
    }

    public function add_section()
    {
        $res = array();
        try
        {
            $data = $this->getData();

            $object = $data->getSection();

            $object->title = Utils::getPost("title");

            if($object->title === null || trim($object->title) == '')
            {
                throw new Exception('Заголовок не указан');
            }

            $parent_id = Utils::getPost('parent_id');

            $parent_id = is_numeric($parent_id) ? $parent_id : null;
            
            $alias = Utils::getPost('alias');

            if(!$parent_id && $alias)
            {
                $section_alias = $data->getSectionTable()->getEntityAlias($alias);
                $parent_id = $section_alias->id;
            }

            if($parent_id)
            {
                $object->parent_id = $parent_id;
                $object->position = count($data->getSections($parent_id))+1;

                $parent = $data->getSection($parent_id);

                if(!$parent) throw new Exception('Не найден родительский раздел');

                if($parent->leaf == 1) throw new Exception('Нельзя создавать подраздел для "'.$parent->title.'"');

                if($parent->children_tpl)
                {
                    $xml = simplexml_load_string($parent->children_tpl);

                    if($xml)
                    {
                        if($xml->section)
                        {
                            $object->section_table = $xml->section;
                        }
                        if($xml->position)
                        {
                            $object->position_table = $xml->position;
                        }
                        if($xml->leaf)
                        {
                            $object->leaf = $xml->leaf;
                        }
                        if($xml->children && $xml->children->tpl)
                        {
                            $object->children_tpl = $xml->children->tpl->asXML();
                        }
                    }
                }
            }
            else
            {
                $object->position = count($data->getSections())+1;
            }

            $id = $data->getSectionTable()->save($object);

            if($data->getSectionTable()->errorInfo)
            {
                throw new Exception($data->getSectionTable()->errorInfo);
            }

            $res['success'] = true;
            $res['msg'] = 'Добавлено';
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }

        $this->setContent(json_encode($res));
    }

    public function delete_section()
    {
        $res = array();
        try
        {
            $data = $this->getData();

            $id = Utils::getVar('id');

            $object = $data->getSection($id);

            if($object)
            {
                $table = $data->getSectionTable();

                $select = $table->select('select count(*) as count from catalog_section where parent_id=:id',array('id'=>$id));
                if($select[0]['count'] > 0)
                {
                    throw new Exception('Объект не может быть удален так как имеет подпункты');
                }

                if($object->section_table)
                {
                    $table_meta = $data->getTableMetaSection($object->section_table);

                    $table->execute('delete from '.$table_meta['table'].' where id=:id',array('id'=>$object->id));

                    if($table->errorInfo) throw new Exception($table->errorInfo);
                }
                if($object->position_table)
                {
                    $table_meta = $data->getTableMeta($object->position_table);

                    $table->execute('delete from '.$table_meta['table'].' where section_id=:id',array('id'=>$object->id));

                    if($table->errorInfo) throw new Exception($table->errorInfo);
                }

                $table->delete($object);
                $errorInfo = $table->errorInfo;

                if($errorInfo)
                {
                    throw new Exception($errorInfo);
                }
            }
            else
            {
                throw new Exception('Объект уже удален');
            }

            $res['success'] = true;
            $res['msg'] = 'Удалено';
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }

        $this->setContent(json_encode($res));
    }

    public function reorder()
    {
        $res = array();

        try
        {
            $data = $this->getData();

            $id = Utils::getVar('id');

            $table = $data->getSectionTable();

            $object = $table->getEntity($id);

            if($object)
            {
                $parent_id = Utils::getPost("parent_id");
                $parent_id = is_numeric($parent_id) ? $parent_id : null;

                $alias = Utils::getPost('alias');

                if(!$parent_id && $alias)
                {
                    $section_alias = $data->getSectionTable()->getEntityAlias($alias);
                    $parent_id = $section_alias->id;
                }


                $index = Utils::getPost("index");

                $object->parent_id = $parent_id;
                $table->save($object);
                $data->reorderItems($object, $index);
            }
            else
            {
                throw new Exception('Объект уже удален');
            }
            $res['success'] = true;
            $res['msg'] = 'Готово';
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }

        $this->setContent(json_encode($res));
    }

    public function position_records()
    {
        $data = $this->getData();

        $section_id = Utils::getVar('section_id');

        $name = Utils::getVar('name');

        $start = Utils::getVar('start');

        $limit = Utils::getVar('limit');

        $table_meta = $data->getTableMeta($name);

        $table = new Table($table_meta['table']);

        if(isset($table_meta['sql']))
        {
            $tsql = $table_meta['sql'];
        }
        else
        {
            $tsql = "select * from ".$table_meta['table'].' where section_id=:section_id';
        }

        if($start != null)
        {
            $lim = " limit ".$start.",".$limit;

            $tsql .= $lim;
        }
        $rows = $table->select($tsql,array('section_id'=>$section_id));

        $tsql = "select count(*) as count from ".$table_meta['table'].' where section_id=:section_id';
        $res = $table->select($tsql,array('section_id'=>$section_id));
        $count = $res[0]["count"];

        $template = $this->createTemplate();

        $template->rows = $rows;

        $template->count = $count;

        $template->table_meta = $table_meta;

        $template->render();
    }

    public function save_position()
    {
        $res = array();

        try
        {

            $data = $this->getData();

            $section_id = Utils::getVar('section_id');

            $table_id = Utils::getVar('_table_id_');

            $table_meta = $data->getTableMeta($table_id);

            if($table_meta == null)
            {
                throw new Exception('Не найдена исходная таблица');
            }

            $fields = $table_meta['fields'];

            $id = Utils::getVar('id');

            $table = new Table($table_meta['table']);

            $object = $id == 0 ? $table->getEntity() : $table->getEntity($id);

            if($id != 0 && $object == null)
            {
                throw new Exception('Запись уже удалена');
            }

            $object->section_id = $section_id;

            foreach($fields as $field)
            {
                if($field['type'] == "image") continue;

                if($field['type'] == "file") continue;

                if(!$field['edit']) continue;
                
                $fn = $field['field'];

                if($field['type'] == "date")
                {
                    if(Utils::getVar($fn))
                    {
                        $times = explode(".",Utils::getVar($fn));
                        $object->$fn = $times ?  mktime(0,0,0,$times[1],$times[0],$times[2]) : null;
                    }
                    else
                    {
                        $object->$fn = null;
                    }
                }
                else
                {
                    $object->$fn = Utils::getVar($fn);
                }
            }

            $table->save($object);

            $errorInfo = $table->errorInfo;

            if($errorInfo)
            {
                throw new Exception($errorInfo);
            }

            $res['success'] = true;
            $res['msg'] = 'Готово';
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }

        $this->setContent(json_encode($res));
    }

    public function  delete_position()
    {
        $res = array();

        try
        {
            $data = $this->getData();

            $table_id = Utils::getVar('_table_id_');

            $table_meta = $data->getTableMeta($table_id);

            if($table_meta == null)
            {
                throw new Exception('Не найдена исходная таблица');
            }

            $fields = $table_meta['fields'];

            $id = Utils::getVar('id');

            $id = trim($id, ',');

            $table = new Table($table_meta['table']);

            $table->select("delete from ".$table_meta['table']." where id in($id)");

            $errorInfo = $table->errorInfo;

            if($errorInfo)
            {
                throw new Exception($errorInfo);
            }

            $res['success'] = true;
            $res['msg'] = 'Готово';
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }

        $this->setContent(json_encode($res));
    }

    public function edit_section_form()
    {
        $template = $this->createTemplate();

        $id = Utils::getVar('id');

        $data = $this->getData();

        $object = $data->getSection($id);

        $template->section = $object;

        $tables = $data->getTables();

        $template->tables = $tables;

        $tables_section = $data->getTablesSection();

        $template->tables_section = $tables_section;

        $template->render();
    }

    public function save_section()
    {
        $res = array();
        try
        {
            $data = $this->getData();

            $id = Utils::getPost("id");

            $object = $data->getSection($id);

            if(!$object)
            {
               throw new Exception('Объект уже удален');
            }

            $object->title = Utils::getPost("title");

            if($object->title === null || trim($object->title) == '')
            {
                throw new Exception('Заголовок не указан');
            }

            $alias = Utils::getPost('alias');
            if($alias)
            {
                $object->alias = Utils::translit($alias);
                $object->alias = Utils::getUniqueAlias($alias, "catalog_section", $object->id);
            }

            $position_table = Utils::getPost('position_table');
            $object->position_table = $position_table;

            $section_table = Utils::getPost('section_table');
            $object->section_table = $section_table;

            $children_tpl = Utils::getPost('children_tpl');
            $object->children_tpl = $children_tpl;

            $leaf = Utils::getPost('leaf');
            $object->leaf = $leaf;

            $data->getSectionTable()->save($object);

            if($data->getSectionTable()->errorInfo)
            {
                throw new Exception($data->getSectionTable()->errorInfo);
            }

            $res['success'] = true;
            $res['msg'] = 'Готово';
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }

        $this->setContent(json_encode($res));
    }

    public function save_table_section()
    {
        $res = array();

        try
        {

            $data = $this->getData();

            $section_id = Utils::getVar('section_id');

            $section = $data->getSection($section_id);

            if(!$section)
            {
                throw new Exception('Объект уже удален');
            }

            $section->title = Utils::getPost('_section_title_');

            if($section->title === null || trim($section->title) == '')
            {
                throw new Exception('Название раздела не может быть пустое');
            }

            $data->getSectionTable()->save($section);

            $errorInfo = $data->getSectionTable()->errorInfo;

            if($errorInfo)
            {
                throw new Exception($errorInfo);
            }


            $table_id = $section->section_table;

            if($table_id)
            {

                $table_meta_section = $data->getTableMetaSection($table_id);

                if($table_meta_section == null)
                {
                    throw new Exception('Не найдена исходная таблица');
                }

                $fields = $table_meta_section['fields'];

                $table = new Table($table_meta_section['table']);

                $object = $table->getEntity($section->id);

                if($object == null)
                {
                    throw new Exception('Запись удалена');
                }

                foreach($fields as $field)
                {
                    if($field['type'] == "image") continue;

                    if($field['type'] == "file") continue;

                    if(!$field['edit']) continue;

                    $fn = $field['field'];

                    if($field['type'] == "date")
                    {
                        if(Utils::getVar($fn))
                        {
                            $times = explode(".",Utils::getVar($fn));
                            $object->$fn = $times ?  mktime(0,0,0,$times[1],$times[0],$times[2]) : null;
                        }
                        else
                        {
                            $object->$fn = null;
                        }
                    }
                    else
                    {
                        $object->$fn = Utils::getVar($fn);
                    }
                }

                $table->save($object);

                $errorInfo = $table->errorInfo;

                if($errorInfo)
                {
                    throw new Exception($errorInfo);
                }
            }

            $res['success'] = true;
            $res['msg'] = 'Готово';
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }

        $this->setContent(json_encode($res));
    }

    public function uploadimage()
    {
        $res = array();

        try
        {
            if($_FILES['file']['size'] > 1048576)
            {
                throw new Exception('Картинка размером больше 1000Кб');
            }

            $id = Utils::getVar('id');

            $ids = explode("/", $id);

            $section_id = $ids[0];
            $type_id = $ids[1];
            $field_id = $ids[2];
            $id = $ids[3];

            $data = $this->getData();

            $section = $data->getSection($section_id);

            if(!$section)
            {
                throw new Exception('Объект уже удален');
            }

            if($type_id == 'position')
            {
                $table_id = $section->position_table;
                $table_meta = $data->getTableMeta($table_id);
            }
            else
            {
                $table_id = $section->section_table;
                $table_meta = $data->getTableMetaSection($table_id);
            }            

            if($table_meta == null)
            {
                throw new Exception('Не найдена исходная таблица');
            }

            $table = new Table($table_meta['table']);

            if($id == 0 && $type_id == 'position')
            {
                $object = $table->getEntity();
                $object->section_id = $section_id;
                $id = $table->save($object);
                if($table->errorInfo)  throw new Exception($table->errorInfo);
            }

            $object = $table->getEntity($id);

            $fields = $table_meta['fields'];
            $field = $fields[$field_id];

            $fn = $field['field'];

            $ext = '.jpg';
            switch ($_FILES['file']['type'])
            {
                    case 'image/jpeg':
                            $ext = '.jpg';
                            break;

                    case 'image/bmp':
                            $ext = '.bmp';
                            break;

                    case 'image/png':
                            $ext = '.png';
                            break;

                    case 'image/jpg':
                            $ext = '.jpg';
                            break;

                    case 'image/gif':
                            $ext = '.gif';
                            break;

                    default:
                            $ext = '.jpg';
            }

            $tmp_name = $_FILES['file']['tmp_name'];
            $name = 'files/catalog/upload/'.md5(uniqid(rand(0, 1000000))).$ext;

            $f = move_uploaded_file($_FILES['file']['tmp_name'], SITE_PATH.$name);

            if($object->$fn && is_file(SITE_PATH.$object->$fn))
            {
                unlink(SITE_PATH.$object->$fn);
            }
            if($object->$fn && is_file(SITE_PATH.get_cache_pic($object->$fn,75,75)))
            {
                unlink(SITE_PATH.get_cache_pic($object->$fn,75,75));
            }

            $object->$fn = $name;

            $table->save($object);

            $res['success'] = true;
            $res['msg'] = 'Картинка загружена';
            $res['src'] = get_cache_pic($name,75,75); 
            $res['id'] = $id;
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }


        $this->setContent(json_encode($res));
    }

    public function uploadfile()
    {
        $res = array();

        try
        {
            $id = Utils::getVar('id');

            $ids = explode("/", $id);

            $section_id = $ids[0];
            $type_id = $ids[1];
            $field_id = $ids[2];
            $id = $ids[3];

            $data = $this->getData();

            $section = $data->getSection($section_id);

            if(!$section)
            {
                throw new Exception('Объект уже удален');
            }

            if($type_id == 'position')
            {
                $table_id = $section->position_table;
                $table_meta = $data->getTableMeta($table_id);
            }
            else
            {
                $table_id = $section->section_table;
                $table_meta = $data->getTableMetaSection($table_id);
            }            

            if($table_meta == null)
            {
                throw new Exception('Не найдена исходная таблица');
            }

            $table = new Table($table_meta['table']);

            if($id == 0 && $type_id == 'position')
            {
                $object = $table->getEntity();
                $object->section_id = $section_id;
                $id = $table->save($object);
                if($table->errorInfo)  throw new Exception($table->errorInfo);
            }

            $object = $table->getEntity($id);

            $fields = $table_meta['fields'];
            $field = $fields[$field_id];

            $fn = $field['field'];

            if(!isset($_FILES["file"]))
            {
                throw new Exception('Нет файла для загрузки');
            }

            $name = $_FILES['file']['name'];

            $ext = strrchr($name,'.');

            $file_src = 'files/catalog/upload/'.md5(uniqid(rand(0, 1000000))).$ext;

            $file_file = SITE_PATH.$file_src;

            $f = move_uploaded_file($_FILES['file']['tmp_name'], $file_file);

            if(!$f) throw new Exception('Ошибка при загрузке файла');
            
            if($object->$fn)
            {
                unlink(SITE_PATH.$object->$fn);
            }

            $object->$fn = $file_src;

            $id = $table->save($object);

            if($table->errorInfo)  throw new Exception($table->errorInfo);

            $res['success'] = true;
            $res['msg'] = 'Файл загружен';
            $res['src'] = $file_src;
            $res['id'] = $id;
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }


        $this->setContent(json_encode($res));
    }

    public function uploadfile_delete()
    {
        $res = array();

        try
        {
            $id = Utils::getVar('id');

            $ids = explode("/", $id);

            $section_id = $ids[0];
            $type_id = $ids[1];
            $field_id = $ids[2];
            $id = $ids[3];

            $data = $this->getData();

            $section = $data->getSection($section_id);

            if(!$section)
            {
                throw new Exception('Объект уже удален');
            }

            if($type_id == 'position')
            {
                $table_id = $section->position_table;
                $table_meta = $data->getTableMeta($table_id);
            }
            else
            {
                $table_id = $section->section_table;
                $table_meta = $data->getTableMetaSection($table_id);
            }

            if($table_meta == null)
            {
                throw new Exception('Не найдена исходная таблица');
            }

            $table = new Table($table_meta['table']);

            $object = $table->getEntity($id);

            $fields = $table_meta['fields'];
            $field = $fields[$field_id];

            $fn = $field['field'];

            if($object->$fn && is_file(SITE_PATH.$object->$fn))
            {
                unlink(SITE_PATH.$object->$fn);
            }

            $object->$fn = null;

            $id = $table->save($object);

            if($table->errorInfo)  throw new Exception($table->errorInfo);

            $res['success'] = true; 
            $res['msg'] = 'Файл удален';
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }


        $this->setContent(json_encode($res));
    }

}
?>
