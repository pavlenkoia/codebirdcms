{
    xtype: 'panel',
    id: 'manual-form',
    layout: 'border',
    frame: false,
    bodyBorder : false,
    listeners:
    {
        render: function(comp)
        {
            Ext.app.ManualLoader = Ext.extend(Ext.ux.tree.XmlTreeLoader, {
                processAttributes : function(attr){
                    if(attr.section){

                        attr.text = attr.section;
                        attr.loaded = true;
                        attr.expanded = false;
                    }
                    else if(attr.title){ 

                        attr.text = attr.title;

                        attr.leaf = true;
                    }
                }
            });
            
            comp.add(
            {
                xtype: 'panel',
                layout: 'border',
                region:'west',
                frame: false,
                split:true,
                width: 300,
                bodyBorder : false,
                items:
                [
                    {
                        xtype: 'treepanel',
                        title: 'Справка',
                        itemId: 'tree-panel',
                        region: 'center',
                        layout: 'fit',
                        split:true,
                        autoScroll: true,
                        rootVisible: false,
                        dataUrl: '/ajax/manual.cm.tree',
                        root: new Ext.tree.AsyncTreeNode({id:'root'}),
                        listeners:
                        {
                            render: function(tp)
                            {
                                tp.getSelectionModel().on('selectionchange', function(tree, node){
                                        var panel = Ext.getCmp('manual-form').getComponent('content-panel');
                                        if(node.attributes['src'] && node.attributes['src'] != '')
                                        {
                                            panel.load({url:node.attributes['src'],text: 'Загрузка...'});
                                        }
                                        else
                                        {
                                            panel.body.update('');
                                        }
                                    });
                            }
                        }
                    },
                    {
                        xtype: 'treepanel',
                        title: 'Сервис',
                        itemId: 'manager-panel',
                        region:'south',
                        split:true,
                        height: 180,
                        layout: 'fit',
                        autoScroll: true,
                        rootVisible: false,
                        dataUrl: '/ajax/cm.cm.manager_tree',
                        root: new Ext.tree.AsyncTreeNode({id:'root'}),
                        listeners:
                        {
                            click: function(node)
                            {
                                if(node.attributes['access'] === false)
                                {
                                    Ext.MessageBox.alert('Доступ запрещен', 'У вас нет достаточных прав на выполнение данной операции.');
                                }
                                else
                                if(node.attributes['editor'] && node.attributes['editor'] != '')
                                {
                                    var rulename = 'manager-editor-'+node.id;
                                    Ext.util.CSS.createStyleSheet( '.'+rulename+'{ background-image: url('+node.attributes['editorIcon']+') !important; }', rulename);
                                    App.showEditor({
                                        url: node.attributes['editor'],
                                        id : 'manager-'+node.id,
                                        caption: node.text,
                                        iconCls: rulename
                                    });
                                }
                            }
                        }
                    }
                ]
            });


            comp.add(
            {
                xtype: 'panel',
                itemId: 'content-panel',
                region: 'center',
                x: 316,
                y: 8,
                anchor: '98% 98%',
                bodyStyle: 'padding: 8px',
                autoScroll: true,
                html: 'Добро пожаловать!!!'
            });
            
        }
    }
}
