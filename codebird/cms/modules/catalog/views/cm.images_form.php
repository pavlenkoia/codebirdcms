{
    layout:'fit',
    width:600,
    height:400,
    plain: true,
    border: false,
    title: <?php echo escapeJSON('Фотографии')?>,
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
                        }
                    },
                    {
                        text: 'Править',
                        iconCls: 'edit-menu',
                        disabled: true,
                        itemId: 'edit',
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
                            id: '<?php echo $images_id ?>'
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
                                var tb = this.getTopToolbar();
                                var dis = (sm.getCount() == 0);
                                tb.getComponent('edit').setDisabled(dis);
                                tb.getComponent('edit').setHandler(edit);
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
                            //dblclick: edit
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
