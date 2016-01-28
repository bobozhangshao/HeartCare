<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/12
 * Time: 09:02
 */

defined('_JEXEC') or die('Restricted Access');

class HeartCareModelUser extends JModelAdmin
{
    //method to get table object
    public function getTable($type = 'HeartCare', $prefix = 'HeartCareTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * 设置model state
     * @return void
     */
    protected function populateState()
    {
        $app = JFactory::getApplication('administrator');

        //load the user state
        $pk = $app->input->getInt('id');
        $this->setState('user.id', $pk);

        $dataType = $app->getUserStateFromRequest('com_heartcare.edit.user.mylistvalue','mylistvalue','','string');

//        $dataType = $app->getUserState('com_heartcare.edit.user.mylistvalue','ECG');
//        if($app->input->getString('mylistvalue',false))
//        {
//            $dataType = $app->input->getString('mylistvalue','ECG');
//        }
        $this->setState('filter.mylistvalue',$dataType);

        $measureDate = $app->getUserStateFromRequest('com_heartcare.user.filter.mycalendar','filter_mycalendar','','string');
//        if(!($measureDate = $app->getUserState('com_heartcare.edit.user.mycalendar')))
//        {
//            $measureDate = $app->input->getInt('mycalendar');
//        }
        $this->setState('filter.mycalendar',$measureDate);

        $params = JComponentHelper::getParams('com_heartcare');
        $this->setState('params', $params);
    }

    /**
     * Abstract method for getting the form from the model.
     *
     * @param   array $data Data for the form.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed  A JForm object on success, false on failure
     *
     * @since   12.2
     */
    public function getForm($data = array(), $loadData = true)
    {
        //是从forms中找到user对应
        $form = $this->loadForm(
            'com_heartcare.user',
            'user',
            array(
                'control' => 'jform',
                'load_data' => $loadData
            )
        );


        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    //获得应该键入到form中的数据.
    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState(
            'com_heartcare.user.edit.data',
            array()
        );

        if (empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * 获取测量数据
     * @return object 测量的数据对象列
     * */
    public function getMeasureData()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('*')->from($db->quoteName('#__health_data'));
        $query->where('user_id = '.(int) $this->getState('user.id'));

        $measureData = $this->getState('filter.mycalendar');
        $dataType = $this->getState('filter.mylistvalue');

        if(!empty($dataType))
        {
            $query->where('data_type = '.$db->quote($dataType));
        }

        if(!empty($measureData))
        {
            $query->where('measure_time = '.$measureData);
        }

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

    //获取该用户注册的设备
    public function getDevices()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from($db->quoteName('#__health_device'));
        $query->where('user_id = '.(int) $this->getState('user.id'));
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

    //获取该用户选择的医生
    //return 数组(元素是对象)
    public function getDoctors()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('cb_doctors')->from($db->quoteName('#__comprofiler'));
        $query->where('user_id = '.(int) $this->getState('user.id'));
        $db->setQuery($query);
        try
        {
            $result = $db->loadObjectList();
            $result1 = json_decode($result[0]->cb_doctors, true);
            $i = 0;
            $result2 = array();

            foreach($result1['doctors'] as $id){
                $db1 = JFactory::getDbo();
                $query1 = $db1->getQuery(true);
                $query1->select('a.id as id, name, username, cb_description');
                $query1->from($db->quoteName('#__users') . ' AS a');
                $query1->leftJoin('#__comprofiler AS d ON d.user_id = a.id');
                $query1->where('d.user_id = '.(int) $id);
                $db1->setQuery($query1);
                $result2[$i++] = $db1->loadObjectList();
            }
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());

            return false;
        }

        return $result2;
    }
}