<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/1/25
 * Time: 09:18
 */
defined('_JEXEC') or die('Restricted Access');

class HeartCareControllerChoose extends JControllerForm
{
    public function ChooseDoctors()
    {
        $user_id = $this->input->getInt('user_id');
        //$input = JFactory::getApplication()->input;

        $doctors_arr['doctors'] = JRequest::getVar('choosed_doctors');

        $doctors = json_encode($doctors_arr);

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array($db->quoteName('cb_doctors') . ' = ' . $db->quote($doctors));
        $conditions = array($db->quoteName('user_id') . ' = '.$user_id);
        $query->update($db->quoteName('#__comprofiler'))->set($fields)->where($conditions);
        $db->setQuery($query);
        try
        {
            $db->execute();
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        $this->setRedirect('index.php/usercenter/health/choose-doctor');
    }

}