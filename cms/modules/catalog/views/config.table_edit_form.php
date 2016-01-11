{
    layout:'fit',
    width: 400,
    height: 340,
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
                    xtype: 'hidden',
                    name: 'is_position',
                    value: <?=escapeJSON($is_position?1:0)?>
                },
                {
                    fieldLabel: 'Название',
                    name: 'title',
                    anchor: '95%',
                    allowBlank: false<?if(!$create){?>,
                    value: <?=escapeJSON($table['title'])?><?}?>
                },
                {
                    xtype: 'combo',
                    fieldLabel: 'Таблица',
                    allowBlank: false,
                    hiddenName: 'table',
                    anchor: '95%',
                    mode: 'local',
                    editable: true,
                    resizable: false,
                    valueField: 'id',
                    displayField: 'display',
                    triggerAction: 'all',
                    <?if($create){?>
                    value: <?=$is_position?escapeJSON('position_'):escapeJSON('section_')?>,
                    <?}else{?>
                    value: <?=escapeJSON($table['table'])?>,
                    <?}?>
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
                            foreach($db_tables as $db_table)
                            {
                                $ar[] = '['.escapeJSON($db_table).','.escapeJSON($db_table).']';
                            }
                            echo implode(',',$ar);
                            ?>
                        ]
                    })
                }<?if($is_position){?>,
                {
                    fieldLabel: 'Сортировка (ORDER BY)',
                    name: 'order',
                    anchor: '95%'<?if(!$create){?>,
                    value: <?=escapeJSON($table['order'])?><?}?>
                },
                {
                    fieldLabel: 'Запрос (sql)',
                    name: 'sql',
                    anchor: '95%',
                    xtype: 'textarea',
                    height: 60<?if(!$create){?>,
                    value: <?=escapeJSON($table['sql'])?><?}?>
                }<?}?>
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
                        url: '/ajax/cm/catalog.config.table_save',
                        method: 'POST',
                        waitTitle: 'Подождите',
                        waitMsg: 'Сохранение...',
                        success: function(form, action){
                                App.msg('Готово', 'Таблица сохранена');
                                if(btn.ownerCt.ownerCt.success_update){
                                    btn.ownerCt.ownerCt.success_update(action.result.item);
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