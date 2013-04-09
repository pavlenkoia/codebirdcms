{
    xtype: 'form',
    id: 'search-form-<?=$site['id']?>',
    frame: true,
    bodyBorder : true,
    title: 'Поиск по сайту '+<?=escapeJSON($site['url'])?>,
    autoScroll: true,
    defaults:
    {
    },
    items:
    [
        {
            xtype: 'hidden',
            name: 'id',
            value: <?php echo $site['id'] ?>
        },
        {
            xtype:'fieldset',
            title: 'Состояние',
            collapsible: false,
            autoHeight:true,
            items:
            [
                {
                    xtype: 'displayfield',
                    fieldLabel: 'Дата последнего индексирования',
                    name: 'indexdate',
                    style: 'font-weight:bold;',
                    value: <?php echo escapeJSON($site['indexdate'])?>
                },
                {
                    xtype: 'displayfield',
                    fieldLabel: 'Статус',
                    name: 'pending',
                    style: 'font-weight:bold;',
                    value: '<?=$site['pending']?'Индексирование не закончено':'Проиндексировано'?>'
                }
            ]
        },
        {
            xtype: 'button',
            text: 'Индексировать',
            style: 'margin-bottom: 12px',
            handler: function(item)
            {
                var form = item.ownerCt;
                form.execIndex(item);
            }
        },
        {
            xtype: 'panel',
            itemId: 'panel-result',
            hideLabel: true,
            height: 200,
            width: '95%',
            autoScroll: true,
            hidden: true,
            bodyStyle: 'background-color: #fff; padding: 8px'
        }
    ],
    labelAlign: 'left',
    buttonAlign: 'center',
    buttons:
    [
        {
            text: 'Закрыть',
            formBind: true,
            handler: function()
            {
                App.closeEditor({id : this.ownerCt.ownerCt.ownerCt.id});
            }
        }
    ],
    execIndex: function(item)
    {
        var form = item.ownerCt;
        var f_id = form.getForm().findField('id');
        Ext.Ajax.request({
            url : '/ajax/search.admin.exec_index',
            method: 'POST',
            params:
            {
                id: f_id.getValue()
            },
            maskEl : form,
            loadingMessage : 'Индексирование...',
            success : function (response)
            {
                var obj = response.responseJSON;
                var panel = form.getComponent('panel-result');
                panel.setVisible(true);
                panel.update(obj.content);
                var f_indexdate = form.getForm().findField('indexdate');
                var f_pending = form.getForm().findField('pending');
                f_indexdate.setValue(obj.indexdate);
                f_pending.setValue(obj.status);
                /*if(obj.success)
                {
                    tree.getLoader().load(tree.root);
                    App.msg('Готово', 'Пункт меню добавлен');
                }
                else
                {
                    Ext.MessageBox.alert('Ошибка', obj.msg);
                }*/
            },
            failure: function(response, opts) {
                //console.log('server-side failure with status code ' + response.status);
                if(response.status = -1){
                    Ext.Ajax.request({
                        url : '/ajax/search.admin.status',
                        method: 'POST',
                        params:
                        {
                            id: f_id.getValue()
                        },
                        maskEl : form,
                        loadingMessage : 'Получение статуса...',
                        success : function (response){
                            var obj = response.responseJSON;
                            var panel = form.getComponent('panel-result');
                            panel.setVisible(false);
                            panel.update('');
                            var f_indexdate = form.getForm().findField('indexdate');
                            var f_pending = form.getForm().findField('pending');
                            f_indexdate.setValue(obj.indexdate);
                            f_pending.setValue(obj.status);
                            if(obj.pending){
                                form.execIndex(item);
                            }
                        }
                    });
                }
            }
        });
    }
}

