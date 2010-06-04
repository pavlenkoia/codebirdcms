<?php

include 'securimage.php';

if(isset($_GET['name']))
{
    $img = new securimage($_GET['name']);
}
else
{
    $img = new securimage();
}


$img->show(); // alternate use:  $img->show('/path/to/background.jpg');

?>
