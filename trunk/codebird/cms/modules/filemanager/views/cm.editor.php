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
            {
                text:'Создать',
                iconCls: 'add-menu',
                itemId: 'add',
                handler: function(item)
                {

                }
            },
            {
                text: 'Удалить',
                iconCls: 'delete-menu',
                disabled: true,
                itemId: 'delete',
                handler: function(item)
                {

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
                    root: new Ext.tree.AsyncTreeNode(),
                    listeners:
                    {
                        click: function(node)
                        {

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
                    xtype: 'panel',
                    itemId: 'folder',
                    border: false
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