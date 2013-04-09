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

                }
            },
            {
                text:'Удалить',
                iconCls: 'delete-menu',
                disabled: true,
                itemId: 'delete',
                handler: function(event, toolEl, panel)
                {

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