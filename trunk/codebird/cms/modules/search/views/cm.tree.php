<?php
$node = array();
$nodes = array();

foreach($sites_rows as $row)
{
    $node['text'] = $row['url'];
    $node['id'] = 'site_'.$row['site_id'];
    $node['leaf'] = true;
    $nodes[] = $node;
}



echo json_encode($nodes);
?>