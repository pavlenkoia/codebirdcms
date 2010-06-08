<?php
$node = array();
$nodes = array();

$id = Utils::getVar("node");

$table = new Table('section_manual');

$section_manual = val('catalog.section.manual');
echo $section_manual;

if($id == 'root')
{
    $rows = $table->select('select s1.title, s2.* from catalog_section s1 inner join section_manual s2 on s1.id=s2.id where s1.parent_id=:id order by s1.position',array('id'=>$section_manual->id));

    foreach ($rows as $row)
    {
        $node['text'] = $row['title'];
        $node['id'] = $row['id'];
        $node['leaf'] = true;
        $node['src'] = "/ajax/cm/manual.show.".$row['id']; 
        $nodes[] = $node;
    }
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
