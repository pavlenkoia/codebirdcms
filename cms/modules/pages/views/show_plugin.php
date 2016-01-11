<?php

if(isset($plugin_mod))
{
    Registry::__instance()->mod_content = val($plugin_mod, $plugin_args);
}
else
{
    Registry::__instance()->mod_content = "";
}

?>
