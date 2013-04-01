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

                    var login = function () {
                        var win = Ext.getCmp('login-window');
                        var form = win.getComponent('form');
                        Ext.state.Manager.set('ln',form.getComponent('name').getValue());
                        form.getForm().submit({
                            url: '/ajax/cm.login',
                            method: 'POST',
                            waitTitle: 'Подождите',
                            waitMsg: 'Проверка...',
                            success: function(f, action)
                            {
                                win.hide();
                                window.location = action.result.msg;
                                //form.getForm().submit({url:action.result.msg,standardSubmit:true,method: 'POST'});
                            },
                            failure: function(f, action)
                            {
                                Ext.MessageBox.alert('Ошибка', action.result.msg);
                            }
                        });
                    };

                    var win = new Ext.Window
                    ({
                        id: 'login-window',
                        layout:'fit',
                        width:310,
                        height:150,
                        title: 'Вход в систему',
                        closable : false,
                        resizable: false,
                        border: false,
                        buttonAlign: 'center',                        
                        items:
                        [
                            {
                                xtype: 'form',
                                itemId: 'form',
                                labelWidth: 75,
                                frame: true,
                                defaults:
                                {
                                    width: 175,
                                    xtype: 'textfield'
                                },
                                items:
                                [
                                    {
                                        fieldLabel: 'Имя',
                                        name: 'name',
                                        itemId: 'name',
                                        listeners: {
                                            specialkey: function(field, e){
                                                if (e.getKey() == e.ENTER) {
                                                    win.getComponent('form').getComponent('password').focus(true);
                                                }
                                            }
                                        }
                                    },
                                    {
                                        inputType: 'password',
                                        fieldLabel: 'Пароль',
                                        name: 'password',
                                        itemId: 'password',
                                        listeners: {
                                            specialkey: function(field, e){
                                                if (e.getKey() == e.ENTER) {
                                                    login();
                                                }
                                            }
                                        }
                                    },
                                    {
                                        xtype: 'checkbox',
                                        boxLabel: 'запомнить меня',
                                        inputValue: 1,
                                        name: 'store',
                                        itemId: 'store',
                                        checked: true
                                    }
                                ]
                                
                            }
                        ],
                        buttons:
                        [
                            {
                                text: 'Войти',
                                handler: login
                        }
                        ]
                    });
                    win.show();                    
                    win.getComponent('form').getComponent('name').setValue(Ext.state.Manager.getProvider().get('ln'));
                    if(win.getComponent('form').getComponent('name').getValue() == '')
                    {
                        win.getComponent('form').getComponent('name').focus(true,1000);
                    }
                    else
                    {
                        win.getComponent('form').getComponent('password').focus(true,1000);
                    }
                }
            });
        }
    }
}();

Ext.BLANK_IMAGE_URL = '/jscripts/ext/resources/images/default/s.gif';

// точка входа в приложение
Ext.onReady(App.init,App,true);


