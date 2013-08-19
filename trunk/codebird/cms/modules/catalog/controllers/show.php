<?php
/* 
 * 
 */

class CatalogController_Show extends Controller_Base
{
    
    private function show($name, $args)
    {
        $template = $this->createTemplate();

        $data = $this->getData('action');
        $data_config = $this->getData('config');
        $template->data = $data;
        $alias = Utils::getVar('alias');
        $uri = Utils::getVar('uri');
        $params = explode("/",$uri);
        $params = array_pad($params,10,null);
        $template->alias = $alias;
        $template->params = $params;

        $result = array();

        $section = $data->GetSection($name);

        $object_level = $this->args->object_level ? $this->args->object_level : 0;

        if($section && $section->position_table)
        {
            $result['section'] = $section;

            $tables = $data_config->GetParam('tables');
            $position_table =  $tables[$section->position_table]['table'];

            $table = new Table($position_table);

            $object = null;
            $param = $params[$object_level];
            if($this->args->mode != 'list' && $param)
            {
                $object = $table->getEntity($param);
            }

            if($object && $object->section_id == $section->id)
            {
                $result['object'] = (array)$object;
            }
            else
            {
                $page_size = $this->args->page_size;

                if(!$page_size && $section->section_table )
                {
                    $tables = $data_config->GetParam('tables_section');
                    $section_table_name =  $tables[$section->section_table]['table'];

                    $section_table = new Table($section_table_name);
                    $section_data = $section_table->getEntity($section->id);
                    if($section_data && $section_data->page_size)
                    {
                        $page_size = $section_data->page_size;
                    }
                }

                $page_size = $page_size ? $page_size : 20;

                $where = $this->args->where;
                $where = $where ? ' and '.$where.' ' : '';

                $pager = $data->getPositionPagerArray($position_table, $section->id, $page_size, $where);

                $order = $this->args->order;

                $rows = $table->select('select * from `'.$position_table.'` where section_id=:section_id '.$where.$order.$pager['limit'], array('section_id'=>$section->id));

                foreach($rows as $row)
                {
                    $row['_url'] = '/'.$alias.'/';
                    for($i = 0; $i <  $object_level; $i++)
                    {
                        $row['_url'] .= $params[$i].'/';
                    }
                    $row['_url'] .= $row['id'].'/';
                    $result['items'][] = $row;
                }


                if($pager['count'] > 1)
                {
                    $result['pager'] = array();

                    $uri_orig = $_SERVER['REQUEST_URI'];
                    $uris = explode('?', $uri_orig);
                    $uris = array_pad($uris,2,null);
                    $url = $uris[0];
                    $arg = $uris[1];

                    $arg = preg_replace("/[&]*page=\d*/","",$arg);

                    $page = Utils::getGet('page');

                    if(!$page || !is_numeric($page) || $page < 1 || $page > $pager['count']) $page = 1;

                    $result['pager']['pre'] = null;
                    $result['pager']['next'] = null;
                    $result['pager']['start'] = null;
                    $result['pager']['end'] = null;

                    $active = false;

                    for($i=1; $i<=$pager['count']; $i++ )
                    {
                        $arg_page = $i===1 ? '' : 'page='.$i;

                        if($arg)
                        {
                            $href = $arg_page ? $url.'?'.$arg.'&'.$arg_page : $url.'?'.$arg;
                        }
                        else
                        {
                            $href = $arg_page ? $url.'?'.$arg_page : $url;
                        }

                        if($page==$i)
                        {
                            $item = array('page'=>$i,'active'=>true,'href'=>$href);
                            $result['pager']['items'][] = $item;

                            $result['pager']['pre'] = $result['pager']['end'];

                            $active = true;
                        }
                        else
                        {
                            $item = array('page'=>$i,'active'=>false,'href'=>$href);
                            $result['pager']['items'][] = $item;

                            if($active && !$result['pager']['next'])
                            {
                                $result['pager']['next'] = $item;
                            }
                        }

                        if(!$result['pager']['start'])
                        {
                            $result['pager']['start'] = $item;
                        }
                        $result['pager']['end'] = $item;
                    }
                }
            }
        }

        $template->result = $result;

        $template->render();
    }

    public function __call($name, $args)
    {
       $this->show($name, $args);
    }

    public function index()
    {
    }
    
}

?>
