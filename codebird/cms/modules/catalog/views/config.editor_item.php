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
            listeners:
            {
                render: function()
                {
                    var ds = new Ext.data.Store
                    ({
                        url: '/ajax/cm/catalog.config.fields_records?table_name=<?=$table['table']?>',
                        reader: new Ext.data.JsonReader
                        ({
                            autoDestroy: true,
                            totalProperty: 'results',
                            idProperty: 'Field',
                            root: 'rows',
                            fields:
                            [
                                'Field',
                                'Type'
                            ]
                        })
                    });
                    var sm = new Ext.grid.CheckboxSelectionModel({
                        listeners:
                        {
                        }
                    });
                    var grid = new Ext.grid.GridPanel
                    ({
                        frame: false,
                        store: ds,
                        height: 150,
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
                        sm: sm,
                        viewConfig:
                        {
                            forceFit: true
                        },
                        listeners:
                        {
                            dblclick: function()
                            {
                                var sels = grid.getSelectionModel().getSelections();
                                if(sels.length > 0)
                                {
                                    var id = sels[0].id;
                                    alert(id);
                                }
                            }
                        }
                    });
                    grid.getStore().load();

                    this.add(grid);

                    var button_add = new Ext.Button
                    ({
                        text: 'Добавить',
                        style: 'margin-top: 5px;',
                        handler: function()
                        {
                            Ext.Ajax.request({
                                url : '/ajax/cm/catalog.config.fields_add_form',
                                params:
                                {
                                    table_name: <?=escapeJSON($table['table'])?>,
                                    grid_id: grid.id
                                },
                                method: 'POST',
                                maskEl : grid,
                                loadingMessage : 'Загрузка...',
                                success : function (response) {
                                    var obj = response.responseJSON;
                                    obj.success_update = function(){
                                        grid.getStore().load();
                                    };
                                    var win = new Ext.Window(obj);
                                    win.show(this.id);
                                }
                            });
                        }
                    });

                    this.add(button_add);
                }
            }
        },
        /*{
            html: <?=escapeJSON('<pre>'.print_r($param_table,1).'</pre>');?>
        },
        {
            html: <?=escapeJSON('<pre>'.print_r($table,1).'</pre>');?>
        }*/
    ]
}