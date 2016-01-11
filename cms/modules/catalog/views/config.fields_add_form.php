{
    layout:'fit',
    width: 400,
    height: 200,
    closeAction:'close',
    plain: true,
    border: false,
    title: 'Добавить поле',
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
                    name: 'table_name',
                    value: <?=escapeJSON($table_name)?>
                },
                {
                    fieldLabel: 'Имя поля',
                    name: 'name',
                    anchor: '95%',
                    allowBlank: false
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
                    value: 'varchar(256)',
                    store: new Ext.data.ArrayStore({
                        id: 0,
                        fields:
                        [
                            'id',
                            'display'
                        ],
                        data:
                        [
                            <?
                            $ar = array();
                            foreach($fields_type as $key=>$value)
                            {
                                $ar[] = '['.escapeJSON($key).','.escapeJSON($value).']';
                            }
                            echo implode(',',$ar);
                            ?>
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