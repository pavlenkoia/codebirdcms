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
                    itemId: 'labelName',
                    value: <?=escapeJSON($table['title'])?>
                },
                {
                    xtype: 'displayfield',
                    fieldLabel: 'Таблица',
                    itemId: 'labelTable',
                    value: <?=escapeJSON($table['table'])?>
                },
                {
                    xtype: 'button',
                    text: 'Изменить',
                    handler: function(btn){
                        Ext.Ajax.request({
                            url : '/ajax/cm/catalog.config.table_edit_form',
                            params:
                            {
                                table_id: <?=escapeJSON($table_id)?>
                            },
                            method: 'POST',
                            maskEl : this.ownerCt,
                            loadingMessage : 'Загрузка...',
                            success : function (response) {
                                var obj = response.responseJSON;
                                obj.success_update = function(item){
                                    if(item && item.name){
                                        btn.ownerCt.getComponent('labelName').setValue(item.name);
                                    }
                                    if(item && item.table){
                                        btn.ownerCt.getComponent('labelTable').setValue(item.table);
                                    }
                                };
                                var win = new Ext.Window(obj);
                                win.show(this.id);
                            }
                        });
                    }
                }
            ]
        },
        {
            xtype:'fieldset',
            title: <?=escapeJSON('Поля таблицы `'.$table['table'].'`')?>,
            collapsible: true,
            collapsed: true,
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
                                    Ext.Ajax.request({
                                        url : '/ajax/cm/catalog.config.fields_edit_form',
                                        params:
                                        {
                                            table_name: <?=escapeJSON($table['table'])?>,
                                            field_name: id
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
                                    table_name: <?=escapeJSON($table['table'])?>
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
        {
            xtype:'fieldset',
            title: <?=escapeJSON('Поля редактора')?>,
            collapsible: true,
            autoHeight:true,
            listeners:
            {
                render: function()
                {
                    var button_add,button_up,button_down;

                    var ds = new Ext.data.Store
                    ({
                        url: '/ajax/cm/catalog.config.editors_records?table_id=<?=$table_id?>',
                        reader: new Ext.data.JsonReader
                        ({
                            autoDestroy: true,
                            totalProperty: 'results',
                            idProperty: 'id',
                            root: 'rows',
                            fields:
                            [
                                'id',
                                'title',
                                'field',
                                'type'
                            ]
                        })
                    });
                    var sm = new Ext.grid.CheckboxSelectionModel({
                        listeners:
                        {
                            rowselect: function(sm, rowIndex, r){
                                button_up.setDisabled(!sm.hasPrevious());
                                button_up.up_id = r.id;
                                button_down.setDisabled(!sm.hasNext());
                                button_down.down_id = r.id;
                            }
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
                                {header: 'Заголовок', dataIndex: 'title',width:240},
                                {header: 'Поле таблицы', dataIndex: 'field',width:240},
                                {header: 'Тип', dataIndex: 'type',width:240}
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
                                    Ext.Ajax.request({
                                        url : '/ajax/cm/catalog.config.editors_edit_form',
                                        params:
                                        {
                                            table_id: <?=escapeJSON($table_id)?>,
                                            editor_name: id,
                                            table_name: <?=escapeJSON($table['table'])?>
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
                            }
                        }
                    });
                    grid.getStore().load();

                    this.add(grid);

                    button_add = new Ext.Button
                    ({
                        text: 'Добавить',
                        style: 'padding: 5px;',
                        handler: function()
                        {
                            Ext.Ajax.request({
                                url : '/ajax/cm/catalog.config.editors_edit_form',
                                params:
                                {
                                    table_id: <?=escapeJSON($table_id)?>,
                                    table_name: <?=escapeJSON($table['table'])?>
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

                    button_up = new Ext.Button
                    ({
                        text: 'Наверх',
                        style: 'padding: 5px;',
                        disabled: true,
                        handler: function()
                        {
                            var id = this.up_id;
                            var index = ds.find('id',id);
                            var record = ds.getById(id);
                            ds.remove(record);
                            ds.insert(index-1,record);
                            sm.selectRow(index-1);

                            Ext.Ajax.request({
                                url : '/ajax/cm/catalog.config.editors_pos',
                                params:
                                {
                                    table_id: <?=escapeJSON($table_id)?>,
                                    editor_name: id,
                                    pos: 'up'
                                },
                                method: 'POST',
                                success : function (response) {

                                }
                            });
                        }
                    });

                    button_down = new Ext.Button
                    ({
                        text: 'Вниз',
                        style: 'padding: 5px;',
                        disabled: true,
                        handler: function()
                        {
                            var id = this.down_id;
                            var index = ds.find('id',id);
                            var record = ds.getById(id);
                            ds.remove(record);
                            ds.insert(index+1,record);
                            sm.selectRow(index+1);

                            Ext.Ajax.request({
                                url : '/ajax/cm/catalog.config.editors_pos',
                                params:
                                {
                                    table_id: <?=escapeJSON($table_id)?>,
                                    editor_name: id,
                                    pos: 'down'
                                },
                                method: 'POST',
                                success : function (response) {

                                }
                            });
                        }
                    });

                    var panel_btn = new Ext.Panel({
                        layout: {
                            type: 'hbox',
                            align: 'left'
                        }
                    });
                    panel_btn.add(button_add);
                    panel_btn.add(button_up);
                    panel_btn.add(button_down);
                    this.add(panel_btn);




                }
            }
        }/*,
        {
            html: <?=escapeJSON('<pre>'.print_r($param_table,1).'</pre>');?>
        } ,
        {
            html: <?=escapeJSON('<pre>'.print_r($table,1).'</pre>');?>
        }*/
    ]
}