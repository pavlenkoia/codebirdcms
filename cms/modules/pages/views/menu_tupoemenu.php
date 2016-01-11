<ul id="nav" class="example1">
<?php
    $config = Config::__("pages");

    $pages = $data->getVisiblePages();

    $first = true;

    foreach($pages as $row)
    {
        if($config->is_alias)
        {
           $href = $row['alias'].'.html';
        }
        else
        {
            $href = 'index.php?mod=pages&id='.$row['id'];
        }

        if($first)
        {
            echo '<li class="first"><a href="'.$href.'">'.$row['title'].'</a> ';
            $first = false;
        }
        else
        {
            echo '<li><a href="'.$href.'">'.$row['title'].'</a> ';
        }


        $sub_pages = $data->getVisiblePages($row['id']);
        if(count($sub_pages) > 0)
        {
            echo " <div> ";
            echo " <span> ";
            foreach($sub_pages as $sub_row)
            {
                if($config->is_alias)
                {
                    $href = $sub_row['alias'].'.html';
                }
                else
                {
                    $href = 'index.php?mod=pages&id='.$sub_row['id'];
                }

                echo '<p><a href="'.$href.'">'.$sub_row['title'].'</a></p> ';
            }
            echo " </span> ";
            echo " </div> ";
        }

        echo '</li> ';
    }
    ?>
</ul>
