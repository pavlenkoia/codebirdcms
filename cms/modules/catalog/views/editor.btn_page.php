<?php
$alias = Utils::getVar('alias');

echo '<input type="image" src="/cms/modules/catalog/html/images/edit.gif" alt="редактировать" title="редактировать" onclick="Editor.showEditorPage({page_id:\''.$alias.'\'});"/>';

?>
