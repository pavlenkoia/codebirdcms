<script type="text/javascript" src="jscripts/jquery.validate.js"></script>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        $.validator.addMethod("requiredCF",function(value, element){
            return value != $(element).attr("title") && value.replace(/(^\s+)|(\s+$)/g, "") != "";
        });

        $("#form_<?=$form->id?>").validate({
            invalidHandler: function(e, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                            var message = errors == 1
                                    ? 'Не заполнено или неверно заполнено выделенное поле.'
                                    : 'Не заполнены или неверно заполнены выделенные поля';
                            $("div.error").html(message);
                            $("div.error").show();
                    } else {
                            $("div.error").hide();
                    }
                    return false;
            },
            //onkeyup: false,
            rules:{
            <?php
                $sep = false;
                foreach($field_rows as $row)
                {                    
                    if($row['valid_empty'] == 1 || $row['valid_email'] == 1)
                    {
                        $sep2 = false;
                        echo $sep ? ',' : '';
                        $sep = true;
                        echo '"field_'.$row['id'].'" : { ';
                        if($row['valid_empty'] == 1)
                        {
                           echo 'requiredCF: true';
                           $sep2 = true;
                        }
                        if($row['valid_email'] == 1)
                        {
                           echo $sep2 ? ',' : '';
                           echo 'email: true';
                           $sep2 = true;
                        }
                        echo '}';
                    }
                }
            ?>
            },
            messages : {
            <?php
                $sep = false;
                foreach($field_rows as $row)
                {                    
                    if($row['valid_empty'] == 1 || $row['valid_email'] == 1)
                    {
                        $sep2 = false;
                        echo $sep ? ',' : '';
                        $sep = true;
                        echo '"field_'.$row['id'].'" : { ';
                        if($row['valid_empty'] == 1)
                        {
                           echo 'requiredCF: ""';
                           $sep2 = true;
                        }
                        if($row['valid_email'] == 1)
                        {
                           echo $sep2 ? ',' : '';
                           echo 'email: ""';
                           $sep2 = true;
                        }
                        echo '}';
                    }
                }
            ?>
            },
            submitHandler: function(form) {
                form.submit(); }
        });
    });
</script>

<div class="form">
<form action="" method="POST" id="form_<?=$form->id?>">

    <?php if($form->title_form) { ?>
    <div class="form-title">
        <?=$form->title_form?>
    </div>
    <?php } ?>

    <div class="error"></div>

    <?php if($success_message) { ?>
    <div class="form-success_message">
        <?=$success_message?>
    </div>
    <?php } ?>

    <?php if($error_message) { ?>
    <div class="form-error_message">
        <?=$error_message?>
    </div>
    <?php } ?>


    <?php if(!$success_message) { ?>


    <?php foreach($field_rows as $row) { ?>
    <?php if($row['type_id'] == 'text') { ?>
    <div class="form-field">
        <label for="field_<?=$row['id']?>"><?=$row['name']?><?=$row['valid_empty'] ? ' * ' : ''?>:</label>
        <input id="field_<?=$row['id']?>" name="field_<?=$row['id']?>" type="text" value="<?=$error_message ? htmlspecialchars(Utils::getPost('field_'.$row['id'])) : ''?>"/>
    </div>
    <?php } elseif($row['type_id'] == 'memo') { ?>
    <div class="form-field">
        <label for="field_<?=$row['id']?>"><?=$row['name']?><?=$row['valid_empty'] ? ' * ' : ''?>:</label>
        <textarea class="textarea" id="field_<?=$row['id']?>" name="field_<?=$row['id']?>" rows="5"><?=$error_message ? htmlspecialchars(Utils::getPost('field_'.$row['id'])) : ''?></textarea>
    </div>
    <?php } elseif($row['type_id'] == 'select') { ?>

    <?php } ?>
    <?php } ?>

    <?php if($form->captcha == 1) { ?>
    <div class="form-captcha">
        <label for="number_<?=$form->id?>">Введите код с картинки:</label>
        <img class="captcha" src="cms/lib/securimage/securimage_show.php?name=number_<?=$form->id?>" onclick='$(this).attr({src:"cms/lib/securimage/securimage_show.php?name=number_<?=$form->id?>&sid=" + Math.random()}); return false;' style="cursor:pointer" title="Обновить код"/>
        <input id="number_<?=$form->id?>" name="number_<?=$form->id?>" type="text"/>
    </div>
    <?php } ?>
    <div class="form-submit">
        <input value="Отправить" name="submit" type="submit">
    </div>

    <?php } ?>

</form>
</div>