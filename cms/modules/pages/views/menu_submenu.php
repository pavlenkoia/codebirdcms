<ul>
<?php
    $alias = Utils::getVar('alias');

    $pages = $data->getVisiblePages($page->id);

    if(count($pages) == 0 && $page->parent_id)
    {
        $pages = $data->getVisiblePages($page->parent_id);
    }

    foreach($pages as $row)
    {
        $href = $row['alias'].'.html';

        if($row['alias'] == $alias)
        {
            echo '<li><span>'.$row['title'].'</span></li>';
        }
        else
        {
            echo '<li><a href="'.$href.'">'.$row['title'].'</a></li>';
        }


    }
?>
</ul>