{
    xtype: 'form',
    id: '<?php echo $module ?>-config-form',
    frame: true,
    bodyBorder : true,
    title: 'Настройки',
    autoScroll: true,
    labelAlign: 'top',
    defaults:
    {
        width: 400        
    },
    items:
    [
        {
            xtype: 'hidden',
            name: 'module',
            value: '<?php echo $module ?>'
        }
        <?php $exists=false; foreach($params as $param){ ?>
        <?php if($param['type'] == 'text'){ $exists=true;?>
        ,{
            xtype: 'textfield',
            fieldLabel: <?php echo escapeJSON($param['description']) ?>,
            name: '<?php echo $param['name'] ?>',
            value: <?php echo escapeJSON($param['value'])?>
        }
        <?php } }?>
    ],
    buttonAlign: 'center',
    buttons:
    [
        <?php if($exists){?>
        {
            text: 'Сохранить',
            formBind: true,
            handler: function()
            {
                var form = this.ownerCt.ownerCt;
                form.getForm().submit({
                    url: '<?php echo SF?>/ajax/cm/modmanager.cm.saveconfig',
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
        },<?php }?>
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