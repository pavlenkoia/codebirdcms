<?php
echo '<input type="image" src="/cms/modules/catalog/html/images/edit.gif" alt="редактировать" title="редактировать" onclick="Editor.showEditorSection({section_id:'.$section_id.'});"/>';
echo '<input type="image" src="/cms/modules/catalog/html/images/delete.gif" alt="удалить" title="удалить" onclick="Editor.deleteSection({section_id:'.$section_id.'});"/>';
?>
