{
    xtype: 'panel',
    id: 'manual-form',
    layout: 'absolute',
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
                xtype: 'treepanel',
                title: 'Помощь',
                itemId: 'tree-panel',
                x: 8,
                y: 8,
                height: 400,
                width: 300,
                margins: '2 2 0 2',
                autoScroll: true,
	        rootVisible: false,
                dataUrl: '/ajax/manual.cm.tree',
	        root: new Ext.tree.AsyncTreeNode({id:'root'}),
//                loader: new Ext.app.ManualLoader({dataUrl:'/ajax/manual.cm.tree'}),
                listeners:
                {
	            render: function(tp)
                    {
                        tp.getSelectionModel().on('selectionchange', function(tree, node){
                                var panel = tp.ownerCt.getComponent('content-panel');
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
            });

            comp.add(
            {
                xtype: 'treepanel',
                title: 'Сервис',
                itemId: 'manager-panel',
                x: 8,
                y: 416,
                width: 300,
                anchor: '0% 98%',
                margins: '2 2 0 2',
                autoScroll: true,
	        rootVisible: false,
                dataUrl: '/ajax/cm.cm.manager_tree',
                root: new Ext.tree.AsyncTreeNode({id:'root'}),
                listeners:
                {
	            click: function(node)
                    {
                        //Ext.MessageBox.alert('Доступ запрещен', 'У вас нет достаточных прав на выполнение данной операции2.');
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
            });

            comp.add(
            {
                xtype: 'panel',
                itemId: 'content-panel',
                x: 316,
                y: 8,
                anchor: '98% 98%',
                bodyStyle: 'padding: 8px',
                html: 'Добро пожаловать!!!'
            });
            
        }
    }
}
