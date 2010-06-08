/**
 * Приложение
 */

var Editor  = function()
{
    Ext.Ajax.on('requestcomplete', function (conn, response, options) {
        // пробуем декодировать ответ как JSON
        // чтобы ошибка не выбивала выполнение кода
        // попытка заключена в защищенный блок
        try {
            // если все прошло ок то в св-во responseJSON объекта response
            // запишется декодированный из JSON объект
            response.responseJSON = Ext.util.JSON.decode(response.responseText);
        }
        catch (e) {
            // при ошибке responseJSON будет false
            response.responseJSON = false;
        }
    });

    return  {
        init : function()
        {
            
        },
        showEditor : function(options)
        {
            Ext.Ajax.request({
                url : '/ajax/cm/catalog.editor.position_edit_form',
                method: 'POST',
                params:
                {
                    section_id: options.section_id,
                    position_id: options.position_id
                },
                //maskEl : '',
                //loadingMessage : 'Загрузка...',
                success : function (response) {
                    var win = new Ext.Window(response.responseJSON);
                    win.show();
                }
            });
        },
        showEditorSection : function(options)
        {
            Ext.Ajax.request({
                url : '/ajax/cm/catalog.editor.section_edit_form',
                method: 'POST',
                params:
                {
                    section_id: options.section_id,
                    position_id: options.position_id
                },
                //maskEl : '',
                //loadingMessage : 'Загрузка...',
                success : function (response) {
                    var win = new Ext.Window(response.responseJSON);
                    win.show();
                }
            });
        },
        deletePosition : function(options)
        {
            Ext.MessageBox.confirm('Подтверждение', 'Вы действительно хотите удалить?',
                function(btn)
                {
                    if(btn == 'yes')
                    {
                        Ext.Ajax.request({
                            url : '/ajax/cm/catalog.cm.delete_position',
                            method: 'POST',
                            params:
                            {
                                id: options.position_id,
                                _table_id_: options.table_id
                            },
                            success : function (response)
                            {
                                var obj = response.responseJSON;
                                if(obj.success)
                                {
                                    window.location = window.location;
                                }
                                else
                                {
                                    Ext.MessageBox.alert('Ошибка', obj.msg);
                                }
                            }
                        });
                    }
                });

        },
        uploadWindow : function(option){
            var win = new Ext.Window({
                title: option.title ? option.title : 'Загрузить картинку',
                layout:'fit',
                width:400,
                height:130,
                closeAction:'close',
                border: false,
                items:[
                    {
                        xtype: 'form',
                        itemId: 'form',
                        frame: true,
                        fileUpload: true,
                        defaults:
                        {
                            anchor: '95%',
                            allowBlank: false,
                            msgTarget: 'side'
                        },
                        items:[
                            {
                                xtype: 'fileuploadfield',
                                name: 'file',
                                emptyText: option.emptyText ? option.emptyText : 'Выберите картинку...',
                                buttonText: 'Выбрать',
                                hideLabel: true
                            },
                            {
                                xtype: 'hidden',
                                name: 'id',
                                value: option.id
                            }
                        ],
                        buttonAlign: 'center',
                        buttons:[
                            {
                                text:'Загрузить',
                                handler: function(){
                                    win.hide();
                                    var form = this.ownerCt.ownerCt;
                                    form.getForm().submit({
                                        url: option.url,
                                        waitMsg: option.waitMsg ? option.waitMsg : 'Загрузка файла...',
                                        success: function(fp, action)
                                        {
                                            if(option.success){
                                                option.success(action.result);
                                            }
                                            win.close();
                                        },
                                        failure: function(fp, action)
                                        {
                                            Ext.MessageBox.alert('Ошибка', action.result.msg);
                                            win.close();
                                        }
                                    });
                                }
                            },
                            {
                                text:'Отмена',
                                handler: function(){
                                    win.close();
                                }
                            }
                        ]
                    }
                ],
                listeners: {
                    show: function(comp)
                    {
                        comp.setWidth(comp.getWidth()-1);
                    }
                }
            });
            if(option.targetId){
                win.show(option.targetId);
            }
            else{
                win.show();
            }
        }
    }
}();


// точка входа в приложение
Ext.onReady(Editor.init,Editor,true);


