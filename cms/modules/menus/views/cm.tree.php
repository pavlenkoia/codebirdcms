<?php
$node = array();
$nodes = array();

foreach($menus as $row)
{
    $node['text'] = $row['title'];
    $node['id'] = $row['id'];
    $node['leaf'] = true;
    $nodes[] = $node;
}

echo json_encode($nodes);
?>
