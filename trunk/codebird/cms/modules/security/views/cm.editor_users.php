{
    xtype: 'panel',
    id: 'panel-security-editor-users',
    frame: false,
    bodyBorder : false,
    title: 'Пользователи',
    autoScroll: true,
    layout: 'fit',
    tbar:
    {
        xtype: 'toolbar',
        items:
        [
            {
                text: 'Создать',
                iconCls: 'add-menu',
                itemId: 'add',
                handler: function(item)
                {
                    Ext.Ajax.request({
                        url : '/ajax/cm/security.cm.add_user_form',
                        method: 'POST',
                        maskEl : 'panel-security-editor-users',
                        loadingMessage : 'Загрузка...',
                        success : function (response) {
                            var win = new Ext.Window(response.responseJSON);
                            win.show(item.id);
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

                }
            },
            {
                text: 'Удалить',
                iconCls: 'delete-menu',
                disabled: true,
                itemId: 'delete',
                handler: function(item)
                {
                    Ext.MessageBox.confirm('Подтверждение', 'Вы действительно хотите удалить выбранных пользователей?',
                        function(btn)
                        {
                            if(btn == 'yes')
                            {
                                var s = Ext.getCmp('panel-security-editor-users').getComponent('grid').getSelectionModel().getSelections();
                                var id = "";
                                for(var i = 0; i < s.length; i++)
                                {
                                    id += s[i].id+',';
                                }
                                Ext.Ajax.request(
                                    {
                                        url : '/ajax/cm/security.cm.delete_user',
                                        method: 'POST',
                                        params:
                                        {
                                            id: id
                                        },
                                        maskEl : 'panel-security-editor-users',
                                        loadingMessage : 'Удаление...',
                                        success : function (response)
                                        {
                                            var obj = response.responseJSON;
                                            if(obj.success)
                                            {
                                                var grid = Ext.getCmp('panel-security-editor-users').getComponent('grid');
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
    items:
    [
        
    ],
    buttonAlign: 'center',
    buttons:
    [
        {
            text: 'Закрыть',
            formBind: true,
            handler: function()
            {
                App.closeEditor({id : this.ownerCt.ownerCt.ownerCt.id});
            }
        }
    ],
    listeners:
    {
        render: function()
        {
            var edit = function(item)
            {
                var sels = Ext.getCmp('panel-security-editor-users').getComponent('grid').getSelectionModel().getSelections();
                if(sels.length > 0)
                {
                    var id = sels[0].id;
                    Ext.Ajax.request({
                        url : '/ajax/cm/security.cm.edit_user_form',
                        method: 'POST',
                        params:
                        {
                            id: id
                        },
                        maskEl : 'panel-security-editor-users',
                        loadingMessage : 'Загрузка...',
                        success : function (response) {
                            var win = new Ext.Window(response.responseJSON);
                            win.show(item.id);
                        }
                    });
                }
            };

            var page_size = 50;

            var ds = new Ext.data.Store
            ({
                url: '/ajax/cm/security.cm.users',
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
                        'name',
                        'disabled'
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
                        var tb = Ext.getCmp('panel-security-editor-users').getTopToolbar();
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
                stateId: 'panel-security-editor-users',
                stateful: true,
                stateEvents: ['columnmove', 'columnresize', 'sortchange'],
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
                        {header: 'Логин', dataIndex: 'name',width:240},
                        {header: 'Статус', dataIndex: 'disabled',width:100}
                    ]
                }),
                sm: sm,
                viewConfig:
                {
                    //forceFit: true
                },
                bbar: paging,
                listeners:
                {
                    dblclick: edit
                }
            });

            this.add(grid);

            grid.getStore().load({params:{start:0, limit:page_size}});


        }
    }
}
