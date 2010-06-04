<table class="content-table">
    <tr>
        <td>
            <ol class="page_list">
                <li>
                    <a class="main" href="admin.php?mod=pages">Создать</a>
                </li>

                <?php
                if(isset($parent_page))
                {
                    echo '<li>
            <a class="main" href="admin.php?mod=pages&mod_action=edit&id='.$parent_page->id.'">[...] '.$parent_page->title.'</a>
            </li>';
                }
                ?>
                <?php
                $data->parent_id = isset($parent_page) ? $parent_page->id : null;
                foreach($data->pages as $row)
                {
                    echo '<li class="set">
        <a href="?mod=pages&mod_action=moveup&id='.$row['id'].'" title="Наверх">↑</a>
        <a href="?mod=pages&mod_action=movedown&id='.$row['id'].'" title="Вниз">↓</a>
        <a href="admin.php?mod=pages&mod_action=edit&id='.$row['id'].'" class="title"> '.$row['title'].'</a>
        <a href="admin.php?mod=pages&p_id='.$row['id'].'" title="Добавить"><b style="color: green;">+</b></a><b style="color: green;"></b>
        <a href="#" title="Удалить" onclick="delete_page(\''.$row['id'].'\',\''.$row['title'].'\');return false;"><b style="color: red;">-</b></a><b style="color: red;"></b>
        </li>';        
                }
                ?>
            </ol>
        </td>
        <td>
            <form name="form_add_pages" method="post">
                <h2>Добавить страницу:
                    <?php
                    if(!isset($parent_page))
                    {
                        echo " на первый уровень";
                    }
                    else
                    {
                        echo " вложением к «".$parent_page->title."»";
                    }
                    ?>
                </h2>
                <?php
                if(isset($error_message))
                {
                    echo "<div class='error_message'>Ошибка: $error_message</div>";
                }
                if(isset($info_message))
                {
                    echo "<div class='info_message'>$info_message</div>";
}
?>
                <table class="table-form" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td>Заголовок:</td>
                            <td><input size="90" name="title" type="text"></td>
                        </tr>

                        <tr>
                            <td>Содержание:</td>
                            <td><textarea id="add_pages_content" name="content" cols="80" rows="3"></textarea>
                        </tr>

                        <tr>
                            <td>Шаблон</td>
                            <td>
                                <select name="template">
                                    <?php
                                    $registry = Registry::__instance();
                                    foreach(Config::__("pages")->templates as $tmpl=>$name)
                                    {
                                        echo '<option value="'.$tmpl.'">'.$name.'</option>';
}
?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <input name="Submit" value="Добавить" type="submit">
                                <input name="mod_action" value="save" type="hidden">
                                <input name="mod_view" value="add_page" type="hidden">
                                <?php
                                if(isset($parent_page))
                                {
                                    echo '<input name="p_id" value="'.$parent_page->id.'" type="hidden">';
}
?>
                            </td>
                        </tr>

                    </tbody></table>
            </form>
        </td>
    </tr>
</table>