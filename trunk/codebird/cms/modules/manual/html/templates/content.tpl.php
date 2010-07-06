<?php
$uri = Utils::getVar('uri');

$params = explode("/",$uri);

$params = array_pad($params,2,null);

if($params[0] == 'tree')
{
    mod('manual.cm.tree','inner=1');
}
else if($params[0] == 'content')
{
    $table = new Table('section_manual');

    $section = $table->getEntity($params[1]);

    if(!$section) return;

    echo $section->content;
}
?>
