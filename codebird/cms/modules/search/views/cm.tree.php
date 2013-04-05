<?php
$node = array();
$nodes = array();

$node['text'] = 'Настройки';
$node['id'] = 'settings';
$node['leaf'] = true;
$nodes[] = $node;

echo json_encode($nodes);
?>