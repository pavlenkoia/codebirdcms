#!/usr/bin/php
<?php
$DOCUMENT_ROOT = join('/', array_slice(explode('/', __FILE__), 0, -3));
putenv("DOCUMENT_ROOT=$DOCUMENT_ROOT");

/*include $DOCUMENT_ROOT."/config.php";

include $DOCUMENT_ROOT."/cms.php";

$table = new Table('sphider_sites','site_id');

$rows = $table->select('select a.site_id, a.url, a.indexdate, b.site_id as pending from sphider_sites a left outer join sphider_pending b on a.site_id=b.site_id');

foreach($rows as $row)
{
    $url = $row['url'];
    $soption = 'full';
    $reindex = 1;

    include($DOCUMENT_ROOT.'/cms/modules/search/html/sphider-utf/admin/spider.php');

    break;
}*/

$allExt = 1;
$soption = 'full';

include($DOCUMENT_ROOT.'/cms/modules/search/html/sphider-utf/admin/spider.php');

exit;
?>