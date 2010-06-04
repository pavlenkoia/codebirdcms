{
    xtype: 'panel',
    id: 'panel-menu-editor-<?php echo $menu->id?>',
    frame: false,
    bodyBorder : false,
    title: <?php echo escapeJSON('Меню: '.$menu->title)?>,
    autoScroll: true,
    layout: 'border',
    tbar:
    {
        xtype: 'toolbar',
        items:
        [
            {
                text:'Создать',
                iconCls: 'add-menu',
                itemId: 'add',
                menu:
                {
                    items:
                    [
                        {
                            text: 'Создать пункт меню',
                            handler: function(item)
                            {
                                Ext.MessageBox.prompt('Создать пункт меню', 'Введите заголовок пункта меню:',
                                    function(btn, textpromt)
                                    {
                                        if(btn == 'ok')
                                        {
                                            var tree = Ext.getCmp('panel-menu-editor-<?php echo $menu->id?>').getComponent('items').getComponent('tree');
                                            Ext.Ajax.request(
                                            {
                                                url : '/ajax/cm/menus.cm.add_item',
                                                method: 'POST',
                                                params:
                                                {
                                                    title: textpromt,
                                                    menus_id: <?php echo $menu->id?>
                                                },
                                                maskEl : tree,
                                                loadingMessage : 'Добавление...',
                                                success : function (response)
                                                {
                                                    var obj = response.responseJSON;
                                                    if(obj.success)
                                                    {
                                                        tree.getLoader().load(tree.root);
                                                        App.msg('Готово', 'Пункт меню добавлен');
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
                            text: 'Создать подпункт меню',
                            itemId: 'add-submenu',
                            disabled: true,
                            handler: function(item)
                            {
                                Ext.MessageBox.prompt('Создать подпункт меню', 'Введите заголовок подпункта для «'+item.node.text+'» :',
                                    function(btn, textpromt)
                                    {
                                        if(btn == 'ok')
                                        {
                                            var tree = Ext.getCmp('panel-menu-editor-<?php echo $menu->id?>').getComponent('items').getComponent('tree');
                                            var node = item.node;
                                            Ext.Ajax.request(
                                            {
                                                url : '/ajax/cm/menus.cm.add_item',
                                                method: 'POST',
                                                params:
                                                {
                                                    title: textpromt,
                                                    parent_id: node.id,
                                                    menus_id: <?php echo $menu->id?>
                                                },
                                                maskEl : tree,
                                                loadingMessage : 'Добавление...',
                                                success : function (response)
                                                {
                                                    var obj = response.responseJSON;
                                                    if(obj.success)
                                                    {
                                                        var id = node.id;
                                                        var tree = node.getOwnerTree();
                                                        node.getOwnerTree().getLoader().load(node.parentNode, function(){tree.getNodeById(id).parentNode.expand();tree.getNodeById(id).expand();});
                                                        App.msg('Готово', 'Подпункт меню добавлен');
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
                }
            },
            {
                text: 'Удалить',
                iconCls: 'delete-menu',
                disabled: true,
                itemId: 'delete',
                handler: function(item)
                {
                    Ext.MessageBox.confirm('Подтверждение', 'Вы действительно хотите удалить пункт меню: '+item.node.text+'?',
                        function(btn)
                        {
                            var node = item.node;
                            var node_parent = node.parentNode;
                            if(btn == 'yes')
                            {
                                Ext.Ajax.request(
                                    {
                                        url : '/ajax/cm/menus.cm.delete_item',
                                        method: 'POST',
                                        params:
                                        {
                                            id: node.id
                                        },
                                        maskEl : node.getOwnerTree(),
                                        loadingMessage : 'Удаление...',
                                        success : function (response)
                                        {
                                            var obj = response.responseJSON;
                                            if(obj.success)
                                            {
                                                var tabs = Ext.getCmp('panel-menu-editor-<?php echo $menu->id?>').getComponent('item').getComponent('tabs');
                                                var id = 'tab-menus-item-'+node.id;
                                                if(tabs.findById(id))
                                                {
                                                    tabs.remove(tabs.findById(id));
                                                }
                                                node_parent.getOwnerTree().getLoader().load(node_parent, function(){node_parent.expand();});
                                                item.setDisabled(true);
                                                item.setTooltip('');
                                                var tb = Ext.getCmp('panel-menu-editor-<?php echo $menu->id?>').getTopToolbar();
                                                var btn = tb.getComponent('add').menu.getComponent('add-submenu');
                                                btn.setDisabled(true);
                                                App.msg('Готово', 'Пункт меню удален');
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
                text:'Конфигурация',
                iconCls: 'config-menu',
                itemId: 'settings',
                handler: function(item)
                {
                    Ext.Ajax.request({
                        url : '/ajax/cm/menus.cm.form_menu',
                        method: 'POST',
                        params:
                        {
                            id: <?php echo $menu->id ?>,
                        },
                        maskEl : 'panel-menu-editor-<?php echo $menu->id?>',
                        loadingMessage : 'Загрузка...',
                        success : function (response) {
                            var win = new Ext.Window(response.responseJSON);
                            win.show(item.id);
                        }
                    });
                }
            }
        ]
    },
    defaults:
    {
        collapsible: false,
        split: true,
        bodyStyle: 'padding2:6px; background-color1: #fff;'
    },
    items:
    [
        {
            region: 'west',
            itemId: 'items',
            width: 250,
            minSize: 150,
            layout: 'fit',
            items:
            [
                {
                    xtype: 'treepanel',
                    itemId: 'tree',
                    animate: true,
                    enableDD: true,
                    containerScroll: false,
                    border: false,
                    autoScroll: true,
                    rootVisible: false,
                    loader: new Ext.tree.TreeLoader(
                    {
                        baseParams:
                        {
                            menus_id:<?php echo $menu->id?>
                        },
                        dataUrl: '/ajax/cm/menus.cm.tree_items'
                    }),
                    root: new Ext.tree.AsyncTreeNode(),
                    listeners:
                    {
                        click: function(node)
                        {
                            var tb = Ext.getCmp('panel-menu-editor-<?php echo $menu->id?>').getTopToolbar();
                            var btn = tb.getComponent('delete');
                            btn.node = node;
                            btn.setDisabled(false);
                            btn.setTooltip('Удалить пункт меню: '+node.text);
                            btn = tb.getComponent('add').menu.getComponent('add-submenu');
                            btn.node = node;
                            btn.setDisabled(false);
                            var tabs = Ext.getCmp('panel-menu-editor-<?php echo $menu->id?>').getComponent('item').getComponent('tabs');
                            var id = 'tab-menus-item-'+node.id;
                            if(tabs.findById(id))
                            {
                                tabs.setActiveTab(tabs.findById(id));
                            }
                            else
                            {
                                tabs.add({
                                    id: id,
                                    title: node.text,
                                    closable:true,
                                    bodyStyle: 'margin: 0px',
                                    layout:'fit',
                                    listeners:
                                    {
                                        afterrender: function(comp)
                                        {
                                            Ext.Ajax.request({
                                                url : '/ajax/cm/menus.cm.editor_item',
                                                method: 'POST',
                                                params:
                                                {
                                                    id: node.id,
                                                },
                                                maskEl : tabs,
                                                loadingMessage : 'Загрузка...',
                                                success : function (response) {
                                                    comp.add(response.responseJSON);
                                                    comp.doLayout();
                                                    comp.node = node;
                                                }
                                            });
                                        }
                                    }
                                }).show();
                                tabs.ownerCt.doLayout();
                            }
                        },
                        beforemovenode: function(tree, node, oldParent, newParent, index)
                        {
                            if(confirm('Сохранить измененый порядок?'))
                            {
                                Ext.Ajax.request(
                                    {
                                        url : '/ajax/cm/menus.cm.reorder',
                                        method: 'POST',
                                        params:
                                        {
                                            id: node.id,
                                            parent_id: newParent.id,
                                            index: index
                                        },
                                        maskEl : tree,
                                        loadingMessage : 'Сохранение...',
                                        success : function (response)
                                        {
                                            var obj = response.responseJSON;
                                            if(obj.success)
                                            {
                                                App.msg('Готово', 'Порядок изменен');
                                            }
                                            else
                                            {
                                                Ext.MessageBox.alert('Ошибка', obj.msg);
                                            }
                                        }
                                    });
                            }
                            else
                            {
                                return false;
                            }
                        }
                    }
                }
            ]
        },
        {
            region: 'center',
            itemId: 'item',
            layout:'fit',
            items:
            [
                {
                    xtype: 'tabpanel',
                    itemId: 'tabs',
                    resizeTabs:true,
                    minTabWidth: 100,
                    tabWidth: 150,
                    enableTabScroll:true,
                    border: false,
                    defaults:
                    {
                        autoScroll:true
                    },
                    plugins: new Ext.ux.TabCloseMenu()
                }
            ]
        }
    ],
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
    ]
}
