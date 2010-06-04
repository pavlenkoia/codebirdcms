<div class="put">
<a href="">Главная</a>&nbsp;>
<?php
foreach($pages as $row)
{
?>
&nbsp;<a href="<?php echo $row->alias?>.html"><?php echo htmlspecialchars($row->title)?></a>&nbsp;>
<?php
}
?>
<b><?php echo htmlspecialchars($page->title)?></b>
</div>
