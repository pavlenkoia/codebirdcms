<?php
$node = array();
$nodes = array();

foreach($items as $row)
{
    $node['text'] = $row['title'];
    $node['id'] = $row['id'];
    $node['leaf'] = (!$row['id2']);
    $nodes[] = $node;
}

echo json_encode($nodes);
?>
