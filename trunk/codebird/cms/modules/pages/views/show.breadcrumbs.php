<a href="/">Главная</a>/
<?php
foreach($pages as $row)
{
	if($row->visible != 1) continue;
?>
<a href="<?php echo $row->alias?>.html"><?php echo htmlspecialchars($row->title)?></a>/
<?php
}
?>
