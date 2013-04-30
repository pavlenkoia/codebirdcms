{
    layout:'fit',
    width: 400,
    height: 400,
    closeAction:'close',
    plain: true,
    border: false,
    title: <?=$editor_name?escapeJSON('Изменить поле редактора'):escapeJSON('Добавить поле редактора')?> ,
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
                    name: 'editor_name',
                    value: <?=escapeJSON($editor_name)?>
                },
                {
                    fieldLabel: 'Заголовок',
                    name: 'title',
                    anchor: '95%',
                    allowBlank: false<?if($editor_name){?>,
                    value: <?=escapeJSON($editor_title)?>
                    <?}?>
                },
                {
                    xtype: 'combo',
                    fieldLabel: 'Поле таблицы',
                    allowBlank: false,
                    hiddenName: 'field',
                    anchor: '95%',
                    mode: 'local',
                    editable: true,
                    resizable: false,
                    valueField: 'id',
                    displayField: 'display',
                    triggerAction: 'all',
                    <?if($editor_name){?>value: <?=escapeJSON($editor_field)?>,<?}?>
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
                            foreach($fields as $field)
                            {
                                $ar[] = '['.escapeJSON($field).','.escapeJSON($field).']';
                            }
                            echo implode(',',$ar);
                            ?>
                        ]
                    })
                },
                {
                    xtype: 'combo',
                    fieldLabel: 'Отображать',
                    hiddenName: 'mode',
                    anchor: '95%',
                    mode: 'local',
                    editable: false,
                    resizable: false,
                    valueField: 'id',
                    displayField: 'display',
                    triggerAction: 'all',
                    value: <?=escapeJSON($editor_mode)?>,
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
                            $ar = array(
                                '["","-"]',
                                '["edit","только в редакторе"]',
                                '["browse","только в гриде"]'
                            );
                            echo implode(',',$ar);
                            ?>
                        ]
                    })
                },
                {
                    xtype: 'combo',
                    fieldLabel: 'Тип редактора',
                    allowBlank: false,
                    hiddenName: 'type',
                    anchor: '95%',
                    mode: 'local',
                    editable: false,
                    resizable: false,
                    valueField: 'id',
                    displayField: 'display',
                    triggerAction: 'all',
                    <?if($editor_name){?>value: <?=escapeJSON($editor_type)?>,<?}?>
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
                            foreach($editors_type as $key=>$value)
                            {
                                $ar[] = '['.escapeJSON($key).','.escapeJSON($value).']';
                            }
                            echo implode(',',$ar);
                            ?>
                        ]
                    }),
                    listeners:{
                        select: function(cb, record, index ){
                            var editor_name = record.id;

                            var is_memo = (editor_name == 'memo');
                            cb.ownerCt.getComponent('editor_height').setVisible(is_memo);

                            var is_select = (editor_name == 'select');
                            cb.ownerCt.getComponent('select').setVisible(is_select);
                            cb.ownerCt.getComponent('display').setVisible(is_select);

                            var is_selecttext = (editor_name == 'selecttext');
                            cb.ownerCt.getComponent('select2').setVisible(is_selecttext);
                            cb.ownerCt.getComponent('sql').setVisible(is_selecttext);
                        }
                    }
                },
                {
                    fieldLabel: 'Высота редактора',
                    name: 'editor_height',
                    itemId: 'editor_height',
                    hidden: <?=$editor_type=='memo'?'false':'true'?>,
                    value: <?=escapeJSON($editor_editor_height)?>
                },
                {
                    fieldLabel: 'SQL запрос для списка (возвращаемые поля id и display)',
                    name: 'select',
                    anchor: '95%',
                    itemId: 'select',
                    hidden: <?=$editor_type=='select'?'false':'true'?>,
                    value: <?=escapeJSON($editor_select)?>
                },
                {
                    fieldLabel: 'Имя дисплей поля в основном запросе',
                    name: 'display',
                    anchor: '95%',
                    itemId: 'display',
                    hidden: <?=$editor_type=='select'?'false':'true'?>,
                    value: <?=escapeJSON($editor_display)?>
                },
                {
                    fieldLabel: 'Возможные значения, разделенные (;) - точкой с запятой',
                    name: 'select2',
                    anchor: '95%',
                    itemId: 'select2',
                    hidden: <?=$editor_type=='selecttext'?'false':'true'?>,
                    value: <?=escapeJSON($editor_select2)?>
                },
                {
                    fieldLabel: 'или SQL запрос (возвращаемые поля id и display)',
                    name: 'sql',
                    anchor: '95%',
                    itemId: 'sql',
                    hidden: <?=$editor_type=='selecttext'?'false':'true'?>,
                    value: <?=escapeJSON($editor_sql)?>
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
                        url: '/ajax/cm/catalog.config.editors_edit',
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
                    Ext.MessageBox.alert('Проверка', 'Заполните необходимые поля формы');
                }
            }
        }<?if($editor_name){?>,
        {
            text: 'Удалить',
            handler: function(btn)
            {
                var form = this.ownerCt.ownerCt.getComponent('form');

                Ext.MessageBox.confirm('Удаление', 'Вы действительно хотите удалить это поле?',
                    function(btn_conf){
                        if(btn_conf == 'yes'){
                            Ext.Ajax.request({
                                url : '/ajax/cm/catalog.config.editors_del',
                                params:
                                {
                                    table_id: <?=escapeJSON($table_id)?>,
                                    editor_name: <?=escapeJSON($editor_name)?>
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
        }<?}?>,
        {
            text: 'Отмена',
            handler: function()
            {
                this.ownerCt.ownerCt.hide();
            }
        }
    ]
}