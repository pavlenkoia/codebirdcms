<?
$res = array();
$res['content'] = $content;
$res['pending'] = $pending;
$res['indexdate'] = $indexdate;
$res['status'] = $status;
$res['links_count'] = $links_count;

echo json_encode($res);
?>