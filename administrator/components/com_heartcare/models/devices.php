<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/12/14
 * Time: 19:14
 */
defined('_JEXEC') or die('Restricted access');

class HeartCareModelDevices extends JModelList
{
    public function __construct(array $config)
    {
        $config['filter_fields'] = array(
            'd.id',
            'd.device_id',
            'd.device_type',
            'a.username',
            'a.name'
        );
        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('name, username, d.id AS id, d.device_id AS device_id, device_type, user_id, d.register_time AS register_time, d.produce_date AS produce_date, D.service AS service');
        $query->from($db->quoteName('#__health_device') . ' AS d');
        $query->leftJoin('#__users AS a ON d.user_id = a.id');

        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            $like = $db->quote('%'. $search . '%');
            $query->where('a.name LIKE'. $like .'OR a.username LIKE'.$like.'OR d.device_id LIKE'.$like.'OR d.device_type LIKE'.$like);
        }

        //add the list ordering clause
        $orderCol = $this->state->get('list.ordering', 'register_time');
        $orderDirn = $this->state->get('list.direction', 'asc');
        $query->order($db->escape($orderCol). ' ' .$db->escape($orderDirn));

        return $query;
    }

}