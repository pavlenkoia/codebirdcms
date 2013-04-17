{
    xtype: 'form',
    id: 'infoblock-form-<?php echo $infoblock->id?>',
    frame: true,
    bodyBorder : true,
    title: 'Инфблок',
    autoScroll: true,
    defaults:
    {
        width: 400
    },
    items:
    [
        {
            xtype: 'hidden',
            name: 'id',
            value: <?php echo $infoblock->id ?>
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Название',
            name: 'name',
            allowBlank: false,
            value: <?php echo escapeJSON($infoblock->name)?>
        },
        {
            xtype: 'textfield',
            fieldLabel: 'Символьный идентификатор',
            width: 180,
            name: 'alias',
            value: <?php echo escapeJSON($infoblock->alias)?>
        },


        {
            xtype: 'panel',
            itemId: 'panel-content',
            fieldLabel: 'Содержание',
            height: 300,
            width: '99%',
            autoScroll: true,
            bodyStyle: 'background-color: #fff; padding: 8px',
            //html: <?php echo escapeJSON($infoblock->html)?>,
            listeners:{
                render : function(element){
                    var textarea =
                    {
                        xtype: 'textarea',
                        fieldLabel: 'Текст',
                        id: 'infoblock-form-textarea-<?php echo $infoblock->id?>',
                        name: 'html',
                        height : 300,
                        width : '98%',
                        value : <?php echo escapeJSON($infoblock->html)?>,
                    };
                    var panel = Ext.getCmp('infoblock-form-<?php echo $infoblock->id?>').getComponent('panel-content');
                    panel.setHeight('auto');
                    panel.add(textarea);
                    panel.body.update('');
                    panel.body.setStyle('background-color','');
                    //panel.getTopToolbar().hide();
                    panel.doLayout();
                }
            },
            tbar:
            {
                xtype: 'toolbar',
                items:
                [
                    {
                        text:'Править в визуальном редакторе',
                        iconCls: 'edit-menu',
                        handler: function(){
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
                            oFCKeditor = new FCKeditor('infoblock-form-textarea-<?php echo $infoblock->id?>');
                            oFCKeditor.BasePath      = oFCKeditorOptions.BasePath;
                            oFCKeditor.ToolbarSet    = oFCKeditorOptions.ToolbarSet;
                            oFCKeditor.Config        = oFCKeditorOptions.Config;
                            oFCKeditor.Height          = 350;
                            oFCKeditor.ReplaceTextarea();
                            var panel = Ext.getCmp('infoblock-form-<?php echo $infoblock->id?>').getComponent('panel-content');
                            panel.getTopToolbar().hide();
                        }
                    }
                ]
            }
        }

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

                var form = this.ownerCt.ownerCt;

                var ta = form.getForm().findField('html');
                if(ta && FCKeditorAPI)
                {
                    var api = FCKeditorAPI.GetInstance(ta.getId());
                    if(api){
                        var val = api.GetHTML();
                        ta.setValue(val);
                    }
                }

                form.getForm().submit({
                        url: '/ajax/cm/infoblock.cm.save',
                        method: 'POST',
                        waitTitle: 'Подождите',
                        waitMsg: 'Сохранение...',
                        success: function(form, action){
                                App.msg('Готово', action.result.msg);
                            },
                        failure: function(form, action){
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
                App.closeEditor({id : this.ownerCt.ownerCt.ownerCt.id});
            }
        }
    ]
}

