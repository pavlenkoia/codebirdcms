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
        width: 300
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
            value: 'Основные настройки',
            style: 'margin-bottom: 12px'
        },
        <?php if($check){ ?>
        {
            xtype: 'displayfield',
            hideLabel: true,
            width: 'auto',
            style: 'margin-bottom: 18px',
            value: "<div class=\"action_uncheck\"><?php echo trim(escapeJSON($check),'"') ?></div>"
        },
        <?php }?>
        {
            xtype: 'textfield',
            fieldLabel: 'Сервер базы данных',
            name: 'db_host',
            value: <?php echo escapeJSON($db_host)?>
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Имя пользователя базы данных',
            name: 'db_user',
            value: <?php echo escapeJSON($db_user)?>
        },
        {
            xtype: 'textfield',
            inputType: 'password',
            fieldLabel: 'Пароль пользователя базы данных',
            name: 'db_user_pass',
            value: <?php echo escapeJSON($db_user_pass)?>
        },
        {
            xtype: 'textfield',
            fieldLabel: 'База данных',
            name: 'db_name',
            value: <?php echo escapeJSON($db_name)?>,
            style: 'margin-bottom: 30px'
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Заголовок сайта',
            name: 'site_name',
            value: <?php echo escapeJSON($site_name)?>,
            style: 'margin-bottom: 18px'
        }
        
    ]
}
