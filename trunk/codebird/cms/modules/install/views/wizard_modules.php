{
    success: true,
    xtype: 'form',
    itemId: 'form',
    frame: true,
    autoScroll: true,
    border: false,
    items:
    [
        {
            xtype: 'hidden',
            name: 'step',
            value: <?php echo $step?>
            
        },
        {
            xtype: 'displayfield',
            hideLabel: true,
            value: 'Устанавливаемые модули',
            style: 'margin-bottom: 12px'
        }
        <?php foreach($modules as $module){?>
        ,{
            xtype: 'checkbox',
            hideLabel: true,
            boxLabel: <?php echo escapeJSON($module['title'])?>,
            name: '<?php echo $module['name']?>-module'

            <?php if($module['required']) echo ',disabled: true'?>

            <?php if($module['installed']) echo ',checked: true'?>

        }
        <?php }?>
    ]
}