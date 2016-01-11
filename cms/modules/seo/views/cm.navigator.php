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
                    dataUrl: '/ajax/cm/seo.cm.tree',
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
                                    url: '/ajax/cm/seo.cm.editor?id='+node.id+'&filename='+node.text,
                                    id : '<?php echo $args->module ?>-edit-'+node.id,
                                    caption: 'Баннер: '+node.text
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