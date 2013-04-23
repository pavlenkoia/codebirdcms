{
    layout:'fit',
    width: 400,
    height: 400,
    closeAction:'close',
    plain: true,
    border: false,
    title: 'Изменить поле редактора',
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
                    value: <?=escapeJSON($table_id)?>
                },
                {
                    fieldLabel: 'Имя',
                    name: 'name',
                    anchor: '95%',
                    allowBlank: false,
                    value: <?=escapeJSON($editor_name)?>
                },
                {
                    fieldLabel: 'Заголовок',
                    name: 'title',
                    anchor: '95%',
                    value: <?=escapeJSON($editor_title)?>
                },
                {
                    xtype: 'combo',
                    fieldLabel: 'Поле таблицы',
                    hiddenName: 'field',
                    anchor: '95%',
                    mode: 'local',
                    editable: true,
                    resizable: false,
                    valueField: 'id',
                    displayField: 'display',
                    triggerAction: 'all',
                    value: <?=escapeJSON($editor_field)?>,
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
                    fieldLabel: 'Тип редактора',
                    hiddenName: 'type',
                    anchor: '95%',
                    mode: 'local',
                    editable: false,
                    resizable: false,
                    valueField: 'id',
                    displayField: 'display',
                    triggerAction: 'all',
                    value: <?=escapeJSON($editor_type)?>,
                    store: new Ext.data.ArrayStore({
                        id: 0,
                        fields:
                        [
                            'id',
                            'display'
                        ],
                        data:
                        [
                            ['text','Текстовая строка'],
                            ['memo','Многострочный редактор'],
                            ['richtext','Визуальный редактор'],
                            ['date','Дата'],
                            ['check','Чекбокс'],
                            ['image','Картинка'],
                            ['file','Файл'],
                            ['int','Целое число'],
                            ['select','Выпадающий список'],
                            ['selecttext','Текстовая строка с выпадающим списком'],
                            ['labeltext','Нередактируемая текстовая строка'],
                            ['images','Картинки']
                        ]
                    }),
                    listeners:{
                        select: function(cb, record, index ){
                            var editor_name = record.id;
                            var is_memo = (editor_name == 'memo');
                            cb.ownerCt.getComponent('editor_height').setVisible(is_memo);
                        }
                    }
                },
                {
                    fieldLabel: 'Высота редактора',
                    name: 'editor_height',
                    itemId: 'editor_height',
                    hidden: <?=$editor_type=='memo'?'false':'true'?>,
                    value: <?=escapeJSON($editor_editor_height)?>
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
                        url: '/ajax/cm/catalog.config.editor_edit',
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