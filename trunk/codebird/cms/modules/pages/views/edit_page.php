<table class="content-table">
    <tr>
        <td>
            <ol class="page_list">
                <li>
                    <a class="main" href="admin.php?mod=pages<?php if($page->parent_id) echo "&p_id=".$page->id; ?>">Создать</a>
                </li>

                <?php
                $data->parent_id = $page->parent_id;
                if($page->parent_id)
                {
                    $parent_page = $data->getPage($page->parent_id);
                    echo '<li>
            <a class="main" href="admin.php?mod=pages&mod_action=edit&id='.$page->parent_id.'">[...] '.$parent_page->title.'</a>
            <a href="admin.php?mod=pages&p_id='.$page->parent_id.'"><b style="color: green;">+</b></a><b style="color: green;"></b>
            </li>';
                }
                $pages1 = $data->pages;
                foreach($pages1 as $row)
                {
                    echo '<li class="set">
        <a href="?mod=pages&mod_action=moveup&id='.$row['id'].'&mod_view=edit_page" title="Наверх">↑</a>
        <a href="?mod=pages&mod_action=movedown&id='.$row['id'].'&mod_view=edit_page" title="Вниз">↓</a>
        <a href="admin.php?mod=pages&mod_action=edit&id='.$row['id'].'" class="title"> '.$row['title'].'</a>
        <a href="admin.php?mod=pages&p_id='.$row['id'].'" title="Добавить"><b style="color: green;">+</b></a><b style="color: green;"></b>
        <a href="#" onclick="delete_page(\''.$row['id'].'\',\''.$row['title'].'\');return false;" title="Удалить"><b style="color: red;">-</b></a><b style="color: red;"></b>
        </li>';
                    if($row['id'] == $page->id)
                    {
                        $data->parent_id = $page->id;
                        $pages2 = $data->pages;
                        foreach($pages2 as $row2)
                        {
                            echo '<ul>
                <li class="set">
                <a href="?mod=pages&mod_action=moveup&id='.$row2['id'].'&mod_view=edit_page">↑</a>
                <a href="?mod=pages&mod_action=movedown&id='.$row2['id'].'&mod_view=edit_page">↓</a>
                <a href="admin.php?mod=pages&mod_action=edit&id='.$row2['id'].'" class="title"> '.$row2['title'].'</a>
                <a href="admin.php?mod=pages&p_id='.$row2['id'].'"><b style="color: green;">+</b></a><b style="color: green;"></b>
                <a href="#" onclick="delete_page(\''.$row2['id'].'\',\''.$row2['title'].'\');return false;"><b style="color: red;">-</b></a><b style="color: red;"></b>
                </li>
                </ul>';
                        }
                    }
                }
                ?>
            </ol>
        </td>
        <td>
            <form name="form_edit_pages" method="post" action="admin.php?mod=pages">
                <h2>Редактировать страницу:  <?php echo $page->title ?></h2>
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
                        <tr>
                            <td>Заголовок:</td>
                            <td><input size="60" name="title" value="<?php echo $page->title ?>" type="text"/></td>
                        </tr>
                        <tr>

                            <td>Псевдоним (ссылка):</td>
                            <td><input size="60" name="alias" value="<?php echo $page->alias ?>" type="text">.html</td>
                        </tr>
                        <tr>
                            <td>Содержание:</td>
                            <td>
                                <textarea id="edit_pages_content" name="content" cols="30" rows="3">
                                    <?php echo $page->content ?>
                                </textarea>
                            </td>

                        </tr>
                        <tr>
                            <td>Шаблон</td>
                            <td>
                                <select name="template">
                                    <?php
                                    $registry = Registry::__instance();
                                    foreach(Config::__("pages")->templates as $tmpl=>$name)
                                    {
                                        if($page->template and $page->template == $tmpl)
                                        {
                                            echo '<option value="'.$tmpl.'" selected>'.$name.'</option>';
                                        }
                                        else
                                        {
                                            echo '<option value="'.$tmpl.'">'.$name.'</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Видимый в меню</td>
                            <td><input name="visible" value="1" <?php if(1==$page->visible)
{echo 'checked';} ?> type="checkbox"></td>
                        </tr>
                        <tr>
                            <td>Главная страница</td>
                            <td><input name="mainpage" value="1" <?php if(1==$page->mainpage) {echo 'checked';} ?> type="checkbox"></td>
                        </tr>
                        <?php
                        if(isset(Config::__('pages')->plugins))
                        {
                            foreach(Config::__('pages')->plugins as $plugin)
                            {
                                if(!$plugin['plug']) continue;

                                $plugin_value = "";

                                if(isset($page->plugins))
                                {
                                    $plugins = explode(";",$page->plugins);
                                    foreach($plugins as $plug)
                                    {
                                        $plugs = explode(":",$plug);
                                        if($plugs[0] == $plugin['name'])
                                        {
                                            $plugin_value = $plug;
                                            break;
                                        }
                                    }
                                }
                                echo '<tr>
          <td align="left" valign="top" width="120">'.$plugin['label'].'</td>
          <td>'.val($plugin['mod'],"name=".$plugin['name'].'&value='.$plugin_value).'</td>
        </tr>';
                            }
}
?>
                        <tr>
                            <td>&nbsp;</td>

                            <td><input name="Submit" value="Сохранить" type="submit">
                                <input name="mod_action" value="save" type="hidden">
                                <input name="mod_view" value="edit_page" type="hidden">
                                <input name="id" value="<?php echo $page->id ?>" type="hidden">
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <hr>
                                Вложенные страницы:
                                <?php
                                if(isset(Config::__('pages')->plugins['submenu']))
                                {
                                    $plugin = Config::__('pages')->plugins['submenu'];

                                    $plugin_value = "";
                                    if(isset($page->plugins))
                                    {
                                        $plugins = explode(";",$page->plugins);
                                        foreach($plugins as $plug)
                                        {
                                            $plugs = explode(":",$plug);
                                            if($plugs[0] == $plugin['name'])
                                            {
                                                $plugin_value = $plug;
                                                break;
                                            }
                                        }
                                    }
                                    echo '[ '.val($plugin['mod'],"name=".$plugin['name'].'&value='.$plugin_value).' '.$plugin['label'].' ]';
}
?>
                                <ol class="page_list2">
                                    <li class="set">
                                        <?php
                                        $data->parent_id = $page->id;
                                        foreach($data->pages as $row)
                                        {
                                            echo '<li class="set">
                        <a href="?mod=pages&mod_action=moveup&id='.$row['id'].'&mod_view=edit_page">↑</a>
                        <a href="?mod=pages&mod_action=movedown&id='.$row['id'].'&mod_view=edit_page">↓</a>
                        <a href="admin.php?mod=pages&mod_action=edit&id='.$row['id'].'" class="title"> '.$row['title'].'</a>
                        <a href="admin.php?mod=pages&p_id='.$row['id'].'"><b style="color: green;">+</b></a><b style="color: green;"></b>
                        <a href="#" onclick="delete_page(\''.$row['id'].'\',\''.$row['title'].'\');return false;"><b style="color: red;">-</b></a><b style="color: red;"></b>
                        </li>';
}
?>
                                    </li>
                                </ol>
                                <br/>
                                <a href="admin.php?mod=pages&p_id=<?php echo $page->id ?>">Добавить вложение</a>

                            </td>
                        </tr>
                </table>

            </form>
        </td>
    </tr>
</table>
