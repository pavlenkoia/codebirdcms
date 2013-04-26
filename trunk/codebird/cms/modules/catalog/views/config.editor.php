{
    xtype: 'panel',
    id: 'panel-catalog-config-editor',
    frame: false,
    bodyBorder : false,
    title: <?php echo escapeJSON('Конфигуратор разделов')?>,
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
                    //enableDD: true,
                    containerScroll: false,
                    border: false,
                    autoScroll: true,
                    rootVisible: false,
                    loader: new Ext.tree.TreeLoader(
                    {
                        dataUrl: '/ajax/cm/catalog.config.tree_tables'
                    }),
                    root: new Ext.tree.AsyncTreeNode({id:'_root'}),
                    listeners:
                    {
                        click: function(node)
                        {
                            var tabs = Ext.getCmp('panel-catalog-config-editor').getComponent('item').getComponent('tabs');
                            var id = 'tab-catalog-config-item-'+node.id;
                            if(tabs.findById(id))
                            {
                                tabs.setActiveTab(tabs.findById(id));
                            }
                            else if(node.id != '_tables_section' && node.id != '_tables_position')
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
                                                url : '/ajax/cm/catalog.config.editor_item',
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
    ]
}