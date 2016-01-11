{
    xtype: 'form',
    id: 'seo-form-<?php echo 'seo'?>',
    frame: true,
    bodyBorder : true,
    title: 'SEO',
    autoScroll: true,
    defaults:
    {
        width: 400
    },
    items:
    [
        {
            xtype: 'textarea',
            fieldLabel: 'Содержание',
            name: 'html',
            width: '98%',
            height: 280,
            value: <?php echo escapeJSON($seo['file_content'])?>
        },
        
        {
            xtype: 'hidden',
            name: 'filename',
            value: <?php echo escapeJSON($seo['file_name'])?>
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
                        url: '/ajax/cm/seo.cm.save',
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

