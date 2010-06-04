{
    success: true,
    xtype: 'form',
    itemId: 'form',
    frame: true,
    autoScroll: true,
    border: false,
    labelAlign: 'top',
    defaults:
    {
        width: 'auto'
    },
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
            value: 'Проверка',
            style: 'margin-bottom: 12px'
        }
        <?php foreach($checklist as $item){ ?>
        ,{
            xtype: 'displayfield',
            hideLabel: true,
            value: '<div class="<?php if($item['check']) echo 'action_check'; else echo 'action_uncheck'; ?>"><?php echo $item['msg']?></div>'
        }
        <?php }?>

        ,{
            xtype: 'displayfield',
            hideLabel: true,
            style: 'margin-top: 18px',
            value: "<?php echo $check['msg']?>"
        }
    ]
}
