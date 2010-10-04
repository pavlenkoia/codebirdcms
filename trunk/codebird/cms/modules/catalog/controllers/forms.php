<?php
/* 
 * 
 */

class CatalogController_Forms extends Controller_Base
{
    private function form($name, $args)
    {

        $data = $this->getData('action');

        $section = $data->getSection($name);

        if(!$section) return;

        $template = $this->createTemplate();        

        $template->data = $data;

        $alias = Utils::getVar('alias');

        $uri = Utils::getVar('uri');

        $params = explode("/",$uri);

        $params = array_pad($params,10,null);

        $template->alias = $alias;

        $template->params = $params;

        $template->render();
    }

//    public function __call($name, $args)
//    {
//       $this->form($name, $args);
//    }

    public function index()
    {
    }

    public function show()
    {
        $alias = $this->args->form;

        $data = $this->getData('action');

        $section = $data->getSection($alias);

        if(!$section) return;

        $table = new Table('section_forms');

        $form = $table->getEntity($section->id);

        if(!$form) return;

        $field_rows = $table->select('select * from position_forms where section_id=:id order by position',
                array('id'=>$form->id));

        $error_message = '';
        $success_message = '';
        if(Utils::getPost('submit'))
        {
            $check = true;
            if($form->captcha == 1)
            {
                $captcha_code = Utils::getPost('number_'.$form->id);

                $securimage = Utils::createSecurimage('number_'.$form->id);

                if(!$securimage->check($captcha_code))
                {
                    $check = false;
                    $error_message = 'Введите верный код с картинки';
                }
            }
            if($check)
            {
                $email = $form->email;
                $mails = explode(',',$email);
                foreach($mails as $mail)
                {

                    $mailer = new Mailer();

                    $mailer->From = $form->efrom;

                    $mailer->FromName = $form->efromname;

                    $mailer->Subject = $form->esubject;

                    $body = '';

                    foreach($field_rows as $row)
                    {
                        $body .= $row['name'].': '.Utils::getPost('field_'.$row['id'])."\n\n";
                    }

                    $mailer->Body = $body;

                    $mailer->AddAddress(trim($mail));

                    if($mailer->Send())
                    {
                        $success_message = $form->success_message;
                    }
                    else
                    {
                        $error_message = 'Ошибка, попробуйте отправить снова.';
                    }
                }
                if(isset($form->mod))
                {
                    val($form->mod,array('form_id'=>$form->id));
                }
            }
        }


        $template = $this->createTemplate();

        $template->data = $data;

        $alias = Utils::getVar('alias');

        $uri = Utils::getVar('uri');

        $params = explode("/",$uri);

        $params = array_pad($params,10,null);

        $template->alias = $alias;

        $template->params = $params;

        $template->form = $form;

        $template->field_rows = $field_rows;

        $template->error_message = $error_message;
        $template->success_message = $success_message;

        $template->render();
    }
}

?>
