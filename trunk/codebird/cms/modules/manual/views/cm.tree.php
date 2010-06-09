<?php
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

//switch ($id)
//{
//    case 'root':
//        $node['text'] = "О системе";
//        $node['id'] = "about";
//        $node['leaf'] = true;
//        $node['src'] = "/cms/modules/manual/html/about2.xhtml";
//        $nodes[] = $node;
//
//        $node['text'] = "Справка";
//        $node['id'] = "help";
//        $node['leaf'] = false;
//        $node['src'] = "";
//        $nodes[] = $node;
//
//        $node['text'] = "Справка по модулям";
//        $node['id'] = "help-modules";
//        $node['leaf'] = false;
//        $node['src'] = "";
//        $nodes[] = $node;
//
//        $node['text'] = "Поддержка";
//        $node['id'] = "suport";
//        $node['leaf'] = true;
//        $node['src'] = "";
//        $nodes[] = $node;
//
//        break;
//
//        break;
//    case 'help':
//        $node['text'] = "Справка 1";
//        $node['leaf'] = true;
//        $nodes[] = $node;
//
//        $node['text'] = "Справка 2";
//        $node['leaf'] = true;
//        $nodes[] = $node;
//
//        break;
//    case 'help-modules';
//        $modules = val("modmanager.cm.modules");
//        foreach($modules as $name=>$module)
//        {
//            $node['text'] = $module['title'];
//            $node['id'] = "help-module-$name";
//            $node['leaf'] = false;
//            $node['src'] = "";
//            $nodes[] = $node;
//        }
//        break;
//    default:
//        break;
//}
echo json_encode($nodes);
?>