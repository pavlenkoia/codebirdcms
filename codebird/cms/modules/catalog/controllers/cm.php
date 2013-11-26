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

            $sections = $data->getSections($section ? $section->id : null);
        }
        else
        {
            $section = $data->getSection($parent_id);

            $order = null;
            if(isset($section->order_child))
            {
                $order = $section->order_child;
            }

            $sections = $data->getSections($parent_id, $order);
        }

        $template->sections = $sections;

        $template->render();
    }

    public function editor()
    {
        $id = Utils::getVar('id');

        $data = $this->getData();

        $alias = Utils::getVar('alias');

        $object = $data->getSection($id);

        if($object)
        {
            $template = $this->createTemplate();
            
            $template->section = $object;

            $template->alias = $alias;
            
            $table_meta = $data->getTableMeta($object->position_table);
            
            if($table_meta)
            {
                $template->table_id = $object->position_table;

                $template->table_title = $table_meta['title'];

                $template->table_name = $table_meta['table'];

                $template->page_size = isset($table_meta['page_size']) ? $table_meta['page_size'] : 100;

                $template->fields = $table_meta['fields'];

                $template->import = isset($table_meta['import']) ? true : null;
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
            
            $alias = Utils::getPost('alias');


            $object = $data->getSection();

            $object->section_table = null;

            $object->title = Utils::getPost("title");

            // генерация алиаса
            $alias = $object->title;
            $alias = Utils::translit($alias);
            $object->alias = Utils::getUniqueAlias($alias, "catalog_section", $object->id);


            if($object->title === null || trim($object->title) == '')
            {
                throw new Exception('Заголовок не указан');
            }

            $parent_id = Utils::getPost('parent_id');

            $parent_id = is_numeric($parent_id) ? $parent_id : null; 

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
                        if($xml->subs)
                        {
                            $subs = $xml->subs;
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

            $section_object = $data->getSectionTable()->getEntity($id);

            if($object->section_table)
            {
                $table_meta_section = $data->getTableMetaSection($object->section_table);

                if($table_meta_section)
                {
                    $table_section = new Table($table_meta_section['table']);

                    $section_data = $table_section->getEntity($id);

                    if(!$section_data)
                    {
                        $table_section->execute('insert into '.$table_meta_section['table'].'(id) values(:id)', array('id'=>$id));
                    }
                }
            }

            if(isset($subs))
            {
                foreach($subs->sub as $sub)
                {                    
                    $object = $data->getSection();
                    $object->parent_id = $id;
                    $object->position = count($data->getSections($id))+1;
                    $object->title = $sub->title;
                    if($sub->section)
                    {
                        $object->section_table = $sub->section;
                    }
                    if($sub->position)
                    {
                        $object->position_table = $sub->position;
                    }
                    if($sub->leaf)
                    {
                        $object->leaf = $sub->leaf;
                    }
                    if($sub->children && $sub->children->tpl)
                    {
                        $object->children_tpl = $sub->children->tpl->asXML();
                    }
                    
                    $data->getSectionTable()->save($object);

                    if($data->getSectionTable()->errorInfo)
                    {
                        throw new Exception($data->getSectionTable()->errorInfo);
                    }
                }
            }

            $res['success'] = true;
            $res['msg'] = 'Добавлено';

            val('catalog.cache.clear');

            if(Event::HasHandlers('OnCatalogAddSection'))
            {
                $params = array();
                $params['object'] = $section_object;
                $params['section'] = $data->getSection($section_object->id);
                Event::Execute('OnCatalogAddSection', $params);
            }
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

            val('catalog.cache.clear');
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

        $order = isset($table_meta['order']) ?  $table_meta['order'] : null;

        if(isset($table_meta['sql']))
        {
            $tsql = $table_meta['sql'];
        }
        else
        {
            $tsql = "select * from ".$table_meta['table'].' where section_id=:section_id '.$order;
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

                if($field['type'] == "images") continue;

                if($field['type'] == "file") continue;

                if(!$field['edit']) continue;
                
                $fn = $field['field'];

                if($field['type'] == "date")
                {
                    if(Utils::getVar($fn))
                    {
                        if($id != 0 && $object->$fn)
                        {
                            $getdate = getdate($object->$fn);
                        }
                        else
                        {
                            $getdate = getdate(time());
                        }
                        $hours = $getdate['hours'];
                        $minutes = $getdate['minutes'];

                        $times = explode(".",Utils::getVar($fn));
                        $object->$fn = $times ?  mktime($hours,$minutes,0,$times[1],$times[0],$times[2]) : null;
                    }
                    else
                    {
                        $object->$fn = null;
                    }
                }
                else
                {
                    if($field['type'] == 'richtext')
                    {
                        $content = Utils::getVar($fn);
                        if($content !== null)
                        {
                            $object->$fn = $content;
                        }
                    }
                    else
                    {
                        $object->$fn = Utils::getVar($fn);
                    }
                }
            }

            $id = $table->save($object);

            $errorInfo = $table->errorInfo;

            if($errorInfo)
            {
                throw new Exception($errorInfo);
            }
          

            if(isset($table_meta['onsave']) && $table_meta['onsave'])
            {
                if(!isset($object->id)) $object = $table->getEntity($id);
                val($table_meta['onsave'],array('object'=>$object));
            }

            if(Event::HasHandlers('OnCatalogSavePosition'))
            {
                if(!isset($object->id)) $object = $table->getEntity($id);
                $params = array();
                $params['object'] = $object;
                $params['section'] = $data->getSection($object->section_id);
                Event::Execute('OnCatalogSavePosition', $params);
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

            $rows = $table->select("select * from ".$table_meta['table']." where id in($id)");

            foreach($rows as $row)
            {
                foreach($fields as $field)
                {
                    if($field['type'] == 'image' || $field['type'] == 'file')
                    {
                        if($row[$field['field']] && is_file(SITE_PATH.$row[$field['field']]))
                        {
                            unlink(SITE_PATH.$row[$field['field']]);
                        }
                    }
                }
            }

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
            else
            {
                $alias = Utils::translit($object->title);
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

            if(Event::HasHandlers('OnCatalogSaveSection'))
            {
                $params = array();
                $params['object'] = $object;
                Event::Execute('OnCatalogSaveSection', $params);
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
                        if($field['type'] == 'richtext')
                        {
                            $content = Utils::getVar($fn);
                            if($content !== null)
                            {
                                $object->$fn = $content;
                            }
                        }
                        else
                        {
                            $object->$fn = Utils::getVar($fn);
                        }
                    }
                }

                $id = $table->save($object);

                $errorInfo = $table->errorInfo;

                if($errorInfo)
                {
                    throw new Exception($errorInfo);
                }

                if(isset($table_meta_section['onsave']) && $table_meta_section['onsave'])
                {
                    if(!isset($object->id)) $object = $table->getEntity($id);
                    val($table_meta_section['onsave'],array('object'=>$object));
                }

                if(Event::HasHandlers('OnCatalogSaveTableSection'))
                {
                    $object = $table->getEntity($object->id);
                    $params = array();
                    $params['object'] = $object;
                    $params['section'] = $section;
                    Event::Execute('OnCatalogSaveTableSection', $params);
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
            if($_FILES['file']['size'] > 3145728)
            {
                throw new Exception('Картинка размером больше 3MB');
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
                            //$ext = '.';
            }

            if($ext == '.bmp' || $ext == '.')
            {
                throw new Exception('Картинка такого формата не поддерживается');
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

            if($object->$fn && is_file(SITE_PATH.$object->$fn))
            {
                unlink(SITE_PATH.$object->$fn);
            }

            if(is_file(SITE_PATH.'files/catalog/upload/'.$name))
            {
                $file_src = 'files/catalog/upload/'.md5(uniqid(rand(0, 1000000))).$ext;
            }
            else
            {
                $file_src = 'files/catalog/upload/'.$name;
            }

            $file_file = SITE_PATH.$file_src;

            $f = move_uploaded_file($_FILES['file']['tmp_name'], $file_file);

            if(!$f) throw new Exception('Ошибка при загрузке файла');
            
            

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

    public function  import_form()
    {
        $template = $this->createTemplate();

        $section_id = Utils::getVar('section_id');

//        $data = $this->getData();
//
//        $object = $data->getSection($id);

        $table_id = Utils::getVar('table_id');

        $template->table_id = $table_id;

        $template->section_id = $section_id;

        $template->render();
    }

    public function import()
    {
        $res = array();
        try
        {
            $data = $this->getData();

            if(!isset($_FILES["file"]))
            {
                throw new Exception('Нет файла для загрузки');
            }

            $file = $_FILES['file']['tmp_name'];
            
            $handle = fopen($file, "r");

            if(!$handle) throw new Exception('Нет файла');

            $section_id = Utils::getVar('section_id');

            $table_meta = $data->getTableMeta(Utils::getVar('table_id'));

            if(!$table_meta || !isset($table_meta['import']))
            {
                throw new Exception('Ошибка конфигурации');
            }

            $name = $table_meta['import'];
            
            $imports = $this->config->$name;

            setlocale(LC_ALL, 'ru_RU.UTF-8');

            $row = 0;

            $maping = array();
            
            $values = array();

            $table = new Table($table_meta['table']);

            $mode = Utils::getVar('mode');

            if($mode == 2)
            {
                $table->execute('delete from '.$table_meta['table'].' where section_id=:id',
                        array('id'=>$section_id));
            }


            $delim = Utils::getVar('delim');
            switch($delim)
            {
                case 1:
                    $delim = ";";
                    break;
                case 2:
                    $delim = ",";
                    break;
                case 3:
                    $delim = "\t";
                    break;
                default:
                    $delim = ';';
            }

            while (($data = $this->xfgetcsv($handle, 1000, $delim)) !== FALSE)
            {
                if($row == 0)
                {
                    for($i = 0; $i < count($data); $i++)
                    {
                        foreach($imports as $import)
                        {
                            if(isset($import['value']))
                            {
                                $map = array();
                                $map['value'] = $import['value'];
                                $map['field'] = $import['field'];
                                $values[] = $map;
                                continue;
                            }
                            if(isset($data[$i]) && trim(mb_strtolower($data[$i])) == trim(mb_strtolower($import['import'])))
                            {
                                $map = array();
                                $map['index'] = $i;
                                $map['field'] = $import['field'];
                                $maping[] = $map;
                            }
                        }
                    }
                }
                else
                {
                    $object = $table->getEntity();
                    $object->section_id = $section_id;
                    foreach($values as $value)
                    {
                        $field = $value['field'];
                        
                        if($value['value'] == 'time()')
                        {
                            $object->$field = time();
                        }
                    }
                    foreach($maping as $map)
                    {
                        if(!isset($data[$map['index']])) continue;
                        
                        $field = $map['field'];
                        
                        $object->$field = trim($data[$map['index']]);
                    }
                    $table->save($object);
                    if($table->errorInfo)
                    {
                        throw new Exception($table->errorInfo);
                    }
                }

                $row++;
            }


            fclose($handle);

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

    private function xfgetcsv($f='', $x='', $s=';', $convert=false)
    {
        $str = fgets($f);
        if($str)
        {
            if(json_encode($str) == 'null' /*|| json_encode($str) === null*/)
            {
                $str = iconv("CP1251", "UTF-8",$str);
            }
            $data= explode($s, trim($str));

            return $data;
        }else
        {
            return FALSE;
        }
    }


    public function select()
    {
        $data = $this->getData();

        $table_id = Utils::getVar('table_id');

        $field = Utils::getVar('field');

        $table_meta = $data->getTableMeta($table_id);

        $fields = $table_meta['fields'];

        $select = $fields[$field]['select'];

        $table = new Table($table_meta['table']);

        $rows = $table->select($select);

        $template = $this->createTemplate();

        $template->rows = $rows;

        $template->table_meta = $table_meta;

        $template->render();
    }

    public function images_form()
    {
        $template = $this->createTemplate();

        $template->page_size = 20;

        $template->id = Utils::getVar('id');

        $template->title = Utils::getVar('title');

        $template->render();
    }

    public function images_records()
    {

        $template = $this->createTemplate();

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

        $images = $object->$fn;

        if(!$images)
        {
            $xml = simplexml_load_string(
'<?xml version="1.0" encoding="UTF-8"?>
<images>
</images>');
        }
        else
        {
            $xml = simplexml_load_string($images);
        }



        $template->xml = $xml;

        $template->render();
    }

    public function uploadimages()
    {
        $res = array();

        try
        {
            $file = $_FILES['file']['size'];

            if($file)
            {
                if($_FILES['file']['size'] > 3145728)
                {
                    throw new Exception('Картинка размером больше 3MB');
                }
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

        $object = $table->getEntity($id);

        $fields = $table_meta['fields'];
        $field = $fields[$field_id];

        $fn = $field['field'];

        $images = $object->$fn;

        if(!$images)
        {
            $xml = simplexml_load_string(
'<?xml version="1.0" encoding="UTF-8"?>
<images>
</images>');
        }
        else
        {
            $xml = simplexml_load_string($images);
        }

            if($file)
            {
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
                        //$ext = '.';
                }

                if($ext == '.bmp' || $ext == '.')
                {
                    throw new Exception('Картинка такого формата не поддерживается');
                }

                $tmp_name = $_FILES['file']['tmp_name'];
                $name = 'files/catalog/upload/'.md5(uniqid(rand(0, 1000000))).$ext;

                $f = move_uploaded_file($_FILES['file']['tmp_name'], SITE_PATH.$name);

            }

            /*if($object->$fn && is_file(SITE_PATH.$object->$fn))
            {
                unlink(SITE_PATH.$object->$fn);
            }
            if($object->$fn && is_file(SITE_PATH.get_cache_pic($object->$fn,75,75)))
            {
                unlink(SITE_PATH.get_cache_pic($object->$fn,75,75));
            }*/

            $img = isset ($ids[4]) ? $ids[4] : null;
            $img  = str_replace('\\', '/', $img);
            if($img)
            {

                foreach($xml->image as $image)
                {
                    if((string)$image->img == $img)
                    {
                        if($file)
                        {
                            unlink(SITE_PATH.(string)$image->img);

                            if(is_file(SITE_PATH.get_cache_pic(SITE_PATH.(string)$image->img,75,75)))
                            {
                                unlink(SITE_PATH.get_cache_pic((string)$image->img,75,75));
                            }

                            $image->img = $name;
                        }


                        $title = Utils::getVar('title');
                        if($image->title)
                        {
                            $image->title = $title;
                        }
                        else
                        {
                            $image->addChild('title',$title);
                        }

                        break;
                        
                    }
                }
            }
            else
            {
                $image = $xml->addChild('image');
                $img = $image->addChild('img',$name);
                if($title = Utils::getVar('title'))
                {
                    $image->addChild('title',$title);
                }

            }

            $object->$fn = $xml->asXml();

            $table->save($object);

            $res['success'] = true;
            $res['msg'] = 'Картинка загружена';

            //$res['src'] = get_cache_pic($name,75,75);
            //$res['id'] = $id;
            $s = '';
            foreach($xml->image as $image)
            {
                $s .= '<img src="'.get_cache_pic($image->img,75,75).'"/>';
            }
            //$res['msg'] = $s;
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }


        $this->setContent(json_encode($res));
    }

    public function deleteimages()
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

            $images = $object->$fn;

            if(!$images)
            {
                $xml = simplexml_load_string(
                        '<?xml version="1.0" encoding="UTF-8"?>
<images>
</images>');
            }
            else
            {
                $xml = simplexml_load_string($images);
            }

            $images = Utils::getVar('images');

            $images = explode(",", $images);


            for($i = count($xml->image)-1; $i >= 0; $i--)
            {
                $image = $xml->image[$i];
                if(in_array((string)$image->img, $images))
                {
                    $dom = dom_import_simplexml($image);
                    $dom->parentNode->removeChild($dom);


                    unlink(SITE_PATH.(string)$image->img);

                    if(is_file(SITE_PATH.get_cache_pic(SITE_PATH.(string)$image->img,75,75)))
                    {
                        unlink(SITE_PATH.get_cache_pic((string)$image->img,75,75));
                    }

                }
            }


            $object->$fn = $xml->asXml();

            $table->save($object);

            $res['success'] = true;
            $res['msg'] = 'Картинка удалена';
            //$res['src'] = get_cache_pic($name,75,75);
            //$res['id'] = $id;
        }
        catch(Exception $e)
        {
            $res['success'] = false;
            $res['msg'] = $e->getMessage();
        }


        $this->setContent(json_encode($res));
    }

    public function getimages()
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

            $images = $object->$fn;

            if(!$images)
            {
                $xml = simplexml_load_string(
                        '<?xml version="1.0" encoding="UTF-8"?>
<images>
</images>');
            }
            else
            {
                $xml = simplexml_load_string($images);
            }


            $res['success'] = true;
            $s = '';
            foreach($xml->image as $image)
            {
                $s .= '<img src="'.get_cache_pic($image->img,75,75).'"/>';
            }
            $res['msg'] = $s;
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
