<?php
if($parent_section && $parent_section->leaf == 1){
?>
{
    layout:'fit',
    width: 400,
    height: 150,
    closeAction:'close',
    plain: true,
    border: false,
    title: 'Добавить подраздел',
    items:
    [
        {
            xtype: 'form',
            itemId: 'form',
            frame: true,
            labelAlign: 'top',
            items:
            [
                {
                    xtype: 'label',
                    text: <?php echo escapeJSON('Нельзя добавлять подразделы в "'.$parent_section->title.'"'); ?>
                }
            ]
         }
    ],
    buttonAlign: 'center',
    buttons:
    [
        {
            text: 'Закрыть',
            handler: function()
            {
                this.ownerCt.ownerCt.hide();
            }
        }
    ]
}
<?php } else { ?>
{
    layout:'fit',
    width: 400,
    height: 150,
    closeAction:'close',
    plain: true,
    border: false,
    title: <?php if($parent_section) echo escapeJSON('Добавить подраздел в "'.$parent_section->title.'"'); else echo escapeJSON('Добавить раздел'); ?>,
    items:
    [
        {
            xtype: 'form',
            itemId: 'form',
            frame: true,
            labelAlign: 'top',
            defaults:
            {
                width: 350,
                xtype: 'textfield'
            },
            items:
            [
                <?php if($parent_section){ ?>
                {
                    xtype: 'hidden',
                    name: 'parent_id',
                    value: <?php echo $parent_section->id ?>
                },
                <?php } ?>
                {
                    xtype: 'textfield',
                    fieldLabel: <?php if($parent_section) echo escapeJSON('Название подраздела'); else echo escapeJSON('Название раздела'); ?>,
                    name: 'title',
                    anchor: '95%',
                    allowBlank: false
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
                if(form.getForm().isValid())
                {
                    form.getForm().submit({
                        url: '/ajax/cm/catalog.cm.add_section',
                        method: 'POST',
                        waitTitle: 'Подождите',
                        waitMsg: 'Сохранение...',
                        success: function(form, action){
                            window.location = window.location;
                            win.close();
                        },
                        failure: function(form, action){
                            win.close();
                        }
                    });
                }
                else
                {
                    Ext.MessageBox.alert('Проверка', 'Заполните все поля');
                }
            }
        },
        {
            text: 'Отмена',
            handler: function()
            {
                this.ownerCt.ownerCt.hide();
            }
        }
    ]
}
<?php } ?>
