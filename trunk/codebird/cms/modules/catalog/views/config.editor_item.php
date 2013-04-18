{
    xtype: 'panel',
    frame: true,
    bodyBorder : true,
    autoScroll: true,
    defaults:
    {
    },
    items:
    [
        {
            html: <?=escapeJSON('<pre>'.print_r($param_table,1).'</pre>');?>
        }
    ]
}