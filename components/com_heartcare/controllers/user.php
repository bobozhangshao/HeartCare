<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/4/18
 * Time: 10:16
 */
defined('_JEXEC') or die('Restricted Access');
require_once JPATH_BASE.'/components/com_users/controller.php';

class HeartCareControllerUser extends  UsersController
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
            if ($options['remember'] == true)
            {
                $app->setUserState('rememberLogin', true);
            }

            echo json_encode($response);
            $app->setUserState('users.login.form.data', array());
            JFactory::getApplication()->close();
        }
        else
        {
            // Login failed !
            $response['login'] = 'FAIL';

            $data['remember'] = (int) $options['remember'];
            echo json_encode($response);
            $app->setUserState('users.login.form.data', $data);
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



    }
}