<?php if(Utils::getVar('admin') !== null) { ?>

<script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<link rel="stylesheet" href="jscripts/fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />

<?php if(!val('security.login')) { ?>
<div style="display:none">
	<form id="login_form" method="post" action="">
	    	<div style="display: none; padding-top: 8px; color:#D2220E; font-style:italic;" id="login_error"></div>
                <div style="padding: 16px">
                    <div>
                        <div style="width: 55px; float: left; font-size: 10pt;">Имя:</div>
                        <input type="text" id="login_name" name="name" size="30" />
                    </div>
                    <div style="padding-top: 8px">
                        <div style="width: 55px; float: left; font-size: 10pt;">Пароль:</div>
                        <input type="password" id="login_pass" name="password" size="30" />
                    </div>
                </div>
                <div style="text-align: center; padding: 8px">
			<input type="submit" value="Войти" style="width: 80px;"/>
		</div>
	</form>
</div>

<script type="text/javascript">
    $().ready(function() {
        $.fancybox(
        {
            'scrolling'		: 'no',
            'titleShow'		: true,
            'titlePosition' : 'outside',
            'showCloseButton' : true,
            'title' : 'Вход в администрирование',
            'href' : '#login_form'
        });

        $("#login_form").bind("submit", function() {

            $("#login_error").hide();
            $.fancybox.resize();

            $.ajax({
                type		: "POST",
                cache	: false,
                url		: "/ajax/cm.login",
                data		: $(this).serializeArray(),
                success: function(data) {
                    data = eval('(' + data + ')' );
                    if(!data.success)
                    {
                        $("#login_error").html(data.msg);
                        $("#login_error").show();
                    }
                    else
                    {
                        window.location = '/';
                    }
                    $.fancybox.resize();
                    $.fancybox.hideActivity();
                }
            });

            return false;
        });
    });

</script>

<? } else { ?>

<div style="display:none">
	<form id="login_form" method="post" action="">
	    	<div style="padding: 16px">
                    Вы действительно хотите выйти из администрирования?
                </div>
                <div style="text-align: center; padding: 8px">
			<input type="submit" value="Выйти" style="width: 80px;"/>
		</div>
	</form>
</div>

<script type="text/javascript">
    $().ready(function() {
        $.fancybox(
        {
            'scrolling'		: 'no',
            'titleShow'		: true,
            'titlePosition' : 'outside',
            'showCloseButton' : true,
            'title' : 'Выход из администрирования',
            'href' : '#login_form'
        });

        $("#login_form").bind("submit", function() {

            $.ajax({
                type		: "POST",
                cache	: false,
                url		: "/ajax/cm.login.logout",
                success: function(data) {
                    window.location = '/';
                }
            });

            return false;
        });
    });

</script>

<?php }?>

<?php } ?>