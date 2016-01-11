<?php
$node = array();
$nodes = array();
foreach($pages as $row)
{
    $node['text'] = $row['title']/*.(!$row['id2'] ? "" : " (".$row['count'].")")*/;
    $node['id'] = $row['id'];
    $node['leaf'] = (!$row['id2']);
    array_push($nodes, $node);
}
echo json_encode($nodes);
?>
