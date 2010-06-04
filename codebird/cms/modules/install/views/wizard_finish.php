{
    success: true,
    finish: true,
    xtype: 'form',
    itemId: 'form',
    frame: true,
    autoScroll: true,
    border: false,
    items:
    [
        {
            xtype: 'hidden',
            name: 'step',
            value: <?php echo $step?>

        },
        {
            xtype: 'displayfield',
            hideLabel: true,
            value: 'Установка успешно завершена',
            style: 'margin-bottom: 18px'
        },
        {
            xtype: 'checkbox',
            hideLabel: true,
            boxLabel: 'запустить контент менеджер',
            name: 'run-cm',
            inputValue: 1,
            checked: true
        }
    ]
}