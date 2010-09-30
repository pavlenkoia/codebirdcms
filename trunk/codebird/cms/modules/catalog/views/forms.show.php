<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<script type="text/javascript" src="jscripts/jquery.validate.js"></script>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function(){
        $.validator.addMethod("requiredCF",function(value, element){
            return value != $(element).attr("title") && value.replace(/(^\s+)|(\s+$)/g, "") != "";
        });

        $("#form_<?=$form->id?>").validate({
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
                        echo '"field['.$row['id'].']" : { ';
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
                        echo '"field['.$row['id'].']" : { ';
                        if($row['valid_empty'] == 1)
                        {
                           echo 'requiredCF: "введите"';
                           $sep2 = true;
                        }
                        if($row['valid_email'] == 1)
                        {
                           echo $sep2 ? ',' : '';
                           echo 'email: "некорректный e-mail"';
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

<form action="" method="POST" id="form_<?=$form->id?>">
    <?php foreach($field_rows as $row) { ?>
    <?php if($row['type_id'] == 'text') { ?>
    <div>
        <label for="field[<?=$row['id']?>]"><?=$row['name']?>:</label>
        <input id="field[<?=$row['id']?>]" name="field[<?=$row['id']?>]" type="text" value=""/>
    </div>
    <?php } ?>
    <?php } ?>
    <div>
        <input value="Отправить" name="submit" type="submit">
    </div>
</form>