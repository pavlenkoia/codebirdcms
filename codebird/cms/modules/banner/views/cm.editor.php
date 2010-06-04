{
    xtype: 'form',
    id: 'banner-form-<?php echo $banner->id?>',
    frame: true,
    bodyBorder : true,
    title: 'Баннер',
    autoScroll: true,
    defaults:
    {
        width: 400
    },
    items:
    [
        {
            xtype: 'hidden',
            name: 'id',
            value: <?php echo $banner->id ?>
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Название',
            name: 'name',
            allowBlank: false,
            value: <?php echo escapeJSON($banner->name)?>
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Символьный идентификатор',
            width: 180,
            name: 'alias',
            value: <?php echo escapeJSON($banner->alias)?>
        },
        {
            xtype: 'textarea',
            fieldLabel: 'Код',
            name: 'html',
            width: '98%',
            height: 280,
            value: <?php echo escapeJSON($banner->html)?>
        }
    ],
    labelAlign: 'top',
    buttonAlign: 'center',
    buttons:
    [
        {
            text: 'Сохранить',
            formBind: true,
            handler: function()
            {
                var form = this.ownerCt.ownerCt;
                form.getForm().submit({
                        url: '/ajax/cm/banner.cm.save',
                        method: 'POST',
                        waitTitle: 'Подождите',
                        waitMsg: 'Сохранение...',
                        success: function(form, action){
                                App.msg('Готово', action.result.msg);
                            },
                        failure: function(form, action){
                                Ext.MessageBox.alert('Ошибка', action.result.msg);
                            }
                    });
            }
        },
        {
            text: 'Закрыть',
            formBind: true,
            handler: function()
            {
                App.closeEditor({id : this.ownerCt.ownerCt.ownerCt.id});
            }
        }
    ]
}

