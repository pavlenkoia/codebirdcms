<?php
$node = array();
$nodes = array();

foreach($feeds as $row)
{
    $node['text'] = $row['name'];
    $node['id'] = $row['id'];
    $node['leaf'] = true;
    $node['icon'] = "/cms/modules/feed/html/images/rss.gif";
    $nodes[] = $node;
}

echo json_encode($nodes);
?>
