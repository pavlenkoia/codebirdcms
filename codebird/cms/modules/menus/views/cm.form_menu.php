{
    layout:'fit',
    width:340,
    height:200,
    closeAction:'close',
    plain: true,
    border: false,
    title: <?php echo escapeJSON('Правка меню: '.$menu->title) ?>,
    items:
    [
        {
            xtype: 'form',
            itemId: 'form',
            frame: true,
            labelAlign: 'top',
            defaults:
            {
                width: 350,
                xtype: 'textfield'
            },
            items:
            [
                {
                    xtype: 'hidden',
                    name: 'id',
                    value: <?php echo $menu->id ?>
                },
                {
                    xtype: 'textfield',
                    fieldLabel: 'Название',
                    name: 'title',
                    anchor: '95%',
                    allowBlank: false,
                    value: <?php echo escapeJSON($menu->title) ?>
                },
                {
                    xtype: 'textfield',
                    fieldLabel: 'Символьное имя',
                    name: 'name',
                    anchor: '50%',
                    allowBlank: false,
                    value: <?php echo escapeJSON($menu->name) ?>
                }
            ]
        },
    ],
    listeners:
    {
        show: function(comp)
        {
            comp.setWidth(comp.getWidth()-1);
        }
    },
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
                        url: '/ajax/cm/menus.cm.save_menu',
                        method: 'POST',
                        waitTitle: 'Подождите',
                        waitMsg: 'Сохранение...',
                        success: function(form, action){
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
