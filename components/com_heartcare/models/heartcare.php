<?php
/**
 * Created by PhpStorm.
 * User: zhangshaobo
 * Date: 15/11/2
 * Time: 15:35
 */
defined('_JEXEC') or die('Restricted Access');

class HeartCareModelHeartCare extends JModelItem
{
    //users数组
    protected $users;

    //获取一个表对象,如果可能就加载它
    public function getTable($type = 'HeartCare', $prefix = 'HeartCareTable', $config = array())
    {
        return JTable::getInstance($type ,$prefix, $config);
    }

    //获取用户名
    public function getUserName($id = 1)
    {
        if (!is_array($this->users))
        {
            $this->users = array();
        }

        if (!isset($this->users[$id]))
        {
            //请求被选的id
            $jinput = JFactory::getApplication()->input;
            $id     = $jinput->get('id', 1, 'INT');

            //获取一个TableHeartCare 实体
            $table = $this->getTable();

            //加载信息
            $table->load($id);

            //分配信息
            $this->users[$id] = $table->username;
        }

        return $this->users[$id];
    }
}


