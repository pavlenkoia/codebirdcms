{
    xtype: 'form',
    frame: true,
    bodyBorder : true,
    autoScroll: true,
    defaults:
    {
    },
    items:
    [
        {
            xtype:'fieldset',
            title: <?=escapeJSON(($is_position)?'Позиции':'Раздел')?>,
            collapsible: false,
            autoHeight:true,
            items:
            [
                {
                    xtype: 'displayfield',
                    fieldLabel: 'Название',
                    value: <?=escapeJSON($table['title'])?>
                },
                {
                    xtype: 'displayfield',
                    fieldLabel: 'Таблица',
                    value: <?=escapeJSON('`'.$table['table'].'`')?>
                }
            ]
        },
        {
            xtype:'fieldset',
            title: <?=escapeJSON('Поля таблицы `'.$table['table'].'`')?>,
            collapsible: false,
            autoHeight:true,
            items:
            [
                {
                    xtype:'grid',
                    frame: false,
                    colModel: new Ext.grid.ColumnModel
                    ({
                        defaults:
                        {
                            width: 120
                        },
                        columns:
                        [
                            {header: 'Имя', dataIndex: 'Field',width:240},
                            {header: 'Тип', dataIndex: 'Type',width:240}
                        ]
                    }),
                    listeners:
                    {
                        render: function()
                        {
                            var ds = new Ext.data.Store
                            ({
                                url: '/ajax/cm/catalog.config.fields_records',
                                data:
                                {
                                    table_name: <?=escapeJSON($table['table'])?>
                                },
                                reader: new Ext.data.JsonReader
                                ({
                                    autoDestroy: true,
                                    idProperty: 'Field',
                                    root: 'rows',
                                    fields:
                                    [
                                        'Field',
                                        'Type'
                                    ]
                                })
                            });
                            //this.setStore(ds);
                            //this.store = ds;
                            //this.getStore().load();
                        }
                    }//,
                    //sm: new Ext.grid.CheckboxSelectionModel({}),
                    /*store: new Ext.data.Store
                    ({
                        url: '/ajax/cm/catalog.config.fields_records',
                        data:
                        {
                            table_name: <?=escapeJSON($table['table'])?>
                        },
                        reader: new Ext.data.JsonReader
                        ({
                            autoDestroy: true,
                            idProperty: 'Field',
                            root: 'rows',
                            fields:
                            [
                                'Field',
                                'Type'
                            ]
                        })
                    })*/
                }
            ]
        },
        {
            html: <?=escapeJSON('<pre>'.print_r($param_table,1).'</pre>');?>
        },
        {
            html: <?=escapeJSON('<pre>'.print_r($table,1).'</pre>');?>
        },
        {
            html: <?=escapeJSON('<pre>'.print_r($db_fields,1).'</pre>');?>
        }
    ]
}