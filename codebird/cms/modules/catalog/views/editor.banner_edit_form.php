{
    layout:'fit',
    width:600,
    height:400,
    plain: true,
    border: false,
    title: <?php echo escapeJSON('Редактирование')?>,
    items:
    [
        {
            xtype: 'form',
            itemId: 'form',
            frame: true,
            autoScroll: true,
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
                    value: '<?php echo $banner->id ?>'
                },
                {
                    xtype: 'textarea',
                    fieldLabel: 'Код',
                    name: 'html',
                    width: '98%',
                    height: 280,
                    value: <?php echo escapeJSON($banner->html)?>
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
                var win = this.ownerCt.ownerCt;
                win.hide();
                var form = win.getComponent('form');
                
                form.getForm().submit({
                    url: '/ajax/cm/catalog.editor.save_banner',
                    method: 'POST',
                    waitTitle: 'Подождите',
                    waitMsg: 'Сохранение...',
                    success: function(form, action){
                        window.location = window.location;
                        win.close();
                    },
                    failure: function(form, action){
                        Ext.MessageBox.alert('Ошибка', action.result.msg);
                        win.close();
                    }
                });
            }
        },
        {
            text: 'Отмена',
            handler: function()
            {
                this.ownerCt.ownerCt.close();
            }
        }
    ]
}
