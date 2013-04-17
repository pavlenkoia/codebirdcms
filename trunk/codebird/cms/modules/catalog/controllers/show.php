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
        $template->data = $data;
        $alias = Utils::getVar('alias');
        $uri = Utils::getVar('uri');
        $params = explode("/",$uri);
        $params = array_pad($params,10,null);
        $template->alias = $alias;
        $template->params = $params;

        $result = array();

        $section = $data->GetSection($name);

        if($section && $section->position_table)
        {
            //$result['section'] = $section;

            $table = new Table($section->position_table);

            $object = null;
            if($this->args->mode != 'list' && $params[0])
            {
                $object = $table->getEntity($params[0]);
            }

            if($object)
            {
                $result['object'] = (array)$object;
            }
            else
            {
                $page_size = $this->args->page_size ? $this->args->page_size : 20;

                $pager = $data->getPositionPagerArray($section->position_table, $section->id, $page_size);

                $order = $this->args->order;

                $rows = $table->select('select * from `'.$section->position_table.'` where section_id=:section_id '.$order.$pager['limit'], array('section_id'=>$section->id));

                $result['items'] = $rows;

                $result['pager'] = array();

                if($pager['count'] > 1)
                {
                    $uri_orig = $_SERVER['REQUEST_URI'];
                    $uris = explode('?', $uri_orig);
                    $uris = array_pad($uris,2,null);
                    $url = $uris[0];
                    $arg = $uris[1];

                    $arg = preg_replace("/[&]*page=\d*/","",$arg);

                    $page = Utils::getGet('page');

                    if(!$page || !is_numeric($page) || $page < 1 || $page > $args->count) $page = 1;

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
                            $result['pager']['items'][] = array('page'=>$i,'active'=>true,'href'=>$href);
                        }
                        else
                        {
                            $result['pager']['items'][] = array('page'=>$i,'active'=>false,'href'=>$href);
                        }
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
