<?php
echo '<input type="image" src="/cms/modules/catalog/html/images/edit.gif" alt="редактировать" title="редактировать" onclick="Editor.showEditor({position_id:'.$position_id.',section_id:'.$section_id.'});"/>';
echo '<input type="image" src="/cms/modules/catalog/html/images/delete.gif" alt="удалить" title="удалить" onclick="Editor.deletePosition({position_id:'.$position_id.',table_id:\''.$table_id.'\'});"/>';
?>
