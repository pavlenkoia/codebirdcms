<?php
    $res = array();
    $res_rows = array();


    //$rows = explode(';', $images);

    //print_r($xml);

    foreach($xml->image as $image)
    {
        $res_row = array();
        $res_row['id'] = (string)$image->img;
        $res_row['img'] = '<img src="'.get_cache_pic((string)$image->img,75,75).'"/>' /*get_cache_pic($row,75,75)*/;
        $res_rows[] = $res_row;
    }

    $res['success'] = true;
    $res['results'] = count($xml->image);
    $res['rows'] = $res_rows;
    echo json_encode($res);
?>
