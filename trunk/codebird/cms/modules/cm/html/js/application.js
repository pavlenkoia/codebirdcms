/**
 * Приложение
 */

var App  = function(){

    var tabs;
    var contentPanel;

    // воспомагательная ф-я, определяет что передано - айди или объект-элемент
    // и возвращает элемент
    Ext.Ajax.FindEl = function (elid) {
        if (Ext.isString(elid)) {
            if (Ext.getCmp(elid)) {
                return Ext.getCmp(elid).getEl();
            } else {
                return {
                    mask :function(){},
                    unmask : function() {}
                };
            }
        } else {
            if (elid instanceof Ext.Element) {
                return elid;
            } else {
                return elid.getEl();
            }
        }
    };
    // перед посылкой запроса
    Ext.Ajax.on('beforerequest', function (conn, options) {
        // если в опцях указано маскировать
        if (options.maskEl) {
    // находим и маскируем элемент
        Ext.Ajax.FindEl(options.maskEl).mask(options.loadingMessage ? options.loadingMessage : 'Пожалуйста, подождите');
    }
    });
    // после завершение или при ошибке
    Ext.Ajax.on('requestcomplete', function (conn, response, options) {
        // демаскируем
        if (options.maskEl) Ext.Ajax.FindEl(options.maskEl).unmask();
    });

    Ext.Ajax.on('requestexception', function (conn, response, options) {
        if(response.status == 403){
            window.location = "/cm/";
        }
        if (options.maskEl) Ext.Ajax.FindEl(options.maskEl).unmask();
    });


    Ext.Ajax.on('requestcomplete', function (conn, response, options) {
        // пробуем декодировать ответ как JSON
        // чтобы ошибка не выбивала выполнение кода
        // попытка заключена в защищенный блок
        try {
            // если все прошло ок то в св-во responseJSON объекта response
            // запишется декодированный из JSON объект
            response.responseJSON = Ext.util.JSON.decode(response.responseText);
//            response.responseJSON = eval('(' + response.responseText + ')' )
        }
        catch (e) {
            // при ошибке responseJSON будет false
            response.responseJSON = false;
        }
    });

    var msgCt;

    function createBox(t, s){
        return ['<div class="msg">',
                '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
                '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3>', t, '</h3>', s, '</div></div></div>',
                '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
                '</div>'].join('');
    }


    return {
        init : function(){
            Ext.QuickTips.init();

            // initialize state manager, we will use cookies
            Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

            

            contentPanel = new Ext.Panel({
                id: 'content-panel',
                region: 'center', 
                layout: 'card',
                margins: '2 5 5 0',
                activeItem: 0,
                border: false
            });

            tabs = new Ext.TabPanel({
                resizeTabs:true, 
                minTabWidth: 100,
                tabWidth: 150,
                enableTabScroll:true,
                defaults: {
                    autoScroll:true
                },
                plugins: new Ext.ux.TabCloseMenu()
            });

            contentPanel.add(tabs);

            tabs.add({
                title: 'CMS',
                iconCls: 'docs-menu',
                closable:false,
                bodyStyle: 'margin: 0px',
                layout:'fit',
                listeners: {
                    render: function(comp){
                        Ext.Ajax.request({
                            url : '/ajax/manual.cm.content',
                            success : function (response) {
                                comp.add(response.responseJSON);
                                comp.doLayout();
                            }
                        });
                    }
                }
            });

            var navigatorPanel = new Ext.Panel({
                id : 'navigator-panel',
                title : 'Модули сайта',
//                region:'north',
                margins:'5 0 5 5',
                split:true,
                width: 210,
                height: 400,                
                layout:'accordion',
                cls: 'navigator'
            });

            

            var vp = new Ext.Viewport({
                layout: 'border',
                title: 'Ext Layout Browser',
                items: [{
                    xtype: 'box',
                    region: 'north',
                    applyTo: 'header',
//                    cls: 'header',
                    height: 30
                },
                {
                    layout: 'fit',
                    id: 'layout-browser',
                    region:'west',
                    border: false,
                    split:true,
                    margins: '2 0 5 5',
                    width: 275,
                    minSize: 100,
                    maxSize: 500,
                    items: [navigatorPanel]
                },
                contentPanel
                ],
                renderTo: Ext.getBody()
            });

            Ext.get('onexit').on('click',function(e){
                e.stopEvent();
                Ext.Ajax.request({
                url : '/ajax/cm.login.logout',
                maskEl : vp,
                loadingMessage : 'Выполняется выход...',
                success : function (response) {
                        window.location = response.responseJSON.msg;
                    }
                });

            });

            tabs.setActiveTab(0);

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
//                easing:'bounceOut',
                callback : function(){
                    loading.fadeOut({
                        duration:.2,
                        remove:true
                    });
                }
            });

            Ext.Ajax.request({
                url : '/ajax/cm.app.navigator',
                maskEl : 'navigator-panel',
                loadingMessage : 'Выполняется запрос...',
                success : function (response) {
                    // обработчик
                    navigatorPanel.add(response.responseJSON);
                    navigatorPanel.doLayout();
                }

            });

            Ext.TaskMgr.start({
                run: function(){
                    Ext.Ajax.request({
                        url : '/ajax/cm/cm.app.appecho'
                    });
                },
                interval: 900000
            });
        },
        showEditor : function(options){
            var id = 'tab-'+options.id;
            if(tabs.findById(id))
            {
                tabs.setActiveTab(tabs.findById(id));
            }
            else
            {
                tabs.add({
                    id: 'tab-'+options.id,
                    title: options.caption,
                    tabTip: options.caption,
                    closable:true,
                    iconCls: options.iconCls ? options.iconCls : '',
                    bodyStyle: 'margin: 0px',
                    layout:'fit',
                    listeners: {
                        afterrender: function(comp){
                            Ext.Ajax.request({
                                url : options.url,
                                maskEl : contentPanel,
                                loadingMessage : 'Загрузка...',
                                success : function (response) {
                                    comp.add(response.responseJSON);
                                    comp.doLayout();
                                }
                            });
                        }
                    }
                }).show();
            }            
        },
        closeEditor: function(options){
            var id = 'tab-'+options.id;
            if(tabs.findById(id))
            {
                tabs.remove(tabs.findById(id));
            }
            else if(tabs.findById(options.id))
            {
                tabs.remove(tabs.findById(options.id));
            }
        },
        uploadWindow : function(option){
            var win = new Ext.Window({
                title: option.title ? option.title : 'Загрузить картинку',
                layout:'fit',
                width:400,
                height:130,
                closeAction:'close',
                border: false,
                items:[
                    {
                        xtype: 'form',
                        itemId: 'form',
                        frame: true,
                        fileUpload: true,
                        defaults:
                        {
                            anchor: '95%',
                            allowBlank: false,
                            msgTarget: 'side'
                        },
                        items:[
                            {
                                xtype: 'fileuploadfield',
                                name: 'file',
                                emptyText: option.emptyText ? option.emptyText : 'Выберите картинку...',
                                buttonText: 'Выбрать',
                                hideLabel: true
                            },
                            {
                                xtype: 'hidden',
                                name: 'id',
                                value: option.id
                            }
                        ],
                        buttonAlign: 'center',
                        buttons:[
                            {
                                text:'Загрузить',
                                handler: function(){
                                    win.hide();
                                    var form = this.ownerCt.ownerCt;
                                    form.getForm().submit({
                                        url: option.url,
                                        waitMsg: option.waitMsg ? option.waitMsg : 'Загрузка файла...',
                                        success: function(fp, action)
                                        {
                                            if(option.success){
                                                option.success(action.result);
                                            }
                                            win.close();
                                        },
                                        failure: function(fp, action)
                                        {
                                            Ext.MessageBox.alert('Ошибка', action.result.msg);
                                            win.close();
                                        }
                                    });
                                }
                            },
                            {
                                text:'Отмена',
                                handler: function(){
                                    win.close();
                                }
                            }
                        ]
                    }
                ],
                listeners: {
                    show: function(comp)
                    {
                        comp.setWidth(comp.getWidth()-1);
                    }
                }
            });
            if(option.targetId){
                win.show(option.targetId);
            }
            else{
                win.show();
            }
        },
        msg : function(title, format){
            if(!msgCt){
                msgCt = Ext.DomHelper.insertFirst(document.body, {id:'msg-div'}, true);
            }
            msgCt.alignTo(document, 't-t');
            var s = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
            var m = Ext.DomHelper.append(msgCt, {html:createBox(title, s)}, true);
            m.slideIn('t').pause(2).ghost("t", {remove:true});
        }
    };
}();


Ext.BLANK_IMAGE_URL = 'jscripts/ext/resources/images/default/s.gif';
//Ext.ns('Application');

// application main entry point
Ext.onReady(App.init,App,true);





