<?php

$_POST['soption'] = 'full';
$_POST['reindex'] = 1;
$_POST['url'] = "http://".$domain."/";


include(SITE_PATH."/cms/modules/search/html/sphider-utf/admin/spider.php");

?>