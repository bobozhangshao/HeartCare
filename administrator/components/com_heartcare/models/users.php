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
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('name, username, email, registerDate, id')->from($db->quoteName('#__users'));

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