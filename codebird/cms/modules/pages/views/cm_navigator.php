{
    xtype: 'panel',
    title: '<?php echo $args->title ?>',
    layout: 'auto',
    autoScroll: true,
    tbar:
    {
        xtype: 'toolbar',
        id: 'pages-navigator-toolbar',
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
                            text: 'Создать страницу',
                            handler: function()
                            {
                                Ext.MessageBox.prompt('Создать страницу', 'Введите название страницы:',
                                    function(btn, textpromt)
                                    {
                                        if(btn == 'ok')
                                        {
                                            var tree = Ext.getCmp('pages-tree');
                                            Ext.Ajax.request(
                                                {
                                                    url : '/ajax/cm/pages.cm.add',
                                                    method: 'POST',
                                                    params:
                                                    {
                                                        title: textpromt
                                                    },
                                                    maskEl : 'navigator-panel',
                                                    loadingMessage : 'Добавление...',
                                                    success : function (response)
                                                        {
                                                            tree.getLoader().load(tree.root);
                                                        }
                                                });
                                        }
                                    });
                            }
                        },
                        {
                            text: 'Создать подстраницу',
                            itemId: 'add-subpage',
                            disabled: true,
                            handler: function(item)
                            {
                                Ext.MessageBox.prompt('Создать подстраницу', 'Введите название подстраницы для '+item.node.text+' :',
                                        function(btn, textpromt)
                                        {
                                            if(btn == 'ok')
                                            {
                                                var node = item.node;
                                                Ext.Ajax.request(
                                                    {
                                                        url : '/ajax/cm/pages.cm.add',
                                                        method: 'POST',
                                                        params:
                                                        {
                                                            id: node.id,
                                                            title: textpromt
                                                        },
                                                        maskEl : 'navigator-panel',
                                                        loadingMessage : 'Добавление...',
                                                        success : function (response)
                                                        {
                                                            var id = node.id;
                                                            var tree = node.getOwnerTree();
                                                            node.getOwnerTree().getLoader().load(node.parentNode, function(){tree.getNodeById(id).parentNode.expand();tree.getNodeById(id).expand();});
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
                itemId: 'delete-page',
                handler: function(item)
                {
                    Ext.MessageBox.confirm('Подтверждение', 'Вы действительно хотите удалить cтраницу: '+item.node.text+'?',
                        function(btn)
                        {
                            var node = item.node;
                            var node_parent = node.parentNode;
                            if(btn == 'yes')
                            {
                                Ext.Ajax.request(
                                    {
                                        url : '/ajax/cm/pages.cm.delete',
                                        method: 'POST',
                                        params:
                                        {
                                            id: node.id
                                        },
                                        maskEl : 'navigator-panel',
                                        loadingMessage : 'Удаление...',
                                        success : function (response)
                                        {
                                            node_parent.getOwnerTree().getLoader().load(node_parent, function(){node_parent.expand();});
                                            App.closeEditor({id : 'pages-edit-'+node.id});
                                            item.setDisabled(true);
                                            item.setTooltip('');
                                            var tb = Ext.getCmp('pages-navigator-toolbar');
                                            var b = tb.getComponent('add').menu.getComponent('add-subpage');
                                            b.setDisabled(true);
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
                    id: 'pages-tree',
                    useArrows: true,
                    animate: true,
                    enableDD: true,
                    containerScroll: true,
                    border: false,
                    rootVisible: false,
                    dataUrl: '/ajax/cm/pages.cm.treepages',
                    root: new Ext.tree.AsyncTreeNode(),
                    listeners :
                    {
                        click : function(node)
                        {
                            App.showEditor({
                                url: '/ajax/cm/pages.cm.editor?id='+node.id,
                                id : 'pages-edit-'+node.id,
                                caption: node.text
                                });
                            var tb = Ext.getCmp('pages-navigator-toolbar');
                            var btn = tb.getComponent('delete-page');
                            btn.node = node;
                            btn.setDisabled(false);
                            btn.setTooltip('Удалить страницу: '+node.text);
                            btn = tb.getComponent('add').menu.getComponent('add-subpage');
                            btn.node = node;
                            btn.setDisabled(false);
                        },
                        beforemovenode: function(tree, node, oldParent, newParent, index)
                        {
                            if(confirm('Сохранить измененый порядок?'))
                            {
                                Ext.Ajax.request(
                                    {
                                        url : '/ajax/cm/pages.cm.reorder',
                                        method: 'POST',
                                        params:
                                        {
                                            id: node.id,
                                            parent_id: newParent.id,
                                            index: index
                                        },
                                        maskEl : 'navigator-panel',
                                        loadingMessage : 'Сохранение...',
                                        success : function (response)
                                            {
                                                var r = response.responseJSON;
                                            }
                                    });
                            }
                            else
                            {
                                return false;
                            }
                        }
                    }
                });
            }
            panel.add(tree);
            panel.firstexp = true;
        }
    }
}
