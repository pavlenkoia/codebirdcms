<?php
$node = array();
$nodes = array();

if(count($files) == 0)
{
    $node['text'] = 'Файлы';
    $node['id'] = 'root';
    $node['leaf'] = false;
    //$node['icon'] = "/cms/modules/feed/html/images/rss.gif";
    $nodes[] = $node;
}
else
{
    
}

//foreach($feeds as $row)
{
//    $node['text'] = 'Один';
//    $node['id'] = 1;
//    $node['leaf'] = true;
//    $node['icon'] = "/cms/modules/feed/html/images/rss.gif";
//    $nodes[] = $node;
//
//    $node['text'] = 'Два';
//    $node['id'] = 2;
//    $node['leaf'] = true;
//    $node['icon'] = "/cms/modules/feed/html/images/rss.gif";
//    $nodes[] = $node;
}

echo json_encode($nodes);
?>
