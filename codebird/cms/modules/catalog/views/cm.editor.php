{
    xtype: 'panel',
    id: 'panel-catalog-editor-<?php echo $section->id?>',
    frame: false,
    bodyBorder : false,
    title: <?php echo escapeJSON('Раздел: '.$section->title)?>,
    autoScroll: true,
    layout: 'fit',
    items:
    [
        {
            xtype: 'tabpanel',
            itemId: 'tabs',
            enableTabScroll:true,
            border: false,
            frame:true,
            activeTab: 0,
            defaults:
            {
                autoScroll:true
            },
            items:
            [
            <?php if($table_id) { ?>
                {
                    xtype: 'panel',
                    title: 'Позиции раздела',
                    id: 'panel-catalog-editor-<?php echo $section->id?>-tab-position',
                    bodyBorder : true,
                    autoScroll: true,
                    layout: 'fit',
                    tbar:
                    {
                        xtype: 'toolbar',
                        items:
                        [
                            {
                                text: 'Добавить',
                                iconCls: 'add-menu',
                                itemId: 'add'
                            },
                            {
                                text: 'Править',
                                iconCls: 'edit-menu',
                                disabled: true,
                                itemId: 'edit'
                            },
                            {
                                text: 'Удалить',
                                iconCls: 'delete-menu',
                                disabled: true,
                                itemId: 'delete',
                                handler: function(item)
                                {
                                    Ext.MessageBox.confirm('Подтверждение', 'Вы действительно хотите удалить выбранные строки?',
                                        function(btn)
                                        {
                                            if(btn == 'yes')
                                            {
                                                var s = Ext.getCmp('panel-catalog-editor-<?php echo $section->id?>-tab-position').getComponent('grid').getSelectionModel().getSelections();
                                                var id = "";
                                                for(var i = 0; i < s.length; i++)
                                                {
                                                    id += s[i].id+',';
                                                }
                                                Ext.Ajax.request(
                                                    {
                                                        url : '/ajax/cm/catalog.cm.delete_position',
                                                        method: 'POST',
                                                        params:
                                                        {
                                                            id: id,
                                                            _table_id_: '<?php echo $table_id?>',
                                                            section_id: <?php echo $section->id ?>
                                                        },
                                                        maskEl : 'panel-catalog-editor-<?php echo $section->id?>-tab-position',
                                                        loadingMessage : 'Удаление...',
                                                        success : function (response)
                                                        {
                                                            var obj = response.responseJSON;
                                                            if(obj.success)
                                                            {
                                                                var grid = Ext.getCmp('panel-catalog-editor-<?php echo $section->id?>-tab-position').getComponent('grid');
                                                                var paging = grid.getBottomToolbar();
                                                                grid.getStore().load({params:{start:paging.cursor, limit:paging.pageSize}});
                                                                grid.getSelectionModel().clearSelections();
                                                            }
                                                            else
                                                            {
                                                                Ext.MessageBox.alert('Ошибка', obj.msg);
                                                            }
                                                        }
                                                    });
                                            }
                                        });
                                }
                            }
                            <?php if($import) { ?>
                            ,{
                                text: 'Импорт',
                                iconCls: 'import-menu',
                                itemId: 'import',
                                handler: function(item)
                                {
                                    Ext.Ajax.request({
                                        url : '/ajax/cm/catalog.cm.import_form',
                                        method: 'POST',
                                        params:
                                        {
                                            section_id: <?php echo $section->id ?>,
                                            table_name: '<?=$table_name?>',
                                            table_id: '<?=$table_id?>'
                                        },
                                        maskEl : 'panel-catalog-editor-<?php echo $section->id?>',
                                        loadingMessage : 'Загрузка...',
                                        success : function (response) {
                                            var win = new Ext.Window(response.responseJSON);
                                            win.show(item.id);
                                        }
                                    });
                                }
                            }
                            <?php } ?>
                        ]
                    },
                    listeners:
                    {
                        render: function()
                        {
                            var edit = function(){
                                editor({title: 'Править', action: 'edit'});
                            };

                            var add = function(){
                                editor({title: 'Добавить', action: 'add'});
                            };

                            var tb = Ext.getCmp('panel-catalog-editor-<?php echo $section->id ?>-tab-position').getTopToolbar();
                            tb.getComponent('add').setHandler(add);

                            
                            <?php foreach($fields as $name=>$field)
                            {
                                if($field['type'] == 'select')
                                {
                            ?>
                                var store_<?php echo $field['field']?> = new Ext.data.Store({
                                    url: '/ajax/cm/catalog.cm.select',
                                    baseParams:
                                    {
                                        table_id: '<?php echo $table_id?>',
                                        field: '<?php echo $name?>'
                                    },
                                    //maskEl : this,
                                    //autoLoad: true,
                                    reader: new Ext.data.JsonReader
                                    ({
                                        totalProperty: 'results',
                                        autoDestroy: true,
                                        idProperty: 'value',
                                        root: 'rows',
                                        fields:
                                        [
                                            'id',
                                            'display'
                                        ]
                                    })
                                });
                                store_<?php echo $field['field']?>.load();
                            <?php
                                }
                            }
                            ?>


                            var editor = function(option){
                                var win = new Ext.Window({
                                        layout:'fit',
                                        width:580,
                                        height:420,
                                        closeAction:'close',
                                        plain: true,
                                        border: false,
                                        modal: false,
                                        title: option.title,
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
                                                    width: 350
                                                }
                                            }
                                        ],
                                        buttonAlign: 'center',
                                        buttons:
                                        [
                                            {
                                                text:'Сохранить',
                                                handler: function()
                                                {
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
                                                            var grid = Ext.getCmp('panel-catalog-editor-<?php echo $section->id?>-tab-position').getComponent('grid');
                                                            var paging = grid.getBottomToolbar();
                                                            grid.getStore().load({params:{start:paging.cursor, limit:paging.pageSize}});
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
                                                    win.close();
                                                }
                                            }
                                        ]
                                    });
                                    var form = win.getComponent('form');

                                    var grid = Ext.getCmp('panel-catalog-editor-<?php echo $section->id?>-tab-position').getComponent('grid');
                                    var sels = grid.getSelectionModel().getSelections();

                                    if((option.action == 'edit' && sels.length == 1) || option.action == 'add')
                                    {
                                        form.add([
                                        {
                                            xtype: 'hidden',
                                            name: 'id',
                                            value: option.action == 'edit' ? sels[0].id : 0
                                        },
                                        {
                                            xtype: 'hidden',
                                            name: '_table_id_',
                                            value: '<?php echo $table_id?>'

                                        },
                                        {
                                            xtype: 'hidden',
                                            name: 'section_id',
                                            value: <?php echo $section->id ?>

                                        }
                                        <?php
                                        foreach($fields as $name=>$field)
                                        {
                                            if(!$field['edit'])
                                            {
                                                continue;
                                            }
                                            switch ($field['type'])
                                            {
                                                case "text" : ?>
                                                    ,{
                                                        xtype: 'textfield',
                                                        fieldLabel: '<?php echo $field['title']?>',
                                                        name: '<?php echo $field['field']?>',
                                                        anchor: '95%',
                                                        value: option.action == 'edit' ? sels[0].get('<?php echo $field['field']?>') : ''
                                                    }
                                                    <?php break;
                                                case "int" :?>
                                                    ,{
                                                        xtype: 'numberfield',
                                                        fieldLabel: '<?php echo $field['title']?>',
                                                        name: '<?php echo $field['field']?>',
                                                        value: option.action == 'edit' ? sels[0].get('<?php echo $field['field']?>') : '',
                                                        width: 100
                                                    }
                                                    <?php break;
                                                case "dec" :?>
                                                    ,{
                                                        xtype: 'numberfield',
                                                        fieldLabel: '<?php echo $field['title']?>',
                                                        name: '<?php echo $field['field']?>',
                                                        value: option.action == 'edit' ? sels[0].get('<?php echo $field['field']?>') : '',
                                                        width: 100,
                                                        allowDecimals: true
                                                    }
                                                    <?php break;
                                                case "memo" :?>
                                                    ,{
                                                        xtype: 'textarea',
                                                        fieldLabel: '<?php echo $field['title']?>',
                                                        name: '<?php echo $field['field']?>',
                                                        value: option.action == 'edit' ? sels[0].get('<?php echo $field['field']?>') : '',
                                                        anchor: '95%',
                                                        height : <?php if(isset($field['editor_height'])) echo $field['editor_height']; else echo '60'; ?>
                                                    }
                                                    <?php break;
                                                case "date" :?>
                                                    ,{
                                                        xtype: 'datefield',
                                                        fieldLabel: '<?php echo $field['title']?>',
                                                        name: '<?php echo $field['field']?>',
                                                        width: 'auto',
                                                        value: option.action == 'edit' ? sels[0].get('<?php echo $field['field']?>') : '',
                                                        format:"d.m.Y"
                                                    }
                                                    <?php break;
                                                case "check" :?>
                                                    ,{
                                                        xtype: 'checkbox',
                                                        fieldLabel: '<?php echo $field['title']?>',
                                                        name: '<?php echo $field['field']?>',
                                                        inputValue: 1,
                                                        checked: option.action == 'edit' ? sels[0].get('<?php echo $field['field']?>') == 'да' ? true : false : ''
                                                    }
                                                    <?php break;
                                                case "select" :?>
                                                ,{
                                                    xtype: 'combo',
                                                    fieldLabel: '<?php echo $field['title']?>',
                                                    hiddenName: '<?php echo $field['field']?>',
                                                    anchor: '95%',
                                                    mode: 'local',
                                                    editable: false,
                                                    resizable: false,
                                                    valueField: 'id',
                                                    displayField: 'display',
                                                    triggerAction: 'all',
                                                    value: option.action == 'edit' ? sels[0].get('<?php echo $field['field']?>') : '',
                                                    store: store_<?php echo $field['field']?>
                                                }
                                                <?php break;
                                                case "selecttext" :?>
                                                ,{
                                                    xtype: 'combo',
                                                    fieldLabel: '<?php echo $field['title']?>',
                                                    hiddenName: '<?php echo $field['field']?>',
                                                    anchor: '95%',
                                                    mode: 'local',
                                                    editable: true,
                                                    resizable: false,
                                                    valueField: 'id',
                                                    displayField: 'display',
                                                    triggerAction: 'all',
                                                    value: option.action == 'edit' ? sels[0].get('<?php echo $field['field']?>') : '',
                                                    store: new Ext.data.ArrayStore({
                                                            id: 0,
                                                            fields:
                                                            [
                                                                'id',
                                                                'display'
                                                            ],
                                                            data: 
                                                            [
                                                            <?php
                                                                if(isset($field['sql']))
                                                                {
                                                                    $rows = $data->getSectionTable()->select($field['sql']);
                                                                    $res = '';
                                                                    foreach($rows as $row)
                                                                    {
                                                                        if($res) $res .= ',';
                                                                        $res .= '['.escapeJSON($row['display']).','.escapeJSON($row['display']).']';
                                                                    }
                                                                    echo $res;
                                                                }
                                                                else
                                                                {
                                                                    $rows = explode(';', $field['select']);
                                                                    $res = '';
                                                                    foreach($rows as $row)
                                                                    {
                                                                        if($res) $res .= ',';
                                                                        $res .= '['.escapeJSON($row).','.escapeJSON($row).']';
                                                                    }
                                                                    echo $res;
                                                                }
                                                            ?>
                                                                
                                                            ]
                                                        })
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
                                                                        var src = option.action == 'edit' ? sels[0].get('<?php echo $field['field']?>') : '';
                                                                        var t = new Ext.Template('<div style="margin:5px 0px 10px 0px;">{src}</div>');
                                                                        t.compile();
                                                                        t.append(this.id, {src:src});
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
                                                                            App.uploadWindow({
                                                                                targetId: this.id,
                                                                                id: '<?php echo $section->id?>/position/<?php echo $name?>/'+form.getForm().findField('id').getValue(),
                                                                                url: '/ajax/cm/catalog.cm.uploadimage',
                                                                                success: function(result)
                                                                                {
                                                                                    var id_f = form.getForm().findField('id');
                                                                                    id_f.setValue(result.id);
                                                                                    var t = new Ext.Template('<img src="{src}" style="margin:5px 0px 10px 0px;"/>');
                                                                                    t.compile();
                                                                                    t.overwrite(btn.ownerCt.ownerCt.getComponent(0).id, {src: result.src+'?sid=' + Math.random()});
                                                                                }
                                                                            });
                                                                        }
                                                                    },
                                                                    {
                                                                        xtype: 'button',
                                                                        text: 'Удалить',
                                                                        handler: function(btn)
                                                                        {
                                                                            Ext.MessageBox.confirm('Подтверждение', 'Вы действительно хотите удалить картинку?',
                                                                                function(btn2)
                                                                                {
                                                                                    if(btn2 == 'yes')
                                                                                    {
                                                                                        Ext.Ajax.request({
                                                                                            url : '/ajax/cm/catalog.cm.uploadfile_delete',
                                                                                            method: 'POST',
                                                                                            params:
                                                                                            {
                                                                                                id: '<?php echo $section->id ?>/position/<?php echo $name?>/'+form.getForm().findField('id').getValue()
                                                                                            },
                                                                                            maskEl : 'panel-catalog-editor-<?php echo $section->id?>',
                                                                                            loadingMessage : 'Удаление...',
                                                                                            success : function (response) {
                                                                                                var t = new Ext.Template('<div style="margin:5px 0px 10px 0px;"></div>');
                                                                                                t.compile();
                                                                                                t.overwrite(btn.ownerCt.ownerCt.getComponent(0).id);
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
                                                                        var src = option.action == 'edit' ? sels[0].get('<?php echo $field['field']?>') : '';
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
                                                                            App.uploadWindow({
                                                                                targetId: this.id,
                                                                                id: '<?php echo $section->id ?>/position/<?php echo $name?>/'+form.getForm().findField('id').getValue(),
                                                                                url: '/ajax/cm/catalog.cm.uploadfile',
                                                                                title: 'Загрузить файл',
                                                                                emptyText: 'Выберите файл...',
                                                                                success: function(result)
                                                                                {
                                                                                    var id_f = form.getForm().findField('id');
                                                                                    id_f.setValue(result.id);
                                                                                    var t = new Ext.Template('<div style="margin:5px 0px 10px 0px;"><a href="{src}" target="_blank"/>{name}</a></div>');
                                                                                    t.compile();
                                                                                    t.overwrite(btn.ownerCt.getComponent(0).id, {src: result.src+'?sid=' + Math.random(), name: result.src});
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
                                                                                                id: '<?php echo $section->id ?>/position/<?php echo $name?>/'+form.getForm().findField('id').getValue()
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
                                                        //width: '99%',
                                                        anchor: '96%',
                                                        autoScroll: true,
                                                        bodyStyle: 'background-color: #fff; padding: 8px',
                                                        html: option.action == 'edit' ? sels[0].get('<?php echo $field['field'] ?>') : '',
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
                                                                                value : option.action == 'edit' ? sels[0].get('<?php echo $field['field'] ?>') : '',
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

                                        ]);
                                        form.doLayout();
                                    }

                                    win.show(this.id);
                            };



                            var page_size = <?php echo $page_size?>;

                            var ds = new Ext.data.Store
                            ({
                                url: '/ajax/cm/catalog.cm.position_records',
                                baseParams:
                                {
                                    name: '<?php echo $table_id?>',
                                    section_id: <?php echo $section->id ?>
                                },
                                maskEl : this,
                                reader: new Ext.data.JsonReader
                                ({
                                    totalProperty: 'results',
                                    autoDestroy: true,
                                    idProperty: 'id',
                                    root: 'rows',
                                    fields:
                                    [
                                        'id'
                                        <?php
                                        foreach($fields as $field)
                                        {
                                            echo ",'".$field['field']."'";
                                            if(isset($field['display']))
                                            {
                                                echo ",'".$field['display']."'";
                                            }
                                        }
                                        ?>

                                    ]
                                })
                            });

                            var paging = new Ext.PagingToolbar
                            ({
                                store: ds,
                                pageSize: page_size,
                                displayInfo: false,
                                id: 'panel-catalog-editor-<?php echo $table_id?>-grid-paging'
                            });

                            var sm = new Ext.grid.CheckboxSelectionModel({
                                listeners:
                                {
                                    selectionchange: function(sm){
                                        var tb = Ext.getCmp('panel-catalog-editor-<?php echo $section->id?>-tab-position').getTopToolbar();
                                        var dis = (sm.getCount() == 0);
                                        tb.getComponent('edit').setDisabled(dis);
                                        tb.getComponent('edit').setHandler(edit);
                                        tb.getComponent('delete').setDisabled(dis);
                                    }
                                }
                            });

                            var grid = new Ext.grid.GridPanel
                            ({
                                itemId: 'grid',
                                frame: true,
                                store: ds,
                                //stateId: 'panel-catalog-editor-<?php echo $section->id ?>-grid-position',
                                //stateful: true,
                                //stateEvents: ['columnmove', 'columnresize', 'sortchange'],
                                colModel: new Ext.grid.ColumnModel
                                ({
                                    defaults:
                                    {
                                        width: 120,
                                        sortable: true
                                    },
                                    columns:
                                    [
                                        sm
                                        <?php
                                        foreach($fields as $field)
                                        {
                                            if(!$field['browse'])
                                            {
                                                continue;
                                            }
                                            if(isset($field['display']))
                                            {
                                                echo ",{header: '".$field['title']."', dataIndex: '".$field['display']."'}";
                                            }
                                            else
                                            {
                                                echo ",{header: '".$field['title']."', dataIndex: '".$field['field']."'}";
                                            }
                                        }
                                        ?>

                                    ]
                                }),
                                sm: sm,
                                viewConfig:
                                {
                                    forceFit: true
                                },
                                bbar: paging,
                                listeners:
                                {
                                    dblclick: edit
                                }
                            });

                            this.add(grid);

                            grid.getStore().load({params:{start:0, limit:page_size}});
                        }
                    }
                },
                <?php } ?>
                {
                    xtype: 'panel',
                    title: 'Данные раздела',
                    id: 'panel-catalog-editor-<?php echo $section->id?>-tab-section',
                    bodyBorder : true,
                    autoScroll: true,
                    layout: 'fit',
                    tbar:
                    {
                        xtype: 'toolbar',
                        items:
                        [
                            {
                                text:'Конфигурация',
                                iconCls: 'config-menu',
                                itemId: 'settings',
                                handler: function(item)
                                {
                                    Ext.Ajax.request({
                                        url : '/ajax/cm/catalog.cm.edit_section_form',
                                        method: 'POST',
                                        params:
                                        {
                                            id: <?php echo $section->id ?>,
                                        },
                                        maskEl : 'panel-catalog-editor-<?php echo $section->id?>',
                                        loadingMessage : 'Загрузка...',
                                        success : function (response) {
                                            var win = new Ext.Window(response.responseJSON);
                                            win.show(item.id);
                                        }
                                    });
                                }
                            }
                        ]
                    },
                    items:
                    [
                    <?php //if(isset($fields_section)) { ?>
                        {
                            xtype: 'form',
                            id: 'panel-catalog-editor-<?php echo $section->id?>-tab-section-form',
                            frame: true,
                            bodyBorder : true,
                            autoScroll: true,
                            defaults:
                            {
                                width: 400
                            },
                            items:
                            [
                                {
                                    xtype: 'hidden',
                                    name: 'section_id',
                                    value: <?php echo $section->id ?>
                                }
                                ,{
                                    xtype: 'textfield',
                                    fieldLabel: 'Название раздела',
                                    name: '_section_title_',
                                    anchor: '95%',
                                    value: <?php echo escapeJSON($section->title) ?>
                                }
                                <?php    if(isset($fields_section)) {
                                foreach($fields_section as $name=>$field)
                                {
                                    if(!$field['edit'])
                                    {
                                        continue;
                                    }
                                    switch ($field['type'])
                                    {
                                        case "text" : ?>
                                            ,{
                                                xtype: 'textfield',
                                                fieldLabel: '<?php echo $field['title']?>',
                                                name: '<?php echo $field['field']?>',
                                                anchor: '95%',
                                                value: <?php echo escapeJSON($section_data->$field['field']) ?>
                                            }
                                            <?php break;
                                        case "int" :?>
                                            ,{
                                                xtype: 'numberfield',
                                                fieldLabel: '<?php echo $field['title']?>',
                                                name: '<?php echo $field['field']?>',
                                                value: <?php echo escapeJSON($section_data->$field['field']) ?>,
                                                width: 100
                                            }
                                            <?php break;
                                        case "memo" :?>
                                            ,{
                                                xtype: 'textarea',
                                                fieldLabel: '<?php echo $field['title']?>',
                                                name: '<?php echo $field['field']?>',
                                                value: <?php echo escapeJSON($section_data->$field['field']) ?>,
                                                anchor: '95%',
                                                height : <?php if(isset($field['editor_height'])) echo $field['editor_height']; else echo '60'; ?>
                                            }
                                            <?php break;
                                        case 'check' : ?>
                                            ,{
                                                xtype: 'checkbox',
                                                fieldLabel: '<?php echo $field['title']?>',
                                                //boxLabel: 'да',
                                                name: '<?php echo $field['field']?>',
                                                inputValue: 1,
                                                checked: <?php if($section_data->$field['field'] == 1) echo 'true'; else echo 'false'?>
                                            }
                                            <?php break;
                                        case 'date' : ?>
                                            ,{
                                                xtype: 'datefield',
                                                fieldLabel: '<?php echo $field['title']?>',
                                                name: '<?php echo $field['field']?>',
                                                width: 'auto',
                                                value: '<?php echo date("d.m.Y",$section_data->$field['field']); ?>',
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
                                                                var src = '<?php echo get_cache_pic($section_data->$field['field'],75,75)?>';
                                                                var t = new Ext.Template('<img src="{src}" style="margin:5px 0px 10px 0px;"/>');
                                                                t.compile();
                                                                t.append(this.id, {src:src+'?sid=' + Math.random()});
                                                            }
                                                        }
                                                    },
                                                    {
                                                        xtype: 'button',
                                                        text: 'Загрузить',
                                                        handler: function(btn)
                                                        {
                                                            App.uploadWindow({
                                                                targetId: this.id,
                                                                id: '<?php echo $section->id ?>/section/<?php echo $name?>/<?php echo $section_data->id ?>',
                                                                url: '/ajax/cm/catalog.cm.uploadimage',
                                                                success: function(result)
                                                                {
                                                                    var t = new Ext.Template('<img src="{src}" style="margin:5px 0px 10px 0px;"/>');
                                                                    t.compile();
                                                                    t.overwrite(btn.ownerCt.getComponent(0).id, {src: result.src+'?sid=' + Math.random()});
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
                                                                var src = '<?php echo $section_data->$field['field']?>';
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
                                                                    App.uploadWindow({
                                                                        targetId: this.id,
                                                                        id: '<?php echo $section->id ?>/section/<?php echo $name?>/<?php echo $section->id ?>',
                                                                        url: '/ajax/cm/catalog.cm.uploadfile',
                                                                        title: 'Загрузить файл',
                                                                        emptyText: 'Выберите файл...',
                                                                        success: function(result)
                                                                        {
                                                                            var t = new Ext.Template('<div style="margin:5px 0px 10px 0px;"><a href="{src}" target="_blank"/>{name}</a></div>');
                                                                            t.compile();
                                                                            t.overwrite(btn.ownerCt.ownerCt.getComponent(0).id, {src: result.src, name: result.src});
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
                                                                                        id: '<?php echo $section->id ?>/section/<?php echo $name?>/<?php echo $section->id ?>'
                                                                                    },
                                                                                    maskEl : 'panel-catalog-editor-<?php echo $section->id?>',
                                                                                    loadingMessage : 'Удаление...',
                                                                                    success : function (response) {
                                                                                        var t = new Ext.Template('<div style="margin:5px 0px 10px 0px;"></div>');
                                                                                        t.compile();
                                                                                        t.overwrite(btn.ownerCt.ownerCt.getComponent(0).id);
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
                                        case "selecttext" :?>
                                            ,{
                                                xtype: 'combo',
                                                fieldLabel: '<?php echo $field['title']?>',
                                                hiddenName: '<?php echo $field['field']?>',
                                                //anchor: '95%',
                                                mode: 'local',
                                                editable: true,
                                                resizable: false,
                                                valueField: 'id',
                                                displayField: 'display',
                                                triggerAction: 'all',
                                                value: <?php echo escapeJSON($section_data->$field['field']) ?>,
                                                store: new Ext.data.ArrayStore({
                                                        id: 0,
                                                        fields:
                                                        [
                                                            'id',
                                                            'display'
                                                        ],
                                                        data:
                                                        [
                                                        <?php
                                                            if(isset($field['sql']))
                                                            {
                                                                $table = new Table('catalog_section');
                                                                $rows = $table->select($field['sql']);
                                                                $res = '';
                                                                foreach($rows as $row)
                                                                {
                                                                    if($res) $res .= ',';
                                                                    $res .= '['.escapeJSON($row['display']).','.escapeJSON($row['display']).']';
                                                                }
                                                                echo $res;
                                                            }
                                                            else
                                                            {
                                                                $rows = explode(';', $field['select']);
                                                                $res = '';
                                                                foreach($rows as $row)
                                                                {
                                                                    if($res) $res .= ',';
                                                                    $res .= '['.escapeJSON($row).','.escapeJSON($row).']';
                                                                }
                                                                echo $res;
                                                            }
                                                        ?>

                                                        ]
                                                    })
                                            }
                                            <?php break;
                                        case "richtext" :?>
                                            ,{
                                                xtype: 'panel',
                                                itemId: 'panel-content-<?php echo $name?>',
                                                fieldLabel: '<?php echo $field['title']?>',
                                                height: 180,
                                                width: '99%',
                                                autoScroll: true,
                                                bodyStyle: 'background-color: #fff; padding: 8px',
                                                html: <?php echo escapeJSON($section_data->$field['field']) ?>,
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
                                                                        value : <?php echo escapeJSON($section_data->$field['field']) ?>,
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
                            <?php } ?>
                            ],
                            labelAlign: 'top',
                            buttonAlign: 'center',
                            buttons:
                            [
                                {
                                    text: 'Сохранить',
                                    formBind: true,
                                    handler: function()
                                    {
                                        {
                                            var form = Ext.getCmp('panel-catalog-editor-<?php echo $section->id?>-tab-section-form');

                                            <?php if(isset($fields_section)) {
                                            foreach($fields_section as $field)
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
                                            } } ?>

                                            form.getForm().submit({
                                                url: '/ajax/cm/catalog.cm.save_table_section',
                                                method: 'POST',
                                                waitTitle: 'Подождите',
                                                waitMsg: 'Сохранение...',
                                                success: function(form, action){

                                                },
                                                failure: function(form, action){
                                                    Ext.MessageBox.alert('Ошибка', action.result.msg);
                                                }
                                            });

                                        }
                                    }
                                }
                            ]
                        }
                    <?php //} ?>
                    ]
                }
            ]
        }
    ],
    buttonAlign: 'center',
    buttons:
    [
        {
            text: 'Закрыть',
            formBind: true,
            handler: function()
            {
                App.closeEditor({id : this.ownerCt.ownerCt.ownerCt.id});
            }
        }
    ]
}