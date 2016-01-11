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
            value: 'Настройка установленных модулей',
            style: 'margin-bottom: 12px'
        },
        {
            layout: 'column',
            items:
            [
                {
                    layout: 'form',
                    items:
                    [
                        {
                            xtype: 'displayfield',
                            hideLabel: true,
                            value: '№'
                        }
                        <?php foreach($modules as $module){?>
                        ,{
                            xtype: 'textfield',
                            hideLabel: true,
                            width: 30,
                            name: '<?php echo $module['module']?>-order',
                            value: '<?php echo $module['order']?>'
                        }
                        <?php }?>
                    ]
                },
                {
                    layout: 'form',
                    items:
                    [
                        {
                            xtype: 'displayfield',
                            hideLabel: true,
                            value: 'Модуль'
                        }
                        <?php foreach($modules as $module){?>
                        ,{
                            xtype: 'textfield',
                            hideLabel: true,
                            readOnly: true,
                            width: 100,
                            value: '<?php echo $module['module']?>'
                        }
                        <?php }?>
                    ]
                },
                {
                    layout: 'form',
                    items:
                    [
                        {
                            xtype: 'displayfield',
                            hideLabel: true,
                            value: 'Название'
                        }
                        <?php foreach($modules as $module){?>
                        ,{
                            xtype: 'textfield',
                            hideLabel: true,
                            width: 300,
                            name: '<?php echo $module['module']?>-title',
                            value: <?php echo escapeJSON($module['title'])?>
                        }
                        <?php }?>
                    ]
                }
            ]
        }
    ]
}