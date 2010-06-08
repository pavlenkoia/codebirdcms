{
    layout:'fit',
    width:580,
    height:420,
    plain: true,
    border: false,
    title: <?php if($position) echo escapeJSON('Редактирование'); else echo escapeJSON('Добавить');?>,
    items:
    [
        {
            xtype: 'form',
            itemId: 'form',
            frame: true,
            autoScroll: true,
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
                    value: '<?php echo $position_id ?>'
                },
                {
                    xtype: 'hidden',
                    name: '_table_id_',
                    value: '<?php echo $table_id?>'

                },
                {
                    xtype: 'hidden',
                    name: 'section_id',
                    value: <?php echo $section_id ?>
                }
                <?php
                foreach($fields as $name=>$field)
                {
                    if(!$field['edit'])
                    {
                        continue;
                    }
                    $value = $position ? $position->$field['field'] : '';
                    switch ($field['type'])
                    {
                        case "text" : ?>
                            ,{
                                xtype: 'textfield',
                                fieldLabel: '<?php echo $field['title']?>',
                                name: '<?php echo $field['field']?>',
                                anchor: '95%',
                                value: <?php echo escapeJSON($value) ?>
                            }
                            <?php break;
                        case "int" :?>
                            ,{
                                xtype: 'numberfield',
                                fieldLabel: '<?php echo $field['title']?>',
                                name: '<?php echo $field['field']?>',
                                value: <?php echo escapeJSON($value) ?>,
                                width: 100
                            }
                            <?php break;
                        case "memo" :?>
                            ,{
                                xtype: 'textarea',
                                fieldLabel: '<?php echo $field['title']?>',
                                name: '<?php echo $field['field']?>',
                                value: <?php echo escapeJSON($value) ?>,
                                anchor: '95%',
                                height : 60
                            }
                            <?php break;
                        case "date" :?>
                            ,{
                                xtype: 'datefield',
                                fieldLabel: '<?php echo $field['title']?>',
                                name: '<?php echo $field['field']?>',
                                width: 'auto',
                                value: '<?php echo $value ? date("d.m.Y",$value) : '';?>',
                                format:"d.m.Y"
                            }
                            <?php break;
                        case "image" :?>
                            ,{
                                xtype: 'panel',
                                fieldLabel: '<?php echo $field['title']?>',
                                items:
                                [
                                    {
                                        xtype: 'box',
                                        listeners:
                                        {   render: function()
                                            {
                                                var src = '<img src="<?php echo get_cache_pic($value,75,75) ?>"/>';
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
                                                id: '<?php echo $section_id?>/position/<?php echo $name?>/<?php echo $position_id ?>',
                                                url: '/ajax/cm/catalog.cm.uploadimage',
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
                            <?php break;
                        case "file" :?>
                            ,{
                                fieldLabel: '<?php echo $field['title']?>',
                                xtype: 'panel',
                                items:
                                [
                                    {
                                        xtype: 'box',
                                        listeners:
                                        {   render: function()
                                            {
                                                var src =  <?php echo escapeJSON($value) ?>;
                                                if(src != '')
                                                {
                                                    var t = new Ext.Template('<div style="margin:5px 0px 10px 0px;"><a href="{src}" target="_blank">{src}</a></div>');
                                                    t.compile();
                                                    t.append(this.id, {src:src});
                                                }
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'panel',
                                        layout:'hbox',
                                        items:
                                        [
                                            {
                                                xtype: 'button',
                                                text: 'Загрузить',
                                                handler: function(btn)
                                                {
                                                    var form = btn.ownerCt.ownerCt.ownerCt;
                                                    Editor.uploadWindow({
                                                        targetId: this.id,
                                                        id: '<?php echo $section_id ?>/position/<?php echo $name?>/<?php echo $position_id ?>',
                                                        url: '/ajax/cm/catalog.cm.uploadfile',
                                                        title: 'Загрузить файл',
                                                        emptyText: 'Выберите файл...',
                                                        success: function(result)
                                                        {
                                                            var id_f = form.getForm().findField('id');
                                                            id_f.setValue(result.id);
                                                            var t = new Ext.Template('<div style="margin:5px 0px 10px 0px;"><a href="{src}" target="_blank"/>{name}</a></div>');
                                                            t.compile();
                                                            t.overwrite(btn.ownerCt.getComponent(0).id, {src: result.src, name: result.src});
                                                        }
                                                    });
                                                }
                                            },
                                            {
                                                xtype: 'button',
                                                text: 'Удалить',
                                                handler: function(btn)
                                                {
                                                    Ext.MessageBox.confirm('Подтверждение', 'Вы действительно хотите удалить файл?',
                                                        function(btn2)
                                                        {
                                                            if(btn2 == 'yes')
                                                            {
                                                                Ext.Ajax.request({
                                                                    url : '/ajax/cm/catalog.cm.uploadfile_delete',
                                                                    method: 'POST',
                                                                    params:
                                                                    {
                                                                        id: '<?php echo $section_id ?>/position/<?php echo $name?>/<?php echo $position_id ?>'
                                                                    },
                                                                    success : function (response) {
                                                                        var obj = response.responseJSON;
                                                                        if(obj.success)
                                                                        {
                                                                            var t = new Ext.Template('<div style="margin:5px 0px 10px 0px;"></div>');
                                                                            t.compile();
                                                                            t.overwrite(btn.ownerCt.ownerCt.getComponent(0).id);
                                                                        }
                                                                        else
                                                                        {
                                                                            Ext.MessageBox.alert('Ошибка', obj.msg);
                                                                        }
                                                                    },
                                                                    failure: function(form, action){
                                                                        Ext.MessageBox.alert('Ошибка', action.result.msg);
                                                                    }
                                                                });
                                                            }
                                                    });
                                                }
                                            }
                                        ]
                                    }
                                ]
                            }
                            <?php break;
                        case "richtext" :?>
                            ,{
                                xtype: 'panel',
                                itemId: 'panel-content',
                                fieldLabel: '<?php echo $field['title']?>',
                                height: 180,
                                width: '99%',
                                autoScroll: true,
                                bodyStyle: 'background-color: #fff; padding: 8px',
                                html: <?php echo escapeJSON($value) ?>,
                                tbar:
                                {
                                    xtype: 'toolbar',
                                    items:
                                    [
                                        {
                                            text:'Правка',
                                            iconCls: 'edit-menu',
                                            handler: function(btn){
                                                    var textarea =
                                                    {
                                                        xtype: 'textarea',
                                                        fieldLabel: 'Текст',
                                                        name: '<?php echo $field['field']?>',
                                                        height : 350,
                                                        width : 600,
                                                        value : <?php echo escapeJSON($value) ?>,
                                                        listeners:
                                                        {
                                                            render : function(element)
                                                                {
                                                                    var oFCKeditorOptions =
                                                                    {
                                                                        BasePath : 'jscripts/fckeditor/' ,
                                                                        Config : {
                                                                            BaseHref : window.location ,
                                                                            SkinPath : '../editor/skins/office2003/' ,
                                                                            ProcessHTMLEntities : true ,
                                                                            ProcessNumericEntities : false
                                                                        },
                                                                        ToolbarSet : 'Default'
                                                                    };
                                                                    oFCKeditor = new FCKeditor(element.id);
                                                                    oFCKeditor.BasePath      = oFCKeditorOptions.BasePath;
                                                                    oFCKeditor.ToolbarSet    = oFCKeditorOptions.ToolbarSet;
                                                                    oFCKeditor.Config        = oFCKeditorOptions.Config;
                                                                    oFCKeditor.Height          = element.height;
                                                                    oFCKeditor.ReplaceTextarea();
                                                                }
                                                         }
                                                    };
                                                    var panel = this.ownerCt.ownerCt;
                                                    panel.setHeight('auto');
                                                    panel.add(textarea);
                                                    panel.body.update('');
                                                    panel.body.setStyle('background-color','');
                                                    panel.getTopToolbar().hide();
                                                    panel.doLayout();
                                                }
                                        }
                                    ]
                                }
                            }
                            <?php break;
                    }
                }
                ?>
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
                var win = this.ownerCt.ownerCt;
                win.hide();
                var form = win.getComponent('form');

                <?php
                foreach($fields as $field)
                {
                    if(!$field['edit'])
                    {
                        continue;
                    }
                    switch ($field['type'])
                    {
                        case "richtext" :?>

                var ta_<?php echo $field['field']?> = form.getForm().findField('<?php echo $field['field']?>');
                if(ta_<?php echo $field['field']?>)
                {
                    var api_<?php echo $field['field']?> = FCKeditorAPI.GetInstance(ta_<?php echo $field['field']?>.getId());
                    var val_<?php echo $field['field']?> = api_<?php echo $field['field']?>.GetHTML();
                    ta_<?php echo $field['field']?>.setValue(val_<?php echo $field['field']?>);
                }

                <?php
                    break;
                    }
                } ?>

                form.getForm().submit({
                    url: '/ajax/cm/catalog.cm.save_position',
                    method: 'POST',
                    waitTitle: 'Подождите',
                    waitMsg: 'Сохранение...',
                    success: function(form, action){
                        window.location = window.location;
                        win.close();
                    },
                    failure: function(form, action){
                        Ext.MessageBox.alert('Ошибка', action.result.msg);
                        win.close();
                    }
                });
            }
        },
        {
            text: 'Отмена',
            handler: function()
            {
                this.ownerCt.ownerCt.close();
            }
        }
    ]
}
