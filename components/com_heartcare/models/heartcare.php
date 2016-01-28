<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/2
 * Time: 15:35
 */
defined('_JEXEC') or die('Restricted Access');
use Joomla\Registry\Registry;

class HeartCareModelHeartCare extends JModelList
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
        $user = JFactory::getUser();

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('*')->from($db->quoteName('#__health_data'));
        $query->where('user_id = '.(int) $user->id);


        // Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering', 'measure_time');
        $orderDirn 	= $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();
        $params = JComponentHelper::getParams('com_heartcare');

        $this->setState('wave.id', $app->input->getInt('wave_id'));

        $this->setState('wave.route', $app->input->getString('wave_route'));

        $this->setState('wave.type', $app->input->getString('wave_type'));

        $this->setState('params', $params);
    }

    //获取医生依据谱线判断
    public function getDoctorSay()
    {
        $db=JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('diagnosis')->from($db->quoteName('#__health_data'));
        $query->where('id = '.(int) $this->getState('wave.id'));

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

    //获取txt数据
    public function getTxtData()
    {
        $file = './media/com_heartcare/data/'.$this->getState('wave.route');
        $content = file_get_contents($file);

        $yname = $this->getState('wave.type');
        $arr = explode("\r\n", $content);
        $len = sizeof($arr);
        $zoom = floatval(288000/$len);

        $xarr = array();

        for ($i=0 ; $i<$len ; $i++ ){
            //$xarr[$i] = number_format($i*(1/360),1).'s';

            if ($i%72 == 0){
                $k = $i/72;
                $xarr[$i] = $k*(0.2);
            }else{
                $xarr[$i] = '';
            }
        }

        $result = array(
            'yname' => $yname,
            'len' => $len,
            'zom' => $zoom,
            'xa' => $xarr,
            'data' => $arr
        );

        $json_result = json_encode($result);

        return $json_result;
    }
}


