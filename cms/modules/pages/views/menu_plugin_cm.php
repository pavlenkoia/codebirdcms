<?php
$value = $args->name.":pages.menu.submenu";
?>
{
    xtype: 'checkbox',
    hideLabel: true,
    name: 'visible',
    inputValue: '<?php echo $value;?>',
    boxLabel: '<?php echo $args->label ?>',
    checked: <?php if($args->value == $value) echo 'true'; else echo 'false'; ?>
}
