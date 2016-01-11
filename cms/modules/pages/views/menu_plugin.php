<?php
$value = $args->name.":pages.menu.submenu";
?>
<input name="plugin_<?php echo $args->name; ?>" value="<?php echo $value;?>" <?php if($args->value == $value){echo 'checked';} ?> type="checkbox"/>

