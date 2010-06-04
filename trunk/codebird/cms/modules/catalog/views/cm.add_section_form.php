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
                    xtype: 'hidden',
                    name: 'alias',
                    value: '<?php echo $alias ?>'
                },
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
    listeners:
    {
        show: function(comp)
        {
            comp.setWidth(comp.getWidth()-1);
        }
    },
    buttonAlign: 'center',
    buttons:
    [
        {
            text:'Сохранить',
            handler: function(btn)
            {
                var form = this.ownerCt.ownerCt.getComponent('form');
                var tree = Ext.getCmp('catalog-navigator-<?php echo $alias ?>').getComponent('tree');
                <?php if($parent_section){ ?>
                var tb = Ext.getCmp('catalog-navigator-<?php echo $alias ?>').getTopToolbar();
                btn_add_sub = tb.getComponent('add').menu.getComponent('add-sub');
                var node = btn_add_sub.node;
                <?php }?>
                if(form.getForm().isValid())
                {
                    form.getForm().submit({
                        url: '/ajax/cm/catalog.cm.add_section',
                        method: 'POST',
                        waitTitle: 'Подождите',
                        waitMsg: 'Сохранение...',
                        success: function(form, action){
                                <?php if($parent_section){ ?>
                                var id = node.id;
                                node.getOwnerTree().getLoader().load(node.parentNode, function(){tree.getNodeById(id).parentNode.expand();tree.getNodeById(id).expand();});
                                <? } else{ ?>
                                tree.getLoader().load(tree.root);
                                <?php }?>
                                btn.ownerCt.ownerCt.hide();
                            },
                        failure: function(form, action){
                                Ext.MessageBox.alert('Ошибка', action.result.msg);
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
