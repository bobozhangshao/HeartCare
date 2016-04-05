<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/3/14
 * Time: 09:53
 */


require_once JPATH_BASE.'/components/com_users/models/registration.php';
//require_once JPATH_BASE.'/components/com_users/models/forms/registration.xml';

//上传测量数据的接口
date_default_timezone_set('prc');//设置时区为Peoples Republic of China
defined('_JEXEC') or die('Restricted Access');
class HeartCareControllerUpload extends JControllerForm
{
    public function upload()
    {
        $app    = JFactory::getApplication();

        $data['user']['id']        = $app->input->post->get('user_id','','string');
        $data['user']['username']  = $app->input->post->get('username','','string');
        $data['user']['email']     = $app->input->post->get('user_email','','string');
        $data['data']['deviceid']  = $app->input->post->get('device_id','','string');
        $data['data']['time']      = $app->input->post->get('datetime','','string');
        $data['data']['datatype']  = $app->input->post->get('datatype','','string');
        $data['data']['devicetype']= $app->input->post->get('device_type','','string');
        $data['file']              = $app->input->files->get('file','','array');

        //判断文件类型是不是文本类型
        if ($data['file']['type'] != 'text/plain')
        {
            return false;
        }

        //获取model : upload
        $model  = $this->getModel('Upload', 'HeartCareModel');

        //判断是否有此用户,有则插入,无则创建
        if($model->check_user($data))
        {
            $response['have_user'] = "YES";
            if($model->to_folder($data)){
                $response['to_folder'] = "OK";
                if($model->insert_measure($data))
                {
                    $response['insert'] = "OK";
                    echo json_encode($response);
                    return true;
                }
                else{
                    $response['insert'] = "FALSE";
                    echo json_encode($response);
                    return false;
                }
            }
            else {
                $response['to_folder'] = "FALSE";
                $response['insert'] = "NO";
                echo json_encode($response);
                return false;
            }
        }
        elseif(!$model->check_user($data))
        {
            $response ['have_user'] = "NO";

            $data['user']['password'] = '123456';

            $model_regist = $this->getModel('Registration', 'UsersModel');

            $requestData['name']      = $data['user']['username'];
            $requestData['username']  = $data['user']['username'];
            $requestData['password1'] = $data['user']['password'];
            $requestData['password2'] = $data['user']['password'];
            $requestData['email1']    = $data['user']['email'];
            $requestData['email2']    = $data['user']['email'];

            /*$form = $model_regist->getForm();

            if (!$form)
            {
                return false;
            }

            $new_user = $model_regist->validate($form, $requestData);

            // Check for validation errors.
            if ($new_user === false)
            {
                // Get the validation messages.
                $errors = $model_regist->getErrors();

                // Push up to three validation messages out to the user.
                for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
                {
                    if ($errors[$i] instanceof Exception)
                    {
                        $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                    }
                    else
                    {
                        $app->enqueueMessage($errors[$i], 'warning');
                    }
                }

                // Save the data in the session.
                $app->setUserState('com_users.registration.data', $requestData);

                return false;
            }*/

            // Attempt to save the data.
            //$return = $model_regist->register($new_user);
            $return = $model_regist->register($requestData);

            if($return === false)
            {
                $response['create_user'] = 'FALSE';
                echo json_encode($response);
                return false;
            }
            else
            {
                $response['create_user'] = 'TRUE';

                if($model->start_user($data))
                {
                    $response['start_user'] = 'OK';
                    if($model->to_folder($data)){
                        $response['to_folder'] = "OK";
                        if($model->insert_measure($data))
                        {
                            $response['insert'] = "OK";
                            echo json_encode($response);
                            return true;
                        }
                        else{
                            $response['insert'] = "FALSE";
                            echo json_encode($response);
                            return false;
                        }
                    }
                    else {
                        $response['to_folder'] = "FALSE";
                        $response['insert'] = "NO";
                        echo json_encode($response);
                        return false;
                    }
                }
                else
                {
                    $response['start_user'] = 'FALSE';
                    echo json_encode($response);
                    return false;
                }
            }
        }
    }
}