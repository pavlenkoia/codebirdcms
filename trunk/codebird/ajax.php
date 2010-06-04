<?php
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' || (isset($_FILES) && count($_FILES)))
{
    session_start();
    include "config.php";

    include "cms.php";

    $registry = Registry::__instance();
    
    if(Utils::getVar("mod"))
    {
        mod(Utils::getVar("mod"));
    }
    elseif(Utils::getVar("mod_action"))
    {
        mod(Utils::getVar("mod_action"));
    }
}
?>