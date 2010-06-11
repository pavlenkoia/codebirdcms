{
    layout:'fit',
    width:600,
    height:500,
    plain: true,
    border: false,
    title: <?php echo escapeJSON('Редактирование')?>,
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
                    value: '<?php echo $page->id ?>'
                },
                {
                    xtype: 'textfield',
                    fieldLabel: 'Заголовок',
                    name: 'title',
                    anchor: '95%',
                    value: <?php echo escapeJSON($page->title) ?>
                },
                {
                    xtype: 'panel',
                    itemId: 'panel-content',
                    fieldLabel: 'Содержание',
                    height: 340,
                    width: '99%',
                    autoScroll: true,
                    bodyStyle: 'background-color: #fff; padding: 8px',
                    html: <?php echo escapeJSON($page->content) ?>,
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
                                            name: 'content',
                                            height : 350,
                                            width : 600,
                                            value : <?php echo escapeJSON($page->content) ?>,
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

                var ta = form.getForm().findField('content');
                if(ta)
                {
                    var api = FCKeditorAPI.GetInstance(ta.getId());
                    var val = api.GetHTML();
                    ta.setValue(val);
                }

                form.getForm().submit({
                    url: '/ajax/cm/catalog.editor.save_page',
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
