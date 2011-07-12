{
    layout:'fit',
    width:600,
    height:400,
    plain: true,
    border: false,
    title: <?php echo $title ? escapeJSON($title) :  escapeJSON('Фотографии')?>,
    items:
    [
        {
            xtype: 'panel',
            bodyBorder : true,
            autoScroll: true,
            layout: 'fit',
            tbar:
            {
                xtype: 'toolbar',
                items:
                [
                    {
                        text: 'Добавить',
                        iconCls: 'add-menu',
                        itemId: 'add',
                        handler: function(item)
                        {
                            var grid =item.ownerCt.ownerCt.getComponent('grid');
                            App.uploadWindow({
                                targetId: this.id,
                                id: '<?=$id?>',
                                url: '/ajax/cm/catalog.cm.uploadimages',
                                success: function(result)
                                {
                                    var paging = grid.getBottomToolbar();
                                    grid.getStore().load({params:{start:paging.cursor, limit:paging.pageSize}});
                                    grid.getSelectionModel().clearSelections();
                                    //alert(result);
                                    //item.ownerCt.ownerCt.ownerCt.t = result.src;
                                }
                            });
                        }
                    },
                    {
                        text: 'Править',
                        iconCls: 'edit-menu',
                        disabled: true,
                        itemId: 'edit',
                        handler: function(item)
                        {
                            var grid =item.ownerCt.ownerCt.getComponent('grid');
                            var s = grid.getSelectionModel().getSelections();
                            App.uploadWindow({
                                targetId: this.id,
                                id: '<?=$id?>/'+s[0].id.replace(/\//g,'\\'),
                                url: '/ajax/cm/catalog.cm.uploadimages',
                                success: function(result)
                                {
                                    var paging = grid.getBottomToolbar();
                                    grid.getStore().load({params:{start:paging.cursor, limit:paging.pageSize}});
                                    grid.getSelectionModel().clearSelections();
                                    item.ownerCt.ownerCt.ownerCt.t = result.src;
                                }
                            });
                        }
                    },
                    {
                        text: 'Удалить',
                        iconCls: 'delete-menu',
                        disabled: true,
                        itemId: 'delete',
                        handler: function(item)
                        {
                            
                             Ext.MessageBox.confirm('Подтверждение', 'Вы действительно хотите удалить выбранные строки?',
                                function(btn)
                                {
                                    if(btn == 'yes')
                                    {
                                        var grid =item.ownerCt.ownerCt.getComponent('grid');
                                        var s = grid.getSelectionModel().getSelections();
                                        var images = "";
                                        for(var i = 0; i < s.length; i++)
                                        {
                                            images += s[i].id+',';
                                        }
                                        Ext.Ajax.request(
                                            {
                                                url : '/ajax/cm/catalog.cm.deleteimages',
                                                method: 'POST',
                                                params:
                                                {
                                                    images: images,
                                                    id: '<?=$id?>'
                                                },
                                                loadingMessage : 'Удаление...',
                                                success : function (response)
                                                {
                                                    var obj = response.responseJSON;
                                                    if(obj.success)
                                                    {
                                                        var paging = grid.getBottomToolbar();
                                                        grid.getStore().load({params:{start:paging.cursor, limit:paging.pageSize}});
                                                        grid.getSelectionModel().clearSelections();

                                                    }
                                                    else
                                                    {
                                                        Ext.MessageBox.alert('Ошибка', obj.msg);
                                                    }
                                                }
                                            });
                                    }
                                });
                        }
                    }
                ]
            },
            listeners:
            {
                render: function(cmp)
                {
                    var page_size = <?php echo $page_size?>;

                    var ds = new Ext.data.Store
                    ({
                        url: '/ajax/cm/catalog.cm.images_records',
                        baseParams:
                        {
                            id: '<?php echo $id ?>'
                        },
                        maskEl : this,
                        reader: new Ext.data.JsonReader
                        ({
                            totalProperty: 'results',
                            autoDestroy: true,
                            idProperty: 'id',
                            root: 'rows',
                            fields:
                            [
                                'id',
                                'img'
                            ]
                        })
                    });

                    var paging = new Ext.PagingToolbar
                    ({
                        store: ds,
                        pageSize: page_size,
                        displayInfo: false
                    });

                    var sm = new Ext.grid.CheckboxSelectionModel({
                        listeners:
                        {
                            selectionchange: function(sm){
                                var tb = cmp.getTopToolbar();
                                var dis = (sm.getCount() == 0);
                                tb.getComponent('edit').setDisabled(dis);
                                tb.getComponent('delete').setDisabled(dis);
                            }
                        }
                    });

                    var grid = new Ext.grid.GridPanel
                    ({
                        itemId: 'grid',
                        frame: true,
                        store: ds,
                        colModel: new Ext.grid.ColumnModel
                        ({
                            defaults:
                            {
                                width: 120,
                                sortable: true
                            },
                            columns:
                            [
                                sm,
                                { header: 'Фото', dataIndex: 'img'}
                            ]
                        }),
                        sm: sm,
                        viewConfig:
                        {
                            forceFit: true
                        },
                        bbar: paging,
                        listeners:
                        {
                            dblclick: function(){
                                var grid = this;
                                var s = grid.getSelectionModel().getSelections();
                                App.uploadWindow({
                                    targetId: this.id,
                                    id: '<?=$id?>/'+s[0].id.replace(/\//g,'\\'),
                                    url: '/ajax/cm/catalog.cm.uploadimages',
                                    success: function(result)
                                    {
                                        var paging = grid.getBottomToolbar();
                                        grid.getStore().load({params:{start:paging.cursor, limit:paging.pageSize}});
                                        grid.getSelectionModel().clearSelections();
                                    }
                                });
                            }
                        }
                    });

                    this.add(grid);

                    grid.getStore().load({params:{start:0, limit:page_size}});
                }
            }
        }
    ],
    buttonAlign: 'center',
    buttons:
    [

        {
            text: 'Закрыть',
            handler: function()
            {
            
                this.ownerCt.ownerCt.close();
                
            }
        }
    ]
}
