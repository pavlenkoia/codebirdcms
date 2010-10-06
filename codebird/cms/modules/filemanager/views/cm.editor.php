{
    xtype: 'panel',
    id: 'panel-filemanager-editor',
    frame: false,
    bodyBorder : false,
    title: <?php echo escapeJSON('Файлы')?>,
    autoScroll: true,
    layout: 'border',
    tbar:
    {
        xtype: 'toolbar',
        items:
        [
            /*{
                text:'Загрузить',
                iconCls: 'add-menu',
                itemId: 'add',
                handler: function(item)
                {

                }
            },*/
            {
                text: 'Удалить',
                iconCls: 'delete-menu',
                disabled: true,
                itemId: 'delete',
                handler: function(item)
                {
                    Ext.MessageBox.confirm('Подтверждение', 'Вы действительно хотите удалить выбранные файлы?',
                    function(btn)
                    {
                        if(btn == 'yes')
                        {
                            var id = '';
                            var selections = item.view.getSelectedRecords();
                            for(var i = 0; i < selections.length; i++)
                            {
                                id += selections[i].data.url+';';
                            }
                            Ext.Ajax.request(
                            {
                                url : '/ajax/cm/filemanager.cm.delete_file',
                                method: 'POST',
                                params:
                                {
                                    id: id,
                                },
                                maskEl : item.ownerCt.ownerCt,
                                loadingMessage : 'Удаление...',
                                success : function (response)
                                {
                                    var obj = response.responseJSON;
                                    if(obj.success)
                                    {
                                        var store = item.view.getStore();
                                        store.load();
                                        item.view.refresh();
                                    }
                                    else
                                    {
                                        Ext.MessageBox.alert('Ошибка', obj.msg);
                                    }
                                }
                            });
                            item.view.refresh();
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
                        dataUrl: '/ajax/cm/filemanager.cm.tree'
                    }),
                    root: new Ext.tree.AsyncTreeNode({id:'root'}),
                    listeners:
                    {
                        click: function(node)
                        {
                            var store = new Ext.data.JsonStore({
                                url: '/ajax/cm/filemanager.cm.files',
                                root: 'files',
                                baseParams:
                                {
                                    'node': node.id
                                },
                                fields: [
                                    'name',
                                    'url',
                                    'src'
                                ]
                            });
                            store.load();

                            var dv = this.ownerCt.ownerCt.getComponent('item').getComponent('files');
                            dv.setStore(store);
                        }
                    }
                }
            ]
        },
        {
            region: 'center',
            itemId: 'item',
            layout:'fit',
            autoScroll: true,
            items:
            [
                {
                    xtype: 'dataview',
                    itemId: 'files',
                    ctCls: 'img-chooser-view',
                    multiSelect: true,
                    overClass:'x-view-over',
                    itemSelector:'div.thumb-wrap',
                    //autoHeight:true,
                    loadingText : 'Подождите...',
                    itemSelector: 'div.thumb-wrap',
                    //emptyText: "пусто",
                    tpl: new Ext.XTemplate(
                        '<tpl for=".">',
                            '<div class="thumb-wrap" id="{name}">',
                            '<div class="thumb"><img src="{src}" title="{name}"></div>',
                            '<span class="x-editable">{name}</span></div>',
                        '</tpl>',
                        '<div class="x-clear"></div>'
                    ),
                    listeners:
                    {
                        selectionchange : function(view, selections)
                        {
                            var tb = view.ownerCt.ownerCt.getTopToolbar();
                            tb.getComponent('delete').setDisabled(view.getSelectionCount() == 0);
                            tb.getComponent('delete').selections = view.getSelectedRecords();
                            tb.getComponent('delete').view = view;
                        }
                    }
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