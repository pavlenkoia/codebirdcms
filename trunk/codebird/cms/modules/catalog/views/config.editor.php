{
    xtype: 'panel',
    id: 'panel-catalog-config-editor',
    frame: false,
    bodyBorder : false,
    title: <?php echo escapeJSON('Конфигуратор разделов')?>,
    autoScroll: true,
    layout: 'border',
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
                        dataUrl: '/ajax/cm/catalog.config.tree'
                    }),
                    root: new Ext.tree.AsyncTreeNode()
                }
            ]
        }
    ]
}