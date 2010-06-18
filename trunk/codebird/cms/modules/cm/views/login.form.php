<?php if(Utils::getVar('admin') !== null) { ?>

<script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<link rel="stylesheet" href="jscripts/fancybox/jquery.fancybox-1.3.1.css" type="text/css" media="screen" />

<?php if(!val('security.login')) { ?>

<script type="text/javascript">
    $().ready(function() {
        $.fancybox(
        {
            'scrolling'		: 'no',
            'titleShow'		: true,
            'titlePosition' : 'outside',
            'showCloseButton' : true,
            'title' : 'Вход в администрирование',
            'href' : '/ajax/cm.login.form_login',
            onComplete : function() {
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
            }
        });
    });

</script>

<? } else { ?>

<script type="text/javascript">
    $().ready(function() {
        $.fancybox(
        {
            'scrolling'		: 'no',
            'titleShow'		: true,
            'titlePosition' : 'outside',
            'showCloseButton' : true,
            'title' : 'Выход из администрирования',
            'href' : '/ajax/cm.login.form_logout',
            onComplete : function(){
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
            }
        });        
    });

</script>

<?php }?>

<?php } ?>