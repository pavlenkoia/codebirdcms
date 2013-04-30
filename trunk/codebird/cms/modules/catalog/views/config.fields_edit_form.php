{
    layout:'fit',
    width: 400,
    height: 200,
    closeAction:'close',
    plain: true,
    border: false,
    title: 'Изменить поле',
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
                    xtype: 'hidden',
                    name: 'old_name',
                    value: <?=escapeJSON($field_name)?>
                },
                {
                    fieldLabel: 'Имя поля',
                    name: 'name',
                    anchor: '95%',
                    allowBlank: false,
                    value: <?=escapeJSON($field_name)?>
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
                    value: <?=escapeJSON($field_type)?>,
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
                        url: '/ajax/cm/catalog.config.fields_edit',
                        method: 'POST',
                        waitTitle: 'Подождите',
                        waitMsg: 'Сохранение...',
                        success: function(form, action){
                                App.msg('Готово', 'Поле изменено');
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
            text: 'Удалить',
            handler: function(btn)
            {
                var form = this.ownerCt.ownerCt.getComponent('form');

                Ext.MessageBox.confirm('Удаление', 'Вы действительно хотите удалить это поле?',
                    function(btn_conf){
                        if(btn_conf == 'yes'){
                            Ext.Ajax.request({
                                url : '/ajax/cm/catalog.config.fields_del',
                                params:
                                {
                                    table_name: <?=escapeJSON($table_name)?>,
                                    field_name: <?=escapeJSON($field_name)?>
                                },
                                method: 'POST',
                                maskEl : btn.ownerCt.ownerCt,
                                loadingMessage : 'Удаление...',
                                success : function (response) {
                                    var result = response.responseJSON;
                                    if(result.success){
                                        App.msg('Готово', 'Поле удалено');
                                        if(btn.ownerCt.ownerCt.success_update){
                                            btn.ownerCt.ownerCt.success_update();
                                        }
                                        btn.ownerCt.ownerCt.hide();
                                    }
                                    else{
                                        Ext.MessageBox.alert('Ошибка', result.msg);
                                    }
                                }
                            });
                        }
                    }
                );
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