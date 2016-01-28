<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 16/1/25
 * Time: 09:20
 */

defined('_JEXEC') or die('Restricted Access');

class HeartCareModelChoose extends JModelList
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id'
            );
        }

        parent::__construct($config);
    }

    public function getListQuery()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('a.id AS id,name,username,user_id,avatar,cb_is_doctor');
        $query->from($db->quoteName('#__users') . ' AS a');
        $query->leftJoin('#__comprofiler AS d ON d.user_id = a.id');
        $query->where('cb_is_doctor = 1');

        // Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering', 'user_id');
        $orderDirn 	= $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    //选择所有医生的描述
    public function getDescription()
    {
        $app = JFactory::getApplication();
        $doctor_id = $app->input->getInt('doctor_id');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('cb_description')->from($db->quoteName('#__comprofiler'));
        $query->where('id = '.(int) $doctor_id);

        $db->setQuery($query);
        try
        {
            $result = $db->loadObjectList();
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        return $result;
    }

    //选择该用户已选定的医生
    public function getChoosedDoctors()
    {
        $user = JFactory::getUser();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('cb_doctors')->from($db->quoteName('#__comprofiler'));
        $query->where('id = '.(int) $user->id);

        $db->setQuery($query);
        try
        {
            $temp = $db->loadObjectList();
            $result = json_decode($temp[0]->cb_doctors)->doctors;
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        return $result;
    }
}