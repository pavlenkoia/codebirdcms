{
    xtype: 'form',
    id: 'feed-form-<?php echo $feed->id?>',
    frame: true,
    bodyBorder : true,
    title: 'Канал',
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
            value: <?php echo $feed->id ?>
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Название',
            name: 'name',
            allowBlank: false,
            value: <?php echo escapeJSON($feed->name)?>
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Символьный идентификатор',
            width: 180,
            name: 'alias',
            value: <?php echo escapeJSON($feed->alias)?>
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Фид (ссылка на rss)',
            name: 'url',
            value: <?php echo escapeJSON($feed->url)?>
        },
        {
            xtype: 'numberfield',
            fieldLabel: 'Интервал обновления канала в минутах',
            width: 80,
            name: 'interval_update',
            value: <?php echo escapeJSON($feed->interval_update)?>
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
                        url: '/ajax/cm/feed.cm.save',
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
