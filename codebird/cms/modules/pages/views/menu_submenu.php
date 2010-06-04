<h2>Меню</h2>
<ul>
    <?php
//    $config = Config::__("pages");
    $pages = $data->getVisiblePages($page->id);
    // определение родительского подменю
    if(count($pages) == 0 && $page->parent_id)
    {
        $pages = $data->getVisiblePages($page->parent_id);
    }
    // ---------------------------------
    foreach($pages as $row)
    {
//        if($config->is_alias)
        {
           $href = $row['alias'].'.html';
        }
//        else
//        {
//            $href = 'index.php?mod=pages&id='.$row['id'];
//        }

        echo '<li><a href="'.$href.'">'.$row['title'].'</a></li>';
    }
    ?>
</ul>