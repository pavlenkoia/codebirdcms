<?php
$res = array();
$res_rows = array();
$res_row = array();
foreach($rows as $row)
{
    $res_row['id'] = $row['id'];
    $res_row['name'] = $row['name'];
    $res_row['disabled'] = $row['disabled'] == 1 ? "заблокирован" : "";
    array_push($res_rows, $res_row);
}
$res['success'] = true;
$res['results'] = $count;
$res['rows'] = $res_rows;
echo json_encode($res);
?>
