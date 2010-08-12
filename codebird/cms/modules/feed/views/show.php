
<?php
foreach($feed_items as $item)
{
?>

<h2><a href="<?php echo $item['link']?>"><?php echo $item['title']?></a></h2>
<p><?php echo $item['description']?></p>
<p><?php echo date("d.m.Y G:i",$item['pubdate'])?></p>

<?php
}
?>