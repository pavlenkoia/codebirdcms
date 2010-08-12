{
    xtype: 'panel',
    title: '<?php echo $args->title ?>',
    layout: 'fit',
    autoScroll: true,
    border: false,
    tbar:
    {
        xtype: 'toolbar',
        items:
        [
            {
                text:'Создать',
                iconCls: 'add-menu',
                itemId: 'add',
                handler: function(event, toolEl, panel)
                {
                    var win = new Ext.Window
                    ({
                        layout:'fit',
                        width:400,
                        height:240,
                        closeAction:'close',
                        plain: true,
                        border: false,
                        title: 'Создать новый канал',
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
                                        xtype: 'textfield',
                                        fieldLabel: 'Название',
                                        name: 'name',
                                        anchor: '95%',
                                        allowBlank: false
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Символьный идентификатор',
                                        name: 'alias',
                                        anchor: '95%',
                                        allowBlank: false
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Фид (ссылка на rss)',
                                        name: 'url',
                                        value: 'http://',
                                        anchor: '95%'
                                    }
                                ]
                            }
                        ],
                        buttonAlign: 'center',
                        buttons:
                        [
                            {
                                text:'Создать',
                                handler: function()
                                {
                                    var form = this.ownerCt.ownerCt.getComponent('form');
                                    if(form.getForm().isValid())
                                    {
                                        form.getForm().submit({
                                            url: '/ajax/cm/feed.cm.add',
                                            method: 'POST',
                                            waitTitle: 'Подождите',
                                            waitMsg: 'Создание канала...',
                                            success: function(form, action)
                                            {
                                                win.hide();
                                                var tree = Ext.getCmp('<?php echo $args->module ?>-navigator-tree');
                                                tree.getLoader().load(tree.root);
                                                App.msg('Готово', 'Канал создан');
                                                App.showEditor({
                                                        url: '/ajax/cm/feed.cm.editor?id='+action.result.id,
                                                        id : 'feed-edit-'+action.result.id,
                                                        caption: action.result.title
                                                        });
                                            },
                                            failure: function(form, action)
                                            {
                                                Ext.MessageBox.alert('Ошибка', action.result.msg);
                                            }
                                        });
                                    }
                                    else
                                    {
                                        App.msg('Проверка', 'Заполните поля');
                                    }
                                }
                            },
                            {
                                text: 'Отмена',
                                handler: function()
                                {
                                    win.hide();
                                }
                            }
                        ]
                    });
                    win.show(this.id);
                }
            },
            {
                text: 'Удалить',
                iconCls: 'delete-menu',
                disabled: true,
                itemId: 'delete',
                handler: function(item)
                {
                    var tree = Ext.getCmp('<?php echo $args->module ?>-navigator-tree');
                    var node = tree.getSelectionModel().getSelectedNode();
                    Ext.MessageBox.confirm('Подтверждение', 'Вы&nbsp;действительно&nbsp;хотите&nbsp;удалить&nbsp;канал:&nbsp;'+node.text+'?',
                        function(btn)
                        {
                            if(btn == 'yes')
                            {
                                Ext.Ajax.request(
                                    {
                                        url : '/ajax/cm/feed.cm.delete',
                                        method: 'POST',
                                        params:
                                        {
                                            id: node.id
                                        },
                                        maskEl : 'navigator-panel',
                                        loadingMessage : 'Удаление...',
                                        success : function (response)
                                        {
                                            var obj = response.responseJSON;
                                            if(obj.success)
                                            {
                                                App.closeEditor({id : '<?php echo $args->module ?>-edit-'+node.id});
                                                tree.getLoader().load(tree.root);
                                                App.msg('Готово', 'Канал удален');
                                                item.setDisabled(true);
                                            }
                                            else
                                            {
                                                Ext.MessageBox.alert('Ошибка', obj.msg);
                                            }
                                        }
                                    });
                            }
                        });
                }
            },
            {
                iconCls: 'config-menu',
                tooltip: 'Настройки модуля',
                handler: function(event, toolEl, panel)
                {
                    App.showEditor({
                        url: '/ajax/cm/modmanager.cm.config?module=<?php echo $args->module ?>',
                        id : '<?php echo $args->module ?>-config',
                        caption: 'Настройки модуля: <?php echo $args->title ?>',
                        iconCls: 'config-menu'
                    });
                }
            }
        ]
    },
    listeners:
    {
        <?php if($args->top) echo 'render'; else echo 'expand' ?> : function(panel,anim)
        {
            if(panel.firstexp != true)
            {
                var tree = new Ext.tree.TreePanel({
                    id: '<?php echo $args->module ?>-navigator-tree',
                    useArrows: true,
                    animate: true,
                    enableDD: true,
                    containerScroll: false,
                    border: false,
                    autoScroll: true,
                    rootVisible: false,
                    dataUrl: '/ajax/cm/feed.cm.tree',
                    root: new Ext.tree.AsyncTreeNode(),
                    listeners :
                    {
                        click : function(node)
                        {
                            var dis = true;
                            if(node.id > 0)
                            {
                                dis = false;
                                App.showEditor({
                                    url: '/ajax/cm/feed.cm.editor?id='+node.id,
                                    id : '<?php echo $args->module ?>-edit-'+node.id,
                                    caption: 'Канал: '+node.text
                                    });
                            }
                            var tb = panel.getTopToolbar();
                            tb.getComponent('delete').setDisabled(dis);
                        }
                    }
                });
                panel.add(tree);
                panel.firstexp = true;
            }
        }
    }
}
