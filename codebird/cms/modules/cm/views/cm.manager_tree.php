<?php
$node = array();
$nodes = array();

$id = Utils::getVar("node");

switch ($id)
{
    case 'root':
        $node['text'] = "Безопасность";
        $node['id'] = "security";
        $node['leaf'] = false;
        $nodes[] = $node;
        break;

    case 'security':
        echo val('security.cm.manager_tree');
        return;
        break;

    default:
        break;
}

echo json_encode($nodes);
?>
