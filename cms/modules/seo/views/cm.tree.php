<?php
$node = array();
$nodes = array();

$seos = array(0=>array('name'=>'robots.txt', 'id'=>122),
              1=>array('name'=>'sitemap.xml', 'id'=>132));

foreach($seos as $row)
{
    $node['text'] = $row['name'];
    $node['id'] = $row['id'];
    $node['leaf'] = true;
    $nodes[] = $node;
}
echo json_encode($nodes);
?>
