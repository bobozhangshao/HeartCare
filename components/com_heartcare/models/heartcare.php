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

    /**
     * 获取txt数据
     * return array
     */
    public function getTxtData()
    {
        $result = array();
        $data_route = $this->get_file((int) $this->getState('wave.id'));
        $file = './media/com_heartcare/data/'.$data_route;
        $content = file_get_contents($file);
        $content = preg_replace('/(\r*)\n/',"\n",$content);//将content字符串中的\n\r替换成\n
        $yname = $this->getState('wave.type');

        $arr = explode("\n", $content);

        if(end($arr) == '')
        {
            array_pop($arr);
        }

        $len = sizeof($arr);
        //缩放的倍数150000是频率2500*时间(6秒)*100,得到百分比
        $zoom = floatval(150000/$len);
        if($zoom > 100)
        {
            $zoom = 100;
        }

        $xarr = array();

        if(($yname == 'ECG')||($yname == 'ICG')||($yname == 'deltaZ')||($yname == 'Z0'))
        {
            //xarr 是x轴坐标
            for ($i=0 ; $i<$len ; $i++ ){
                //$xarr[$i] = number_format($i*(1/360),1).'s';
                //每个坐标都对应
                $xarr[$i] = 0.004*$i;
                //除以50是指0.2秒的数据量,频率为250
                /*if ($i%50 == 0){
                    $k = $i/50;
                    $xarr[$i] = $k*(0.2);
                }else{
                    $xarr[$i] = '';
                }*/
            }

            $result = array(
                'yname' => $yname,
                'len' => $len,
                'zom' => $zoom,
                'xa' => $xarr,
                'data' => $arr
            );

        }
        //ACC数据
        elseif(($yname == 'ACC')||($yname == 'GRRO'))
        {
            $arr_x = array();
            $arr_y = array();
            $arr_z = array();
            foreach($arr as $key=>$value)
            {
                $arr_xyz = explode(' ', $value);
                $arr_x[$key] = (float)$arr_xyz[0];
                $arr_y[$key] = (float)$arr_xyz[1];
                $arr_z[$key] = (float)$arr_xyz[2];

                $arr_x[$key] = round($arr_x[$key],3);
                $arr_y[$key] = round($arr_y[$key],3);
                $arr_z[$key] = round($arr_z[$key],3);
            }

            for ($i=0 ; $i<$len ; $i++ ){
                $xarr[$i] = $i*0.05;
                //除以20是指1秒的数据量,频率为20hz
//                if ($i%20 == 0){
//                    $k = $i/20;
//                    $xarr[$i] = $k;
//                }else{
//                    $xarr[$i] = '';
//                }
            }


            $result = array(
                'yname' => $yname,
                'len' => $len,
                'zom' => $zoom,
                'xa' => $xarr,
                'data_x' => $arr_x,
                'data_y' => $arr_y,
                'data_z' => $arr_z
            );

        }
        //HR,RR数据
        elseif(($yname == 'HR')||($yname == 'RR')||($yname='BP'))
        {
            $zoom = 2000/$len;
            for ($i=0 ; $i<$len ; $i++ ){
                $xarr[$i] = $i;
            }

            $result = array(
                'yname' => $yname,
                'len' => $len,
                'zom' => $zoom,
                'xa' => $xarr,
                'data' => $arr
            );

        }

        $json_result = json_encode($result);

        return $json_result;
    }

    /**
     * 内部删除测量数据
     * 按照id删除
     * */
    public function remove_measure(array $measure)
    {
        //删除数据库中记录
        $db=JFactory::getDbo();
        $query = $db->getQuery(true);
        $conditions = array(
            $db->quoteName('id') . ' = ' . $measure['id']
        );
        $query->delete($db->quoteName('#__health_data'));
        $query->where($conditions);

        $db->setQuery($query);
        try
        {
            $result = $db->execute();

            //从文件夹中移除
            if($result)
            {
                $measure_file = JPATH_BASE.'/media/com_heartcare/data/'.$measure['data_route'];
                if($measure['data_route'] != '')
                {
                    if(unlink($measure_file))
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return true;
                }
            }
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

    }

    /**
     * 删除测量数据的接口
     * 按照文件名删除measure_data在数据库中的记录
     * */
    public function remove_interface($filename,array &$response)
    {
        $db=JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete($db->quoteName('#__health_data'));
        $query->where($db->quoteName('data_route') . ' = \'' . $filename.'\'');

        $db->setQuery($query);
        try {
            $result = $db->execute();
            if($result)
            {
                $response['SQLexe'] = 'OK';
                return true;
            }
            else
            {
                $response['SQLexe'] = 'FALSE';
                return false;
            }
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());
            $response['SQL_ERROR'] = 'FALSE';
            return false;
        }


    }


    /**
     * 根据用户名查找user_id
     * */
    public function get_user_id(array $user)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')->from($db->quoteName('#__users'))->where($db->quoteName('username').' = '.$db->quote($user['username']));
        $db->setQuery($query);

        try
        {
            $result = $db->loadObjectList();

            return $result;
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }
    }

    /**
     * 根据用户id查出他所有的测量记录
     * return array
     * */
    public function get_user_files(array $user)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('data_route')->from($db->quoteName('#__health_data'))->where($db->quoteName('user_id').' = '.$db->quote($user['id']));
        $db->setQuery($query);

        try
        {
            $result = $db->loadObjectList();

            foreach($result as $key=>$value)
            {
                $tmp = get_object_vars($value);
                $result[$key] = $tmp['data_route'];
            }
//            echo "<pre>";
//            print_r($result);
//            echo "</pre>";
            return $result;
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }
    }

    /**
     * 根据用户id查出他最早测量记录,和总记录数量
     * return array
     * */
    public function get_measure_info(array $user)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('count(*),max(measure_time),min(measure_time)')->from($db->quoteName('#__health_data'))->where($db->quoteName('user_id').' = '.$db->quote($user['id']));
        $db->setQuery($query);
        $result = array();

        try
        {
            $data = $db->loadObjectList();

            foreach($data as $key=>$value)
            {
                $tmp = get_object_vars($value);
                $result['count'] = $tmp['count(*)'];
                $result['last'] = $tmp['max(measure_time)'];
                $result['first'] = $tmp['min(measure_time)'];
            }

            return $result;
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
     * 根据测量数据id查询文件名
     * return string data_route
     * */
    public function get_file($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('data_route')->from($db->quoteName('#__health_data'))->where('id = '.$id);
        $db->setQuery($query);

        try
        {
            $result = $db->loadObjectList();

            $result = $result[0]->data_route;
            return $result;
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }
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
}


