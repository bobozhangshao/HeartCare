<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/5
 * Time: 11:28
 */

defined('_JEXEC') or die('Restricted access');

class HeartCareModelUsers extends JModelList
{
    //构造函数,选择作为过滤器的列
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id',
                'name',
                'username'
            );
        }

        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $user = JFactory::getUser();

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('name, username, email, registerDate, a.id AS id, cb_is_doctor, cb_description, cb_doctors');
        $query->from($db->quoteName('#__users') . ' AS a');
        $query->leftJoin('#__comprofiler AS d ON d.user_id = a.id');
        
        if ($user->name != 'Super User')
        {
            $like_doctor = $db->quote('%'. $user->id . '%');
            $query->where('d.cb_doctors LIKE'.$like_doctor);
        }
        //filter :like/search
        $search = $this->getState('filter.search');

        if (!empty($search))
        {
            $like = $db->quote('%'. $search . '%');
            $query->where('name LIKE'. $like .'OR username LIKE'.$like);
        }

        //add the list ordering clause
        $orderCol = $this->state->get('list.ordering', 'registerDate');
        $orderDirn = $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol). ' ' .$db->escape($orderDirn));

        return $query;
    }
}