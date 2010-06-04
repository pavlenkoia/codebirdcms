<ul>
<?php

    $items = $data->getItems($menu->id);

    $uri_orig = $_SERVER['REQUEST_URI'];
    $uris = explode('?', $uri_orig);
    $uri = $uris[0];

    foreach($items as $row)
    {
        if($row['visible'] != 1) continue;

        $href = SF.$row['type_link'];

        if($uri == $href)
        {
            $select = ' class="menu-select"';
        }
        else
        {
            $select = '';
        }

        if($row['type'] == '_label')
        {
            $href = $uri_orig."#";
        }

        echo '<li'.$select.'><a href="'.$href.'" '.$row['attr'].'>'.$row['title'].'</a> ';

        $sub_items = $data->getItems($menu->id,$row['id']);
        if(count($sub_items) > 0)
        {
            echo " <div> ";
            echo " <ul> ";
            foreach($sub_items as $sub_row)
            {
                if($sub_row['visible'] != 1) continue;

                $href = SF.$sub_row['type_link'];

                if($uri == $href)
                {
                    $select = ' class="menu-select"';
                }
                else
                {
                    $select = '';
                }             

                if($sub_row['type'] == '_label')
                {
                    $href = $uri_orig."#";
                }

                echo '<li'.$select.'><a href="'.$href.'" '.$sub_row['attr'].'>'.$sub_row['title'].'</a></li> ';
            }
            echo " </ul> ";
            echo " </div> ";
        }

        echo '</li> ';
    }
?>
</ul>
