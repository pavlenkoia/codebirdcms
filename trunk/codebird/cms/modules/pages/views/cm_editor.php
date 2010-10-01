{
    xtype: 'form',
    id: 'pages-form-<?php echo $page->id?>',
    frame: true,
    bodyBorder : true,
    title: <?php echo escapeJSON($page->title)?>,
    autoScroll: true,
    defaults:
    {
        width: 400
    },
    labelAlign: 'top',
    items:
    [
        {
            xtype: 'hidden',
            name: 'id',
            value: <?php echo $page->id ?>
        },
        {
            layout: 'column',
            width: '100%',
            items:
            [
                {
                    layout: 'form',
                    columnWidth: .5,
                    items:
                    [
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Заголовок',
                            name: 'title',
                            anchor:'95%',
                            value: <?php echo escapeJSON($page->title)?>
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Заголовок 2',
                            name: 'title2',
                            anchor:'95%',
                            value: <?php echo escapeJSON($page->title2)?>
                        },
                        {
                            xtype: 'textfield',
                            fieldLabel: 'Псевдоним (ссылка)',
                            name: 'alias',
                            anchor:'95%',
                            value: <?php echo escapeJSON($page->alias)?>
                        }
                    ]
                },
                {
                    layout: 'form',
                    columnWidth: .5,
                    items:
                    [
                        {
                            xtype: 'hidden',
                            name: 'template',
                            value: <?php echo escapeJSON($page->template)?>
                        },
                        {
                            xtype: 'combo',
                            anchor:'95%',
                            fieldLabel: 'Шаблон',
                            hiddenName: 'template',
                            value: <?php echo escapeJSON($page->template)?>,
                            mode: 'local',
                            editable: false,
                            resizable: false,
                            valueField: 'value',
                            displayField: 'display',
                            triggerAction: 'all',
                            store:
                            {
                                xtype: 'arraystore',
                                fields: ['value','display'],
                                data:
                                [
                                    <?php
                                    $array = array();
                                    foreach(Config::__("pages")->templates as $template=>$label)
                                    {
                                        array_push($array,"['".$template."','".$label."']");
//                                        echo "['".$template."','".$label."'],";
                                    }
                                    echo implode(",", $array);
                                    ?>
                                ]
                            }
                        },
                        {
                            xtype: 'checkbox',
                            hideLabel: true,
                            name: 'visible',
                            inputValue: 1,
                            boxLabel: 'видимый в карте сайта',
                            checked: <?php if($page->visible == 1) echo 'true'; else echo 'false'; ?>
                        },
                        {
                            xtype: 'checkbox',
                            hideLabel: true,
                            name: 'mainpage',
                            inputValue: 1,
                            boxLabel: 'главная страница',
                            disabled: <?php if($page->mainpage == 1) echo 'true'; else echo 'false'; ?>,
                            checked: <?php if($page->mainpage == 1) echo 'true'; else echo 'false'; ?>
                        }
                    ]
                }
            ]
        },
        {
            xtype: 'textarea',
            fieldLabel: 'Анонс',
            name: 'announcement',
            width: '98%',
            height: 60,
            value: <?php echo escapeJSON($page->announcement)?>
        },
        {
            xtype: 'panel',
            itemId: 'panel-content',
            fieldLabel: 'Содержание',
            height: 200,
            width: '99%',
            autoScroll: true,
            bodyStyle: 'background-color: #fff; padding: 8px',
            html: <?php echo escapeJSON($page->content)?>,
            tbar:
            {
                xtype: 'toolbar',
                items:
                [
                    {
                        text:'Правка',
                        iconCls: 'edit-menu',
                        handler: function(){
                                var textarea =
                                {
                                    xtype: 'textarea',
                                    fieldLabel: 'Текст',
                                    id: 'pages-form-textarea-<?php echo $page->id?>',
                                    name: 'content',
                                    height : 400,
                                    width : 600,
                                    value : <?php echo escapeJSON($page->content)?>,
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
                                var panel = Ext.getCmp('pages-form-<?php echo $page->id?>').getComponent('panel-content');
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
        },
        {
            xtype: 'textfield',
            fieldLabel: 'title',
            name: 'head_title',
            anchor:'95%',
            value: <?php echo escapeJSON($page->head_title)?>
        },
        {
            xtype: 'textfield',
            fieldLabel: 'meta keywords',
            name: 'meta_keywords',
            anchor:'95%',
            value: <?php echo escapeJSON($page->meta_keywords)?>
        },
        {
            xtype: 'textfield',
            fieldLabel: 'meta description',
            name: 'meta_description',
            anchor:'95%',
            value: <?php echo escapeJSON($page->meta_description)?>
        }        
        <?php
        if(isset($plugins))
        {
            foreach($plugins as $plugin)
            {
                if(!$plugin['plug'] || $plugin['plug'] == "false") continue;

                if(isset($plugin['templates']))
                {
                    $templates = explode(",", $plugin['templates']);
                    if(!in_array($page->template, $templates)) continue;
                }

                $plugin_value = "";

                if(isset($page->plugins))
                {
                    $plugins = explode(";",$page->plugins);
                    foreach($plugins as $plug)
                    {
                        $plugs = explode(":",$plug);
                        if($plugs[0] == $plugin['name'])
                        {
                            $plugin_value = $plug;
                            break;
                        }
                    }
                }
                $view = isset($plugin['view']) ? '&view='.$plugin['view'] : '';
                echo ','.val($plugin['mod'],'name='.$plugin['name'].'&value='.$plugin_value.'&label='.$plugin['label'].$view);
            }
        }
        ?>
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
                var ta = form.getForm().findField('content');
                if(ta)
                {
                    var api = FCKeditorAPI.GetInstance(ta.getId());
                    var val = api.GetHTML();
                    ta.setValue(val);
                }
                form.getForm().submit({
                    url: '/ajax/cm/pages.cm.save',
                    method: 'POST',
                    waitTitle: 'Подождите',
                    waitMsg: 'Сохранение...',
                    success: function(form, action){
                            App.msg('Готово','Страница сохранена');
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

