{
    layout:'fit',
    width: 400,
    height: 300,
    closeAction:'close',
    plain: true,
    border: false,    
    title: 'Создать пользователя',
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
                //width: 350,
                xtype: 'textfield'
            },
            items:
            [
                {
                    fieldLabel: 'Логин',
                    name: 'name',
                    anchor: '95%',
                    allowBlank: false
                },
                {
                    inputType: 'password',
                    fieldLabel: 'Пароль',
                    name: 'pass',
                    anchor: '65%'
                },
                {
                    inputType: 'password',
                    fieldLabel: 'Подтверждение пароля',
                    name: 'pass2',
                    anchor: '65%'
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
                            checked: false
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
                        url: '/ajax/cm/security.cm.add_user',
                        method: 'POST',
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