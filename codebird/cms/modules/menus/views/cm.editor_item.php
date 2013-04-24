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
            xtype: 'hidden',
            name: 'id',
            value: <?php echo $item->id ?>
        },
        {
            xtype:'fieldset',
            collapsible: false,
            autoHeight:true,
            items:
            [
                {
                    xtype: 'textfield',
                    fieldLabel: 'Заголовок',
                    name: 'title',
                    width: 300,
                    allowBlank: false,
                    value: <?php echo escapeJSON($item->title)?>
                },
                {
                    xtype: 'checkbox',
                    fieldLabel: 'Видимый в меню',
                    inputValue: 1,
                    name: 'visible',
                    checked: <?php if($item->visible == 1) echo 'true'; else echo 'false'?>
                }
            ]
        },
        {
            xtype:'fieldset',
            title: 'Тип',
            collapsible: false,
            autoHeight:true,
            items:
            [
                {
                    xtype: 'hidden',
                    name: 'type',
                    value: <?php echo escapeJSON($item->type)?>
                },
                {
                    xtype: 'hidden',
                    name: 'type_id',
                    value: <?php echo escapeJSON($item->type_id)?>
                },
                {
                    xtype: 'button',
                    text: 'Выбрать',
                    style: {marginBottom: '12px'},
                    handler: function(item)
                    {
                        var win = new Ext.Window
                        ({
                            layout:'fit',
                            width:480,
                            height:400,
                            closeAction:'close',
                            plain: true,
                            border: false,
                            title: 'Выбрать тип',
                            items:
                            [
                                {
                                    xtype: 'treepanel',
                                    itemId: 'tree',
                                    animate: true,
                                    enableDD: false,
                                    containerScroll: false,
                                    border: false,
                                    autoScroll: true,
                                    rootVisible: false,
                                    loader: new Ext.tree.TreeLoader(
                                    {
                                        dataUrl: '/ajax/cm/menus.cm.tree_types'
                                    }),
                                    root: new Ext.tree.AsyncTreeNode({id:'root'}),
                                    listeners:
                                    {
                                        click: function(node)
                                        {
                                            var btn = node.getOwnerTree().ownerCt.buttons[0];
                                            if(node.attributes['type'] && node.attributes['type'] != '')
                                            {
                                                btn.setDisabled(false);
                                                item.node = node;
                                            }
                                            else
                                            {
                                                btn.setDisabled(true);
                                            }
                                        }
                                    }
                                }
                            ],
                            buttonAlign: 'center',
                            buttons:
                            [
                                {
                                    text:'Выбрать',
                                    disabled: true,
                                    itemId: 'select',
                                    handler: function()
                                    {
                                        var node = item.node;
                                        var typeCt = item.ownerCt;
                                        var form = typeCt.ownerCt;
                                        var type = form.getForm().findField('type');
                                        type.setValue(node.attributes['type']);
                                        if(node.attributes['type_id'])
                                        {
                                            var type_id = form.getForm().findField('type_id');
                                            type_id.setValue(node.attributes['type_id']);
                                        }
                                        if(node.attributes['type_label'])
                                        {
                                            var type_label = form.getForm().findField('type_label');
                                            type_label.setValue(node.attributes['type_label']);
                                        }
                                        var type_link = form.getForm().findField('type_link');
                                        if(node.attributes['type'] == '_label')
                                        {
                                            type_link.setVisible(false);
                                        }
                                        else
                                        {
                                            type_link.setVisible(true);
                                        }
                                        if(node.attributes['type_link'])
                                        {
                                            type_link.setValue(node.attributes['type_link']);
                                        }
                                        win.hide();
                                    }
                                },
                                {
                                    text: 'Отмена',
                                    handler: function()
                                    {
                                        win.hide();
                                    }
                                }
                            ]
                        });
                        win.show(this.id);
                    }
                },
                {
                    xtype: 'displayfield',
                    name: 'type_label',
                    hideLabel: true,
                    value: <?php echo escapeJSON($type_label)?>,
                    style: 'margin-bottom: 12px'
                },
                {
                    xtype: 'textfield',
                    fieldLabel: 'Ссылка',
                    name: 'type_link',
                    value: <?php echo escapeJSON($type_link)?>,
                    hidden: <?php if($type_link != '') echo 'false'; else echo 'true'?>,
                    width: 330
                }
            ]
        },
        {
            xtype:'fieldset',
            title: 'Параметры',
            collapsible: false,
            autoHeight:true,
            items:
            [
                {
                    xtype: 'textfield',
                    fieldLabel: 'Атрибуты ссылки',
                    name: 'attr',
                    anchor: '95%',
                    value: <?php echo escapeJSON($item->attr)?>
                }
            ]
        },
        {
            xtype:'fieldset',
            title: 'Картинка',
            collapsible: false,
            autoHeight:true,
            items:
            [
                {
                    xtype: 'box',
                    listeners:
                    {   render: function()
                        {
                            var src = '<img src="<?php  echo get_cache_pic($item->img_src,100,100,true,'',"files/menus/cache/") ?>"/>';
                            var t = new Ext.Template('<div style="margin:5px 0px 10px 0px;">{src}</div>');
                            t.compile();
                            t.append(this.id, {src:src});
                        }
                    }
                },
                {
                    xtype: 'button',
                    text: 'Загрузить',
                    handler: function(btn)
                    {
                        var form = btn.ownerCt.ownerCt;
                        Editor.uploadWindow({
                            targetId: this.id,
                            id: '<?php echo  $item->id ?>',
                            url: '/ajax/cm/menus.cm.uploadimage',
                            success: function(result)
                            {
                                var id_f = form.getForm().findField('id');
                                id_f.setValue(result.id);
                                var t = new Ext.Template('<img src="{src}" style="margin:5px 0px 10px 0px;"/>');
                                t.compile();
                                t.overwrite(btn.ownerCt.getComponent(0).id, {src: result.src});
                            }
                        });
                    }
                }
            ]
        }
    ],
    buttonAlign: 'center',
    buttons:
    [
        {
            text: 'Сохранить',
            formBind: true,
            handler: function()
            {
                var form = this.ownerCt.ownerCt;
                var tab = this.ownerCt.ownerCt.ownerCt;
                var node = tab.node;
                var title = form.getForm().findField('title').getValue();
                node.setText(title);
                tab.setTitle(title);
                form.getForm().submit({
                        url: '/ajax/cm/menus.cm.save_item',
                        method: 'POST',
                        waitTitle: 'Подождите',
                        waitMsg: 'Сохранение...',
                        success: function(form, action)
                        {   
                            App.msg('Готово','Пункт меню сохранен');
                        },
                        failure: function(form, action)
                        {
                            Ext.MessageBox.alert('Ошибка', action.result.msg);
                        }
                    });
            }
        },
        {
            text: 'Закрыть',
            formBind: true,
            handler: function()
            {
                var tab = this.ownerCt.ownerCt.ownerCt;
                var tabs = this.ownerCt.ownerCt.ownerCt.ownerCt;
                var id = tab.id;
                if(tabs.findById(id))
                {
                    tabs.remove(tabs.findById(id));
                }
            }
        }
    ]
}
