<?php

function out_page_list($pages, $data)
{
    echo "<ul>";
    foreach($pages as $row)
    {
        echo "<li>";
        $href = $row['alias'].'.html';
        echo '<a href="'.$href.'">'.$row['title'].'</a> ';
        $pages_sub = $data->getVisiblePages($row['id']);
        if(count($pages_sub) > 0)
        {
            out_page_list($pages_sub, $data);
        }
        echo "</li> ";
    }
    echo "</ul>";
}

$pages = $data->getVisiblePages();

out_page_list($pages, $data);
?>
