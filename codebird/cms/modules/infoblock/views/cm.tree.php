<?php
$node = array();
$nodes = array();

foreach($infoblocks as $row)
{
    $node['text'] = $row['name'];
    $node['id'] = $row['id'];
    $node['leaf'] = true;
    $nodes[] = $node;
}

echo json_encode($nodes);
?>
