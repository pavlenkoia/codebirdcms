/**
 * Приложение
 */

var App  = function()
{
    return  {
        init : function()
        {
            Ext.QuickTips.init();
            // initialize state manager, we will use cookies
            Ext.state.Manager.setProvider(new Ext.state.CookieProvider());


            var loading = Ext.get('loading');
            var mask = Ext.get('loading-mask');
            mask.setOpacity(.8);
            mask.shift({
                xy:loading.getXY(),
                width:loading.getWidth(),
                height:loading.getHeight(),
                remove:true,
                duration:1,
                opacity:.3,
                callback : function()
                {
                    loading.fadeOut({
                        duration:.2,
                        remove:true
                    });

                    var firstForm = {
                                xtype: 'form',
                                itemId: 'form',
                                frame: true,
                                items:
                                [
                                    {
                                        xtype: 'hidden',
                                        name: 'step',
                                        value: 0
                                    },
                                    {
                                        xtype: 'displayfield',
                                        hideLabel: true,
                                        value: 'Добро пожаловать в программу установки CMS',
                                        style: 'margin-bottom: 18px'
                                    },
                                    {
                                        xtype: 'displayfield',
                                        hideLabel: true,
                                        value: "Для продолжения нажмите 'Далее&nbsp;&gt;'"
                                    }
                                ]
                            };

                    var win = new Ext.Window
                    ({
                        id: 'install-window',
                        layout:'fit',
                        width:600,
                        height:500,
                        title: 'Установка CMS',
                        closable : false,
                        resizable: true,
                        border: false,
                        iconCls:'install-icon',
                        items:
                        [
                            firstForm
                        ],
                        buttons:
                        [
                            {
                                text: '< Назад',
                                disabled: true,
                                ref: '../backButton'
                            },
                            {
                                text: 'Далее >',
                                ref: '../nextButton'
                            },
                            {
                                text: 'Готово',
                                disabled: true,
                                ref: '../finishButton'
                            }
                        ]
                    });

                    var next = function () {
                        var form = win.getComponent('form');
                        form.getForm().submit({
                            url: '/install.php?action=install.wizard.next',
                            method: 'POST',
                            waitTitle: 'Подождите',
                            waitMsg: 'Выполнение...',
                            success: function(f, action)
                            {
                                var obj = Ext.util.JSON.decode(action.response.responseText);
                                win.removeAll(true);
                                win.add(obj);
                                win.doLayout();
                                win.buttons[0].setDisabled(false);
                                if(action.result.finish)
                                {
                                    win.buttons[0].setDisabled(true);
                                    win.buttons[1].setDisabled(true);
                                    win.buttons[2].setDisabled(false);
                                }
                            },
                            failure: function(f, action)
                            {
                                Ext.MessageBox.alert('Ошибка', action.result.msg);
                            }
                        });
                    };

                    var back = function () {
                        var form = win.getComponent('form');
                        var step = form.getForm().findField('step').value;
                        if(step < 2)
                        {
                            win.removeAll(true);
                            win.add(firstForm);
                            win.doLayout();
                            win.buttons[0].setDisabled(true);
                        }
                        else
                        {
                            form.getForm().submit({
                                url: '/install.php?action=install.wizard.back',
                                method: 'POST',
                                waitTitle: 'Подождите',
                                waitMsg: 'Назад...',
                                success: function(f, action)
                                {
                                    var obj = Ext.util.JSON.decode(action.response.responseText);
                                    win.removeAll(true);
                                    win.add(obj);
                                    win.doLayout();
                                },
                                failure: function(f, action)
                                {
                                    Ext.MessageBox.alert('Ошибка', action.result.msg);
                                }
                            });
                        }
                    };

                    var finish = function() {
                        var form = win.getComponent('form');
                        var value = form.getForm().findField('run-cm').checked;
                        if(value)
                        {
                            window.location = "/cm/";
                        }
                        else
                        {
                            win.removeAll(true);
                            win.add(firstForm);
                            win.doLayout();
                            win.buttons[0].setDisabled(true);
                            win.buttons[1].setDisabled(false);
                            win.buttons[2].setDisabled(true);
                        }
                    }

                    win.buttons[0].setHandler(back);
                    win.buttons[1].setHandler(next);
                    win.buttons[2].setHandler(finish);

                    win.show();
                }
            });
        }
    }
}();

Ext.BLANK_IMAGE_URL = '/jscripts/ext/resources/images/default/s.gif';

// точка входа в приложение
Ext.onReady(App.init,App,true);


