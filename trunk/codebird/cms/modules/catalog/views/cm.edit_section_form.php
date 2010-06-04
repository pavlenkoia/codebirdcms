{
    layout:'fit',
    width: 440,
    height: 430,
    closeAction:'close',
    plain: true,
    border: false,
    title: <?php echo escapeJSON('Конфигурация раздела: '.$section->title); ?>,
    items:
    [
        {
            xtype: 'form',
            itemId: 'form',
            frame: true,
            labelAlign: 'top',
            defaults:
            {
                width: 350,
                xtype: 'textfield'
            },
            items:
            [
                {
                    xtype: 'hidden',
                    name: 'id',
                    value: <?php echo $section->id ?>
                },
                {
                    xtype: 'textfield',
                    fieldLabel: 'Название раздела',
                    name: 'title',
                    anchor: '95%',
                    value: <?php echo escapeJSON($section->title); ?>,
                    allowBlank: false
                },
                {
                    xtype: 'textfield',
                    fieldLabel: 'Символьное имя',
                    name: 'alias',
                    value: <?php echo escapeJSON($section->alias); ?>,
                    anchor: '50%'
                },
                {
                    xtype: 'combo',
                    itemId: 'section_table',
                    anchor:'95%',
                    fieldLabel: 'Тип раздела',
                    hiddenName: 'section_table',
                    mode: 'local',
                    editable: false,
                    resizable: false,
                    valueField: 'value',
                    displayField: 'display',
                    value: <?php echo escapeJSON($section->section_table); ?>,
                    triggerAction: 'all',
                    store:
                    {
                        xtype: 'arraystore',
                        fields: ['value','display'],
                        data:
                        [
                            <?php
                            $array = array();
                            $array[] = "['','-']";
                            foreach($tables_section as $table)
                            {
                                $array[] = "['".$table['id']."','".$table['name']."']";
                            }
                            echo implode(",", $array);
                            ?>
                        ]
                    }
                },
                {
                    xtype: 'combo',
                    itemId: 'position_table',
                    anchor:'95%',
                    fieldLabel: 'Позиции',
                    hiddenName: 'position_table',
                    mode: 'local',
                    editable: false,
                    resizable: false,
                    valueField: 'value',
                    displayField: 'display',
                    value: <?php echo escapeJSON($section->position_table); ?>,
                    triggerAction: 'all',
                    store:
                    {
                        xtype: 'arraystore',
                        fields: ['value','display'],
                        data:
                        [
                            <?php
                            $array = array();
                            $array[] = "['','-']";
                            foreach($tables as $table)
                            {
                                $array[] = "['".$table['id']."','".$table['name']."']";
                            }
                            echo implode(",", $array);
                            ?>
                        ]
                    }
                },
                {
                    xtype: 'checkbox',
                    //fieldLabel: '',
                    boxLabel: 'не создавать подразделов',
                    name: 'leaf',
                    inputValue: 1,
                    checked: <?php if($section->leaf == 1) echo 'true'; else echo 'false'?>
                },
                {
                    xtype: 'textarea',
                    fieldLabel: 'Шаблон для дочерних элементов',
                    name: 'children_tpl',
                    width: '95%',
                    height: 70,
                    value: <?php echo escapeJSON($section->children_tpl)?>
                }
            ]
        },
    ],
    listeners:
    {
        show: function(comp)
        {
            comp.setWidth(comp.getWidth()-1);
        }
    },
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
                        url: '/ajax/cm/catalog.cm.save_section',
                        method: 'POST',
                        waitTitle: 'Подождите',
                        waitMsg: 'Сохранение...',
                        success: function(form, action){                                
                                btn.ownerCt.ownerCt.hide();
                                App.closeEditor({id : 'catalog-section-edit-<?php echo $section->id ?>'});
                                App.showEditor({
                                    url: '/ajax/cm/catalog.cm.editor?id=<?php echo $section->id ?>',
                                    id : 'catalog-section-edit-<?php echo $section->id ?>',
                                    caption: <?php echo escapeJSON($section->title) ?>
                                    });
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