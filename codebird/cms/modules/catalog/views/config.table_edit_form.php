{
    layout:'fit',
    width: 400,
    height: 400,
    closeAction:'close',
    plain: true,
    border: false,
    title: <?=escapeJSON(($is_position)?'Таблица позиций':'Таблица раздела')?>,
    items:
    [
        {
            xtype: 'form',
            itemId: 'form',
            frame: true,
            labelAlign: 'top',
            autoScroll: true,
            defaults:
            {
                //width: 350,
                xtype: 'textfield'
            },
            items:
            [
                {
                    xtype: 'hidden',
                    name: 'table_id',
                    value: <?=escapeJSON($table_id)?>
                },
                {
                    fieldLabel: 'Название',
                    name: 'title',
                    anchor: '95%',
                    allowBlank: false,
                    value: <?=escapeJSON($table['title'])?>
                },
                {
                    fieldLabel: 'Таблица',
                    name: 'table',
                    anchor: '95%',
                    allowBlank: false,
                    value: <?=escapeJSON($table['table'])?>
                },
                {
                    xtype: 'combo',
                    fieldLabel: 'Тип поля',
                    hiddenName: 'type',
                    anchor: '95%',
                    mode: 'local',
                    editable: false,
                    resizable: false,
                    valueField: 'id',
                    displayField: 'display',
                    triggerAction: 'all',
                    value: 'varchar',
                    store: new Ext.data.ArrayStore({
                        id: 0,
                        fields:
                        [
                            'id',
                            'display'
                        ],
                        data:
                        [
                            ['varchar','varchar(256)'],
                            ['text','text'],
                            ['int','int'],
                            ['float','float'],
                            ['smallint','smallint']
                        ]
                    })
                }
            ]
        }
    ],
    buttonAlign: 'center',
    buttons:
    [
        {
            text:'Сохранить',
            handler: function(btn)
            {
                var form = this.ownerCt.ownerCt.getComponent('form');

                if(form.getForm().isValid())
                {
                    form.getForm().submit({
                        url: '/ajax/cm/catalog.config.fields_add',
                        method: 'POST',
                        waitTitle: 'Подождите',
                        waitMsg: 'Сохранение...',
                        success: function(form, action){
                                App.msg('Готово', 'Поле добавлено');
                                if(btn.ownerCt.ownerCt.success_update){
                                    btn.ownerCt.ownerCt.success_update();
                                }
                                btn.ownerCt.ownerCt.hide();
                            },
                        failure: function(form, action){
                                Ext.MessageBox.alert('Ошибка', action.result.msg);
                            }
                    });
                }
                else
                {
                    Ext.MessageBox.alert('Проверка', 'Заполните все поля');
                }
            }
        },
        {
            text: 'Отмена',
            handler: function()
            {
                this.ownerCt.ownerCt.hide();
            }
        }
    ]
}