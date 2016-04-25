<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/4/18
 * Time: 10:16
 */
defined('_JEXEC') or die('Restricted Access');
//require_once JPATH_BASE.'/components/com_users/controller.php';
require_once JPATH_BASE.'/components/com_users/models/registration.php';
//require_once JPATH_BASE.'/components/com_users/models/remind.php';

class HeartCareControllerUser extends  JControllerForm
{
    /**
     * app登录接口
     * */
    public function login()
    {
        $app    = JFactory::getApplication();

        $user = array();
        $user['username']  = $app->input->post->get('username','','string');
        $user['password']  = $app->input->post->get('password','','string');
        $user['secretkey'] = '';

        $options['remember'] = false;
        $options['return'] = 'index.php?option=com_users&view=profile';

        $credentials = array();
        $credentials['username']  = $user['username'];
        $credentials['password']  = $user['password'];
        $credentials['secretkey'] = $user['secretkey'];


        $response = array();
        if (true === $app->login($credentials, $options))
        {
            // Success
            $response['login'] = 'OK';

            echo json_encode($response);
            JFactory::getApplication()->close();
        }
        else
        {
            // Login failed !
            $response['login'] = 'FAIL';

            echo json_encode($response);
            JFactory::getApplication()->close();

        }
    }

    /**
     * app登出接口
     * */
    public function logout()
    {

        $app = JFactory::getApplication();

        $user = array();
        $user['username']  = $app->input->post->get('username','','string');

        $model = $this->getModel('HeartCare','HeartCareModel');
        $tmp = $model->get_user_id($user);
        $user['id'] = $tmp[0]->id;

        $error  = $app->logout($user['id']);

        $response = array();

        // Check if the log out succeeded.
        if (!($error instanceof Exception))
        {
            $response['logout'] = 'OK';
            echo json_encode($response);
            JFactory::getApplication()->close();
        }
        else
        {
            $response['logout'] = 'FAILED';
            echo json_encode($response);
            JFactory::getApplication()->close();
        }

    }

    /**
     * app注册接口
     * */
    public function register()
    {
        $app = JFactory::getApplication();
        $user['username']  = $app->input->post->get('username','','string');
        $user['password']  = $app->input->post->get('password','','string');
        $user['email']     = $app->input->post->get('user_email','','string');

        $model_regist = $this->getModel('Registration', 'UsersModel');
        $model = $this->getModel('User', 'HeartCareModel');

        $response = array();
        if(!$model->check_username($user))
        {
            $response['username'] = 'OK';
            if(!$model->check_email($user))
            {
                $response['email'] = 'OK';

                $requestData['name']      = $user['username'];
                $requestData['username']  = $user['username'];
                $requestData['password1'] = $user['password'];
                $requestData['password2'] = $user['password'];
                $requestData['email1']    = $user['email'];
                $requestData['email2']    = $user['email'];

                $return = $model_regist->register($requestData);

                if($return === false)
                {
                    $response['create_user'] = 'FALSE';
                    echo json_encode($response);
                    JFactory::getApplication()->close();
                }
                else
                {
                    $response['create_user'] = 'TRUE';
                    echo json_encode($response);
                    JFactory::getApplication()->close();
                }
            }
            else
            {
                $response['email'] = 'EXIST';
                echo json_encode($response);
                JFactory::getApplication()->close();
            }

        }
        else
        {
            $response['username'] = 'EXIST';
            echo json_encode($response);
            JFactory::getApplication()->close();
        }
    }

    /**
     * app用户名找回
     * */
    public function username_remind()
    {
        $app    = JFactory::getApplication();
        $response = array();

        $user = array();
        $user['email']  = $app->input->post->get('user_email','','string');

        $model = $this->getModel('User', 'HeartCareModel');

        $have_email = $model->check_email($user);
        if($have_email)
        {
            $response['have_email'] = 'EXIST';
            $return = $model->processRemindRequest($user);
        }
        else
        {
            $response['have_email'] = 'NOT EXIST';
            $return = false;
        }

        // Check for a hard error.
        if ($return == false)
        {
            $response['sendmail'] = 'FALSE';
            echo json_encode($response);
            JFactory::getApplication()->close();

        }
        else
        {
            $response['sendmail'] = 'OK';
            echo json_encode($response);
            JFactory::getApplication()->close();
        }
    }


    /**
     * app用户密码重置接口
     * */
    public function password_reset()
    {
        $app   = JFactory::getApplication();
        $response = array();

        $model = $this->getModel('User', 'HeartCareModel');

        $user['email']  = $app->input->post->get('user_email', '', 'string');

        $have_email = $model->check_email($user);
        if($have_email)
        {
            $response['have_email'] = 'EXIST';
            $return = $model->processResetRequest($user); // Submit the password reset request.
        }
        else
        {
            $response['have_email'] = 'NOT EXIST';
            $return = false;
        }

        // Check for a hard error.
        if ($return instanceof Exception)
        {
            $response['sendmail'] = 'FALSE';
            echo json_encode($response);
            JFactory::getApplication()->close();
        }
        elseif ($return === false)
        {
            $response['sendmail'] = 'FALSE';
            echo json_encode($response);
            JFactory::getApplication()->close();
        }
        else
        {
            $response['sendmail'] = 'OK';
            echo json_encode($response);
            JFactory::getApplication()->close();
        }

    }

    /**
     * app用户状态查询
     * */
    public function user_state()
    {
        $app    = JFactory::getApplication();

        $response = array();

        $user = array();
        $user['username']  = $app->input->get('username','','string');
        $model = $this->getModel('User','HeartCareModel');

        if($model->check_username($user))
        {
            $response['have_user'] = 'EXIST';
        }
        else
        {
            $response['have_user'] = 'NOT EXIST';
            $response['online'] = 'NO';
            echo json_encode($response);
            JFactory::getApplication()->close();
        }

        if($model->user_state($user))
        {
            $response['online'] = 'YES';
            echo json_encode($response);
            JFactory::getApplication()->close();
        }
        else
        {
            $response['online'] = 'NO';
            echo json_encode($response);
            JFactory::getApplication()->close();
        }
    }

}