{
    layout:'fit',
    width:600,
    height:400,
    plain: true,
    border: false,
    title: <?php echo $title ? escapeJSON($title) :  escapeJSON('Выбрать')?>,
    items:
    [
        {
            xtype: 'panel',
            itemId: 'panel',
            bodyBorder : true,
            autoScroll: true,
            layout: 'fit',
            tbar:
            {
                xtype: 'toolbar',
                items:
                [

                ]
            },
            listeners:
            {
                render: function(cmp)
                {
                    var sm = new Ext.grid.CheckboxSelectionModel({
                        checkOnly: true,
                        listeners:
                        {
                            /*selectionchange: function(sm){
                                var tb = cmp.getTopToolbar();
                                var dis = (sm.getCount() == 0);
                                tb.getComponent('edit').setDisabled(dis);
                                tb.getComponent('delete').setDisabled(dis);
                            }*/
                        }
                    });

                    var ds = new Ext.data.Store
                    ({
                        url: '/ajax/cm/catalog.cm.rel_records',
                        baseParams:
                        {
                            id: '<?php echo $id ?>',
                            'rel[]': [<?=implode(',',$rel)?>]
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
                                'title',
                                'checked'
                            ]
                        }),
                        listeners:{
                            load: function(st,records){
                                var ar = [];
                                for(var i=0; i < records.length; i++){
                                    if(records[i].data['checked'] == 1){
                                        ar.push(records[i]);
                                    }
                                }
                                sm.selectRecords(ar);
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
                                { header: 'Название', dataIndex: 'title'}
                            ]
                        }),
                        sm: sm,
                        viewConfig:
                        {
                            forceFit: true
                        },
                        //bbar: paging,
                        listeners:
                        {
                        }
                    });

                    this.add(grid);

                    grid.getStore().load();
                }
            }
        }
    ],
    buttonAlign: 'center',
    buttons:
    [
        {
            text: 'OK',
            handler: function(btn)
            {
                var grid = btn.ownerCt.ownerCt.getComponent('panel').getComponent('grid');
                var records = grid.getSelectionModel().getSelections();
                var rel = [];
                for(var i=0; i < records.length; i++){
                    rel.push(records[i].data['id']);
                }
                btn.ownerCt.ownerCt.result = rel.length > 0 ? rel : [-1];

                btn.ownerCt.ownerCt.close();
            }
        },
        {
            text: 'Отмена',
            handler: function()
            {
                this.ownerCt.ownerCt.close();
            }
        }
    ]
}
