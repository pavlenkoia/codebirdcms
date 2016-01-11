<?php
$node = array();
$nodes = array();

if(Utils::getVar('node') == 'root')
{
    $node['text'] = 'Загруженные файлы';
    $node['id'] = '/';
    $node['leaf'] = false;
    //$node['icon'] = "/cms/modules/feed/html/images/rss.gif";
    $nodes[] = $node;
}
else
{
    foreach($files as $file)
    {
        $node['text'] = $file['name'];
        $node['id'] = $file['parent'].DS.$file['name'];
        $node['leaf'] = $file['type'] == 'folder' ? false : true;
        $nodes[] = $node;
    }
}

echo json_encode($nodes);
?>
