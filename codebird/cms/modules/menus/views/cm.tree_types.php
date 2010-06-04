<?php
$node = array();
$nodes = array();

$id = Utils::getVar("node");

$params = explode("/",$id);
$params = array_pad($params,2,null);

if($params[0] == 'root')
{
    $node['text'] = 'Страницы';
    $node['id'] = 'pages';
    $node['leaf'] = false;
    $nodes[] = $node;

    $node['text'] = 'Модули';
    $node['id'] = 'modules';
    $node['leaf'] = false;
    $nodes[] = $node;

    $node['text'] = 'Внешняя ссылка';
    $node['id'] = 'link';
    $node['leaf'] = true;
    $node['type'] = '_link';
    $node['type_label'] = "Внешняя ссылка";
    $node['type_link'] = "/";
    $nodes[] = $node;

    $node['text'] = 'Просто заголовок';
    $node['id'] = 'label';
    $node['leaf'] = true;
    $node['type'] = '_label';
    $node['type_label'] = "Просто заголовок";
    $node['type_link'] = "";
    $nodes[] = $node;
}
elseif($params[0] == 'pages')
{
    $table = new Table("pages");

    if(!$params[1])
    {
        $sql = "select p1.*, count(p1.id) as count, p2.id as id2 from pages p1 left outer join pages p2 on ( p2.parent_id=p1.id) where p1.parent_id is null group by p1.id order by p1.position";
        $rows = $table->select($sql);
    }
    else
    {
        $sql = "select p1.*, count(p1.id) as count, p2.id as id2 from pages p1 left outer join pages p2 on ( p2.parent_id=p1.id) where p1.parent_id=:id group by p1.id order by p1.position";
        $rows = $table->select($sql,array("id"=>$params[1]));
    }
    foreach($rows as $row)
    {
        $node['text'] = $row['title'];
        $node['id'] = 'pages/'.$row['id'];
        $node['leaf'] = (!$row['id2']);
        $node['type'] = '_pages';
        $node['type_id'] = $row['id'];
        $node['type_label'] = "Страница: ".$row['title'];
        $node['type_link'] = "/".$row['alias'].".html";
        array_push($nodes, $node);
    }
}
elseif($params[0] == 'modules')
{
    $modules = val("modmanager.cm.modules");
    foreach($modules as $name=>$module)
    {
        $config = Config::getConfig($name);
        if(isset($config->site) && $config->site === true)
        {
            $node['text'] = $module['title'];
            $node['id'] = "modules/$name";
            $node['leaf'] = true;
            $node['type'] = $name;
            $node['type_label'] = "Модуль: ".$module['title'];
            $node['type_link'] = "/$name/";
            $nodes[] = $node;
        }
    }
}

echo json_encode($nodes);
?>
