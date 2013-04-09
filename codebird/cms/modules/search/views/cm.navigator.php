{
    xtype: 'panel',
    title: '<?=$args->title ?>',
    layout: 'fit',
    autoScroll: true,
    border: false,
    tbar:
    {
        xtype: 'toolbar',
        items:
        [
            {
                text:'Добавить',
                iconCls: 'add-menu',
                itemId: 'add',
                handler: function(event, toolEl, panel)
                {
                    var win = new Ext.Window
                    ({
                        layout:'fit',
                        width:400,
                        height:150,
                        closeAction:'close',
                        plain: true,
                        border: false,
                        title: 'Добавить новый сайт',
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
                                        fieldLabel: 'URL',
                                        name: 'url',
                                        anchor: '95%',
                                        allowBlank: false,
                                        value: 'http://'
                                    }
                                ]
                            }
                        ],
                        buttonAlign: 'center',
                        buttons:
                        [
                            {
                                text:'Добавить',
                                handler: function()
                                {
                                    var form = this.ownerCt.ownerCt.getComponent('form');
                                    if(form.getForm().isValid())
                                    {
                                        form.getForm().submit({
                                            url: '/ajax/cm/search.cm.add',
                                            method: 'POST',
                                            waitTitle: 'Подождите',
                                            waitMsg: 'Добавление сайта...',
                                            success: function(form, action)
                                            {
                                                win.hide();
                                                var tree = Ext.getCmp('<?php echo $args->module ?>-navigator-tree');
                                                tree.getLoader().load(tree.root);
                                                App.msg('Готово', 'Сайт добавлен');
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
                text:'Удалить',
                iconCls: 'delete-menu',
                disabled: true,
                itemId: 'delete',
                handler: function(event, toolEl, panel)
                {
                    var tree = Ext.getCmp('<?php echo $args->module ?>-navigator-tree');
                    var node = tree.getSelectionModel().getSelectedNode();
                    Ext.MessageBox.confirm('Подтверждение', 'Вы&nbsp;действительно&nbsp;хотите&nbsp;удалить&nbsp;сайт&nbsp;'+node.text,
                        function(btn){
                            if(btn == 'yes')
                            {
                                Ext.Ajax.request({
                                    url : '/ajax/cm/search.cm.delete',
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
                                            App.msg('Готово', 'Сайт удален');
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
                    dataUrl: '/ajax/cm/search.cm.tree',
                    root: new Ext.tree.AsyncTreeNode(),
                    listeners :
                    {
                        click : function(node)
                        {
                            var dis = true;
                            if(node.id)
                            {
                                dis = false;
                                App.showEditor({
                                    url: '/ajax/cm/search.cm.editor?id='+node.id,
                                    id : '<?php echo $args->module ?>-edit-'+node.id,
                                    caption: '<?=$args->title ?>'
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