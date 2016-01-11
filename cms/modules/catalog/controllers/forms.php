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
        $error_message_captcha = '';
        $success_message = '';
        $res = array();
        if(Utils::getPost('submit_'.$form->id))
        {
            $check = true;
            if($form->captcha == 1)
            {
                $captcha_code = Utils::getPost('number_'.$form->id);

                $securimage = Utils::createSecurimage('number_'.$form->id);

                if(!$securimage->check($captcha_code))
                {
                    $check = false;
                    $error_message_captcha = 'Введите верный код с картинки';
                    $res['errors']['number_'.$form->id] = $error_message_captcha;
                }
            }
            //if($check)
            {
                $email = $form->email;
                $mails = explode(',',$email);

                foreach($mails as $mail)
                {
                    $error_message = '';

                    $mailer = new Mailer();

                    $mailer->From = $form->efrom;

                    $mailer->FromName = $form->efromname;

                    $mailer->Subject = $form->esubject;

                    $body = $form->html;

                    /*if($form->header_mail)
                    {
                        $body .= $form->header_mail."\n\n";
                    }*/

                    foreach($field_rows as $row)
                    {
                        if($row['type_id'] == 'file')
                        {
                            $nameid = $row['nameid'];
                            if(isset($_FILES[$nameid]))
                            {
                                $mailer->AddAttachment($_FILES[$nameid]['tmp_name'],$_FILES[$nameid]['name']);
                            }
                            continue;
                        }

                        if(isset($row['nameid']) && $row['nameid'])
                        {
                            $value = Utils::getPost($row['nameid']);
                        }
                        else
                        {
                            $value = Utils::getPost('field_'.$row['id']);
                        }

                        if($row['valid_empty'] == 1)
                        {
                            if(!$value || trim($value) == '')
                            {
                                $error_message .= 'Не заполнено: '.$row['name']."\n";
                                $res['errors'][$row['nameid']] = $row['name'];
                                $check = false;
                            }
                        }

                        if($row['valid_email'] == 1 && !$res['errors'][$row['nameid']])
                        {
                            if(trim($value) && !filter_var(trim($value), FILTER_VALIDATE_EMAIL))
                            {
                                $error_message .= 'Не верный: '.$row['name']."\n";
                                $res['errors'][$row['nameid']] = $row['name'];
                                $check = false;
                            }
                        }

                        if($form->html)
                        {
                            $body = str_replace('#'.$row['nameid'].'#',$value,$body);
                        }
                        else
                        {
                            $body .= $row['name'].': '.$value."\n\n";
                        }
                    }

                    if($template_fields = $this->args->template_fields)
                    {
                        foreach($template_fields as $key=>$value)
                        {
                            $body = str_replace('#'.$key.'#',$value,$body);
                        }
                    }

                    if($check)
                    {
                        $mailer->Body = $body;

                        if(trim($mail))
                        {
                            $mailer->AddAddress(trim($mail));
                        }

                        $cancel = false;
                        if(Event::HasHandlers('OnBeforeSendForm'))
                        {
                            $params = array();
                            $params['mailer'] = &$mailer;
                            $params['alias'] = $alias;
                            $params['section'] = $section;
                            $params['form'] = $form;
                            $params['fields'] = $field_rows;
                            $params['cancel'] = &$cancel;
                            Event::Execute('OnBeforeSendForm', $params);
                        }

                        if(!$cancel)
                        {
                            if($mailer->Send())
                            {
                                $success_message = $form->success_message;
                                $is_send = true;
                            }
                            else
                            {
                                $error_message = 'Ошибка, попробуйте отправить снова.';
                                break;
                            }
                        }
                    }
                    else
                    {
                        break;
                    }
                }
                if($is_send && Event::HasHandlers('OnAfterSendForm'))
                {
                    $params = array();
                    $params['mailer'] = $mailer;
                    $params['alias'] = $alias;
                    $params['section'] = $section;
                    $params['form'] = $form;
                    $params['fields'] = $field_rows;
                    Event::Execute('OnAfterSendForm', $params);
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
        $template->res = $res;

        if(Utils::GetPost('submit_'.$form->id) && Utils::GetPost('ajax_form')==1){

            if($error_message || $error_message_captcha)
            {
                $res["result"] = "error";
                $res['message'] = $error_message.$error_message_captcha;
            }
            else
            {
                $res["result"] = "success";
                $res['message'] = $success_message;
            }

            echo json_encode($res);

            return;
        }

        $template->render();
    }
}

?>
