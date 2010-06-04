<?php
$node = array();
$nodes = array();

$node['text'] = "Пользователи";
$node['id'] = "security-users";
$node['leaf'] = true;
$node['editorIcon'] = "/cms/modules/security/html/images/user.gif";
$node['icon'] = "/cms/modules/security/html/images/user.gif";
if(!$data->inRole('admin'))
{
    $node['access'] = false;
}
else
{
    $node['editor'] = "/ajax/cm/security.cm.editor_users";
}
$nodes[] = $node;

echo json_encode($nodes);

?>
