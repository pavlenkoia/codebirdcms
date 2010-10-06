<?php
$res = array();
$res_rows = array();
$res_row = array();

foreach($files as $file)
{
    $res_row['name'] =  $file['name'];

    $ext = mb_strtolower(strrchr($file['name'],'.'));

    if($ext == '.jpg' || $ext == '.gif' || $ext == '.png')
    {
        $res_row['src'] = get_cache_pic($file['url'],80,60);
        $res_row['url'] = $file['url'];
    }
    else
    {
        $res_row['src'] = '/cms/modules/filemanager/html/img/file-manager.png';
        $res_row['url'] =  $file['url'];
    }

    $res_rows[] = $res_row;
}

$res['files'] = $res_rows;
echo json_encode($res);
?>
