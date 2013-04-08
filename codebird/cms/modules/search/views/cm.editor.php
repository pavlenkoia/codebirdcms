{
    xtype: 'form',
    id: 'search-form-<?=$site['id']?>',
    frame: true,
    bodyBorder : true,
    title: 'Поиск по сайту '+<?=escapeJSON($site['url'])?>,
    autoScroll: true,
    defaults:
    {
    },
    items:
    [
        {
            xtype: 'hidden',
            name: 'id',
            value: <?php echo $site['id'] ?>
        },
        {
            xtype:'fieldset',
            title: 'Состояние',
            collapsible: false,
            autoHeight:true,
            items:
            [
                {
                    xtype: 'displayfield',
                    fieldLabel: 'Дата индексации',
                    name: 'indexdate',
                    style: 'font-weight:bold;',
                    value: <?php echo escapeJSON($site['indexdate'])?>
                },
                {
                    xtype: 'displayfield',
                    fieldLabel: 'Статус',
                    name: 'pending',
                    style: 'font-weight:bold;',
                    value: '<?=$site['pending']?'Индексирование не закончено':'Проиндексировано'?>'
                }
            ]
        }
    ],
    labelAlign: 'left',
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

