<?php
App::SetProperty('title',404);
header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
?>
<?php include "html/templates/header.tpl.php" ?>

404

<?php include "html/templates/footer.tpl.php" ?>
