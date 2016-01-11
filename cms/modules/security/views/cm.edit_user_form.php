{
    layout:'fit',
    width: 400,
    height: 340,
    closeAction:'close',
    plain: true,
    border: false,    
    title: <?php echo escapeJSON('Править пользователя '.$user->name)?>,
    items:
    [
        {
            xtype: 'form',
            itemId: 'form',
            frame: true,
            labelAlign: 'top',
            autoScroll: true,
            defaults:
            {
                width: 350,
                xtype: 'textfield'
            },
            items:
            [
                {
                    fieldLabel: 'Логин',
                    name: 'name',
                    anchor: '95%',
                    disabled : true,
                    value: <?php echo escapeJSON($user->name)?>
                },
                {
                    inputType: 'password',
                    fieldLabel: 'Новый пароль',
                    name: 'pass',
                    anchor: '65%'
                },
                {
                    inputType: 'password',
                    fieldLabel: 'Подтверждение нового пароля',
                    name: 'pass2',
                    anchor: '65%'
                },
                {
                    xtype: 'checkbox',
                    fieldLabel: 'Статус',
                    boxLabel: 'заблокирован',
                    name: 'disabled',
                    inputValue: 1,
                    checked: <?php if($user->disabled == 1) echo 'true'; else echo 'false'?>
                },
                {
                    xtype: 'checkboxgroup',
                    fieldLabel: 'Роли',
                    items:
                    [
                        <?php $f=false; foreach($roles as $role) {
                            if($f) echo ','; else $f=true;
                        ?>
                        {
                            boxLabel: <?php echo escapeJSON($role['name'])?>,
                            name: 'role_<?php echo $role['id'] ?>',
                            inputValue: 1,
                            checked: <?php if(in_array($role['id'], $user_roles)) echo 'true'; else echo 'false';?>
                        }
                        <?php } ?>
                    ]
                }
            ]
        }
    ],
    buttonAlign: 'center',
    buttons:
    [
        {
            text:'Сохранить',
            handler: function(btn)
            {
                var form = this.ownerCt.ownerCt.getComponent('form');

                if(form.getForm().isValid())
                {
                    form.getForm().submit({
                        url: '/ajax/cm/security.cm.edit_user',
                        method: 'POST',
                        params:
                        {
                            id: <?php echo $user->id ?>
                        },
                        waitTitle: 'Подождите',
                        waitMsg: 'Сохранение...',
                        success: function(form, action){
                                var grid = Ext.getCmp('panel-security-editor-users').getComponent('grid');
                                var paging = grid.getBottomToolbar();
                                grid.getStore().load({params:{start:paging.cursor, limit:paging.pageSize}});
                                btn.ownerCt.ownerCt.hide();
                            },
                        failure: function(form, action){
                                Ext.MessageBox.alert('Ошибка', action.result.msg);
                            }
                    });
                }
                else
                {
                    Ext.MessageBox.alert('Проверка', 'Заполните все поля');
                }
            }
        },
        {
            text: 'Отмена',
            handler: function()
            {
                this.ownerCt.ownerCt.hide();
            }
        }
    ]
}
