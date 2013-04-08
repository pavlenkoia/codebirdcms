<?
$res = array();
$res['content'] = $content;
$res['pending'] = $pending;
$res['indexdate'] = $indexdate;
$res['status'] = $status;

echo json_encode($res);
?>