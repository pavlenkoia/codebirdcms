<?php $alias = $args->param; ?>
{
    xtype: 'panel',
    title: '<?php echo $args->title ?>',
    id: 'catalog-navigator-<?php echo $alias ?>',
    layout: 'auto',
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
                menu:
                {
                    items:
                    [
                        {
                            text: 'Создать раздел',
                            handler: function(item)
                            {
                                Ext.Ajax.request({
                                    url : '/ajax/cm/catalog.cm.add_section_form',
                                    method: 'POST',
                                    params:
                                    {
                                        alias: '<?php echo $alias ?>'
                                    },
                                    maskEl : 'catalog-navigator-<?php echo $alias ?>',
                                    loadingMessage : 'Загрузка...',
                                    success : function (response) {
                                        var win = new Ext.Window(response.responseJSON);
                                        win.show('catalog-navigator-<?php echo $alias ?>');
                                    }
                                });
                            }
                        },
                        {
                            text: 'Создать подраздел',
                            itemId: 'add-sub',
                            disabled: true,
                            handler: function(item)
                            { 
                                var tree = Ext.getCmp('catalog-navigator-<?php echo $alias ?>').getComponent('tree');
                                var node = item.node;
                                Ext.Ajax.request({
                                    url : '/ajax/cm/catalog.cm.add_section_form',
                                    method: 'POST',
                                    params:
                                    {
                                        alias: '<?php echo $alias ?>',
                                        parent_id: node.id
                                    },
                                    maskEl : 'catalog-navigator-<?php echo $alias ?>',
                                    loadingMessage : 'Загрузка...',
                                    success : function (response) {
                                        var win = new Ext.Window(response.responseJSON);
                                        win.show('catalog-navigator-<?php echo $alias ?>');
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
                    Ext.MessageBox.confirm('Подтверждение', 'Вы действительно хотите удалить раздел: '+item.node.text+'?',
                        function(btn)
                        {
                            var node = item.node;
                            var node_parent = node.parentNode;
                            if(btn == 'yes')
                            {
                                Ext.Ajax.request(
                                {
                                    url : '/ajax/cm/catalog.cm.delete_section',
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
                                            App.closeEditor({id : 'catalog-section-edit-'+node.id});
                                            node_parent.getOwnerTree().getLoader().load(node_parent, function(){node_parent.expand();});
                                            item.setDisabled(true);
                                            item.setTooltip('');
                                            var tb = Ext.getCmp('catalog-navigator-<?php echo $alias ?>').getTopToolbar();
                                            var btn = tb.getComponent('add').menu.getComponent('add-sub');
                                            btn.setDisabled(true);
                                            btn.node = null;
                                            App.msg('Готово', 'Раздел удален');
                                        }
                                        else
                                        {
                                            Ext.MessageBox.alert('Ошибка', obj.msg);
                                        }
                                    }
                                });
                            }
                        }
                    );
                }
            },
            {
                tooltip:'Обновить',
                iconCls: 'refresh-menu',
                itemId: 'refresh',
                handler: function(item)
                {
                    var tree = Ext.getCmp('catalog-navigator-<?php echo $alias ?>').getComponent('tree');
                    tree.getLoader().load(tree.root);
                }
            }<?if(Security_User::inRole('dev')){?>,
            {
                iconCls: 'config-menu',
                tooltip: 'Конфигуратор разделов',
                handler: function(event, toolEl, panel)
                {
                    App.showEditor({
                        url: '/ajax/cm/catalog.config.editor',
                        id : 'catalog-config-editor',
                        caption: 'Конфигуратор разделов',
                        iconCls: 'config-menu'
                    });
                }
            }<?}?>
        ]
    },
    listeners:
    {
        <?php if($args->top) echo 'render'; else echo 'expand' ?>: function(panel,anim)
        {
            if(panel.firstexp != true)
            {
                var tree = new Ext.tree.TreePanel({
                    itemId: 'tree',
                    useArrows: true,
                    animate: true,
                    enableDD: true,
                    containerScroll: true,
                    border: false,
                    rootVisible: false,
                    dataUrl: '/ajax/cm/catalog.cm.tree<?php if($alias) echo '?alias='.$alias ?>',
                    root: new Ext.tree.AsyncTreeNode(),
                    listeners:
                    {
                        click: function(node)
                        {
                            App.showEditor({
                                url: '/ajax/cm/catalog.cm.editor?id='+node.id,
                                id : 'catalog-section-edit-'+node.id,
                                caption: node.text
                                });

                            var tb = Ext.getCmp('catalog-navigator-<?php echo $alias ?>').getTopToolbar();
                            var btn = tb.getComponent('delete');
                            btn.node = node;
                            btn.setDisabled(false);
                            btn.setTooltip('Удалить раздел: '+node.text);
                            btn = tb.getComponent('add').menu.getComponent('add-sub');
                            btn.node = node;
                            btn.setDisabled(false);
                        },
                        beforemovenode: function(tree, node, oldParent, newParent, index)
                        {
                            if(confirm('Сохранить измененый порядок?'))
                            {
                                Ext.Ajax.request(
                                    {
                                        url : '/ajax/cm/catalog.cm.reorder',
                                        method: 'POST',
                                        params:
                                        {
                                            id: node.id,
                                            parent_id: newParent.id,
                                            alias: '<?php echo $alias ?>',
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
                });
                panel.add(tree);
                panel.firstexp = true;
            }
        }
    }
}
