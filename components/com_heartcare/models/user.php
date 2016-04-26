<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/4/18
 * Time: 10:16
 */
defined('_JEXEC') or die('Restricted Access');

class HeartCareModelUser extends JModelList
{
    /**
     * 检查邮箱是否存在
     * return bool
     * */
    public function check_email(array $user)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from($db->quoteName('#__users'));
        $query->where(' email = '.$db->quote($user['email']));
        $db->setQuery($query);

        try
        {
            $result = $db->loadObjectList();

            if($result){
                return true;
            }
            else
            {
                return false;
            }
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

    }

    /**
     * 检查用户名是否存在
     * return bool
     * */
    public function check_username(array $user)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from($db->quoteName('#__users'));
        $query->where(' username = '.$db->quote($user['username']));
        $db->setQuery($query);

        try
        {
            $result = $db->loadObjectList();

            if($result){
                return true;
            }
            else
            {
                return false;
            }
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }
    }

    /**
     * Send the remind username email
     *
     * @param   array  $data  Array with the data received from post
     *
     * @return  boolean
     */
    public function processRemindRequest(array $data)
    {
        $data['email'] = JStringPunycode::emailToPunycode($data['email']);

        // Check for an error.
        if ($data instanceof Exception)
        {
            return false;
        }

        // Find the user id for the given email address.
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__users'))
            ->where($db->quoteName('email') . ' = ' . $db->quote($data['email']));

        // Get the user id.
        $db->setQuery($query);

        try
        {
            $user = $db->loadObject();
        }
        catch (RuntimeException $e)
        {
            $this->setError(JText::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);

            return false;
        }

        // Check for a user.
        if (empty($user))
        {
            $this->setError(JText::_('COM_USERS_USER_NOT_FOUND'));

            return false;
        }

        // Make sure the user isn't blocked.
        if ($user->block)
        {
            $this->setError(JText::_('COM_USERS_USER_BLOCKED'));

            return false;
        }

        $config = JFactory::getConfig();

        // Assemble the login link.
        $link = 'index.php?option=com_users&view=login' ;
        $mode = $config->get('force_ssl', 0) == 2 ? 1 : (-1);

        // Put together the email template data.
        $data = JArrayHelper::fromObject($user);
        $data['fromname'] = $config->get('fromname');
        $data['mailfrom'] = $config->get('mailfrom');
        $data['sitename'] = $config->get('sitename');
        $data['link_text'] = JRoute::_($link, false, $mode);
        $data['link_html'] = JRoute::_($link, true, $mode);


//        $subject = JText::sprintf(
//            'COM_USERS_EMAIL_USERNAME_REMINDER_SUBJECT',
//            $data['sitename']
//        );
        $subject = "您在 ".$data['sitename']." 的用户名";

//        echo "<pre>";
//        print_r($subject);
//        echo "</pre>";
//        JFactory::getApplication()->close();

       /* 原来的应用
       $body = JText::sprintf(
            'COM_USERS_EMAIL_USERNAME_REMINDER_BODY',
            $data['sitename'],
            $data['username'],
            $data['link_text']
        );*/
        $body = "您好，\n\n您账户".$data['sitename']."的用户名找回已经处理。\n\n您的用户名是".$data['username']."。\n\n您可以点击下面的链接，进行用户登录。\n\n".$data['link_text']." \n\n谢谢。";


        // Send the password reset request email.
        $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $user->email, $subject, $body);

        // Check for an error.
        if ($return !== true)
        {
            $this->setError(JText::_('COM_USERS_MAIL_FAILED'), 500);

            return false;
        }

        return true;
    }

    /**
     * Method to start the password reset process.
     *
     * @param   array  $data  The data expected for the form.
     *
     * @return  mixed  Exception | JException | boolean
     *
     * @since   1.6
     */
    public function processResetRequest(array $data)
    {
        $config = JFactory::getConfig();

        $data['email'] = JStringPunycode::emailToPunycode($data['email']);

        // Find the user id for the given email address.
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('id')
            ->from($db->quoteName('#__users'))
            ->where($db->quoteName('email') . ' = ' . $db->quote($data['email']));

        // Get the user object.
        $db->setQuery($query);

        try
        {
            $userId = $db->loadResult();
        }
        catch (RuntimeException $e)
        {
            $this->setError(JText::sprintf('COM_USERS_DATABASE_ERROR', $e->getMessage()), 500);

            return false;
        }

        // Check for a user.
        if (empty($userId))
        {
            $this->setError(JText::_('COM_USERS_INVALID_EMAIL'));

            return false;
        }

        // Get the user object.
        $user = JUser::getInstance($userId);

        // Make sure the user isn't blocked.
        if ($user->block)
        {
            $this->setError(JText::_('COM_USERS_USER_BLOCKED'));

            return false;
        }

        // Make sure the user isn't a Super Admin.
        if ($user->authorise('core.admin'))
        {
            $this->setError(JText::_('COM_USERS_REMIND_SUPERADMIN_ERROR'));

            return false;
        }

        // Make sure the user has not exceeded the reset limit
        if (!$this->checkResetLimit($user))
        {
            $resetLimit = (int) JFactory::getApplication()->getParams()->get('reset_time');
            $this->setError(JText::plural('COM_USERS_REMIND_LIMIT_ERROR_N_HOURS', $resetLimit));

            return false;
        }

        // Set the confirmation token.
        $token = JApplicationHelper::getHash(JUserHelper::genRandomPassword());
        $hashedToken = JUserHelper::hashPassword($token);

        $user->activation = $hashedToken;

        // Save the user to the database.
        if (!$user->save(true))
        {
            return new JException(JText::sprintf('COM_USERS_USER_SAVE_FAILED', $user->getError()), 500);
        }

        // Assemble the password reset confirmation link.
        $mode = $config->get('force_ssl', 0) == 2 ? 1 : (-1);
        $link = 'index.php?option=com_users&view=reset&layout=confirm&token=' . $token;

        // Put together the email template data.
        $data = $user->getProperties();
        $data['fromname'] = $config->get('fromname');
        $data['mailfrom'] = $config->get('mailfrom');
        $data['sitename'] = $config->get('sitename');
        $data['link_text'] = JRoute::_($link, false, $mode);
        $data['link_html'] = JRoute::_($link, true, $mode);
        $data['token'] = $token;

        $subject = "您账户".$data['sitename']."的密码重置请求";

        $body = "您好！\n\n 重置您账户 ".$data['sitename']." 密码的请求已经处理。您需要输入验证码来验证请求的合法。\n\n验证码是：".$data['token']."\n\n点击下面的链接，然后重置您的密码\n\n ".$data['link_text']." \n\n谢谢。";

        // Send the password reset request email.
        $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $user->email, $subject, $body);

        // Check for an error.
        if ($return !== true)
        {
            return new JException(JText::_('COM_USERS_MAIL_FAILED'), 500);
        }

        return true;
    }

    /**
     * Method to check if user reset limit has been exceeded within the allowed time period.
     *
     * @param   JUser  $user  User doing the password reset
     *
     * @return  boolean true if user can do the reset, false if limit exceeded
     *
     * @since    2.5
     */
    public function checkResetLimit($user)
    {
        $params = JFactory::getApplication()->getParams();
        $maxCount = (int) $params->get('reset_count');
        $resetHours = (int) $params->get('reset_time');
        $result = true;

        $lastResetTime = strtotime($user->lastResetTime) ? strtotime($user->lastResetTime) : 0;
        $hoursSinceLastReset = (strtotime(JFactory::getDate()->toSql()) - $lastResetTime) / 3600;

        if ($hoursSinceLastReset > $resetHours)
        {
            // If it's been long enough, start a new reset count
            $user->lastResetTime = JFactory::getDate()->toSql();
            $user->resetCount = 1;
        }
        elseif ($user->resetCount < $maxCount)
        {
            // If we are under the max count, just increment the counter
            ++$user->resetCount;
        }
        else
        {
            // At this point, we know we have exceeded the maximum resets for the time period
            $result = false;
        }

        return $result;
    }

    /**
     * 查询用户登录状态
     * */
    public function user_state(array $user)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from($db->quoteName('#__session'));
        $query->where(' username = '.$db->quote($user['username']));
        $db->setQuery($query);

        try
        {
            $result = $db->loadObjectList();

            if($result){
                return true;
            }
            else
            {
                return false;
            }
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }
    }

    /**
     * 根据用户名查询email
     * */
    public function find_email(array $user)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('email')->from($db->quoteName('#__users'));
        $query->where(' username = '.$db->quote($user['username']));
        $db->setQuery($query);

        try
        {
            $result = $db->loadObjectList();

            if($result){
                return $result;
            }
            else
            {
                return false;
            }
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }
    }

}