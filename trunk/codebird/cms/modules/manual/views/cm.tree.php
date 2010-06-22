<?php
if($source && !$inner)
{

    $content = file_get_contents($source.'/manual/tree/');

    echo $content;

}
else
{

$node = array();
$nodes = array();

$id = Utils::getVar("node");

$table = new Table('section_manual');

$section_manual = val('catalog.section.manual');

if($id == 'root')
{
    $id = $section_manual ? $section_manual->id : -1;
}

$rows = $table->select('select s1.title, s2.*, count(s1.id) as count, p2.id as id2 from catalog_section s1 inner join section_manual s2 on s1.id=s2.id left outer join catalog_section p2 on p2.parent_id=s1.id where s1.parent_id=:id group by s1.id order by s1.position',array('id'=>$id));

foreach ($rows as $row)
{
    $node['text'] = $row['title'];
    $node['id'] = $row['id'];
    $node['leaf'] = (!$row['id2']);
    $node['src'] = "/ajax/cm/manual.show.".$row['id'];
    $nodes[] = $node;
}

echo json_encode($nodes);
} ?>
