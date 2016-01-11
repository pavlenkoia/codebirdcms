<?php
$res = array();
$res_rows = array();
$res_row = array();
foreach($rows as $row)
{
    $res_row['id'] = $row['id'];
    $res_row['display'] = $row['display'];
    $res_rows[] = $res_row;
}
$res['success'] = true;
$res['rows'] = $res_rows;
echo json_encode($res);
?>
